{{-- resources/views/landlord/request/show.blade.php --}}

<x-app-layout title="Maintenance Request Details">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Maintenance Request Details
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-6">
        <div class="bg-white shadow sm:rounded-lg p-6 mb-8">
            <div class="flex items-end justify-between gap-x-3 mb-6">
                <div class="font-bold text-2xl flex items-center">
                    <i class="ph-bold ph-caret-left text-lime-600"></i>
                    <x-a variant="text" :href="route('request.index')">
                        Back To Requests
                    </x-a>
                </div>
                {{-- Edit Status button for landlords --}}
                @if (auth()->check() && auth()->user()->role === 'landlord')
                    <div>
                        <x-a variant="primary" :href="route('request.edit', $requestRecord)">
                            <i class="ph-bold ph-pencil"></i> Edit Status
                        </x-a>
                    </div>
                @endif
            </div>

            {{-- Maintenance Request Details --}}
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Request Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm font-medium text-gray-600">Request ID:</p>
                    <p class="text-lg font-bold text-gray-900">#{{ $requestRecord->id }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Status:</p>
                    <x-badge
                        :color="match($requestRecord->status) {
                            'pending' => 'yellow',
                            'in_progress' => 'lime',
                            'resolved' => 'green',
                            'rejected' => 'red',
                            default => 'gray'
                        }"
                        icon="ph-heartbeat"
                        class="text-base"
                    >
                        {{ ucwords(str_replace('_', ' ', $requestRecord->status)) }}
                    </x-badge>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-600">Title:</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $requestRecord->title }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm font-medium text-gray-600">Description:</p>
                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $requestRecord->description }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Submitted On:</p>
                    <p class="text-gray-700">{{ $requestRecord->created_at->format('F j, Y, h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Last Updated On:</p>
                    <p class="text-gray-700">{{ $requestRecord->updated_at->format('F j, Y, h:i A') }}</p>
                </div>
            </div>

            {{-- Tenant and Property Details --}}
            <h3 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Tenant & Property Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">Tenant Name:</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $requestRecord->tenant->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Tenant Email:</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $requestRecord->tenant->user->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Property Name:</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $requestRecord->room->property->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Property Address:</p>
                    <p class="text-gray-700">{{ $requestRecord->room->property->address ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Room Code:</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $requestRecord->room->code ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Room Name:</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $requestRecord->room->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
