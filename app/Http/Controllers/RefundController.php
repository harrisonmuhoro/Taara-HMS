<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\Payment;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Refund::class);

        $query = Refund::with(['payment.invoice.guest', 'processor'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->whereHas('payment', fn ($p) => $p->where('branch_id', auth()->user()->branch_id)))
            ->latest('processed_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $refunds = $query->paginate(15)->withQueryString();

        return view('finance.refunds.index', compact('refunds'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Refund::class);
        $payment = null;
        if ($request->filled('payment_id')) {
            $payment = Payment::with('invoice')
                ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
                ->findOrFail($request->payment_id);
        }

        return view('finance.refunds.create', compact('payment'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Refund::class);

        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
            'refund_method' => 'required|string|max:50',
        ]);

        try {
            DB::transaction(function () use ($validated, &$refund) {
                $payment = Payment::query()
                    ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
                    ->lockForUpdate()
                    ->findOrFail($validated['payment_id']);

                $totalRefunded = $payment->refunds()
                    ->where('status', 'COMPLETED')
                    ->lockForUpdate()
                    ->sum('amount');

                if (($totalRefunded + (float) $validated['amount']) > (float) $payment->amount) {
                    throw new \InvalidArgumentException('Refund amount cannot exceed the original payment amount.');
                }

                $refund = Refund::create(array_merge($validated, [
                    'invoice_id' => $payment->invoice_id,
                    'processed_by' => auth()->id(),
                    'processed_at' => now(),
                    'status' => 'COMPLETED',
                ]));

                $invoice = AppModelsInvoice::query()
                    ->lockForUpdate()
                    ->findOrFail($payment->invoice_id);
                $invoice->recalculateTotals();

                AuditService::log('REFUND_PROCESSED', $refund, null, $refund->toArray());
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        // Optionally update invoice status if fully refunded
        // For now, just logging the refund is sufficient.

        return redirect()->route('finance.refunds.index')
            ->with('success', 'Refund processed successfully.');
    }
}
