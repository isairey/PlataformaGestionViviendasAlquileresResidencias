<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Announcement $announcement): bool
    {
        if ($user->role === 'landlord') {
            return true;
        }

        if ($user->role === 'tenant') {
            $tenant = $user->tenant;
            if (! $tenant) {
                return false;
            }
            $room = $tenant->room;
            $propertyId = $room ? $room->property_id : null;
            $roomId = $room ? $room->id : null;

            // System-wide
            if (is_null($announcement->property_id) && is_null($announcement->room_id)) {
                return true;
            }
            // Property-wide
            if ($announcement->property_id === $propertyId && is_null($announcement->room_id)) {
                return true;
            }
            // Room-specific
            if ($announcement->property_id === $propertyId && $announcement->room_id === $roomId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'landlord';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Announcement $announcement): bool
    {
        return $user->role === 'landlord';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->role === 'landlord';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Announcement $announcement): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return false;
    }
}
