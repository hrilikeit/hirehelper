<?php

namespace App\Policies;

use App\Models\ClientProject;
use App\Models\User;
use App\Support\AdminAccess;

class ClientProjectPolicy
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
        return AdminAccess::canViewProjects($user);
    }

    public function view(User $user, ClientProject $project): bool
    {
        return AdminAccess::canViewProjects($user);
    }

    public function create(User $user): bool
    {
        return AdminAccess::canCreateProjects($user);
    }

    public function update(User $user, ClientProject $project): bool
    {
        return AdminAccess::canEditProject($user, $project);
    }

    public function delete(User $user, ClientProject $project): bool
    {
        return AdminAccess::canDeleteProject($user, $project);
    }

    public function deleteAny(User $user): bool
    {
        return AdminAccess::isSuperAdmin($user) || AdminAccess::isAdmin($user);
    }
}
