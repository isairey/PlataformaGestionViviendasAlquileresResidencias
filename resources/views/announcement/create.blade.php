<x-app-layout title="Add Announcement">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Announcement
        </h2>
    </x-slot>
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-8">
            <form method="POST" action="{{ route('announcement.store') }}" x-data="{
                type: '{{ old('type', 'system') }}',
                property_id: '{{ old('property_id') }}',
                room_id: '{{ old('room_id') }}',
                rooms: window.roomsData,
            }">
                @csrf

                <!-- Type Dropdown -->
                <div class="mb-6">
                    <x-input.label for="type" :required="true">Type</x-input.label>
                    <x-input.select id="type" name="type" x-model="type" :options="['system' => 'System', 'property' => 'Property', 'room' => 'Room']" :selected="old('type')"
                        placeholder="Please select a type" autofocus />
                    <x-input.error for="type" />
                </div>

                <!-- Property Dropdown (shown for property/room) -->
                <div class="mb-6" x-show="type == 'property' || type == 'room'" x-cloak>
                    <x-input.label for="property_id" :required="true">Property</x-input.label>
                    <x-input.select id="property_id" name="property_id" x-model="property_id" @change="room_id = ''"
                        :options="$properties->pluck('name', 'id')" :selected="old('property_id')" placeholder="Please select a property" />
                    <x-input.error for="property_id" />
                </div>

                <!-- Room Dropdown (shown for room only) -->
                <div class="mb-6" x-show="type == 'room'" x-cloak>
                    <x-input.label for="room_id" :required="true">Room</x-input.label>
                    <select id="room_id" name="room_id" x-model="room_id"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-md focus:ring-lime-600 focus:border-lime-600 block w-full">
                        <option value="" disabled>-- Please select a room --</option>
                        <template x-for="(room, index) in rooms.filter(r => r.property_id == property_id)"
                            :key="room.id">
                            <option :value="room.id" x-text="room.name"></option>
                        </template>
                    </select>
                    <x-input.error for="room_id" />
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <x-input.label for="title" :required="true">Title</x-input.label>
                    <x-input.text id="title" type="text" name="title" :value="old('title')" />
                    <x-input.error for="title" />
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <x-input.label for="description" :required="true">Description</x-input.label>
                    <x-input.textarea id="description" name="description" rows="6">
                        {{ old('description') }}
                    </x-input.textarea>
                    <x-input.error for="description" />
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-center gap-x-12 gap-y-3 flex-wrap">
                    <x-a variant="clean" href="{{ route('announcement.index') }}">
                        Cancel
                    </x-a>
                    <x-button type="submit">
                        Post Announcement
                    </x-button>
                </div>
            </form>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            window.roomsData = @json($rooms);
        </script>
    @endPushOnce

</x-app-layout>
