@props(['caption', 'value', 'footer' => false, 'footerCaption' => '', 'footerDate' => '', 'wide' => false, 'headerActions' => null])

<div {{ $attributes->merge(['class' => 'bg-white shadow-lg sm:rounded-lg mb-2 ' . ($wide ? 'w-full' : '')]) }}>
    <div class="p-4 bg-white">
        <div class="flex items-center justify-between mb-2">
            <h3 class="flex-1 text-base font-bold text-center text-gray-500">
                {{ $caption }}
            </h3>
            @isset($headerActions)
                <div class="ml-2">
                    {{ $headerActions }}
                </div>
            @endisset
        </div>
        <div class="border-t border-gray-200 mb-2"></div>
        @if ($value !== '') {{-- Only display value if it's not empty --}}
            <div class="text-2xl font-extrabold text-gray-900 text-center">
                {{ $value }}
            </div>
        @endif
        @if (isset($footer) && $footer)
            <div class="mt-2 text-xs text-gray-500 text-center">
                {{ $footerCaption ?? '' }}:
                <span class="font-semibold">{{ $footerDate ?? '' }}</span>
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
