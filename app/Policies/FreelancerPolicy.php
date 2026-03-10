<?php

namespace App\Policies;

use App\Models\Freelancer;
use App\Models\User;
use App\Support\AdminAccess;

class FreelancerPolicy
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
        return AdminAccess::canViewFreelancers($user);
    }

    public function view(User $user, Freelancer $freelancer): bool
    {
        return AdminAccess::canViewFreelancers($user);
    }

    public function create(User $user): bool
    {
        return AdminAccess::canManageFreelancers($user);
    }

    public function update(User $user, Freelancer $freelancer): bool
    {
        return AdminAccess::canManageFreelancers($user);
    }

    public function delete(User $user, Freelancer $freelancer): bool
    {
        return AdminAccess::canManageFreelancers($user);
    }

    public function deleteAny(User $user): bool
    {
        return AdminAccess::canManageFreelancers($user);
    }
}
