<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'project_offer_id',
        'week_start',
        'sun',
        'mon',
        'tue',
        'wed',
        'thu',
        'fri',
        'sat',
        'total_hours',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'sun' => 'decimal:2',
            'mon' => 'decimal:2',
            'tue' => 'decimal:2',
            'wed' => 'decimal:2',
            'thu' => 'decimal:2',
            'fri' => 'decimal:2',
            'sat' => 'decimal:2',
            'total_hours' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function offer()
    {
        return $this->belongsTo(ProjectOffer::class, 'project_offer_id');
    }

    /**
     * Recalculate total_hours and amount from daily values.
     */
    public function recalculate(): static
    {
        $this->total_hours = (float) $this->sun + (float) $this->mon + (float) $this->tue
            + (float) $this->wed + (float) $this->thu + (float) $this->fri + (float) $this->sat;

        $rate = (float) ($this->offer?->hourly_rate ?? 0);
        $this->amount = $this->total_hours * $rate;

        return $this;
    }

    /**
     * Get the week start date for a given date (Sunday-based weeks).
     */
    public static function weekStartFor(\DateTimeInterface|string $date): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($date)->startOfWeek(\Carbon\Carbon::SUNDAY);
    }

    /**
     * Get current week's total spend for a user across all active offers.
     */
    public static function currentWeekSpend(int $userId): float
    {
        $weekStart = static::weekStartFor(now());

        return static::query()
            ->where('week_start', $weekStart)
            ->whereHas('offer', fn ($q) => $q
                ->whereHas('project', fn ($p) => $p->where('user_id', $userId))
                ->where('status', 'active')
            )
            ->sum('amount');
    }

    /**
     * Get weekly totals for the Hours trend chart (last 6 weeks).
     */
    public static function weeklyTrend(int $userId, int $weeks = 6): array
    {
        $results = [];
        $weekStart = static::weekStartFor(now());

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = $weekStart->copy()->subWeeks($i);
            $hours = static::query()
                ->where('week_start', $start)
                ->whereHas('offer', fn ($q) => $q
                    ->whereHas('project', fn ($p) => $p->where('user_id', $userId))
                    ->where('status', 'active')
                )
                ->sum('total_hours');

            $results[] = [
                'week' => $start->format('M j'),
                'hours' => round((float) $hours, 2),
            ];
        }

        return $results;
    }
}
