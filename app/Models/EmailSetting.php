<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if an email type is active.
     */
    public static function isActive(string $key): bool
    {
        $setting = static::query()->where('key', $key)->first();

        // Default to active if the setting doesn't exist yet
        return $setting ? $setting->is_active : true;
    }
}
