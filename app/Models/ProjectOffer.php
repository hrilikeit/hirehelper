<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        if (filled($this->freelancer?->name)) {
            return (string) $this->freelancer->name;
        }

        if (filled($this->freelancer_email)) {
            $localPart = Str::before((string) $this->freelancer_email, '@');
            $localPart = str_replace(['.', '_', '-'], ' ', $localPart);
            $derivedName = trim(Str::headline($localPart));

            if ($derivedName !== '') {
                return $derivedName;
            }
        }

        return 'Freelancer';
    }

    public function getFreelancerDisplayTitleAttribute(): string
    {
        return (string) ($this->freelancer?->title ?: $this->role ?: 'Freelancer profile');
    }

    public function getFreelancerDisplayLocationAttribute(): string
    {
        return (string) ($this->freelancer?->display_location ?: 'Available remotely');
    }

    public function getFreelancerDisplayAvatarUrlAttribute(): string
    {
        return $this->freelancer?->avatar_url ?: asset('workspace-assets/img/avatar-jade.svg');
    }
}
