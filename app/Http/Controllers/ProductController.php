<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::with('category')->where('branch_id', auth()->user()->branch_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('inventory.products.index', compact('products'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $categories = InventoryCategory::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();

        return view('inventory.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'category_id' => [
                'required',
                Rule::exists('inventory_categories', 'id')
                    ->where(fn ($query) => $query->where('branch_id', auth()->user()->branch_id)),
            ],
            'unit' => 'required|string|max:50',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        if (empty($validated['sku'])) {
            $validated['sku'] = strtoupper(Str::random(8));
        }

        Product::create(array_merge($validated, [
            'branch_id' => auth()->user()->branch_id,
            'current_stock' => 0,
            'status' => 'active',
        ]));

        return redirect()->route('inventory.products.index')->with('success', 'Product created successfully.');
    }
}
