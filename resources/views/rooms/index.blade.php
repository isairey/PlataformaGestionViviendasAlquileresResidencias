<x-app-layout title="Rooms for {{ $property->name }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rooms in {{ $property->name }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">

        {{-- Property Name Heading --}}
        @php
            $icon = match ($property->type) {
                'apartment' => 'ph-building',
                'house' => 'ph-house-line',
                'dorm' => 'ph-door',
                'condominium' => 'ph-building-apartment',
                default => 'ph-question',
            };

            $color = match ($property->type) {
                'apartment' => 'text-yellow-600',
                'house' => 'text-pink-600',
                'dorm' => 'text-orange-600',
                'condominium' => 'text-purple-600',
                default => 'text-gray-600',
            };
        @endphp

        <h1 class="text-4xl font-bold mb-6 flex items-center gap-3">
            <i class="ph-bold {{ $icon }} {{ $color }} text-4xl"></i>
            {{ $property->name }}
        </h1>




        {{-- Navigation Links --}}
        <div class="flex justify-between items-center mb-6">
            <div class="font-bold text-2xl flex items-center">
                <i class="ph-bold ph-caret-left text-lime-600"></i>
                <x-a variant="text" :href="route('property.index')">
                    Back to Property List
                </x-a>
            </div>

            <x-button onclick="window.location.href='{{ route('property.rooms.create', $property->id) }}'">
                <i class="ph-bold ph-plus"></i>
                Add Room
            </x-button>
        </div>

        {{-- Room List --}}
        @if ($rooms->isEmpty())
            <div class="bg-white p-6 rounded shadow text-gray-700">
                <p>No rooms found for this property.</p>
            </div>
        @else
            <div class="relative overflow-hidden" style="min-height: 760px;">
                <div class="tab-pane transition-transform duration-500 ease-in-out bg-white"
                    style="display: block; transform: translateX(0%); position: absolute; width: 100%;">
                    <x-table.container id="rooms-table">
                        <x-slot name="header">
                            <x-table.header>No.</x-table.header>
                            <x-table.header>Room Code</x-table.header>
                            <x-table.header>Room Name</x-table.header>
                            <x-table.header>Capacity</x-table.header>
                            <x-table.header>Tenants</x-table.header>
                            <x-table.header>Move-In Date</x-table.header>
                            <x-table.header>Move-Out Date</x-table.header>
                            <x-table.header>Rent Amount</x-table.header>
                            <x-table.header>Actions</x-table.header>
                        </x-slot>

                        <x-slot name="body">
                            @foreach ($rooms as $index => $room)
                                <x-table.row>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $room->code }}</td>
                                    <td>{{ $room->name }}</td>
                                    <td>{{ $room->max_occupancy }}</td>
                                    <td>{{ $room->tenants->pluck('user.name')->join(', ') }}</td>
                                    <td>{{ $room->tenants->first()?->move_in_date ?? 'N/A' }}</td>
                                    <td>{{ $room->tenants->first()?->move_out_date ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($room->rent_amount, 2) }}</td>
                                    <td class="flex items-center space-x-2">
                                        <a href="{{ route('property.rooms.edit', [$property->id, $room->id]) }}"
                                            class="inline-block px-4 py-2 rounded-sm bg-lime-600 text-white hover:underline">Edit</a>

                                        <form method="POST"
                                            action="{{ route('property.rooms.destroy', [$property->id, $room->id]) }}"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 rounded-sm bg-red-600 text-white hover:underline ml-1"
                                                onclick="return confirm('Are you sure you want to delete this room?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </x-table.row>
                            @endforeach
                        </x-slot>
                    </x-table.container>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
