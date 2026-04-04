<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\ClientProject;
use App\Models\ProjectOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdminAccess
{
    public static function role(?User $user): ?UserRole
    {
        return $user ? UserRole::tryFrom((string) $user->role) : null;
    }

    public static function isInternal(?User $user): bool
    {
        return self::role($user)?->isInternal() ?? false;
    }

    public static function isSuperAdmin(?User $user): bool
    {
        return self::role($user) === UserRole::SuperAdmin;
    }

    public static function isAdmin(?User $user): bool
    {
        return self::role($user) === UserRole::Admin;
    }

    public static function isProjectManager(?User $user): bool
    {
        return self::role($user) === UserRole::ProjectManager;
    }

    public static function isSalesManager(?User $user): bool
    {
        return self::role($user) === UserRole::SalesManager;
    }

    public static function canAccessAdmin(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return self::isInternal($user) && ($user->is_active ?? true);
    }

    public static function canViewInternalUsers(?User $user): bool
    {
        return self::isSuperAdmin($user) || self::isAdmin($user);
    }

    public static function canManageInternalUsers(?User $user): bool
    {
        return self::isSuperAdmin($user);
    }

    public static function canViewClients(?User $user): bool
    {
        return self::isInternal($user);
    }

    public static function canEditClients(?User $user): bool
    {
        return self::isSuperAdmin($user) || self::isAdmin($user);
    }

    public static function canViewFreelancers(?User $user): bool
    {
        return self::isInternal($user);
    }

    public static function canManageFreelancers(?User $user): bool
    {
        return self::isSuperAdmin($user) || self::isAdmin($user) || self::isSalesManager($user);
    }

    public static function canViewProjects(?User $user): bool
    {
        return self::isInternal($user);
    }

    public static function canCreateProjects(?User $user): bool
    {
        return self::isSuperAdmin($user) || self::isAdmin($user) || self::isSalesManager($user);
    }

    public static function canViewOffers(?User $user): bool
    {
        return self::isInternal($user);
    }

    public static function canCreateOffers(?User $user): bool
    {
        return self::isSuperAdmin($user) || self::isAdmin($user) || self::isSalesManager($user);
    }

    public static function canManageAcceptedDelivery(?User $user): bool
    {
        return self::isSuperAdmin($user) || self::isAdmin($user) || self::isProjectManager($user);
    }

    public static function scopeProjects(Builder $query, ?User $user): Builder
    {
        if (self::isSuperAdmin($user) || self::isAdmin($user)) {
            return $query;
        }

        if (self::isSalesManager($user) && $user) {
            return $query
                ->whereIn('status', ['draft', 'pending'])
                ->where(function (Builder $builder) use ($user) {
                    $builder
                        ->whereNull('sales_manager_id')
                        ->orWhere('sales_manager_id', $user->id);
                });
        }

        if (self::isProjectManager($user) && $user) {
            return $query
                ->whereIn('status', ['accepted', 'completed', 'cancelled'])
                ->where(function (Builder $builder) use ($user) {
                    $builder
                        ->whereNull('project_manager_id')
                        ->orWhere('project_manager_id', $user->id);
                });
        }

        return $query->whereRaw('1 = 0');
    }

    public static function scopeActiveProjects(Builder $query, ?User $user): Builder
    {
        if (self::isSuperAdmin($user) || self::isAdmin($user)) {
            return $query;
        }

        if (self::isProjectManager($user) && $user) {
            return $query
                ->where(function (Builder $builder) use ($user) {
                    $builder
                        ->whereNull('project_manager_id')
                        ->orWhere('project_manager_id', $user->id);
                });
        }

        // Sales managers do not see active projects
        return $query->whereRaw('1 = 0');
    }

    public static function scopeOffers(Builder $query, ?User $user): Builder
    {
        if (self::isSuperAdmin($user) || self::isAdmin($user)) {
            return $query;
        }

        if (self::isSalesManager($user) && $user) {
            return $query
                ->where('status', 'pending')
                ->where(function (Builder $builder) use ($user) {
                    $builder
                        ->whereNull('sales_manager_id')
                        ->orWhere('sales_manager_id', $user->id);
                });
        }

        if (self::isProjectManager($user) && $user) {
            return $query
                ->whereIn('status', ['active', 'closed'])
                ->where(function (Builder $builder) use ($user) {
                    $builder
                        ->whereNull('project_manager_id')
                        ->orWhere('project_manager_id', $user->id);
                });
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canEditProject(?User $user, ClientProject $project): bool
    {
        if (self::isSuperAdmin($user) || self::isAdmin($user)) {
            return true;
        }

        if (self::isSalesManager($user) && $user) {
            return in_array((string) $project->status, ['draft', 'pending'], true)
                && (! $project->sales_manager_id || $project->sales_manager_id === $user->id);
        }

        if (self::isProjectManager($user) && $user) {
            return in_array((string) $project->status, ['accepted', 'completed', 'cancelled'], true)
                && (! $project->project_manager_id || $project->project_manager_id === $user->id);
        }

        return false;
    }

    public static function canDeleteProject(?User $user, ClientProject $project): bool
    {
        if (self::isSuperAdmin($user) || self::isAdmin($user)) {
            return true;
        }

        if (self::isSalesManager($user) && $user) {
            return in_array((string) $project->status, ['draft', 'pending'], true)
                && (! $project->sales_manager_id || $project->sales_manager_id === $user->id);
        }

        return false;
    }

    public static function canEditOffer(?User $user, ProjectOffer $offer): bool
    {
        if (self::isSuperAdmin($user) || self::isAdmin($user)) {
            return true;
        }

        if (self::isSalesManager($user) && $user) {
            return $offer->status === 'pending'
                && (! $offer->sales_manager_id || $offer->sales_manager_id === $user->id);
        }

        if (self::isProjectManager($user) && $user) {
            return in_array((string) $offer->status, ['active', 'closed'], true)
                && (! $offer->project_manager_id || $offer->project_manager_id === $user->id);
        }

        return false;
    }

    public static function canDeleteOffer(?User $user, ProjectOffer $offer): bool
    {
        if (self::isSuperAdmin($user) || self::isAdmin($user)) {
            return true;
        }

        if (self::isSalesManager($user) && $user) {
            return $offer->status === 'pending'
                && (! $offer->sales_manager_id || $offer->sales_manager_id === $user->id);
        }

        return false;
    }

    /**
     * Sales users can only access Freelancers and Hiring Projects.
     */
    public static function canAccessNonSalesResource(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return ! self::isSalesManager($user);
    }
}
