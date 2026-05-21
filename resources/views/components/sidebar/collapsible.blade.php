@props(['icon', 'title', 'badge' => null])

@php
    $storageKey = 'sidebar_collapsible_' . Str::slug($title);
@endphp

<div x-data="{
    isExpanded: localStorage.getItem('{{ $storageKey }}') === 'true',
    toggle() {
        this.isExpanded = !this.isExpanded;
        localStorage.setItem('{{ $storageKey }}', this.isExpanded);
    }
}" class="flex flex-col">

    {{-- Header --}}
    <button type="button" x-on:click="toggle"
        class="flex items-center gap-x-2 w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-800 hover:text-gray-900 hover:bg-gray-100 hover:border-gray-400 focus:outline-hidden focus:text-gray-900 focus:bg-gray-100 focus:border-gray-400 transition duration-150 ease-in-out">
        <i class="ph ph-{{ $icon }} text-lg"></i>
        <span>{{ $title }}</span>

        <div class="ms-auto flex items-center gap-x-2">
            @if ($badge)
                <span
                    class="inline-flex items-center justify-center min-w-5 h-5 px-2 text-xs font-semibold rounded-full text-lime-800 bg-lime-200">
                    {{ $badge }}
                </span>
            @endif

            <i class="ph-bold ph-caret-down text-lg transition-transform" :class="isExpanded ? 'rotate-180' : ''"></i>
        </div>

    </button>

    {{-- Content --}}
    <ul x-cloak x-collapse x-show="isExpanded" class="flex flex-col ms-4 border-s border-gray-200 mt-1">
        {{ $slot }}
    </ul>
</div>
