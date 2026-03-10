<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'title',
        'headline',
        'specialization',
        'hourly_rate',
        'overview',
        'bio',
        'skills',
        'tools',
        'country',
        'city',
        'location',
        'english_level',
        'timezone',
        'availability',
        'avatar',
        'status',
        'years_experience',
        'average_rating',
        'review_count',
        'completed_jobs',
        'is_featured',
        'portfolio_url',
        'linkedin_url',
        'github_url',
        'intro_video_url',
        'internal_notes',
        'added_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'tools' => 'array',
            'hourly_rate' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function offers()
    {
        return $this->hasMany(ProjectOffer::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by_user_id');
    }

    public function getAvatarUrlAttribute(): string
    {
        return asset('workspace-assets/img/' . ($this->avatar ?: 'avatar-jade.svg'));
    }

    public function getDisplayLocationAttribute(): string
    {
        return collect([$this->city, $this->country ?: $this->location])
            ->filter()
            ->implode(', ');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('status', 'active');
    }
}
