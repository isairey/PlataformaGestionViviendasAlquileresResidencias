@props([
    'value' => null,
])

@php
    $status = strtolower($value);
    $styles = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'completed' => 'bg-green-100 text-green-700',
    ];
    $style = $styles[$status] ?? 'bg-gray-100 text-gray-700';
@endphp

<span {{ $attributes->merge([
    'class' => "px-2 py-1 rounded text-xs font-semibold {$style}"
]) }}>
    {{ ucfirst($value) ?? 'Unknown' }}
</span>
