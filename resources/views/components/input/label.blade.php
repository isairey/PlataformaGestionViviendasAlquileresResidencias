@props(['required' => false])

@php
    $baseClass = 'block font-medium text-sm text-gray-700 mb-1';
    $requiredClass = $required ? " after:ml-0.25 after:text-red-500 after:content-['*']" : '';
@endphp

<label {{ $attributes->merge(['class' => $baseClass . $requiredClass]) }}>
    {{ $slot }}
</label>
