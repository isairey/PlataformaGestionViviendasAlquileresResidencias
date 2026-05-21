@props([
    'step' => 1,
    'labels' => [],
])

@php
    $total = count($labels);
    $currentLabel = $labels[$step - 1] ?? '';
@endphp

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <div class="flex items-center justify-between mb-4">
        <span class="text-sm font-medium text-gray-900">Step {{ $step }} of {{ $total }}</span>
        <span class="text-sm text-gray-500">{{ $currentLabel }}</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-lime-600 h-2 rounded-full transition-all duration-300"
            style="width: {{ ($step / $total) * 100 }}%">
        </div>
    </div>
</div>
