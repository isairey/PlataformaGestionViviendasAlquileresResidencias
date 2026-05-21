@props([
    'variant' => 'primary',
    'uppercase' => true,
    'disabled' => false,
])

@php
    $isPlainText = $variant === 'text';

    $case = $uppercase ? 'text-xs uppercase tracking-widest' : 'text-sm';

    $baseClass = implode(' ', [
        $case,
        'inline-flex items-center justify-center gap-x-1 px-4 py-2 border rounded-md font-semibold',
        'transition ease-in-out duration-150',
        'focus:outline-hidden focus:ring-2 focus:ring-offset-2',
        $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer',
    ]);

    $variantStyles = [
        'primary' => [
            'base' => 'bg-lime-600 border-transparent text-white',
            'hover' => 'hover:bg-lime-700',
            'focus' => 'focus:bg-lime-700 focus:ring-lime-700',
            'active' => 'active:bg-lime-800',
        ],
        'clean' => [
            'base' => 'bg-white border-gray-300 text-gray-700 shadow-xs',
            'hover' => 'hover:bg-gray-50',
            'focus' => 'focus:ring-lime-600',
        ],
        'dark' => [
            'base' => 'bg-gray-800 border-transparent text-white',
            'hover' => 'hover:bg-gray-700',
            'focus' => 'focus:bg-gray-700 focus:ring-lime-600',
            'active' => 'active:bg-gray-900',
        ],
        'danger' => [
            'base' => 'bg-red-600 border-transparent text-white',
            'hover' => 'hover:bg-red-500',
            'focus' => 'focus:ring-red-500',
            'active' => 'active:bg-red-700',
        ],
        'text' => [
            'base' => 'text-sm text-lime-600 bg-transparent focus:outline-hidden',
            'hover' => 'hover:underline hover:text-lime-700',
            'focus' => 'focus:underline focus:text-lime-700',
        ],
    ];

    $styles = $variantStyles[$variant] ?? $variantStyles['primary'];

    $interactive = $disabled
        ? ''
        : implode(' ', array_filter([$styles['hover'] ?? '', $styles['focus'] ?? '', $styles['active'] ?? '']));

    $buttonClass = $isPlainText
        ? implode(' ', [$styles['base'], $interactive, $disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'])
        : implode(' ', [$baseClass, $styles['base'], $interactive]);
@endphp

@if ($disabled)
    <span {{ $attributes->merge(['class' => $buttonClass]) }} aria-disabled="true">
        {{ $slot }}
    </span>
@else
    <button {{ $attributes->merge(['class' => $buttonClass]) }}>
        {{ $slot }}
    </button>
@endif
