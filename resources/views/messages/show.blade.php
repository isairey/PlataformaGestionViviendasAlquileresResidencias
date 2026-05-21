<x-app-layout title="Chat with {{ $otherParticipant->name ?? 'Unknown User' }}">
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('messages.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat with {{ $otherParticipant->name ?? 'Unknown User' }}
            </h2>
        </div>
    </x-slot>

    @php
        $pictureBanner = $otherParticipant->profile_picture
            ? Storage::url($otherParticipant->profile_picture)
            : Vite::asset('resources/assets/images/default_profile.png');
    @endphp

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg border-l-4 border-lime-800 mb-8">
            <!-- Messages Container -->
            <div class="p-4 flex gap-2 items-center text-lg font-bold border-b border-gray-200 rounded-b-md">
                <x-a href="{{ route('messages.index') }}">
                    <i class="ph-bold ph-arrow-left text-lg"></i>
                </x-a>
                <img class="size-8 aspect-square rounded-full" src="{{ $pictureBanner }}" />
                {{ $otherParticipant->name ?? 'Unknown User' }}
            </div>
            <div id="messages-container"
                class="h-[calc(100vh-15rem)] overflow-y-auto p-4 space-y-4 border-b">
                @foreach ($messages as $message)
                    @if ($message->content !== null || $message->type !== 'text')
                        @php
                            $isCurrentUser = $message->user_id === auth()->id();
                            $profilePicture = $isCurrentUser
                                ? (auth()->user()->profile_picture
                                    ? Storage::url(auth()->user()->profile_picture)
                                    : Vite::asset('resources/assets/images/default_profile.png'))
                                : ($message->user->profile_picture
                                    ? Storage::url($message->user->profile_picture)
                                    : Vite::asset('resources/assets/images/default_profile.png'));

                            $meta = is_string($message->metadata)
                                ? json_decode($message->metadata, true)
                                : $message->metadata;
                            $fileUrl = $meta['path'] ?? null ? Storage::url($meta['path']) : null;
                        @endphp

                        <div class="message {{ $isCurrentUser ? 'flex justify-end' : 'flex justify-start' }}">
                            <div class="flex items-start gap-2.5 {{ $isCurrentUser ? 'flex-row-reverse' : '' }}">
                                <img class="aspect-square w-8 h-8 rounded-full" src="{{ $profilePicture }}"
                                    alt="Avatar">
                                <div
                                    class="flex flex-col w-full max-w-sm p-4 {{ $isCurrentUser ? 'bg-lime-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-xl break-words">
                                    <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                        <span
                                            class="text-sm font-semibold">{{ $isCurrentUser ? 'You' : $message->user->name }}</span>
                                        <span
                                            class="text-sm font-normal {{ $isCurrentUser ? 'text-lime-100' : 'text-gray-500' }}">
                                            {{ $message->created_at->format('g:i A') }}
                                        </span>
                                    </div>

                                    @if ($message->type === 'image' && $fileUrl)
                                        <img src="{{ $fileUrl }}" class="rounded-lg max-w-xs mt-2"
                                            alt="Attached image">
                                    @elseif ($message->type === 'file' && $fileUrl)
                                        <a href="{{ $fileUrl }}" download class="text-blue-600 underline mt-2">
                                            {{ $meta['original_name'] ?? 'file' }}
                                        </a>
                                    @else
                                        <p class="font-normal py-2.5">{{ $message->content }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Message Input Form -->
            <form id="message-form" class="p-4" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col w-full gap-2">
                    <!-- File attachment indicator -->
                    <div id="attachment-indicator" class="hidden bg-lime-50 border border-lime-200 rounded-lg p-3 mb-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="ph-bold ph-paperclip text-lime-600"></i>
                                <span class="text-sm text-lime-800">
                                    <span id="attachment-name">File attached</span>
                                    <span class="text-xs text-lime-600 ml-1">(<span id="attachment-size"></span>)</span>
                                </span>
                            </div>
                            <button type="button" id="remove-attachment" class="text-lime-600 hover:text-lime-800">
                                <i class="ph-bold ph-x text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 md:flex-row">
                        <div class="flex-1 relative">
                            <x-input.textarea class="resize-none transition-all duration-200" rows="2"
                                id="message-input" name="content" placeholder="Type your message...">
                            </x-input.textarea>

                            <!-- Overlay for when file is attached and text should be disabled -->
                            <div id="textarea-overlay"
                                class="hidden absolute inset-0 bg-gray-100 bg-opacity-90 rounded-md items-center justify-center">
                                <span class="text-gray-600 text-sm">Remove attachment to type a message</span>
                            </div>
                        </div>

                        <input type="file" name="attachment" id="attachment" class="hidden" />

                        <div class="flex items-stretch gap-2 justify-stretch">
                            <x-button variant="dark" type="button" id="attachment-btn" class="flex-1">
                                <i class="ph-bold ph-paperclip text-lg"></i>
                            </x-button>
                            <x-button type="submit" id="send-btn" class="flex-2">
                                <i class="ph-bold ph-paper-plane-tilt text-lg"></i>
                            </x-button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

    @push('scripts')
        <script type="module">
            window.autoAnimate(document.getElementById('messages-container'));
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const conversationId = {{ $conversation->id }};
                const currentUserId = {{ auth()->id() }};
                const messageForm = document.getElementById('message-form');
                const messageInput = document.getElementById('message-input');
                const attachmentInput = document.getElementById('attachment');
                const attachmentBtn = document.getElementById('attachment-btn');
                const messagesContainer = document.getElementById('messages-container');
                const attachmentIndicator = document.getElementById('attachment-indicator');
                const attachmentName = document.getElementById('attachment-name');
                const attachmentSize = document.getElementById('attachment-size');
                const removeAttachmentBtn = document.getElementById('remove-attachment');
                const textareaOverlay = document.getElementById('textarea-overlay');
                const sendBtn = document.getElementById('send-btn');

                // File size formatter
                function formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                }

                // Handle attachment button click
                attachmentBtn.addEventListener('click', () => {
                    attachmentInput.click();
                });

                // Handle file selection
                attachmentInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];

                    if (file) {
                        // Show attachment indicator
                        attachmentIndicator.classList.remove('hidden');
                        attachmentName.textContent = file.name;
                        attachmentSize.textContent = formatFileSize(file.size);

                        // Disable textarea and show overlay
                        messageInput.disabled = true;
                        messageInput.placeholder = "Remove attachment to type a message";
                        textareaOverlay.classList.remove('hidden');
                        textareaOverlay.classList.add('flex');

                        // Clear any existing text
                        messageInput.value = '';
                    }
                });

                // Handle attachment removal
                removeAttachmentBtn.addEventListener('click', () => {
                    // Clear file input
                    attachmentInput.value = '';

                    // Hide attachment indicator
                    attachmentIndicator.classList.add('hidden');

                    // Re-enable textarea
                    messageInput.disabled = false;
                    messageInput.placeholder = "Type your message...";
                    textareaOverlay.classList.add('hidden');
                    textareaOverlay.classList.remove('flex');

                    // Focus back to input
                    messageInput.focus();
                });

                // Update send button state
                function updateSendButton() {
                    const hasContent = messageInput.value.trim().length > 0;
                    const hasFile = attachmentInput.files.length > 0;

                    if (hasContent || hasFile) {
                        sendBtn.disabled = false;
                        sendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        sendBtn.disabled = true;
                        sendBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }

                // Listen for input changes
                messageInput.addEventListener('input', updateSendButton);
                attachmentInput.addEventListener('change', updateSendButton);

                // Initialize send button state
                updateSendButton();

                // Real-time broadcasting
                window.Echo.private(`conversation.${conversationId}`)
                    .listen('MessageSent', (e) => {
                        addMessageToChat(e.message);
                    });

                messageForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const content = messageInput.value.trim();
                    const file = attachmentInput.files[0];

                    if (!content && !file) return;

                    const formData = new FormData();
                    if (content) formData.append('content', content);
                    if (file) formData.append('attachment', file);

                    // Disable form while sending
                    const submitButton = messageForm.querySelector('button[type="submit"]');
                    submitButton.disabled = true;

                    fetch(`{{ route('messages.store', $conversation) }}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw new Error(JSON.stringify(errorData));
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                messageInput.value = '';

                                // Reset attachment state
                                if (attachmentInput.files.length > 0) {
                                    removeAttachmentBtn.click(); // This will reset everything
                                }

                                clearErrorMessages();
                                updateSendButton();
                            } else {
                                showError(data.message || 'Failed to send message');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            try {
                                const errorData = JSON.parse(error.message);
                                if (errorData.errors) {
                                    let errorMessages = [];
                                    for (const field in errorData.errors) {
                                        errorMessages.push(...errorData.errors[field]);
                                    }
                                    showError(errorMessages.join('<br>'));
                                } else if (errorData.message) {
                                    showError(errorData.message);
                                } else {
                                    showError('Failed to send message. Please try again.');
                                }
                            } catch (parseError) {
                                showError('Failed to send message. Please try again.');
                            }
                        })
                        .finally(() => {
                            submitButton.disabled = false;
                            updateSendButton();
                        });
                });

                // Function to show error messages
                function showError(message) {
                    clearErrorMessages();
                    const errorDiv = document.createElement('div');
                    errorDiv.id = 'error-message';
                    errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
                    errorDiv.innerHTML = `
                <div class="flex justify-between items-center">
                    <span>${message}</span>
                    <button onclick="clearErrorMessages()" class="text-red-700 hover:text-red-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
                    messageForm.parentNode.insertBefore(errorDiv, messageForm);
                    setTimeout(clearErrorMessages, 5000);
                }

                function clearErrorMessages() {
                    const errorMessage = document.getElementById('error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }

                window.clearErrorMessages = clearErrorMessages;

                // [Rest of your existing addMessageToChat and escapeHtml functions remain the same]
                function addMessageToChat(message) {
                    const messagesContainer = document.getElementById('messages-container');
                    const isCurrentUser = message.user_id === currentUserId;

                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${isCurrentUser ? 'flex justify-end' : 'flex justify-start'}`;

                    let contentHtml = '';

                    if ((message.type === 'file' || message.type === 'image') && message.metadata) {
                        const meta = message.metadata;
                        const isImage = meta.mime && meta.mime.startsWith('image/');
                        const fileUrl = meta.url || `/storage/${meta.path}`;

                        if (isImage || message.type === 'image') {
                            contentHtml =
                                `<img src="${fileUrl}" class="rounded-lg max-w-xs mt-2 auto-scroll-image" alt="Attached image">`;
                        } else {
                            contentHtml =
                                `<a href="${fileUrl}" download class="text-blue-600 underline mt-2">${escapeHtml(meta.original_name || 'file')}</a>`;
                        }
                    } else if (message.content && message.content.trim() !== '') {
                        const formattedContent = escapeHtml(message.content).replace(/\n/g, '<br>');
                        contentHtml =
                            `<p class="font-normal py-2.5 ${isCurrentUser ? 'text-white' : 'text-gray-900'}">${formattedContent}</p>`;
                    }

                    if (contentHtml) {
                        messageDiv.innerHTML = `
                    <div class="flex items-start gap-2.5 ${isCurrentUser ? 'flex-row-reverse' : ''}">
                        <img class="aspect-square w-8 h-8 rounded-full" src="${message.user.profile_picture || '/path/to/default-avatar.png'}" alt="${escapeHtml(message.user.name)} avatar">
                        <div class="flex flex-col w-full max-w-sm min-w-[100px] p-4 ${isCurrentUser ? 'bg-lime-600' : 'bg-gray-100'} rounded-xl break-words">
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                <span class="text-sm font-semibold ${isCurrentUser ? 'text-white' : 'text-gray-900'}">
                                    ${isCurrentUser ? 'You' : escapeHtml(message.user.name)}
                                </span>
                                <span class="text-sm font-normal ${isCurrentUser ? 'text-lime-100' : 'text-gray-500'}">
                                    ${message.created_at}
                                </span>
                            </div>
                            ${contentHtml}
                        </div>
                    </div>
                `;

                        messagesContainer.appendChild(messageDiv);

                        const img = messageDiv.querySelector('img.auto-scroll-image');
                        if (img) {
                            img.addEventListener('load', () => {
                                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                            });
                        } else {
                            messagesContainer.scrollTop = messagesContainer.scrollHeight;
                        }
                    }
                }

                function escapeHtml(text) {
                    if (!text) return '';
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }

                // Scroll to bottom on load
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                messageInput.focus();
            });
        </script>
    @endpush
</x-app-layout>
