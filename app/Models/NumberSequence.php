<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberSequence extends Model
{
    use HasFactory;

protected $fillable = [
        'branch_id',
        'document_type',
        'current_value',
    ];

protected $casts = [
        'current_value' => 'integer',
    ];

public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
