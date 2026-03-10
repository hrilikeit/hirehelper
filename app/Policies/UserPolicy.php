<?php

namespace App\Policies;

use App\Models\User;
use App\Support\AdminAccess;

class UserPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (AdminAccess::isSuperAdmin($user)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return AdminAccess::canViewClients($user);
    }

    public function view(User $user, User $record): bool
    {
        if (! AdminAccess::canViewClients($user)) {
            return false;
        }

        if ($record->isInternalUser()) {
            return AdminAccess::canViewInternalUsers($user);
        }

        return true;
    }

    public function create(User $user): bool
    {
        return AdminAccess::canManageInternalUsers($user);
    }

    public function update(User $user, User $record): bool
    {
        if ($record->isInternalUser()) {
            return AdminAccess::canManageInternalUsers($user);
        }

        return AdminAccess::canEditClients($user);
    }

    public function delete(User $user, User $record): bool
    {
        return AdminAccess::canManageInternalUsers($user) && ! $record->isRole('superadmin');
    }

    public function deleteAny(User $user): bool
    {
        return AdminAccess::canManageInternalUsers($user);
    }
}
