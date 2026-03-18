<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcbaSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'is_live',
        'test_base_url',
        'test_username',
        'test_password',
        'live_base_url',
        'live_username',
        'live_password',
        'verification_amount',
        'verification_currency',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_live' => 'boolean',
            'test_username' => 'encrypted',
            'test_password' => 'encrypted',
            'live_username' => 'encrypted',
            'live_password' => 'encrypted',
            'verification_amount' => 'decimal:2',
        ];
    }

    /**
     * Safe computed attribute for Filament table / infolist rendering.
     */
    public function getEnvironmentLabelAttribute(): string
    {
        return $this->is_live ? 'Live' : 'Test';
    }

    /**
     * Keep the old method for existing service calls.
     */
    public function environmentLabel(): string
    {
        return $this->environment_label;
    }

    public function baseUrl(): string
    {
        $url = $this->is_live
            ? ($this->live_base_url ?: 'https://ipay.arca.am/payment/rest')
            : ($this->test_base_url ?: 'https://ipaytest.arca.am:8445/payment/rest');

        return rtrim((string) $url, '/');
    }

    public function username(): ?string
    {
        return $this->is_live ? $this->live_username : $this->test_username;
    }

    public function password(): ?string
    {
        return $this->is_live ? $this->live_password : $this->test_password;
    }

    public function registerUrl(): string
    {
        return $this->baseUrl() . '/register.do';
    }

    public function orderStatusUrl(): string
    {
        return $this->baseUrl() . '/getOrderStatusExtended.do';
    }

    public function isConfigured(): bool
    {
        return $this->is_active
            && filled($this->username())
            && filled($this->password())
            && filled($this->baseUrl());
    }

    public function currencyNumericCode(): string
    {
        return match (strtoupper((string) $this->verification_currency)) {
            'USD' => '840',
            'EUR' => '978',
            'RUB' => '643',
            default => '051',
        };
    }

    public function verificationAmountMinorUnits(): int
    {
        return (int) round((float) $this->verification_amount * 100);
    }

    public static function active(): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
