<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoginToken extends Model
{
    protected $fillable = [
        'user_id',
        'created_by',
        'token',
        'expires_at',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Create a one-time login token for a user (valid for 5 minutes).
     */
    public static function createFor(int $userId, int $createdBy): static
    {
        return static::create([
            'user_id' => $userId,
            'created_by' => $createdBy,
            'token' => Str::random(48),
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * Check if this token is still valid.
     */
    public function isValid(): bool
    {
        return ! $this->used_at && $this->expires_at->isFuture();
    }
}
