@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'bg-gray-50 border border-gray-300 text-gray-900 rounded-md focus:ring-lime-600 focus:border-lime-600 block w-full']) }}>
