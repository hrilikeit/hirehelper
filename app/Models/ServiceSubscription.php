<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSubscription extends Model
{
    protected $fillable = [
        'service_id',
        'user_id',
        'status',
        'paypal_order_id',
        'paypal_capture_id',
        'paypal_subscription_id',
        'paypal_subscription_status',
        'paypal_payer_email',
        'amount',
        'currency',
        'subscribed_at',
        'next_billing_at',
        'cancelled_at',
        'paypal_payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'subscribed_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paypal_payload' => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
