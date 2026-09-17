<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'reservation_id',
        'guest_id',
        'room_id',
        'actual_check_in',
        'actual_check_out',
        'expected_check_out',
        'status',
        'checked_in_by',
        'checked_out_by',
    ];

    protected $casts = [
        'actual_check_in' => 'datetime',
        'actual_check_out' => 'datetime',
        'expected_check_out' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function folios(): HasMany
    {
        return $this->hasMany(Folio::class);
    }

    public function activeFolio(): HasOne
    {
        return $this->hasOne(Folio::class)->where('status', 'OPEN');
    }
}
