<x-app-layout title="Add Room">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Room</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-8">
            <form method="POST" action="{{ route('property.rooms.store', $property->id) }}">
                @csrf

                <!-- Room Name -->
                <div class="mb-6">
                    <x-input.label for="name" :required="true">Room Name</x-input.label>
                    <x-input.text id="name" type="text" name="name" :value="old('name')" autofocus />
                    <x-input.error for="name" />
                </div>

                <!-- Code -->
                <div class="mb-6">
                    <x-input.label for="code" :required="true">Room Code</x-input.label>
                    <x-input.text id="code" type="text" name="code" :value="old('code')" />
                    <x-input.error for="code" />
                </div>

                <!-- Rent Amount -->
                <div class="mb-6">
                    <x-input.label for="rent_amount" :required="true">Rent Amount</x-input.label>
                    <x-input.text id="rent_amount" type="number" name="rent_amount" :value="old('rent_amount')" />
                    <x-input.error for="rent_amount" />
                </div>

                <!-- Max Occupancy -->
                <div class="mb-6">
                    <x-input.label for="max_occupancy" :required="true">Max Occupancy</x-input.label>
                    <x-input.text id="max_occupancy" type="number" name="max_occupancy" :value="old('max_occupancy')" />
                    <x-input.error for="max_occupancy" />
                </div>

                <div class="flex items-center justify-center gap-x-12 gap-y-3 flex-wrap">
                    <x-a variant="clean" href="{{ route('property.rooms', $property->id) }}">Cancel</x-a>
                    <x-button type="submit">Add Room</x-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
