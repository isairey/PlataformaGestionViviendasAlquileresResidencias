<?php

namespace App\Http\Controllers;

use App\Enums\AnnouncementType;
use App\Models\Announcement;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type', 'all');
        $search = $request->query('search');
        $perPage = 5;

        if ($user->role === 'landlord') {
            $landlord = $user->landlord()->first();
            $announcements = Announcement::relevantToLandlord($landlord)
                ->filterByType($type)
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->paginate($perPage)
                ->appends($request->query());
        } elseif ($user->role === 'tenant') {
            $tenant = $user->tenant()->first();
            $announcements = Announcement::relevantToTenant($tenant)
                ->filterByType($type, $tenant)
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->paginate($perPage)
                ->appends($request->query());
        } else {
            return abort(403);
        }

        return view('announcement.index', [
            'announcements' => $announcements,
            'filter' => $type,
        ]);
    }

    // public function index(Request $request)
    // {
    //     $role = auth()->user()->role;
    //     $type = $request->query('type', 'all');
    //     $announcements = collect();

    //     if ($role === 'landlord') {
    //         $query = Announcement::query();

    //         if ($type === 'system') {
    //             $query->whereNull('property_id')->whereNull('room_id');
    //         } elseif ($type === 'property') {
    //             $query->whereNotNull('property_id')->whereNull('room_id');
    //         } elseif ($type === 'room') {
    //             $query->whereNotNull('property_id')->whereNotNull('room_id');
    //         }
    //         // 'all' applies no additional filters
    //         $announcements = $query->latest()->get();

    //     } elseif ($role === 'tenant') {
    //         $tenant = auth()->user()->tenant;
    //         $query = Announcement::relevantToTenant($tenant);

    //         if ($type === 'system') {
    //             $query->whereNull('property_id')->whereNull('room_id');
    //         } elseif ($type === 'property') {
    //             $room = $tenant->room;
    //             $propertyId = $room ? $room->property_id : null;
    //             $query->where('property_id', $propertyId)->whereNull('room_id');
    //         } elseif ($type === 'room') {
    //             $room = $tenant->room;
    //             $propertyId = $room ? $room->property_id : null;
    //             $roomId = $room ? $room->id : null;
    //             $query->where('property_id', $propertyId)->where('room_id', $roomId);
    //         }
    //         $announcements = $query->latest()->get();
    //     } else {
    //         return abort(403);
    //     }

    //     return view('announcement.index', [
    //         'announcements' => $announcements,
    //         'filter' => $type,
    //     ]);
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Announcement::class);
        $properties = Property::all();

        // Get rooms with their property information and structure for JavaScript
        $rooms = Room::with('property')->get()->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'property_id' => $room->property_id,
                'property_name' => $room->property->name,
            ];
        });

        return view('announcement.create', compact('properties', 'rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_column(AnnouncementType::cases(), 'value'))],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:500'],
            'property_id' => [
                'nullable',
                'required_if:type,property,room',
                'exists:properties,id',
            ],
            'room_id' => [
                'nullable',
                'required_if:type,room',
                'exists:rooms,id',
            ],
        ]);

        Announcement::create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'property_id' => in_array($validated['type'], ['property', 'room']) ? $validated['property_id'] : null,
            'room_id' => $validated['type'] === 'room' ? $validated['room_id'] : null,
        ]);

        return redirect()->route('announcement.index')->with('toast.success', [
            'title' => 'Announcement Created',
            'content' => 'Your announcement has been successfully posted and is now visible to the intended audience.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        return view('announcement.show', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);
        $properties = Property::all();

        // Get rooms with their property information and structure for JavaScript
        $rooms = Room::with('property')->get()->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'property_id' => $room->property_id,
                'property_name' => $room->property->name,
            ];
        });

        return view('announcement.edit', compact('announcement', 'properties', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $validated = $request->validate([
            'type' => ['required', Rule::in(array_column(AnnouncementType::cases(), 'value'))],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:500'],
            'property_id' => [
                'nullable',
                'required_if:type,property,room',
                'exists:properties,id',
            ],
            'room_id' => [
                'nullable',
                'required_if:type,room',
                'exists:rooms,id',
            ],
        ]);

        $announcement->update([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'property_id' => in_array($validated['type'], ['property', 'room']) ? $validated['property_id'] : null,
            'room_id' => $validated['type'] === 'room' ? $validated['room_id'] : null,
        ]);

        return redirect()->route('announcement.index')->with('toast.success', [
            'title' => 'Announcement Updated',
            'content' => 'The announcement has been successfully updated.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return redirect()->route('announcement.index')->with('toast.success', [
            'title' => 'Announcement Deleted',
            'content' => 'The announcement has been successfully deleted.',
        ]);
    }
}
