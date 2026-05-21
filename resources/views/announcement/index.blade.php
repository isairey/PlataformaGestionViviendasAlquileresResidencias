<x-app-layout title="Announcements" :manualSlide="true">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Announcements
        </h2>
    </x-slot>

    <div class="max-w-(--breakpoint-2xl) mx-auto sm:px-6 lg:px-8">
        <div class="text-gray-900">
            <div class="flex flex-col items-center justify-between gap-3 mb-4 sm:flex-row">
                <div class="flex items-center flex-wrap gap-2">
                    <x-badge color="blue" icon="ph-globe" :interactive="true" type="a"
                        href="{{ route('announcement.index', ['type' => 'all']) }}">All</x-badge>
                    <x-badge color="orange" icon="ph-users-three" :interactive="true" type="a"
                        href="{{ route('announcement.index', ['type' => 'system']) }}">System</x-badge>
                    <x-badge color="yellow" icon="ph-house" :interactive="true" type="a"
                        href="{{ route('announcement.index', ['type' => 'property']) }}">Property</x-badge>
                    <x-badge color="pink" icon="ph-door" :interactive="true" type="a"
                        href="{{ route('announcement.index', ['type' => 'room']) }}">Room</x-badge>
                </div>

                @can('create', \App\Models\Announcement::class)
                    <div>
                        <x-a variant="dark" :href="route('announcement.create')">
                            <i class="ph-bold ph-plus"></i>
                            New Announcement
                        </x-a>
                    </div>
                @endcan
            </div>

            <form method="GET" action="{{ route('announcement.index') }}" class="mb-4">
                <x-input.search class="max-w-md" name="search" value="{{ request('search') }}"
                    placeholder="Search announcements..." />
                <input type="hidden" name="type" value="{{ request('type', 'all') }}">
            </form>


            {{ $announcements->links() }}

            <br />

            <div id="view-transition-slide">
                @forelse ($announcements as $announcement)
                    <x-card.announcement :announcement="$announcement" id="side-content" />
                @empty
                    <div class="bg-white shadow p-8 sm:rounded-lg border-l-4 border-lime-800 mb-8 text-center text-2xl">
                        No Announcements
                    </div>
                @endforelse
            </div>

            @if ($announcements->count() > 0)
                {{ $announcements->links() }}
            @endif
        </div>
    </div>
</x-app-layout>
