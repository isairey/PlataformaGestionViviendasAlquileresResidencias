<x-app-layout title="Properties">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Properties
        </h2>
    </x-slot>

    <div class="max-w-(--breakpoint-2xl) mx-auto sm:px-6 lg:px-8">
        <div class="text-gray-900">
            <div class="p-6 flex items-end justify-between gap-x-3 mb-4">
                <div class="flex items-center flex-wrap gap-2">
                    <a href="{{ route('property.index') }}" class="inline-flex">
                        <x-badge color="blue" icon="ph-globe" :interactive="true" type="button">All</x-badge>
                    </a>
                    <a href="{{ route('property.index', ['type' => 'apartment']) }}" class="inline-flex">
                        <x-badge color="yellow" icon="ph-building" :interactive="true" type="button">Apartments</x-badge>
                    </a>
                    <a href="{{ route('property.index', ['type' => 'house']) }}" class="inline-flex">
                        <x-badge color="pink" icon="ph-house-line" :interactive="true" type="button">Houses</x-badge>
                    </a>
                    <a href="{{ route('property.index', ['type' => 'dorm']) }}" class="inline-flex">
                        <x-badge color="orange" icon="ph-door" :interactive="true" type="button">Dorms</x-badge>
                    </a>
                    <a href="{{ route('property.index', ['type' => 'condominium']) }}" class="inline-flex">
                        <x-badge color="purple" icon="ph-building-apartment" :interactive="true" type="button">Condominiums</x-badge>
                    </a>
                </div>
                <x-button onclick="window.location.href='{{ route('property.create') }}'">
                    <i class="ph-bold ph-plus"></i>
                    New Property
                </x-button>
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            @forelse ($properties as $property)
                <x-card.property :property="$property"/>
            @empty
                <p>Nothing here at the moment</p>
            @endforelse
        </div>

        {{ $properties->links() }}
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {

            });
        </script>
    @endPushOnce
</x-app-layout>
