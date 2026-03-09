<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_project_id',
        'project_offer_id',
        'sender_type',
        'sender_name',
        'message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function project()
    {
        return $this->belongsTo(ClientProject::class, 'client_project_id');
    }

    public function offer()
    {
        return $this->belongsTo(ProjectOffer::class, 'project_offer_id');
    }
}
