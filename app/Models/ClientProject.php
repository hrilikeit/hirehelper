<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'experience_level',
        'timeframe',
        'specialty',
        'status',
        'last_saved_at',
    ];

    protected function casts(): array
    {
        return [
            'last_saved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
