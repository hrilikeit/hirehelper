<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\AdminAccess;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company',
        'phone',
        'job_title',
        'avatar_url',
        'is_active',
        'notify_messages',
        'notify_reports',
        'reminder_frequency',
        'country',
        'last_login_at',
        'last_login_ip',
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
            'is_active' => 'boolean',
            'notify_messages' => 'boolean',
            'notify_reports' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function roleEnum(): ?UserRole
    {
        return UserRole::tryFrom((string) $this->role);
    }

    public function isRole(UserRole|string $role): bool
    {
        $value = $role instanceof UserRole ? $role->value : $role;

        return $this->role === $value;
    }

    /**
     * @param  array<int, UserRole|string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->isRole($role)) {
                return true;
            }
        }

        return false;
    }

    public function isInternalUser(): bool
    {
        return $this->roleEnum()?->isInternal() ?? false;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return AdminAccess::canAccessAdmin($this);
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

    public function invoiceDetail()
    {
        return $this->hasOne(ClientInvoiceDetail::class);
    }

    public function addedFreelancers()
    {
        return $this->hasMany(Freelancer::class, 'added_by_user_id');
    }

    public function soldProjects()
    {
        return $this->hasMany(ClientProject::class, 'sales_manager_id');
    }

    public function managedProjects()
    {
        return $this->hasMany(ClientProject::class, 'project_manager_id');
    }

    public function soldOffers()
    {
        return $this->hasMany(ProjectOffer::class, 'sales_manager_id');
    }

    public function managedOffers()
    {
        return $this->hasMany(ProjectOffer::class, 'project_manager_id');
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    /**
     * Record login time, IP, and detect country via IP geolocation.
     */
    public function recordLogin(string $ip): void
    {
        $this->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);

        // Detect country from IP if not already set
        if (! $this->country && $ip !== '127.0.0.1') {
            try {
                $response = @file_get_contents("https://ipapi.co/{$ip}/country_name/");
                if ($response && strlen($response) < 100 && ! str_contains($response, 'error')) {
                    $this->country = trim($response);
                }
            } catch (\Throwable) {
                // Silently fail — country is optional
            }
        }

        $this->save();
    }
}
