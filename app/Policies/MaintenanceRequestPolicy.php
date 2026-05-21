<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\User;

class MaintenanceRequestPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'tenant';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->role === 'tenant') {
            return $user->tenant->id === $maintenanceRequest->tenant_id;
        }

        if ($user->role === 'landlord') {
            $propertyIds = $user->landlord ? $user->landlord->properties()->pluck('id') : collect();

            return $propertyIds->isNotEmpty() && in_array($maintenanceRequest->room->property_id, $propertyIds->toArray());
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->role === 'tenant') {
            return $user->tenant->id === $maintenanceRequest->tenant_id;
        }

        if ($user->role === 'landlord') {
            $propertyIds = $user->landlord ? $user->landlord->properties()->pluck('id') : collect();

            return $propertyIds->isNotEmpty() && in_array($maintenanceRequest->room->property_id, $propertyIds->toArray());
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MaintenanceRequest $maintenanceRequest): bool
    {
        if ($user->role === 'tenant') {
            return $user->tenant->id === $maintenanceRequest->tenant_id;
        }

        if ($user->role === 'landlord') {
            $propertyIds = $user->landlord ? $user->landlord->properties()->pluck('id') : collect();

            return $propertyIds->isNotEmpty() && in_array($maintenanceRequest->room->property_id, $propertyIds->toArray());
        }

        return false;
    }
}
