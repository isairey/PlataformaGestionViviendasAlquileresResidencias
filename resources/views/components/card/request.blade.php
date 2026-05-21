@props(['request','full' => false, 'statusText' => 'Unknown', 'typeDisplay' => 'status'])

@php
    // Explicitly set the $full variable, ensuring it's always defined
    $full = $full ?? false; // This line ensures $full is set, using its passed value or defaulting to false

    $request_type_icon = [
        'tenant' => ['text' => 'Tenant', 'icon' => 'ph-user-circle', 'color' => 'lime'],
        'room' => ['text' => 'Room', 'icon' => 'ph-door', 'color' => 'yellow'],
        // This 'status' key can be a fallback or for generic display if needed, but we'll use specific status mappings below.
        'status' => ['text' => 'Status', 'icon' => 'ph-heartbeat', 'color' => 'red'],
    ];

    // Define mappings for status to color and icon
    $status_badge_properties = [
        'pending' => ['text' => 'Pending', 'icon' => 'ph-warning', 'color' => 'yellow'],
        'in_progress' => ['text' => 'In Progress', 'icon' => 'ph-spinner-gap', 'color' => 'lime'],
        'resolved' => ['text' => 'Resolved', 'icon' => 'ph-check-fat', 'color' => 'green'],
        'rejected' => ['text' => 'Rejected', 'icon' => 'ph-trash', 'color' => 'red'],
    ];

    // Determine current status text, icon, and color based on the request's status
    $current_status_info = $status_badge_properties[$request->status] ?? ['text' => 'Unknown Status', 'icon' => 'ph-question', 'color' => 'gray'];

@endphp

<div class="bg-white shadow p-8 sm:rounded-lg border-l-4 border-lime-800 mb-8">
    <div class="flex items-center justify-between mb-2">
        {{-- Use the current status info for the badge --}}
        <x-badge :color="$current_status_info['color']" :icon="$current_status_info['icon']" :size="$full ? 'lg' : 'md'" :interactive="true">
            {{ $current_status_info['text'] }}
        </x-badge>

        @if (auth()->user()->role === 'landlord')
            <x-dropdown position="bottom" align="default" width="48" :fullWidth="false">
                <x-slot name="trigger">
                    <x-button variant="clean">
                        <i class="ph-bold ph-dots-three-vertical"></i>
                    </x-button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link href="{{ route('request.edit', $request) }}">
                        <i class="ph-bold ph-pencil"></i> Edit Status
                    </x-dropdown-link>

                    <x-dropdown-link href="#" color="red" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-announcement-deletion-{{ $request->id }}')">
                        <i class="ph-bold ph-trash"></i> Delete
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        @endif
    </div>
    @if ($full)
        <h3 class="font-extrabold text-4xl my-4">{{ $request->title }}</h3>
    @else
        <h3 class="font-extrabold text-2xl my-4">{{ $request->title }}</h3>
    @endif
    <hr class="mb-4 border-gray-200" />

    @if ($full)
        <p class="text-gray-800 pr-4 max-w-4xl text-xl leading-relaxed mb-8">
            {!! nl2br(e($request->description)) !!}
        </p>
    @else
        <p class="text-gray-800 pr-4 max-w-4xl block sm:hidden text-base">
            {{ Str::words($request->description, 25) }}
        </p>
        <p class="text-gray-800 pr-4 max-w-4xl hidden sm:block text-base">
            {{ Str::words($request->description, 50) }}
        </p>
    @endif

    <div class="flex flex-col sm:flex-row gap-2 items-center justify-between mt-4 text-base">
        <div class="flex items-center gap-x-4 flex-wrap text-center">
            <div class="text-gray-600 flex items-center gap-x-1">
                <i class="ph-fill ph-calendar-dots text-lime-600 text-base"></i>
                {{ $request->created_at->format('F j, Y, g:i a') }}
            </div>
            <div class="text-gray-600 flex items-center gap-x-1">
                <i class="ph-fill ph-user text-lime-600 text-base"></i>
                {{ $request->tenant->user->name }}
            </div>
            {{-- Add property name and room --}}
            @if ($request->tenant->room)
                <div class="text-gray-600 flex items-center gap-x-1">
                    <i class="ph-fill ph-house text-lime-600 text-base"></i>
                    {{ $request->tenant->room->name }}
                    @if ($request->tenant->room->property)
                        ({{ $request->tenant->room->property->name }})
                    @endif
                </div>
            @endif
        </div>

        @if (!$full)
            <div>
                <x-a variant="primary" href="{{ route('request.show', $request) }}">View Details</x-a>
            </div>
        @endif
    </div>
</div>

<x-modal name="confirm-announcement-deletion-{{ $request->id }}" :show="false" :centered="true" focusable>
    <form method="POST" action="{{ route('request.destroy', $request) }}" class="p-6">
        @csrf
        @method('DELETE')

        <h2 class="text-lg font-medium text-gray-900">
            Are you sure you want to delete this request?
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end">
            <x-button variant="clear" type="button" x-on:click="$dispatch('close')">
                Cancel
            </x-button>

            <x-button type="submit" variant="danger" class="ms-3">
                Delete Request
            </x-button>
        </div>
    </form>
</x-modal>
