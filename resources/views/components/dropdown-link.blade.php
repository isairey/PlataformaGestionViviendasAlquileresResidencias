@props([
    'color' => 'gray',
    'variant' => 'default', // default, active, disabled
    'size' => 'base', // sm, base, lg
])

@php
    // Define color configurations
    $colors = [
        'gray' => [
            'default' => [
                'border' => 'border-transparent hover:border-gray-400 focus:border-gray-400',
                'text' => 'text-gray-800 hover:text-gray-900 focus:text-gray-900',
                'bg' => 'hover:bg-gray-100 focus:bg-gray-100',
            ],
            'active' => [
                'border' => 'border-gray-500',
                'text' => 'text-gray-900',
                'bg' => 'bg-gray-100',
            ],
            'disabled' => [
                'border' => 'border-transparent',
                'text' => 'text-gray-400',
                'bg' => 'bg-transparent cursor-not-allowed',
            ],
        ],
        'blue' => [
            'default' => [
                'border' => 'border-transparent hover:border-blue-400 focus:border-blue-400',
                'text' => 'text-blue-700 hover:text-blue-800 focus:text-blue-800',
                'bg' => 'hover:bg-blue-50 focus:bg-blue-50',
            ],
            'active' => [
                'border' => 'border-blue-500',
                'text' => 'text-blue-900',
                'bg' => 'bg-blue-100',
            ],
            'disabled' => [
                'border' => 'border-transparent',
                'text' => 'text-blue-300',
                'bg' => 'bg-transparent cursor-not-allowed',
            ],
        ],
        'red' => [
            'default' => [
                'border' => 'border-transparent hover:border-red-400 focus:border-red-400',
                'text' => 'text-red-700 hover:text-red-800 focus:text-red-800',
                'bg' => 'hover:bg-red-50 focus:bg-red-50',
            ],
            'active' => [
                'border' => 'border-red-500',
                'text' => 'text-red-900',
                'bg' => 'bg-red-100',
            ],
            'disabled' => [
                'border' => 'border-transparent',
                'text' => 'text-red-300',
                'bg' => 'bg-transparent cursor-not-allowed',
            ],
        ],
        'green' => [
            'default' => [
                'border' => 'border-transparent hover:border-green-400 focus:border-green-400',
                'text' => 'text-green-700 hover:text-green-800 focus:text-green-800',
                'bg' => 'hover:bg-green-50 focus:bg-green-50',
            ],
            'active' => [
                'border' => 'border-green-500',
                'text' => 'text-green-900',
                'bg' => 'bg-green-100',
            ],
            'disabled' => [
                'border' => 'border-transparent',
                'text' => 'text-green-300',
                'bg' => 'bg-transparent cursor-not-allowed',
            ],
        ],
        'yellow' => [
            'default' => [
                'border' => 'border-transparent hover:border-yellow-400 focus:border-yellow-400',
                'text' => 'text-yellow-700 hover:text-yellow-800 focus:text-yellow-800',
                'bg' => 'hover:bg-yellow-50 focus:bg-yellow-50',
            ],
            'active' => [
                'border' => 'border-yellow-500',
                'text' => 'text-yellow-900',
                'bg' => 'bg-yellow-100',
            ],
            'disabled' => [
                'border' => 'border-transparent',
                'text' => 'text-yellow-400',
                'bg' => 'bg-transparent cursor-not-allowed',
            ],
        ],
        'purple' => [
            'default' => [
                'border' => 'border-transparent hover:border-purple-400 focus:border-purple-400',
                'text' => 'text-purple-700 hover:text-purple-800 focus:text-purple-800',
                'bg' => 'hover:bg-purple-50 focus:bg-purple-50',
            ],
            'active' => [
                'border' => 'border-purple-500',
                'text' => 'text-purple-900',
                'bg' => 'bg-purple-100',
            ],
            'disabled' => [
                'border' => 'border-transparent',
                'text' => 'text-purple-300',
                'bg' => 'bg-transparent cursor-not-allowed',
            ],
        ],
        'indigo' => [
            'default' => [
                'border' => 'border-transparent hover:border-indigo-400 focus:border-indigo-400',
                'text' => 'text-indigo-700 hover:text-indigo-800 focus:text-indigo-800',
                'bg' => 'hover:bg-indigo-50 focus:bg-indigo-50',
            ],
            'active' => [
                'border' => 'border-indigo-500',
                'text' => 'text-indigo-900',
                'bg' => 'bg-indigo-100',
            ],
            'disabled' => [
                'border' => 'border-transparent',
                'text' => 'text-indigo-300',
                'bg' => 'bg-transparent cursor-not-allowed',
            ],
        ],
    ];

    // Define size configurations
    $sizes = [
        'sm' => 'ps-2 pe-3 py-1 text-sm',
        'base' => 'ps-3 pe-4 py-2 text-base',
        'lg' => 'ps-4 pe-5 py-3 text-lg',
    ];

    // Get current color config
    $currentColor = $colors[$color] ?? $colors['gray'];
    $currentVariant = $currentColor[$variant] ?? $currentColor['default'];

    // Build classes
    $classes = collect([
        'flex items-center gap-x-2 w-full border-l-4 text-start font-medium transition duration-150 ease-in-out focus:outline-hidden',
        $sizes[$size] ?? $sizes['base'],
        $currentVariant['border'],
        $currentVariant['text'],
        $currentVariant['bg'],
    ])
        ->filter()
        ->join(' ');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
