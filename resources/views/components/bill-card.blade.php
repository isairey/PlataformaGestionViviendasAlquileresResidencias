@props([
    'title' => '',
    'paymentMethods' => '',
    'amount' => '',
    'date' => '',
    'buttonText' => 'Pay Bill',
    'buttonUrl' => '#',
    'buttonAction' => null,
    'disabled' => false,
    'disabledMessage' => '',
])

<div class="bg-white rounded-2xl shadow-lg paybill-card w-full max-w-4xl py-14 px-16 flex flex-col md:flex-row md:items-center justify-between mb-6 transition-all">
    <div class="flex-1">
        <div class="md:text-left">
            <div class="font-semibold text-gray-700 text-xl mb-4 tracking-wide leading-tight">
                {{ $title }}
            </div>
            <hr class="border-t border-gray-200 mb-7">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-16">
                <div>
                    <div class="text-black font-bold text-lg md:text-xl mb-2 leading-snug">
                        {{ $paymentMethods }}
                    </div>
                    <div class="text-gray-400 text-sm font-medium tracking-widest uppercase mb-5">
                        Payment Methods
                    </div>
                    <div class="text-lime-700 font-extrabold text-2xl md:text-3xl mb-2 mt-4 leading-none">
                        {{ $amount }}
                    </div>
                    <div class="text-gray-400 text-sm font-medium tracking-widest uppercase mb-2">
                        Total Amount
                    </div>
                </div>
                <div class="md:text-right flex flex-col items-end gap-4 md:gap-4">
                    <div class="text-black font-bold text-lg md:text-xl mb-1 leading-snug">
                        {{ $date }}
                    </div>
                    <div class="text-gray-400 text-sm font-medium tracking-widest uppercase mb-4">
                        Date
                    </div>
                    <button
                        @if($disabled)
                            disabled
                            class="bg-lime-600 opacity-80 cursor-not-allowed px-10 py-3 rounded-lg font-semibold flex items-center gap-2 text-lg shadow min-w-[140px] justify-center outline-none"
                            style="pointer-events: none;"
                            title="{{ $disabledMessage }}"
                        @else
                            @if($buttonAction)
                                onclick="{{ $buttonAction }}"
                            @elseif($buttonUrl !== '#')
                                onclick="window.location='{{ $buttonUrl }}'"
                            @endif
                            class="bg-lime-600 hover:bg-lime-700 focus:bg-lime-800 focus:ring-2 focus:ring-lime-400 focus:ring-offset-2 transition-all text-white px-10 py-3 rounded-lg font-semibold flex items-center gap-2 text-lg shadow min-w-[140px] justify-center outline-none"
                        @endif
                    >
                        {{ $buttonText }} <span class="ml-2">&#8250;</span>
                    </button>
                    @if($disabled && $disabledMessage)
                        <div class="text-sm text-red-600 mt-2">{{ $disabledMessage }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
