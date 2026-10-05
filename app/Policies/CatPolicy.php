<?php

namespace App\Policies;

use App\Models\Cat;
use App\Models\User;

class CatPolicy
{
    /**
     * Determine whether the user can view any cats.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the cat.
     */
    public function view(User $user, Cat $cat): bool
    {
        return $cat->canAccessKtamBy($user);
    }

    /**
     * Determine whether the user can create cats.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the cat profile.
     */
    public function update(User $user, Cat $cat): bool
    {
        return $cat->canBeEditedBy($user);
    }

    /**
     * Determine whether the user can delete the cat profile.
     */
    public function delete(User $user, Cat $cat): bool
    {
        return $cat->canBeDeletedBy($user);
    }

    /**
     * Determine whether the user can toggle status (alive/deceased) of the cat.
     */
    public function toggleStatus(User $user, Cat $cat): bool
    {
        return $cat->canToggleStatusBy($user);
    }

    /**
     * Determine whether the user can manage photos (upload, set primary, delete) of the cat.
     */
    public function managePhotos(User $user, Cat $cat): bool
    {
        return $cat->canManagePhotosBy($user);
    }

    /**
     * Determine whether the user can access KTAM card (download/preview).
     */
    public function accessKtam(User $user, Cat $cat): bool
    {
        return $cat->canAccessKtamBy($user);
    }
}
