@props([
    'align' => 'right',
    'width' => '48',
    'position' => 'bottom',
    'contentClasses' => 'py-1 bg-white',
    'fullWidth' => true,
])

@php
    $alignmentClasses = match ($align) {
        'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
        'top' => 'origin-top',
        default => 'ltr:origin-top-right rtl:origin-top-left end-0',
    };

    $width = match ($width) {
        '48' => 'w-48',
        default => $width,
    };

    $positionClasses = $position === 'top' ? 'bottom-full mb-2' : 'mt-2';
@endphp

<div class="relative {{ $fullWidth ? 'w-full' : '' }}" x-data="{ open: false }" @click.outside="open = false"
    @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 {{ $positionClasses }} {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
        style="display: none;" @click="open = false">
        <div class="rounded-md ring-1 ring-black/5 {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
