<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company',
        'phone',
        'notify_messages',
        'notify_reports',
        'reminder_frequency',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_messages' => 'boolean',
            'notify_reports' => 'boolean',
        ];
    }

    public function projects()
    {
        return $this->hasMany(ClientProject::class);
    }

    public function billingMethods()
    {
        return $this->hasMany(ClientBillingMethod::class);
    }

    public function defaultBillingMethod()
    {
        return $this->hasOne(ClientBillingMethod::class)->where('is_default', true);
    }
}
