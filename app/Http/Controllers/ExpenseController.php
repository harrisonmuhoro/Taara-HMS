<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditService;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Expense::class);

        $query = Expense::with(['category', 'paymentMethod', 'creator', 'approver'])
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->latest('expense_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('receipt_number', 'like', "%{$search}%");
            });
        }

        $expenses = $query->paginate(15)->withQueryString();
        
        $statsQuery = Expense::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id));
        $stats = [
            'total_pending' => (clone $statsQuery)->where('status', 'PENDING')->sum('amount'),
            'total_approved' => (clone $statsQuery)->where('status', 'APPROVED')->sum('amount'),
        ];

        return view('finance.expenses.index', compact('expenses', 'stats'));
    }

    public function create()
    {
        $this->authorize('create', Expense::class);
        $categories = ExpenseCategory::query()
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('name')->get();
        $paymentMethods = PaymentMethod::query()
            ->where('status', 'ACTIVE')
            ->when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('name')->get();

        return view('finance.expenses.create', compact('categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Expense::class);

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
            'description' => 'required|string|max:1000',
            'receipt_number' => 'nullable|string|max:100',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $category = ExpenseCategory::when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->findOrFail($validated['category_id']);
        if (! empty($validated['payment_method_id'])) {
            PaymentMethod::when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
                ->findOrFail($validated['payment_method_id']);
        }

        $attachment = $request->hasFile('attachment')
            ? $request->file('attachment')->store('expense-receipts', 'local')
            : null;

        $expense = Expense::create(array_merge($validated, [
            'category_id' => $category->id,
            'branch_id' => auth()->user()->branch_id,
            'status' => 'PENDING',
            'created_by' => auth()->id(),
            'attachment' => $attachment,
        ]));
        AuditService::log('EXPENSE_CREATED', $expense, null, $expense->toArray());

        return redirect()->route('finance.expenses.index')
            ->with('success', 'Expense logged successfully and is pending approval.');
    }

    public function downloadAttachment(Expense $expense)
    {
        $this->authorize('view', $expense);
        abort_unless($expense->attachment && Storage::disk('local')->exists($expense->attachment), 404);

        return Storage::disk('local')->download($expense->attachment, basename($expense->attachment));
    }

    public function approve(Expense $expense)
    {
        $this->authorize('approve', $expense);

        $expense->update([
            'status' => 'APPROVED',
            'approved_by' => auth()->id(),
        ]);
        AuditService::log('EXPENSE_APPROVED', $expense, null, $expense->fresh()->toArray());

        return back()->with('success', 'Expense approved successfully.');
    }
}
