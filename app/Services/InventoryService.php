<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Handle receiving a purchase order.
     * Updates product stock levels and logs stock movements.
     */
    public function receivePurchase(Purchase $purchase): bool
    {
        if ($purchase->status === 'RECEIVED') {
            throw new Exception("Purchase order is already received.");
        }

        DB::transaction(function () use ($purchase) {
            $purchase = Purchase::query()->lockForUpdate()->with('items')->findOrFail($purchase->id);
            if ($purchase->status === 'RECEIVED') {
                throw new Exception("Purchase order is already received.");
            }
            $purchase->update(['status' => 'RECEIVED']);

            foreach ($purchase->items as $item) {
                $product = Product::query()->lockForUpdate()->findOrFail($item->product_id);

                // Create Stock Movement
                StockMovement::create([
                    'branch_id' => $purchase->branch_id,
                    'product_id' => $product->id,
                    'movement_type' => 'PURCHASE',
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'balance_after' => $product->current_stock + $item->quantity,
                    'created_by' => auth()->id(),
                    'notes' => 'Received from PO #' . $purchase->purchase_number,
                ]);

                // Update current stock
                $product->increment('current_stock', $item->quantity);
            }
        });

        return true;
    }

    /**
     * Handle a manual stock adjustment.
     */
    public function adjustStock(Product $product, string $type, int $quantity, ?string $notes = null): bool
    {
        if (!in_array($type, ['ADJUSTMENT', 'DAMAGE', 'CONSUMPTION', 'RETURN'])) {
            throw new Exception("Invalid adjustment type.");
        }

        DB::transaction(function () use ($product, $type, $quantity, $notes) {
            $product = Product::query()->lockForUpdate()->findOrFail($product->id);
            if ($quantity < 0 && abs($quantity) > $product->current_stock) {
                throw new Exception("Cannot deduct more than current stock.");
            }

            StockMovement::create([
                'branch_id' => $product->branch_id,
                'product_id' => $product->id,
                'movement_type' => $type,
                'quantity' => $quantity, // Negative for deduction, positive for addition
                'unit_cost' => $product->cost_price,
                'reference_type' => null,
                'reference_id' => null,
                'balance_after' => $product->current_stock + $quantity,
                'created_by' => auth()->id(),
                'notes' => $notes,
            ]);

            if ($quantity > 0) {
                $product->increment('current_stock', $quantity);
            } else {
                $product->decrement('current_stock', abs($quantity));
            }
        });

        return true;
    }
}
