{{--
  Badge component with customizable:
  - color (from predefined options)
  - icon (Phosphor icon class)
  - interactive (adds hover/focus styles)
  - type (HTML tag)
  - size (xs, sm, md, lg)

  Available colors: gray, blue, green, red, yellow, purple, pink, indigo, lime, orange
--}}

@props([
    'color' => 'gray',
    'icon' => null,
    'interactive' => false,
    'type' => 'span',
    'size' => 'sm',
])

@php
    // Size classes
    $sizes = [
        'xs' => 'px-2 py-0.5 text-[10px]',
        'sm' => 'px-2.5 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-3.5 py-1.5 text-base',
    ];
    $sizeClasses = $sizes[$size] ?? $sizes['sm'];

    // Base classes
    $baseClasses = 'inline-flex items-center rounded-full font-medium transition-colors';
    $iconClasses = $icon ? 'mr-1' : '';

    // Color classes - explicitly include all variants to ensure JIT compilation
    $colorVariants = [
        'gray' => [
            'base' => 'bg-gray-100 text-gray-800',
            'hover' => 'hover:bg-gray-200',
            'focus' => 'focus:ring-gray-500',
        ],
        'lime' => [
            'base' => 'bg-lime-100 text-lime-800',
            'hover' => 'hover:bg-lime-200',
            'focus' => 'focus:ring-lime-500',
        ],
        'blue' => [
            'base' => 'bg-blue-100 text-blue-800',
            'hover' => 'hover:bg-blue-200',
            'focus' => 'focus:ring-blue-500',
        ],
        'green' => [
            'base' => 'bg-green-100 text-green-800',
            'hover' => 'hover:bg-green-200',
            'focus' => 'focus:ring-green-500',
        ],
        'red' => [
            'base' => 'bg-red-100 text-red-800',
            'hover' => 'hover:bg-red-200',
            'focus' => 'focus:ring-red-500',
        ],
        'yellow' => [
            'base' => 'bg-yellow-100 text-yellow-800',
            'hover' => 'hover:bg-yellow-200',
            'focus' => 'focus:ring-yellow-500',
        ],
        'purple' => [
            'base' => 'bg-purple-100 text-purple-800',
            'hover' => 'hover:bg-purple-200',
            'focus' => 'focus:ring-purple-500',
        ],
        'pink' => [
            'base' => 'bg-pink-100 text-pink-800',
            'hover' => 'hover:bg-pink-200',
            'focus' => 'focus:ring-pink-500',
        ],
        'indigo' => [
            'base' => 'bg-indigo-100 text-indigo-800',
            'hover' => 'hover:bg-indigo-200',
            'focus' => 'focus:ring-indigo-500',
        ],

        'orange' => [
            'base' => 'bg-orange-100 text-orange-800',
            'hover' => 'hover:bg-orange-200',
            'focus' => 'focus:ring-orange-500',
        ],
    ];

    $selectedColor = $colorVariants[$color] ?? $colorVariants['gray'];
    $colorClasses = $selectedColor['base'];
    $interactiveClasses = $interactive
        ? "{$selectedColor['hover']} focus:outline-none focus:ring-2 {$selectedColor['focus']} focus:ring-offset-2 cursor-pointer"
        : '';
@endphp

<{{ $type }}
    {{ $attributes->merge([
        'class' => "$baseClasses $sizeClasses $colorClasses $interactiveClasses",
    ]) }}>
    @if ($icon)
        <i class="ph-bold {{ $icon }} {{ $iconClasses }}"></i>
    @endif
    {{ $slot }}
    </{{ $type }}>
