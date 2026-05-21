{{-- resources/views/components/toast.blade.php --}}
@props([
    'type' => 'success', // success, error, warning, info, plain
    'title' => null,
    'autoClose' => true,
])

@php
    $config = [
        'success' => [
            'bg' => 'bg-lime-100',
            'text' => 'text-lime-500',
            'progress' => 'bg-lime-500',
            'shadow-sm' => 'shadow-[0_0_10px_3px_rgba(124,207,0.7)]',
            'icon' => 'ph-check-circle',
            'defaultTitle' => 'Success',
        ],
        'error' => [
            'bg' => 'bg-red-100',
            'text' => 'text-red-500',
            'progress' => 'bg-red-500',
            'shadow-sm' => 'shadow-[0_0_10px_3px_rgba(239,68,68,0.7)]',
            'icon' => 'ph-x-circle',
            'defaultTitle' => 'Error',
        ],
        'warning' => [
            'bg' => 'bg-yellow-100',
            'text' => 'text-yellow-500',
            'progress' => 'bg-yellow-500',
            'shadow-sm' => 'shadow-[0_0_10px_3px_rgba(234,179,8,0.7)]',
            'icon' => 'ph-warning',
            'defaultTitle' => 'Warning',
        ],
        'info' => [
            'bg' => 'bg-blue-100',
            'text' => 'text-blue-500',
            'progress' => 'bg-blue-500',
            'shadow-sm' => 'shadow-[0_0_10px_3px_rgba(59,130,246,0.7)]',
            'icon' => 'ph-info',
            'defaultTitle' => 'Info',
        ],
        'plain' => [
            'bg' => 'bg-gray-100',
            'text' => 'text-gray-500',
            'progress' => 'bg-gray-500',
            'shadow-sm' => 'shadow-[0_0_10px_3px_rgba(107,114,128,0.7)]',
            'icon' => '',
            'defaultTitle' => 'Notification',
        ],
    ];

    $currentConfig = $config[$type] ?? $config['success'];
    $displayTitle = $title ?? $currentConfig['defaultTitle'];
    $toastId = 'toast-' . uniqid();
@endphp

<div x-data="{
    show: true,
    autoClose: @json($autoClose),
    duration: 3000,
    init() {
        if (this.autoClose) {
            setTimeout(() => {
                this.show = false;
            }, this.duration);
        }
    },
    close() {
        this.show = false;
    }
}" x-show="show" x-transition:enter="transform transition ease-out duration-500"
    x-transition:enter-start="translate-x-full opacity-0 scale-95"
    x-transition:enter-end="translate-x-0 opacity-100 scale-100"
    x-transition:leave="transform transition ease-in duration-300"
    x-transition:leave-start="translate-x-0 opacity-100 scale-100"
    x-transition:leave-end="translate-x-full opacity-0 scale-95"
    class="bg-white ml-4 rounded-lg shadow-xl overflow-hidden backdrop-blur-xs {{ $type === 'plain' ? 'border border-gray-200' : '' }}">

    @if ($type !== 'plain')
        {{-- Standard toast with icon --}}
        <div class="p-4 flex space-x-3">
            <div class="relative w-10 h-10">
                <div class="absolute inset-0 rounded-md {{ $currentConfig['bg'] }}"></div>
                <i
                    class="ph-fill {{ $currentConfig['icon'] }} {{ $currentConfig['text'] }} text-3xl absolute inset-0 m-auto w-fit h-fit"></i>
            </div>
            <div class="flex-1 flex-col">
                <p class="font-semibold text-gray-900">{{ $displayTitle }}</p>
                @if ($slot->isNotEmpty())
                    <p class="text-sm text-gray-700 mt-1">{{ $slot }}</p>
                @endif
            </div>
            <button @click="close()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="ph ph-x text-lg"></i>
            </button>
        </div>
        @if ($autoClose)
            <div class="h-1.5 bg-gray-200">
                <div
                    class="h-full {{ $currentConfig['progress'] }} animate-shrink rounded-tr-full {{ $currentConfig['shadow-sm'] }}">
                </div>
            </div>
        @endif
    @else
        {{-- Plain toast --}}
        <div class="p-4 flex items-center space-x-3">
            <div class="flex-1 text-sm text-gray-800">{{ $slot }}</div>
            <button @click="close()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="ph ph-x text-lg"></i>
            </button>
        </div>
        @if ($autoClose)
            <div class="h-1 bg-gray-200">
                <div
                    class="h-full {{ $currentConfig['progress'] }} animate-shrink rounded-tr-full {{ $currentConfig['shadow-sm'] }}">
                </div>
            </div>
        @endif
    @endif
</div>
