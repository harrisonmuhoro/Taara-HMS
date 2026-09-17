<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);

        $query = Purchase::with(['supplier', 'creator'])->where('branch_id', auth()->user()->branch_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('purchase_number', 'like', "%{$search}%");
        }

        $purchases = $query->latest('purchase_date')->paginate(15)->withQueryString();

        return view('inventory.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $this->authorize('create', Purchase::class);

        $suppliers = Supplier::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();
        $products = Product::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();

        return view('inventory.purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Purchase::class);

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        $supplier = Supplier::where('branch_id', auth()->user()->branch_id)
            ->findOrFail($validated['supplier_id']);
        $productIds = collect($validated['items'])->pluck('product_id')->unique();
        $validProductCount = Product::where('branch_id', auth()->user()->branch_id)
            ->whereIn('id', $productIds)
            ->count();
        abort_unless($validProductCount === $productIds->count(), 403);

        $purchaseNumber = 'PO-' . date('Ym') . '-' . str_pad(Purchase::max('id') + 1, 4, '0', STR_PAD_LEFT);
        
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += ($item['quantity'] * $item['unit_cost']);
        }

        $purchase = Purchase::create([
            'branch_id' => auth()->user()->branch_id,
            'supplier_id' => $supplier->id,
            'purchase_number' => $purchaseNumber,
            'purchase_date' => $validated['purchase_date'],
            'subtotal' => $subtotal,
            'total_amount' => $subtotal,
            'status' => 'DRAFT',
            'created_by' => auth()->id(),
        ]);

        foreach ($validated['items'] as $item) {
            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_cost' => $item['unit_cost'],
                'total_amount' => $item['quantity'] * $item['unit_cost'],
            ]);
        }

        return redirect()->route('inventory.purchases.show', $purchase)->with('success', 'Purchase Order created.');
    }

    public function show(Purchase $purchase)
    {
        $this->authorize('view', $purchase);
        $purchase->load(['items.product', 'supplier', 'creator']);
        return view('inventory.purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, Purchase $purchase, InventoryService $inventoryService)
    {
        $this->authorize('update', $purchase);

        $validated = $request->validate([
            'status' => 'required|in:ORDERED,RECEIVED,CANCELLED',
        ]);

        if ($validated['status'] === 'RECEIVED' && $purchase->status !== 'RECEIVED') {
            try {
                $inventoryService->receivePurchase($purchase);
                return back()->with('success', 'Purchase Order received and stock updated.');
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        $purchase->update(['status' => $validated['status']]);
        return back()->with('success', 'Status updated.');
    }
}
