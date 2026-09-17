<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Supplier::class);

        $query = Supplier::where('branch_id', auth()->user()->branch_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('supplier_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('inventory.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorize('create', Supplier::class);
        return view('inventory.suppliers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Supplier::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
        ]);

        $supplierNumber = 'SUP-' . str_pad(Supplier::max('id') + 1, 5, '0', STR_PAD_LEFT);

        Supplier::create(array_merge($validated, [
            'branch_id' => auth()->user()->branch_id,
            'supplier_number' => $supplierNumber,
            'status' => 'active',
        ]));

        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier added successfully.');
    }
}
