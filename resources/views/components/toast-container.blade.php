<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-3 max-w-sm">
    @stack('toasts')

    @php
        $toastTypes = ['success', 'error', 'warning', 'info', 'plain'];
        $toasts = session('toast', []);
    @endphp

    @if (!empty($toasts))
        @foreach ($toastTypes as $type)
            @if (isset($toasts[$type]))
                <x-toast :type="$type" :title="$toasts[$type]['title'] ?? ucfirst($type)" :autoClose="$toasts[$type]['autoClose'] ?? true">
                    {{ $toasts[$type]['content'] ?? '' }}
                </x-toast>
            @endif
        @endforeach
    @endif


    {{-- <x-toast type="success" title="Profile Updated">
        Your profile has been updated successfully!
    </x-toast>

    @pushOnce('toasts')
        <x-toast type="warning" title="Upload Failed">
            The file you selected is too large. Please choose a smaller file.
        </x-toast>
    @endPushOnce
    @pushOnce('toasts')
        <x-toast type="info" title="Upload Failed">
            The file you selected is too large. Please choose a smaller file.
        </x-toast>
    @endPushOnce

    @pushOnce('toasts')
        <x-toast type="error" title="Upload Failed">
            The file you selected is too large. Please choose a smaller file.
        </x-toast>
    @endPushOnce

    @pushOnce('toasts')
        <x-toast type="plain">
            This is a simple notification without an icon.
        </x-toast>
    @endPushOnce --}}
</div>
