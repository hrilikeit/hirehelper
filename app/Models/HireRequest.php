<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HireRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'project_title',
        'needs',
        'outcome',
        'timeline',
        'budget',
        'team',
        'context',
        'name',
        'email',
        'company',
        'website',
        'source',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
    ];
}
