<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreelancerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'freelancer_id',
        'review_title',
        'date_from',
        'date_to',
        'stars',
        'hours',
        'rate',
        'review_text',
    ];

    protected function casts(): array
    {
        return [
            'date_from' => 'date',
            'date_to' => 'date',
            'stars' => 'integer',
            'hours' => 'integer',
            'rate' => 'decimal:2',
        ];
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(Freelancer::class);
    }
}
