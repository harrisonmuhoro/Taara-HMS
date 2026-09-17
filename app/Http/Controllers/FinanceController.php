<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * Display the finance dashboard (Invoices).
     */
    public function invoices(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);
        $query = Invoice::with(['guest', 'branch'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->latest('issued_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('guest', function($g) use ($search) {
                      $g->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        $stats = [
            'total_unpaid' => Invoice::whereIn('status', ['UNPAID', 'PARTIALLY_PAID'])->sum('balance_due'),
            'total_paid' => Invoice::where('status', 'PAID')->sum('amount_paid'),
        ];

        return view('finance.invoices', compact('invoices', 'stats'));
    }

    /**
     * Display a specific invoice.
     */
    public function showInvoice(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['guest', 'branch', 'items', 'payments']);
        return view('finance.invoice_show', compact('invoice'));
    }

    /**
     * Display the payments list.
     */
    public function payments(Request $request)
    {
        $this->authorize('viewAny', Payment::class);
        $query = Payment::with(['invoice.guest', 'branch'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('finance.payments', compact('payments'));
    }
}
