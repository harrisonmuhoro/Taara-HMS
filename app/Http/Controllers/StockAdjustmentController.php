<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', StockMovement::class);

        $query = StockMovement::with(['product', 'creator'])->where('branch_id', auth()->user()->branch_id);

        if ($request->filled('type')) {
            $query->where('movement_type', $request->type);
        }
        
        $movements = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('inventory.adjustments.index', compact('movements'));
    }

    public function create()
    {
        $this->authorize('create', StockMovement::class);

        $products = Product::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();

        return view('inventory.adjustments.create', compact('products'));
    }

    public function store(Request $request, InventoryService $inventoryService)
    {
        $this->authorize('create', StockMovement::class);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:ADJUSTMENT,DAMAGE,CONSUMPTION,RETURN',
            'quantity' => 'required|integer|not_in:0',
            'notes' => 'required|string|max:500',
        ]);

        $product = Product::where('branch_id', auth()->user()->branch_id)
            ->findOrFail($validated['product_id']);

        if ($validated['quantity'] < 0 && abs($validated['quantity']) > $product->current_stock) {
            return back()->withErrors(['quantity' => 'Cannot deduct more than current stock.'])->withInput();
        }

        try {
            $inventoryService->adjustStock($product, $validated['movement_type'], $validated['quantity'], $validated['notes']);
            return redirect()->route('inventory.adjustments.index')->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
