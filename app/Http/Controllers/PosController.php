<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MpesaTransaction;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Setting;
use App\Services\DocumentNumberService;
use App\Services\MpesaService;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PosController extends Controller
{
    public function __construct(
        protected DocumentNumberService $documentNumbers,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Order::class);

        $branchId = auth()->user()->branch_id;

        $categories = MenuCategory::where('branch_id', $branchId)
            ->where('is_active', true)
            ->with(['items' => fn ($q) => $q->where('is_available', true)])
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
        abort_unless(
            auth()->user()->isSuperAdmin()
                || auth()->user()->hasPermission('restaurant.order_create')
                || auth()->user()->hasPermission('restaurant.manage'),
            403
        );

        $branchId = $request->user()->branch_id;
        $isSuper = $request->user()->isSuperAdmin();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_item_id' => [
                'required',
                Rule::exists('menu_items', 'id')
                    ->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId))
                    ->where('is_available', true),
            ],
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,room_charge,mpesa',
            'reservation_id' => [
                'required_if:payment_method,room_charge',
                'nullable',
                Rule::exists('reservations', 'id')->when(! $isSuper, fn ($r) => $r->where('branch_id', $branchId)),
            ],
            'phone' => 'required_if:payment_method,mpesa|nullable|string',
            'notes' => 'nullable|string',
        ]);

        $order = DB::transaction(function () use ($validated) {
            $branchId = auth()->user()->branch_id;
            $subtotalMinor = 0;

            $orderItems = [];
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::where('branch_id', $branchId)
                    ->where('is_available', true)
                    ->findOrFail($item['menu_item_id']);
                $lineSubtotalMinor = Money::toMinor($menuItem->price) * $item['quantity'];
                $subtotalMinor += $lineSubtotalMinor;
                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'unit_price' => Money::fromMinor(Money::toMinor($menuItem->price)),
                    'quantity' => $item['quantity'],
                    'subtotal' => Money::fromMinor($lineSubtotalMinor),
                ];
            }

            $taxRate = Money::percentageBasisPoints(Setting::getByKey('tax_rate', (int) $branchId, '0.00'));
            $taxAmountMinor = Money::percentageOf($subtotalMinor, $taxRate);
            $totalMinor = $subtotalMinor + $taxAmountMinor;

            if (! empty($validated['reservation_id'])) {
                Reservation::where('branch_id', $branchId)
                    ->where('status', 'CHECKED_IN')
                    ->findOrFail($validated['reservation_id']);
            }

            $isMpesa = $validated['payment_method'] === 'mpesa';

            $order = Order::create([
                'branch_id' => $branchId,
                'created_by' => auth()->id(),
                'reservation_id' => $validated['reservation_id'] ?? null,
                'order_number' => $this->documentNumbers->next($branchId, 'ORD'),
                'type' => $validated['payment_method'] === 'room_charge' ? 'room_charge' : 'walk_in',
                'status' => $isMpesa ? 'pending' : 'completed',
                'notes' => $validated['notes'] ?? null,
                'subtotal' => Money::fromMinor($subtotalMinor),
                'tax_amount' => Money::fromMinor($taxAmountMinor),
                'total' => Money::fromMinor($totalMinor),
                'payment_method' => $validated['payment_method'],
                'completed_at' => $isMpesa ? null : now(),
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        if ($validated['payment_method'] === 'mpesa') {
            try {
                $mpesaService = app(MpesaService::class);
                $stkResponse = $mpesaService->stkPush(
                    $validated['phone'],
                    $order->total,
                    $order->order_number,
                    'Restaurant Order '.$order->order_number
                );

                if (isset($stkResponse['ResponseCode']) && $stkResponse['ResponseCode'] == '0') {
                    MpesaTransaction::create([
                        'order_id' => $order->id,
                        'transaction_type' => 'STK_PUSH',
                        'merchant_request_id' => $stkResponse['MerchantRequestID'],
                        'checkout_request_id' => $stkResponse['CheckoutRequestID'],
                        'phone_number' => $validated['phone'],
                        'amount' => $order->total,
                        'status' => 'pending',
                    ]);

                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'checkout_request_id' => $stkResponse['CheckoutRequestID'],
                            'message' => 'STK Push sent to '.$validated['phone'],
                        ]);
                    }

                    return back()->with('success', 'Order #'.$order->order_number.' placed! STK push prompt sent to '.$validated['phone'].'.');
                }

                $errorMessage = $stkResponse['errorMessage'] ?? $stkResponse['ResponseDescription'] ?? 'Failed to initiate STK push.';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'order_id' => $order->id,
                        'message' => $errorMessage,
                    ], 400);
                }

                return back()->with('error', 'Order placed as pending, but M-Pesa prompt failed: '.$errorMessage);
            } catch (\Exception $e) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'order_id' => $order->id,
                        'message' => $e->getMessage(),
                    ], 500);
                }

                return back()->with('error', 'Order placed as pending, but M-Pesa error: '.$e->getMessage());
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'message' => 'Order placed successfully!',
            ]);
        }

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
