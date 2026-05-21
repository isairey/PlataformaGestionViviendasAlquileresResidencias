<x-app-layout title="Edit Room - {{ $room->name }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Room for {{ $property->name }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-8">

            <!-- Tenants Section -->
            @if($room->tenants->count())
                <div class="mt-12 mb-8">
                    <h3 class="text-lg font-semibold mb-4">Tenants</h3>
                    <ul class="space-y-6" id="tenant-list">
                        @foreach ($room->tenants as $tenant)
                            <li class="flex items-center justify-between border p-4 rounded-md tenant-item" data-tenant-id="{{ $tenant->id }}">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ $tenant->user->name }}</p>
                                </div>
                                <button type="button" class="remove-tenant bg-red-500 text-white px-3 py-1 rounded">Remove</button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Room Edit Form -->
            <div class="mt-16">
                <form id="room-form" method="POST" action="{{ route('property.rooms.update', [$property->id, $room->id]) }}">
                    @csrf
                    @method('PUT')

                    <!-- Hidden input for staged tenant removal -->
                    <input type="hidden" name="remove_tenants" id="remove-tenants" value="[]">

                    <!-- Room Name -->
                    <div class="mb-6">
                        <x-input.label for="name" :required="true">Room Name</x-input.label>
                        <x-input.text id="name" type="text" name="name" :value="old('name', $room->name)" autofocus />
                        <x-input.error for="name" />
                    </div>

                    <!-- Room Code -->
                    <div class="mb-6">
                        <x-input.label for="code" :required="true">Room Code</x-input.label>
                        <x-input.text id="code" type="text" name="code" :value="old('code', $room->code)" />
                        <x-input.error for="code" />
                    </div>

                    <!-- Rent Amount -->
                    <div class="mb-6">
                        <x-input.label for="rent_amount" :required="true">Rent Amount</x-input.label>
                        <x-input.text id="rent_amount" type="number" step="0.01" name="rent_amount" :value="old('rent_amount', $room->rent_amount)" />
                        <x-input.error for="rent_amount" />
                    </div>

                    <!-- Max Occupancy -->
                    <div class="mb-6">
                        <x-input.label for="max_occupancy" :required="true">Max Occupancy</x-input.label>
                        <x-input.text id="max_occupancy" type="number" name="max_occupancy" :value="old('max_occupancy', $room->max_occupancy)" />
                        <x-input.error for="max_occupancy" />
                    </div>

                    <!-- Buttons -->
                    <div class="mt-8 flex items-center justify-center gap-x-12 gap-y-3 flex-wrap">
                        <x-a variant="clean" href="{{ route('property.rooms', $property->id) }}">Cancel</x-a>
                        <x-button type="submit">Update Room</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const tenantList = document.getElementById('tenant-list');
        const removeTenantsInput = document.getElementById('remove-tenants');
        const removedTenantIds = [];

        if (tenantList) {
            tenantList.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-tenant')) {
                const tenantItem = e.target.closest('.tenant-item');
                const tenantId = tenantItem.dataset.tenantId;
                const tenantName = tenantItem.querySelector('p')?.textContent || 'this tenant';

                const confirmRemoval = confirm(`Are you sure you want to remove ${tenantName}?`);
                if (confirmRemoval) {
                    removedTenantIds.push(tenantId);
                    removeTenantsInput.value = JSON.stringify(removedTenantIds);
                    tenantItem.remove();
                }
            }

            });
        }

        const cancelBtn = document.querySelector(`a[href="{{ route('property.rooms', $property->id) }}"]`);
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                removeTenantsInput.value = '[]';
            });
        }
    </script>
    @endpush
</x-app-layout>
