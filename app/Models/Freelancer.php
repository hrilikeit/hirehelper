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
        'hourly_rate',
        'overview',
        'skills',
        'location',
        'avatar',
        'status',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'hourly_rate' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function offers()
    {
        return $this->hasMany(ProjectOffer::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        return asset('workspace-assets/img/' . ($this->avatar ?: 'avatar-jade.svg'));
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('status', 'active');
    }
}
