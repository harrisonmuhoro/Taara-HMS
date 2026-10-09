<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'folio_id',
        'guest_id',
        'invoice_number',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'service_charge',
        'grand_total',
        'amount_paid',
        'balance_due',
        'status',
        'issued_at',
        'due_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function folio(): BelongsTo
    {
        return $this->belongsTo(Folio::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function recalculateTotals(): void
    {
        $amountPaidMinor = Money::toMinor($this->payments()->where('status', 'COMPLETED')->sum('amount'));
        $totalRefundsMinor = Money::toMinor($this->refunds()->where('status', 'COMPLETED')->sum('amount'));
        $effectivePaidMinor = max(0, $amountPaidMinor - $totalRefundsMinor);
        $grandTotalMinor = Money::toMinor($this->grand_total);
        $balanceDueMinor = max(0, $grandTotalMinor - $effectivePaidMinor);
        $this->amount_paid = Money::fromMinor($amountPaidMinor);
        $this->balance_due = Money::fromMinor($balanceDueMinor);

        if ($balanceDueMinor <= 0 && $grandTotalMinor > 0) {
            $this->status = 'PAID';
        } elseif ($effectivePaidMinor > 0) {
            $this->status = 'PARTIALLY_PAID';
        }
        $this->save();
    }
}
