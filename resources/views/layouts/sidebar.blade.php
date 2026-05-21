<!-- Sidebar Toggle Button -->
<div class="bg-white flex items-center justify-between transition px-3 py-1.5 sm:p-0 sm:hidden">
    <div class="flex-1 pl-2">
        <h2 class="font-semibold text-gray-900">{{ $header }}</h2>
    </div>

    <button type="button"
        class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
        data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar" aria-controls="default-sidebar">
        <i class="ph-bold ph-list text-2xl"></i>
        <span class="sr-only">Open sidebar</span>
    </button>
</div>



<!-- Sidebar Container -->
<aside id="default-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0 bg-white"
    aria-label="Sidebar">

    <div class="flex flex-col h-full">

        {{-- Top: Scrollable content --}}
        <div class="flex-1 overflow-y-auto px-3 py-4">

            {{-- Header --}}
            <a class="flex items-center gap-2 px-2 py-1" href={{ route('dashboard') }}>
                <img src="{{ Vite::asset('resources/assets/images/logo_text.svg') }}" class="w-full px-2" />
            </a>

            <hr class="mt-2 border-gray-200" />

            {{-- Navigation Links --}}
            <nav class="space-y-2 font-medium mt-6">
                <x-sidebar.item :href="route('dashboard')" :active="request()->routeIs('dashboard')" title="Dashboard" icon="squares-four" />
                @if (auth()->user()->role === 'landlord')
                    <x-sidebar.item :href="route('property.index')" :active="request()->routeIs('property.*')" title="Properties" icon="house-line" />
                @elseif (auth()->user()->role === 'tenant')
                @endif
                <x-sidebar.item :href="route('transaction.index')" :active="request()->routeIs('transaction.*')" title="Transactions" icon="money" />
                <hr />
                <x-sidebar.item :href="route('announcement.index')" :active="request()->routeIs('announcement.*')" title="Announcements" icon="megaphone" />
                <x-sidebar.item :href="route('request.index')" :active="request()->routeIs('request.*')" title="Maintenance Request" icon="wrench" />
                <hr />
                <x-sidebar.item :href="route('messages.index')" :active="request()->routeIs('messages.*')" title="Chats" icon="chat-circle-text" />


                {{-- <x-sidebar.collapsible icon="package" title="Test" badge="3">
                    <x-sidebar.item href="#" title="Transactions" icon="money" badge="12" />
                    <x-sidebar.item href="#" title="Transactions" icon="money" badge="12" />

                </x-sidebar.collapsible> --}}
            </nav>
        </div>

        {{-- Footer --}}
        <div class="px-1 border-t border-gray-200 flex justify-end">
            <x-dropdown position="top" align="right" width="48">
                <x-slot name="trigger">
                    <button
                        class="inline-flex items-center gap-x-2 justify-between w-full px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 transition ease-in-out duration-150">
                        <div class="flex items-center gap-x-2">
                            @php
                                $profilePicture = auth()->user()->profile_picture
                                    ? Storage::url(auth()->user()->profile_picture)
                                    : Vite::asset('resources/assets/images/default_profile.png');
                            @endphp
                            <img src="{{ $profilePicture }}" class="aspect-square w-10 border rounded-full"
                                alt="Profile" />
                            {{-- @endif --}}
                            <div class="text-left">
                                <p class="font-bold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-sm">{{ Str::upper(auth()->user()->role) }}</p>
                            </div>

                        </div>

                        <div class="ms-2">
                            <i class="ph-fill ph-caret-up-down"></i>
                        </div>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        <i class="ph-bold ph-gear"></i> Settings
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <i class="ph-bold ph-sign-out"></i>
                            Log Out
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</aside>

<!-- Main Content Area -->
<div class="p-4 sm:ml-64" @if (!$manualSlide) id="view-transition-slide" @endif>
    <main class="p-4 border-2 border-dashed rounded-lg">
        {{ $slot }}
    </main>
</div>
