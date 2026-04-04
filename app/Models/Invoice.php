<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'client_project_id',
        'project_offer_id',
        'invoice_number',
        'type',
        'amount',
        'currency',
        'hours',
        'hourly_rate',
        'status',
        'payment_method',
        'paypal_transaction_id',
        'period_start',
        'period_end',
        'description',
        'meta',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'hours' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'meta' => 'array',
            'period_start' => 'date',
            'period_end' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }

    public function offer()
    {
        return $this->belongsTo(ProjectOffer::class, 'project_offer_id');
    }

    /**
     * Generate a unique invoice number: INV-YYYYMMDD-XXXXX
     */
    public static function generateNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $latest = static::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        if ($latest) {
            $seq = (int) substr($latest, -5) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Create an invoice for a weekly timesheet payment.
     */
    public static function createForTimesheet(
        \App\Models\Timesheet $timesheet,
        int $userId,
        ?int $projectId = null,
        ?string $paypalTransactionId = null,
    ): static {
        $offer = $timesheet->offer;

        return static::create([
            'user_id' => $userId,
            'client_project_id' => $projectId ?? $offer?->client_project_id,
            'project_offer_id' => $timesheet->project_offer_id,
            'invoice_number' => static::generateNumber(),
            'type' => 'weekly',
            'amount' => $timesheet->amount,
            'currency' => 'USD',
            'hours' => $timesheet->total_hours,
            'hourly_rate' => $offer?->hourly_rate,
            'status' => 'paid',
            'payment_method' => 'paypal',
            'paypal_transaction_id' => $paypalTransactionId,
            'period_start' => $timesheet->week_start,
            'period_end' => $timesheet->week_start->copy()->addDays(6),
            'description' => 'Weekly hours — ' . ($offer?->freelancer_display_name ?? 'Freelancer'),
            'paid_at' => now(),
        ]);
    }

    /**
     * Create an invoice for a bonus payment.
     */
    public static function createForBonus(
        BonusPayment $bonus,
        int $userId,
        ?int $projectId = null,
    ): static {
        $offer = $bonus->offer;

        return static::create([
            'user_id' => $userId,
            'client_project_id' => $projectId ?? $offer?->client_project_id,
            'project_offer_id' => $bonus->project_offer_id,
            'invoice_number' => static::generateNumber(),
            'type' => 'bonus',
            'amount' => $bonus->amount,
            'currency' => 'USD',
            'status' => 'paid',
            'payment_method' => 'paypal',
            'paypal_transaction_id' => $bonus->paypal_capture_id,
            'description' => 'Bonus payment' . ($bonus->note ? ' — ' . $bonus->note : ''),
            'paid_at' => $bonus->paid_at ?? now(),
        ]);
    }

    /**
     * Get formatted amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format((float) $this->amount, 2);
    }
}
