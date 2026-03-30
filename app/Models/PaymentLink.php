<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelancer_id',
        'amount',
        'currency',
        'description',
        'slug',
        'status',
        'paypal_order_id',
        'paypal_order_status',
        'paypal_capture_id',
        'paypal_capture_status',
        'paypal_payer_email',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $paymentLink): void {
            if (blank($paymentLink->slug)) {
                $paymentLink->slug = static::generateUniqueSlug();
            }

            if (blank($paymentLink->currency)) {
                $paymentLink->currency = 'USD';
            }

            if (blank($paymentLink->status)) {
                $paymentLink->status = 'open';
            }
        });
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPublicUrlAttribute(): string
    {
        return route('payment-links.show', $this);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2, '.', '');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid' || filled($this->paid_at);
    }

    public function isPayable(): bool
    {
        return $this->status === 'open';
    }

    public function applyPayPalOrder(array $order): void
    {
        $capture = data_get($order, 'purchase_units.0.payments.captures.0');

        $orderStatus = strtoupper((string) data_get($order, 'status', ''));
        $captureStatus = strtoupper((string) data_get($capture, 'status', ''));

        $status = match (true) {
            $captureStatus === 'COMPLETED' || $orderStatus === 'COMPLETED' => 'paid',
            $captureStatus === 'PENDING' => 'pending',
            $this->status === 'paid' => 'paid',
            default => 'open',
        };

        $paidAt = $this->paid_at;

        if ($status === 'paid' && ! $paidAt) {
            $paidAt = filled(data_get($capture, 'create_time'))
                ? Carbon::parse((string) data_get($capture, 'create_time'))
                : now();
        }

        $this->forceFill([
            'status' => $status,
            'paypal_order_id' => data_get($order, 'id', $this->paypal_order_id),
            'paypal_order_status' => $orderStatus ?: $this->paypal_order_status,
            'paypal_capture_id' => data_get($capture, 'id', $this->paypal_capture_id),
            'paypal_capture_status' => $captureStatus ?: $this->paypal_capture_status,
            'paypal_payer_email' => data_get($order, 'payer.email_address', $this->paypal_payer_email),
            'paid_at' => $paidAt,
        ])->save();
    }

    public static function generateUniqueSlug(): string
    {
        do {
            $slug = strtolower(Str::random(8));
        } while (static::query()->where('slug', $slug)->exists());

        return $slug;
    }
}
