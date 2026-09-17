<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestDocument extends Model
{
    use HasFactory;

    protected $fillable = ['guest_id', 'uploaded_by', 'name', 'path', 'mime_type', 'size'];

    public function guest(): BelongsTo { return $this->belongsTo(Guest::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }
}
