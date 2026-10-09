<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'reservation_number',
        'guest_id',
        'room_type_id',
        'room_id',
        'booking_source_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'base_rate',
        'discount_amount',
        'tax_amount',
        'service_charge',
        'total_amount',
        'deposit_amount',
        'deposit_paid',
        'deposit_paid_at',
        'deposit_receipt_no',
        'deposit_expires_at',
        'special_requests',
        'status',
        'created_by',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'base_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_paid' => 'boolean',
        'deposit_paid_at' => 'datetime',
        'deposit_expires_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function bookingSource(): BelongsTo
    {
        return $this->belongsTo(BookingSource::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stays(): HasMany
    {
        return $this->hasMany(Stay::class);
    }

    public function mpesaTransactions(): HasMany
    {
        return $this->hasMany(MpesaTransaction::class);
    }

    public function getPaidAmountAttribute(): string
    {
        $paidAmount = $this->relationLoaded('mpesaTransactions')
            ? $this->mpesaTransactions->where('status', 'completed')->sum('amount')
            : $this->mpesaTransactions()->where('status', 'completed')->sum('amount');

        // Preserve historical reservations created before transaction tracking.
        if ($this->deposit_paid && ! $this->deposit_receipt_no) {
            return Money::fromMinor(Money::toMinor($this->deposit_amount) + Money::toMinor($paidAmount));
        }

        return $paidAmount;
    }

    public function getBalanceDueAttribute(): string
    {
        return Money::fromMinor(max(0, Money::toMinor($this->total_amount) - Money::toMinor($this->paid_amount)));
    }

    public function getNightsAttribute(): int
    {
        return max(1, $this->check_in_date->diffInDays($this->check_out_date));
    }
}
