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
     * Get daily hours for the trend chart (last N weeks, one bar per day).
     */
    public static function weeklyTrend(int $userId, int $weeks = 6): array
    {
        $dayColumns = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
        $results = [];
        $weekStart = static::weekStartFor(now());

        // Get all timesheets for this period
        $earliest = $weekStart->copy()->subWeeks($weeks - 1);
        $timesheets = static::query()
            ->where('week_start', '>=', $earliest)
            ->whereHas('offer', fn ($q) => $q
                ->whereHas('project', fn ($p) => $p->where('user_id', $userId))
                ->where('status', 'active')
            )
            ->get()
            ->keyBy('week_start');

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $ws = $weekStart->copy()->subWeeks($i);

            foreach ($dayColumns as $dayIdx => $dayCol) {
                $date = $ws->copy()->addDays($dayIdx);
                $ts = $timesheets->first(fn ($t) => $t->week_start->toDateString() === $ws->toDateString());
                $hours = $ts ? round((float) $ts->{$dayCol}, 2) : 0;

                $results[] = [
                    'week' => $date->format('D j'),
                    'hours' => $hours,
                ];
            }
        }

        return $results;
    }
}
