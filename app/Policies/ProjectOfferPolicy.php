<?php

namespace App\Policies;

use App\Models\ProjectOffer;
use App\Models\User;
use App\Support\AdminAccess;

class ProjectOfferPolicy
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
        return AdminAccess::canViewOffers($user);
    }

    public function view(User $user, ProjectOffer $offer): bool
    {
        return AdminAccess::canViewOffers($user);
    }

    public function create(User $user): bool
    {
        return AdminAccess::canCreateOffers($user);
    }

    public function update(User $user, ProjectOffer $offer): bool
    {
        return AdminAccess::canEditOffer($user, $offer);
    }

    public function delete(User $user, ProjectOffer $offer): bool
    {
        return AdminAccess::canDeleteOffer($user, $offer);
    }

    public function deleteAny(User $user): bool
    {
        return AdminAccess::isSuperAdmin($user) || AdminAccess::isAdmin($user);
    }
}
