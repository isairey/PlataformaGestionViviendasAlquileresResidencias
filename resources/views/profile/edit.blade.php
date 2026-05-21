<x-app-layout title="Profile">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Profile</h2>
    </x-slot>

    <div>
        <div class="max-w-(--breakpoint-2xl) mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Tab Navigation -->
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="relative">
                    <div id="tab-container" class="relative overflow-x-auto scrollbar-hidden -mx-1 px-1">
                        <div class="flex bg-gray-100 pt-1 pb-2 rounded-lg relative min-w-full w-max">
                            <!-- Sliding Indicator -->
                            <div id="tab-indicator"
                                class="absolute top-1 left-0 h-10 bg-white rounded-md shadow-xs transition-all duration-300 ease-out border-b-4 border-lime-800">
                            </div>

                            <button id="tab-profile"
                                class="tab-button relative z-10 px-4 py-2 text-sm font-medium text-center text-gray-700 hover:text-gray-900 min-w-max"
                                data-tab="profile">
                                <span class="sm:inline hidden">Profile Information</span>
                                <span class="inline sm:hidden">Profile</span>
                            </button>

                            @if ($user->role === 'landlord')
                                <button id="tab-landlord"
                                    class="tab-button relative z-10 px-4 py-2 text-sm font-medium text-center text-gray-700 hover:text-gray-900 min-w-max"
                                    data-tab="landlord">
                                    <span class="sm:inline hidden">Landlord Information</span>
                                    <span class="inline sm:hidden">Landlord</span>
                                </button>
                            @endif

                            <button id="tab-password"
                                class="tab-button relative z-10 px-4 py-2 text-sm font-medium text-center text-gray-700 hover:text-gray-900 min-w-max"
                                data-tab="password">
                                <span class="sm:inline hidden">Change Password</span>
                                <span class="inline sm:hidden">Password</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div>
                <!-- Profile Tab -->
                <div id="content-profile" class="tab-content space-y-6">
                    <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

                <!-- Landlord Tab -->
                @if ($user->landlord)
                    <div id="content-landlord" class="tab-content hidden">
                        <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                            <div class="max-w-xl">
                                @include('profile.partials.update-landlord-information-form')
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Change Password Tab -->
                <div id="content-password" class="tab-content hidden">
                    <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-lg">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tabButtons = document.querySelectorAll('.tab-button');
                const tabContents = document.querySelectorAll('.tab-content');
                const tabIndicator = document.getElementById('tab-indicator');
                const tabContainer = document.getElementById('tab-container');

                let activeTab = 'profile';

                function updateIndicator(button) {
                    const left = button.offsetLeft;
                    const width = button.offsetWidth;

                    tabIndicator.style.left = `${left}px`;
                    tabIndicator.style.width = `${width}px`;
                }

                function setActiveTab(tabId) {
                    tabButtons.forEach(button => {
                        const isActive = button.dataset.tab === tabId;
                        button.classList.toggle('text-lime-700', isActive);
                        button.classList.toggle('text-gray-700', !isActive);
                    });

                    tabContents.forEach(content => {
                        content.classList.toggle('hidden', content.id !== `content-${tabId}`);
                    });

                    const activeButton = document.querySelector(`[data-tab="${tabId}"]`);
                    if (activeButton) {
                        updateIndicator(activeButton);
                    }

                    activeTab = tabId;
                }

                tabButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        setActiveTab(button.dataset.tab);
                    });
                });

                // Initialize
                const firstButton = document.querySelector(`[data-tab="${activeTab}"]`);
                if (firstButton) {
                    updateIndicator(firstButton);
                    setActiveTab(activeTab);
                }

                // Update indicator on resize
                window.addEventListener('resize', () => {
                    const activeButton = document.querySelector(`[data-tab="${activeTab}"]`);
                    if (activeButton) {
                        updateIndicator(activeButton);
                    }
                });
            });
        </script>
    @endpushOnce
</x-app-layout>
