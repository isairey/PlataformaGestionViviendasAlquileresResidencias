@props([
    'options' => [],
    'selected' => null,
    'placeholder' => 'Please Select',
])

<select
    {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 rounded-md focus:ring-lime-600 focus:border-lime-600 block w-full']) }}>

    {{-- Empty initial option --}}
    <option value="" disabled {{ $selected === null ? 'selected' : '' }}> -- {{ $placeholder }} -- </option>

    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        @foreach ($options as $value => $label)
            <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    @endif
</select>
