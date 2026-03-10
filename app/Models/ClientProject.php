<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sales_manager_id',
        'project_manager_id',
        'title',
        'description',
        'experience_level',
        'timeframe',
        'specialty',
        'status',
        'external_reference',
        'acceptance_notes',
        'accepted_at',
        'last_saved_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'last_saved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salesManager()
    {
        return $this->belongsTo(User::class, 'sales_manager_id');
    }

    public function projectManager()
    {
        return $this->belongsTo(User::class, 'project_manager_id');
    }

    public function offers()
    {
        return $this->hasMany(ProjectOffer::class);
    }

    public function messages()
    {
        return $this->hasMany(ProjectMessage::class);
    }

    public function latestOffer()
    {
        return $this->hasOne(ProjectOffer::class)->latestOfMany();
    }

    public function getIsAcceptedAttribute(): bool
    {
        return ! in_array((string) $this->status, ['draft', 'pending'], true);
    }
}
