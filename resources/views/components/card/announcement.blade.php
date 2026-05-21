@props(['announcement', 'full' => false])

@php
    $announcement_type_icon = [
        'system' => ['text' => 'System', 'icon' => 'ph-users-three', 'color' => 'orange'],
        'property' => ['text' => 'Property', 'icon' => 'ph-house', 'color' => 'yellow'],
        'room' => ['text' => 'Room', 'icon' => 'ph-door', 'color' => 'pink'],
    ];

    if ($announcement->room_id) {
        $type = 'room';
    } elseif ($announcement->property_id) {
        $type = 'property';
    } else {
        $type = 'system';
    }
@endphp

<div class="bg-white shadow p-8 sm:rounded-lg border-l-4 border-lime-800 mb-8">
    <div class="flex items-center justify-between mb-2">
        <x-badge :color="$announcement_type_icon[$type]['color']" :icon="$announcement_type_icon[$type]['icon']" :size="$full ? 'lg' : 'md'" :interactive="true">
            {{ $announcement_type_icon[$type]['text'] }}
        </x-badge>

        @if (auth()->user()->role === 'landlord')
            <x-dropdown position="bottom" align="default" width="48" :fullWidth="false">
                <x-slot name="trigger">
                    <x-button variant="clean">
                        <i class="ph-bold ph-dots-three-vertical"></i>
                    </x-button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link href="{{ route('announcement.edit', $announcement) }}">
                        <i class="ph-bold ph-pencil"></i> Edit
                    </x-dropdown-link>

                    <x-dropdown-link href="#" color="red" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-announcement-deletion-{{ $announcement->id }}')">
                        <i class="ph-bold ph-trash"></i> Delete
                    </x-dropdown-link>
                </x-slot>
            </x-dropdown>
        @endif
    </div>
    @if ($full)
        <h3 class="font-extrabold text-4xl my-4">{{ $announcement->title }}</h3>
    @else
        <h3 class="font-extrabold text-2xl my-4">{{ $announcement->title }}</h3>
    @endif
    <hr class="mb-4 border-gray-200" />

    @if ($full)
        <p class="text-gray-800 pr-4 max-w-4xl text-xl leading-relaxed mb-8">
            {!! nl2br(e($announcement->description)) !!}
        </p>
    @else
        <p class="text-gray-800 pr-4 max-w-4xl block sm:hidden text-base">
            {{ Str::words($announcement->description, 25) }}
        </p>
        <p class="text-gray-800 pr-4 max-w-4xl hidden sm:block text-base">
            {{ Str::words($announcement->description, 50) }}
        </p>
    @endif

    <div
        class="flex flex-col sm:flex-row gap-2 items-center mt-4 justify-center text-sm sm:text-base sm:justify-between">
        <div class="flex items-center gap-x-4 flex-wrap text-center justify-center sm:justify-normal">
            <div class="text-gray-600 flex items-center gap-x-1">
                <i class="ph-fill ph-calendar-dots text-lime-600 text-base"></i>
                {{ $announcement->created_at->format('F j, Y, g:i a') }}
            </div>
            <div class="text-gray-600 flex items-center gap-1">
                <i class="ph-fill ph-map-pin text-lime-600 text-base"></i>
                @if ($announcement->room_id && $announcement->room)
                    {{ $announcement->room->name }} ({{ $announcement->room->property->name ?? 'Property' }})
                @elseif ($announcement->property_id && $announcement->property)
                    {{ $announcement->property->name }}
                @else
                    All Properties
                @endif
            </div>
        </div>

        @if (!$full)
            <div>
                <x-a variant="primary" href="{{ route('announcement.show', $announcement) }}">View Details</x-a>
            </div>
        @endif
    </div>
</div>

<x-modal name="confirm-announcement-deletion-{{ $announcement->id }}" :show="false" :centered="true" focusable>
    <form method="POST" action="{{ route('announcement.destroy', $announcement) }}" class="p-6">
        @csrf
        @method('DELETE')

        <h2 class="text-lg font-medium text-gray-900">
            Are you sure you want to delete this announcement?
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            This action cannot be undone.
        </p>

        <div class="mt-6 flex justify-end">
            <x-button variant="clear" type="button" x-on:click="$dispatch('close')">
                Cancel
            </x-button>

            <x-button type="submit" variant="danger" class="ms-3">
                Delete Announcement
            </x-button>
        </div>
    </form>
</x-modal>
