<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaypalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'is_live',
        'api_username',
        'api_password',
        'client_id',
        'client_secret',
        'webhook_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_live' => 'boolean',
            'api_username' => 'encrypted',
            'api_password' => 'encrypted',
            'client_id' => 'encrypted',
            'client_secret' => 'encrypted',
            'webhook_id' => 'encrypted',
        ];
    }

    public function baseUrl(): string
    {
        return $this->is_live
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    public function environmentLabel(): string
    {
        return $this->is_live ? 'Live' : 'Sandbox';
    }

    public function isConfigured(): bool
    {
        return $this->is_active
            && filled($this->client_id)
            && filled($this->client_secret);
    }

    public static function active(): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
