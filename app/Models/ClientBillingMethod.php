<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientBillingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'method_type',
        'label',
        'last_four',
        'is_default',
        'provider',
        'provider_customer_id',
        'provider_payer_id',
        'provider_email',
        'provider_setup_token_id',
        'provider_payment_token_id',
        'provider_payload',
        'verified_at',
    ];

    protected $appends = [
        'display_label',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'provider_payload' => 'array',
            'verified_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDisplayLabelAttribute(): string
    {
        if ($this->provider === 'paypal' || $this->method_type === 'PayPal') {
            return 'PayPal';
        }

        if ($this->provider === 'acba_arca') {
            return 'ACBA / ArCa card';
        }

        if (filled($this->last_four)) {
            return $this->method_type . ' · •••• ' . $this->last_four;
        }

        return filled($this->label)
            ? $this->method_type . ' · ' . $this->label
            : $this->method_type;
    }
}
