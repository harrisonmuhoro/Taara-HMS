<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', MenuItem::class);

        $branchId = auth()->user()->branch_id;
        $categories = MenuCategory::where('branch_id', $branchId)
            ->withCount('items')
            ->orderBy('sort_order')
            ->get();

        $items = MenuItem::with('category')
            ->where('branch_id', $branchId)
            ->orderBy('menu_category_id')
            ->paginate(20);

        return view('restaurant.menu.index', compact('categories', 'items'));
    }

    public function create()
    {
        $this->authorize('create', MenuItem::class);
        $categories = MenuCategory::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();
        return view('restaurant.menu.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', MenuItem::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'category_name' => 'nullable|string|max:255', // for new category
        ]);

        // Create category on the fly if requested
        if ($request->filled('category_name')) {
            $cat = MenuCategory::create([
                'branch_id' => auth()->user()->branch_id,
                'name' => $request->category_name,
            ]);
            $validated['menu_category_id'] = $cat->id;
        }

        MenuItem::create(array_merge($validated, [
            'branch_id' => auth()->user()->branch_id,
            'is_available' => $request->boolean('is_available', true),
        ]));

        return redirect()->route('restaurant.menu.index')->with('success', 'Menu item created.');
    }

    public function edit(MenuItem $menu)
    {
        $this->authorize('update', $menu);
        $categories = MenuCategory::where('branch_id', auth()->user()->branch_id)->orderBy('name')->get();
        return view('restaurant.menu.form', ['item' => $menu, 'categories' => $categories]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $this->authorize('update', $menu);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'menu_category_id' => 'required|exists:menu_categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'prep_time_minutes' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
        ]);

        $menu->update(array_merge($validated, [
            'is_available' => $request->boolean('is_available'),
        ]));

        return redirect()->route('restaurant.menu.index')->with('success', 'Menu item updated.');
    }
}
