<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_project_id',
        'freelancer_id',
        'role',
        'hourly_rate',
        'weekly_limit',
        'manual_time',
        'multi_offer',
        'status',
        'billing_method',
        'sent_at',
        'activated_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'hourly_rate' => 'decimal:2',
            'manual_time' => 'boolean',
            'multi_offer' => 'boolean',
            'sent_at' => 'datetime',
            'activated_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function messages()
    {
        return $this->hasMany(ProjectMessage::class);
    }

    public function getWeeklyAmountAttribute(): float
    {
        return (float) $this->hourly_rate * (int) $this->weekly_limit;
    }
}
