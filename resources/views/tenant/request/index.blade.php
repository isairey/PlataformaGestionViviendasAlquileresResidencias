<x-app-layout title="Maintenance Request">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Maintenance Request
        </h2>
    </x-slot>

        <div class="max-w-(--breakpoint-2xl) mx-auto sm:px-6 lg:px-8">
            <div class="text-gray-900">
                <div class="flex items-end justify-between gap-x-3 mb-4">
                    <div class="flex items-center flex-wrap gap-2">
                        <x-badge color="gray" icon="ph-globe" :interactive="true" type="a"
                        href="{{ route('request.index', ['status' => 'all']) }}">All</x-badge>
                    <x-badge color="yellow" icon="ph-warning" :interactive="true" type="a"
                        href="{{ route('request.index', ['status' => 'pending']) }}">Pending</x-badge>
                    <x-badge color="lime" icon="ph-spinner-gap" :interactive="true" type="a"
                        href="{{ route('request.index', ['status' => 'in_progress']) }}">In Progress</x-badge>
                    <x-badge color="green" icon="ph-check-fat" :interactive="true" type="a"
                        href="{{ route('request.index', ['status' => 'resolved']) }}">Resolved</x-badge>
                    <x-badge color="red" icon="ph-trash" :interactive="true" type="a"
                        href="{{ route('request.index', ['status' => 'rejected']) }}">Rejected</x-badge>
                    </div>
                    @can('create', \App\Models\MaintenanceRequest::class)
                        <div>
                            <x-a variant="dark" :href="route('request.create')">
                                <i class="ph-bold ph-plus"></i>
                                Issue a Maintenance Request
                            </x-a>
                        </div>
                    @endcan
            </div>
                </div>
                @forelse ($requests as $request)
                    <x-card.request :request="$request" />
                @empty
                    <p>No Maintenance Request</p>
                @endforelse

            </div>
        </div>
</x-app-layout>
