<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'setting_key',
        'setting_value',
        'setting_type',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function getByKey(string $key, ?int $branchId = null, mixed $default = null): mixed
    {
        $setting = $branchId
            ? static::where('setting_key', $key)->where('branch_id', $branchId)->first()
            : null;

        $setting ??= static::where('setting_key', $key)
            ->whereNull('branch_id')
            ->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->setting_type) {
            'boolean' => (bool) $setting->setting_value,
            'integer' => (int) $setting->setting_value,
            'json' => json_decode($setting->setting_value, true),
            default => $setting->setting_value,
        };
    }
}
