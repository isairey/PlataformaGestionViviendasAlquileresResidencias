<x-app-layout title="Messages">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Messages
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="text-gray-900">

            <!-- Start Conversation Modal/Form -->
            <x-button class="mb-4" x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'start-conversation-modal')">
                Start New Conversation
            </x-button>

            <div>
                @if ($conversations->isEmpty())
                    <div class="p-8 text-center bg-white">
                        <i class="ph-bold ph-chat-circle-text text-7xl text-gray-700"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No conversations yet</h3>
                        <p class="text-gray-500">Start a conversation to get in touch with other users.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4 max-w-2xl">
                        @foreach ($conversations as $conversation)
                            @php
                                $otherParticipant = $conversation->getOtherParticipant(auth()->id());
                                $latestMessage = $conversation->latestMessage->first();

                                $profilePicture = $otherParticipant->profile_picture
                                    ? Storage::url($otherParticipant->profile_picture)
                                    : Vite::asset('resources/assets/images/default_profile.png');
                            @endphp
                            <a href="{{ route('messages.show', $conversation) }}"
                                class="bg-white shadow p-4 sm:rounded-lg border-l-4 border-lime-800 block">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 self-start">
                                        <img src="{{ $profilePicture }}"
                                            class="h-16 w-16 rounded-full bg-lime-100 hover:bg-lime-200 transition-colors" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="text-base font-medium text-gray-900 truncate">
                                                {{ $otherParticipant->name ?? 'Unknown User' }}
                                                <div class="text-xs mt-1 flex flex-col sm:flex-row flex-wrap gap-1">
                                                    @if ($otherParticipant->role === 'tenant')
                                                        <x-badge color="lime" icon="ph-house" :interactive="true">
                                                            {{ $otherParticipant->tenant->room->property->name }}
                                                        </x-badge>
                                                        <x-badge color="orange" icon="ph-door" :interactive="true">
                                                            {{ $otherParticipant->tenant->room->name }}
                                                        </x-badge>
                                                    @else
                                                        <x-badge size="md" color="lime"
                                                            icon="ph-identification-badge" :interactive="true">
                                                            Landlord
                                                        </x-badge>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 sm:hidden">
                                                {{ $latestMessage?->created_at?->shortRelativeDiffForHumans() }}
                                            </p>
                                            <p class="text-xs text-gray-500 hidden sm:inline">
                                                {{ $latestMessage?->created_at?->diffForHumans() }}
                                            </p>
                                        </div>
                                        @if ($latestMessage)
                                            <p class="ms-1 text-sm text-gray-500 truncate mt-1">
                                                {{ Str::limit($latestMessage->content, 50) }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-modal maxWidth="lg" name="start-conversation-modal" :show="$errors->isNotEmpty()" focusable>
        <form method="post" action="{{ route('messages.start') }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">
                Select User
            </h2>

            <x-input.search id="user-search" placeholder="Search by name, email, property, room..." />

            <div class="mt-4">
                <x-input.label for="recipient_id" :required="true">User</x-input.label>
                <x-input.select id="recipient_id" size="12" name="recipient_id" :selected="old('recipient_id')"
                    placeholder="Select the user">
                    @foreach ($availableUsers as $user)
                        <option value="{{ $user->id }}"
                            data-search="{{ $user->name }} {{ $user->email }}
                @if ($user->role === 'tenant' && $user->tenant && $user->tenant->room) {{ $user->tenant->room->name }} {{ $user->tenant->room->property->name }} @endif
                @if ($user->role === 'landlord') Landlord @endif"
                            {{ old('recipient_id') == $user->id ? 'selected' : '' }}> 👤
                            {{ $user->name }}
                            @if ($user->role === 'tenant')
                                ({{ $user->tenant?->room?->property?->name }} - {{ $user->tenant?->room?->name }})
                            @elseif ($user->role === 'landlord')
                                (Landlord)
                            @endif


                        </option>
                    @endforeach
                </x-input.select>

                <x-input.error for="recipient_id" />

            </div>

            <div class="mt-6 flex justify-end">
                <x-button variant="clean" type="button" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>

                <x-button class="ms-3">
                    Start Conversation
                </x-button>
            </div>
        </form>
    </x-modal>

    @pushOnce('scripts')
        <script>
            // Simple search filter with room and property search
            document.getElementById('user-search').addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                const select = document.getElementById('recipient_id');
                const options = select.getElementsByTagName('option');

                for (let i = 0; i < options.length; i++) {
                    const option = options[i];
                    const searchData = option.getAttribute('data-search') || option.textContent;
                    const matches = searchData.toLowerCase().includes(searchText);
                    option.style.display = matches ? '' : 'none';
                }
            });
        </script>
    @endPushOnce
</x-app-layout>
