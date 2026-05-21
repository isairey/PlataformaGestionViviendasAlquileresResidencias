<x-app-layout title="Add New Property">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add New Property
        </h2>
    </x-slot>

    <div class="bg-white shadow-xs sm:rounded-lg p-4">
        <div class="text-center p-4">
            <h1 class="font-bold text-xl"><i class="ph-bold ph-plus-circle"></i> Add New Property</h1>
        </div>

        <form method="POST" action="{{ route('property.store') }}">
            @csrf

            <div class="mb-2 p-2">
                <x-input.label for="name" :required="true">Property Name</x-input.label>
                <x-input.text id="name" type="text" name="name" :value="old('name')" autofocus />
                <x-input.error for="name" />
            </div>

            <div class="mb-2 p-2">
                <x-input.label for="type" :required="true">Property Type</x-input.label>
                <x-input.select id="type" name="type" placeholder="Select a type"
                    :options="[
                        'apartment' => 'Apartment',
                        'house' => 'House',
                        'dorm' => 'Dorm',
                        'condominium' => 'Condominium', ]"
                    :selected="old('type')"
                    :value="old('type')" />
                <x-input.error for="type" />
            </div>

            <div class="mb-2 p-2">
                <x-input.label for="address" :required="true">Property Address</x-input.label>
                <x-input.text id="address" type="text" name="address" :value="old('address')" />
                <p class="text-xs text-center">Building/Unit/House Number, Street Name, Barangay, City/Municipality, Province, ZIP Code</p>
                <x-input.error for="address" />
            </div>

            <div class="mb-2 p-2">
                <x-input.label for="description" :required="true">Description</x-input.label>
                <x-input.textarea id="description" name="description" rows="4" :value="old('description')" />
                <x-input.error for="description" />
            </div>

            <div class="flex items-end justify-between gap-4 mb-4 mt-4 p-4">
                <x-button id="cancelBtn" variant="clean" >Cancel</x-button>
                <x-button type="submit" variant="primary">Submit</x-button>
            </div>
        </form>
    </div>

</x-app-layout>

<script>
    document.getElementById('cancelBtn').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent form submission
        window.history.back(); // Navigate to previous page
    });
</script>
