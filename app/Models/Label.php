<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    protected $fillable = [
        'name',
        'color',
        'description',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(ClientProject::class, 'client_project_label');
    }
}
