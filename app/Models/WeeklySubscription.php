<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_offer_id',
        'user_id',
        'weekly_amount',
        'currency',
        'status',
        'paypal_product_id',
        'paypal_plan_id',
        'paypal_subscription_id',
        'paypal_subscription_status',
        'paypal_payer_email',
        'started_at',
        'next_billing_at',
        'cancelled_at',
        'paypal_payload',
    ];

    protected function casts(): array
    {
        return [
            'weekly_amount' => 'decimal:2',
            'started_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paypal_payload' => 'array',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(ProjectOffer::class, 'project_offer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format((float) $this->weekly_amount, 2);
    }
}
