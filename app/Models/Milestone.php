<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milestone extends Model
{
    protected $fillable = [
        'milestone_project_id',
        'sort_order',
        'name',
        'description',
        'amount',
        'status',
        'paypal_order_id',
        'paypal_capture_id',
        'funded_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'funded_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(MilestoneProject::class, 'milestone_project_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFunded(): bool
    {
        return $this->status === 'funded';
    }

    public function isReleased(): bool
    {
        return $this->status === 'released';
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format((float) $this->amount, 2);
    }
}
