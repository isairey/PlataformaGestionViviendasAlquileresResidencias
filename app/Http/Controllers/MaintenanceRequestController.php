<?php

namespace App\Http\Controllers;

use App\Enums\MaintenanceRequestStatus;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MaintenanceRequestController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;
        $info = $request->query('status');

        $requests = collect();

        if ($role === 'landlord') {
            $propertyIds = $user->landlord ? $user->landlord->properties()->pluck('id') : collect();

            if ($propertyIds->isNotEmpty()) {
                $query = MaintenanceRequest::whereIn('room_id', Room::whereIn('property_id', $propertyIds)->pluck('id'));

                if ($info && $info !== 'all') {
                    $query->where('status', $info);
                }

                $requests = $query->with('tenant.user')->latest()->get();
            } else {
                $requests = collect();
            }
        } elseif ($role === 'tenant') {
            $tenant = $user->tenant;
            $query = MaintenanceRequest::where('tenant_id', $tenant->id);

            if ($info && $info !== 'all') {
                $query->where('status', $info);
            }

            $requests = $query->with('tenant.user')->latest()->get();
        } else {
            abort(403, 'Unauthorized Access');
        }

        return match ($role) {
            'landlord' => view('landlord.request.index', [
                'requests' => $requests,
            ]),
            'tenant' => view('tenant.request.index', [
                'requests' => $requests,
            ]),
        };
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', MaintenanceRequest::class);

        $user = auth()->user();
        $tenant = $user->tenant;

        return view('tenant.request.create', ['room' => $tenant->room]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', MaintenanceRequest::class);
        $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'required|string|max:3000',
        ]);

        $user = auth()->user();
        $tenant = $user->tenant;

        MaintenanceRequest::create([
            'tenant_id' => $tenant->id,
            'room_id' => $tenant->room_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => MaintenanceRequestStatus::PENDING->value,
        ]);

        return redirect()->route('request.index')->with('toast.success', [
            'title' => 'Request Posted',
            'content' => 'Your request has been sent to your landlord.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        // Removed eager loading as Model::automaticallyEagerLoadRelationships() handles it globally.
        $requestRecord = MaintenanceRequest::findOrFail($id);
        $this->authorize('view', $requestRecord);
        $user = $request->user();

        if ($user->role === 'tenant') {
            return view('tenant.request.show', compact('requestRecord'));
        } elseif ($user->role === 'landlord') {
            return view('landlord.request.show', compact('requestRecord'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $requestRecord = MaintenanceRequest::findOrFail($id);
        $this->authorize('update', $requestRecord);
        $user = auth()->user();

        if ($user->role === 'tenant') {
            return view('tenant.request.edit', ['request' => $requestRecord]);
        } elseif ($user->role === 'landlord') {
            return view('landlord.request.edit', ['request' => $requestRecord]);
        } else {
            abort(403, 'Unauthorized Access'); // Fallback if no specific role or policy fails
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $requestRecord = MaintenanceRequest::findOrFail($id);
        $this->authorize('update', $requestRecord); // Using the Policy

        $user = auth()->user();

        if ($user->role === 'landlord') {
            $request->validate(['status' => 'required|in:pending,in_progress,resolved,rejected']);
            $requestRecord->status = $request->status;
        } else { // Tenant attempting to update
            $request->validate([
                'title' => 'required|string|max:50',
                'description' => 'required|string|max:3000',
            ]);
            $requestRecord->title = $request->title;
            $requestRecord->description = $request->description;
        }

        $requestRecord->save();

        return redirect()->route('request.index')->with('toast.success', [
            'title' => 'Request Updated',
            'content' => 'Your request has been updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $requestRecord = MaintenanceRequest::findOrFail($id);
        $this->authorize('delete', $requestRecord); // Using the Policy

        $requestRecord->delete();

        return redirect()->route('request.index')->with('toast.success', [
            'title' => 'Request Deleted',
            'content' => 'Your request has been deleted successfully.',
        ]);
    }
}
