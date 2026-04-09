<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'freelancer_id',
        'name',
        'slug',
        'description',
        'monthly_price',
        'currency',
        'active_users',
        'star_rating',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'monthly_price' => 'decimal:2',
            'star_rating' => 'decimal:2',
        ];
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ServiceSubscription::class);
    }

    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(ServiceSubscription::class)->where('status', 'active');
    }

    /**
     * Generate a unique slug from the service name.
     */
    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function getPublicUrlAttribute(): string
    {
        return url('/services/' . $this->slug);
    }
}
