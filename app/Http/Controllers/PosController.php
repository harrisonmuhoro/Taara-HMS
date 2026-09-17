<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $branchId = auth()->user()->branch_id;

        $categories = MenuCategory::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with(['items' => fn($q) => $q->where('is_available', true)])
            ->orderBy('sort_order')
            ->get();

        // Active reservations for room charge option
        $activeReservations = Reservation::with('guest', 'room')
            ->where('branch_id', $branchId)
            ->where('status', 'CHECKED_IN')
            ->get();

        return view('restaurant.pos', compact('categories', 'activeReservations'));
    }

    public function checkout(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,room_charge',
            'reservation_id' => 'required_if:payment_method,room_charge|nullable|exists:reservations,id',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $branchId = auth()->user()->branch_id;
            $subtotal = 0;

            $orderItems = [];
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::where('branch_id', $branchId)
                    ->where('is_available', true)
                    ->findOrFail($item['menu_item_id']);
                $lineSubtotal = $menuItem->price * $item['quantity'];
                $subtotal += $lineSubtotal;
                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'unit_price' => $menuItem->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $taxRate = 0.16; // 16% VAT
            $taxAmount = $subtotal * $taxRate;
            $total = $subtotal + $taxAmount;

            if (! empty($validated['reservation_id'])) {
                Reservation::where('branch_id', $branchId)
                    ->where('status', 'CHECKED_IN')
                    ->findOrFail($validated['reservation_id']);
            }

            $order = Order::create([
                'branch_id' => $branchId,
                'created_by' => auth()->id(),
                'reservation_id' => $validated['reservation_id'] ?? null,
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad(Order::max('id') + 1, 4, '0', STR_PAD_LEFT),
                'type' => $validated['payment_method'] === 'room_charge' ? 'room_charge' : 'walk_in',
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'completed_at' => now(),
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }
        });

        return back()->with('success', 'Order placed successfully!');
    }

    public function orders(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $branchId = auth()->user()->branch_id;

        $orders = Order::with(['items', 'creator', 'reservation.guest'])
            ->where('branch_id', $branchId)
            ->latest()
            ->paginate(20);

        return view('restaurant.orders', compact('orders'));
    }
}
