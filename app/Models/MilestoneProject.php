<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MilestoneProject extends Model
{
    protected $fillable = [
        'user_id',
        'freelancer_id',
        'title',
        'description',
        'token',
        'total_amount',
        'status',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'confirmed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $project) {
            if (empty($project->token)) {
                $project->token = Str::random(48);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('sort_order');
    }

    public function getPublicUrlAttribute(): string
    {
        return route('milestones.show', $this->token);
    }

    public function getTotalFundedAttribute(): float
    {
        return (float) $this->milestones()->where('status', 'funded')->sum('amount')
             + (float) $this->milestones()->where('status', 'released')->sum('amount');
    }

    public function getTotalReleasedAttribute(): float
    {
        return (float) $this->milestones()->where('status', 'released')->sum('amount');
    }

    public function recalculateTotal(): void
    {
        $this->update(['total_amount' => $this->milestones()->sum('amount')]);
    }
}
