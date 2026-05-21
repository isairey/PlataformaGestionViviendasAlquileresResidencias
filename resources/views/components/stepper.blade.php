@props([
    'steps' => [], // ['Personal Info', 'Account Info', 'Confirmation']
    'current' => 0, // Current step (0-based index)
])

<ol class="flex items-center w-full text-sm font-medium text-gray-500 sm:text-base">
    @foreach ($steps as $index => $label)
        <li
            class="flex items-center {{ $index < count($steps) - 1 ? 'w-full after:content-[\'\'] after:w-full after:h-px after:inline-block after:mx-2 sm:after:mx-4 ' . ($index < $current ? 'after:bg-lime-600' : 'after:bg-gray-200') : '' }}">
            <span
                class="flex items-center gap-2 whitespace-nowrap {{ $index <= $current ? 'text-lime-600' : 'text-gray-400' }}">
                @if ($index < $current)
                    <i class="ph-fill ph-check-circle text-lg lg:text-xl"></i>
                @else
                    <span class="font-semibold">{{ $index + 1 }}</span>
                @endif

                <span class="hidden sm:inline">{{ $label }}</span>
            </span>
        </li>
    @endforeach
</ol>
