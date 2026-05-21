@php
    $announcement_type_icon = [
        'system' => ['text' => 'System', 'icon' => 'ph-users-three', 'color' => 'orange'],
        'property' => ['text' => 'Property', 'icon' => 'ph-house', 'color' => 'yellow'],
        'room' => ['text' => 'Room', 'icon' => 'ph-door', 'color' => 'pink'],
    ];

    // Determine announcement type based on foreign keys
    if ($announcement->room_id) {
        $type = 'room';
    } elseif ($announcement->property_id) {
        $type = 'property';
    } else {
        $type = 'system';
    }
@endphp

<x-app-layout title="Announcements">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Announcements
        </h2>
    </x-slot>

    <div class="max-w-(--breakpoint-2xl) mx-auto sm:px-6 lg:px-8">
        <div class="text-gray-900">
            <div class="flex items-end justify-between gap-x-3 mb-2">
                <div class="font-bold text-2xl flex items-center">
                    <i class="ph-bold ph-caret-left text-lime-600"></i>
                    <x-a variant="text" :href="route('announcement.index')">
                        Back To Announcements
                    </x-a>
                </div>
            </div>

            <x-card.announcement :announcement="$announcement" :full="true" />
        </div>
</x-app-layout>
