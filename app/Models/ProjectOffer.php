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
        'freelancer_email',
        'sales_manager_id',
        'project_manager_id',
        'role',
        'hourly_rate',
        'weekly_limit',
        'manual_time',
        'multi_offer',
        'status',
        'payment_status',
        'billing_method',
        'external_reference',
        'notes',
        'sent_at',
        'accepted_at',
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
            'accepted_at' => 'datetime',
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
        return $this->belongsTo(Freelancer::class)->withTrashed();
    }

    public function salesManager()
    {
        return $this->belongsTo(User::class, 'sales_manager_id');
    }

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function messages()
    {
        return $this->hasMany(ProjectMessage::class);
    }

    public function getWeeklyAmountAttribute(): float
    {
        return (float) $this->hourly_rate * (int) $this->weekly_limit;
    }

    public function getIsAcceptedAttribute(): bool
    {
        return in_array((string) $this->status, ['active', 'closed'], true);
    }

    public function getFreelancerDisplayNameAttribute(): string
    {
        return $this->freelancer_email
            ?: $this->freelancer?->contact_email
            ?: $this->freelancer?->name
            ?: 'Freelancer';
    }

    public function getFreelancerDisplayLocationAttribute(): string
    {
        return $this->freelancer?->display_location ?: 'Email invite';
    }

    public function getFreelancerDisplayAvatarUrlAttribute(): string
    {
        return $this->freelancer?->avatar_url ?: asset('workspace-assets/img/avatar-jade.svg');
    }
}
