<?php

namespace App\Http\Controllers;

class RoomTenantController extends Controller
{
    public function destroy($propertyId, $roomId, \App\Models\Tenant $tenant)
    {
        // Optional: Confirm the tenant belongs to this room and property
        if ($tenant->room_id != $roomId) {
            abort(403, 'Tenant does not belong to this room.');
        }

        $tenant->delete();

        return redirect()
            ->route('property.rooms.edit', [$propertyId, $roomId])
            ->with('success', 'Tenant removed successfully.');
    }
}
