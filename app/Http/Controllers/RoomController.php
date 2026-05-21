<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms for a property.
     */
    public function index(Property $property)
    {
        $rooms = $property->rooms()->with(['tenants.user'])->get();

        return view('rooms.index', compact('property', 'rooms'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create(Property $property)
    {
        return view('rooms.create', compact('property'));
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:7', Rule::unique('rooms', 'code')],
            'rent_amount' => ['required', 'numeric'],
            'max_occupancy' => ['required', 'integer', 'min:1'],
        ]);

        $validated['property_id'] = $property->id;

        Room::create($validated);

        return redirect()->route('property.rooms', $property->id)->with('toast.success', [
            'title' => 'Room Added',
            'content' => 'The room has been successfully added to the property.',
        ]);
    }

    /**
     * Show the form for editing an existing room.
     */
    public function edit(Property $property, Room $room)
    {
        return view('rooms.edit', compact('property', 'room'));
    }

    /**
     * Update the specified room.
     */
    public function update(Request $request, Property $property, Room $room)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:7', Rule::unique('rooms', 'code')->ignore($room->id)],
            'rent_amount' => ['required', 'numeric'],
            'max_occupancy' => ['required', 'integer', 'min:1'],
        ]);

        $room->update($validated);

        if ($request->filled('remove_tenants')) {
            $tenantIds = json_decode($request->input('remove_tenants'), true);

            if (is_array($tenantIds)) {
                foreach ($tenantIds as $tenantId) {
                    $tenant = $room->tenants()->find($tenantId);
                    if ($tenant) {
                        $tenant->user()->delete(); // If you soft-delete tenants or detach them
                        // Or use: $room->tenants()->detach($tenantId); if it's a pivot
                    }
                }
            }
        }

        return redirect()->route('property.rooms', $property->id)->with('toast.success', [
            'title' => 'Room Updated',
            'content' => 'The room has been successfully updated.',
        ]);
    }

    /**
     * Remove the specified room.
     */
    public function destroy($propertyId, $roomId)
    {
        $room = Room::with('tenants')->findOrFail($roomId);

        if ($room->tenants()->count() > 0) {
            return redirect()->back()->withErrors('Cannot delete room with active tenants.');
        }

        $room->delete();

        return redirect()->route('property.rooms', $propertyId)->with('success', 'Room deleted.');
    }
}
