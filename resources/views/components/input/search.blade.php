@props([
    'iconPosition' => 'left', // left | right
])

<div class="relative">
    @if ($iconPosition === 'left')
        <i
            class="ph-bold ph-magnifying-glass absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400"></i>
    @endif

    <input type="search"
        {{ $attributes->merge([
            'class' =>
                'bg-gray-50 border border-gray-300 text-gray-900 rounded-md focus:ring-lime-600 focus:border-lime-600 block w-full ' .
                ($iconPosition === 'left' ? 'pl-10 pr-4' : 'pl-4 pr-10'),
        ]) }} />

    @if ($iconPosition === 'right')
        <i
            class="ph-bold ph-magnifying-glass absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400"></i>
    @endif
</div>
