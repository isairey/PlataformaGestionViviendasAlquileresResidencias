<x-app-layout title="Pay Bills">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pay Your Bills
        </h2>
    </x-slot>

    <div x-data="{ showGcashModal: false, showMayaModal: false, showCashModal: false }" class="max-w-7xl mx-auto p-6 bg-white rounded shadow mt-12">
        <h1 class="text-2xl font-bold mb-6">Choose a Payment Method</h1>
        <div class="mb-6">
            <div class="font-semibold mb-2">
                {{ $displayType === 'outstanding' ? 'Outstanding Amount:' : 'Monthly Amount:' }}
            </div>
            <div class="text-2xl text-lime-700 font-extrabold mb-2">
                ₱{{ number_format($displayAmount, 2) }}
            </div>
            <div class="text-sm text-gray-500 mb-2">
                Due Date: {{ $displayDueDate ? \Carbon\Carbon::parse($displayDueDate)->format('m-d-Y') : '-' }}
            </div>
        </div>
        <div class="mb-8">
            <div class="font-semibold mb-2">Available Payment Methods:</div>
            <div class="flex flex-wrap gap-4">
                @foreach(\App\Enums\PaymentMethod::cases() as $method)
                    @php
                        $targetBill = $displayType === 'outstanding'
                            ? (\App\Models\Bill::where('tenant_id', auth()->user()->tenant->id ?? null)
                                ->where('status', \App\Enums\BillStatus::OVERDUE->value)
                                ->where('amount_due', '>', 0)
                                ->orderBy('due_date', 'asc')
                                ->first())
                            : $monthlyBill;
                    @endphp
                    <form method="POST" action="{{ route('transaction.store') }}" class="flex-1 min-w-[120px]" onsubmit="return false;">
                        @csrf
                        @if($method->name === 'GCASH')
                            <input type="hidden" name="payment_method" value="gcash">
                            <input type="hidden" name="bill_id" value="{{ $targetBill->id ?? '' }}">
                            <x-button
                                variant="primary"
                                type="button"
                                class="w-full px-6 py-3 mb-2 flex items-center justify-center gap-2 text-base"
                                @click="showGcashModal = true"
                                :disabled="!$targetBill"
                            >
                                <svg class="h-6 w-6 mr-2 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><use href="#icon-banknotes"></use></svg>
                                <span>{{ ucfirst(str_replace('_', ' ', $method->name)) }}</span>
                            </x-button>
                        @elseif($method->name === 'MAYA')
                            <input type="hidden" name="payment_method" value="maya">
                            <input type="hidden" name="bill_id" value="{{ $targetBill->id ?? '' }}">
                            <x-button
                                variant="primary"
                                type="button"
                                class="w-full px-6 py-3 mb-2 flex items-center justify-center gap-2 text-base"
                                @click="showMayaModal = true"
                                :disabled="!$targetBill"
                            >
                                <svg class="h-6 w-6 mr-2 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><use href="#icon-credit-card"></use></svg>
                                <span>{{ ucfirst(str_replace('_', ' ', $method->name)) }}</span>
                            </x-button>
                        @else
                            <input type="hidden" name="payment_method" value="cash">
                            <input type="hidden" name="bill_id" value="{{ $targetBill->id ?? '' }}">
                            <x-button
                                variant="primary"
                                type="button"
                                class="w-full px-6 py-3 mb-2 flex items-center justify-center gap-2 text-base"
                                @click="showCashModal = true"
                                :disabled="!$targetBill"
                            >
                                <svg class="h-6 w-6 mr-2 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><use href="#icon-cash"></use></svg>
                                <span>{{ ucfirst(str_replace('_', ' ', $method->name)) }}</span>
                            </x-button>
                        @endif
                    </form>
                @endforeach
            </div>
        </div>
        <div>
            <p class="italic text-gray-500">
                Click a payment method button to proceed.
            </p>
        </div>
        <div class="mt-8">
            <a href="{{ route('transaction.index') }}"
               class="text-lime-600 hover:underline text-sm flex items-center gap-1">
                <svg class="h-4 w-4 inline" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Transactions
            </a>
        </div>

        <!-- GCash Modal with Blur Effect -->
        <div
            x-show="showGcashModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center"
            style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
        >
            <div
                class="absolute inset-0 bg-transparent"
                @click="showGcashModal = false"
            ></div>
            <div class="relative bg-white rounded-lg shadow-lg p-8 max-w-md w-full z-10">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold mb-2">GCash Payment</h2>
                    <p class="mb-4">Scan the QR Code below to pay your landlord.</p>
                    @if($gcashQrUrl)
                        <img src="{{ $gcashQrUrl }}" alt="GCash QR Code" class="mx-auto mb-4 h-40 w-40 object-contain border rounded" />
                    @else
                        <div class="text-red-500 mb-4">GCash QR code not available.</div>
                    @endif
                </div>
                <form method="POST" action="{{ route('transaction.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_method" value="gcash">
                    <input type="hidden" name="bill_id" value="{{ $targetBill->id ?? '' }}">
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Amount Sent (₱) <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="amount_sent"
                            step="0.01"
                            min="{{ $targetBill->amount_due ?? 0 }}"
                            max="{{ $targetBill->amount_due ?? 0 }}"
                            value="{{ $targetBill->amount_due ?? 0 }}"
                            required
                            class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-lime-600"
                            placeholder="Enter amount sent"
                            oninput="this.setCustomValidity(this.value != '{{ $targetBill->amount_due ?? 0 }}' ? 'Amount sent must match the bill amount (₱{{ number_format($targetBill->amount_due ?? 0, 2) }})' : '')"
                            oninvalid="this.setCustomValidity('Amount sent must match the bill amount (₱{{ number_format($targetBill->amount_due ?? 0, 2) }})')"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Reference Number <span class="text-red-500">*</span></label>
                        <input type="text" name="reference_number" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-lime-600" placeholder="Enter reference number">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Your GCash Number <span class="text-red-500">*</span></label>
                        <input type="text" name="gcash_number" required maxlength="11" class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-lime-600" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold w-full">
                            Upload Proof of Payment <span class="text-red-500">*</span>
                            <span class="text-gray-400 text-xs">(screenshot, photo, etc.)</span>
                        </label>
                        <input
                            type="file"
                            name="payment_photo"
                            accept="image/png, image/jpeg"
                            class="w-full mb-4 rounded-lg border border-gray-200 shadow-xs object-cover"
                            required
                            help="PNG or JPG (Max 2MB)"
                            onchange="if(this.files[0]?.size > 2097152){ alert('File size must be 2MB or less.'); this.value=''; }"
                        >
                        <span class="text-gray-400 text-xs block -mt-4">PNG or JPG (Max 2MB)</span>
                    </div>
                    <div class="flex justify-end gap-2">
                        <x-button type="button" variant="clean" @click="showGcashModal = false">Cancel</x-button>
                        <x-button type="submit" variant="primary">Finish Transaction</x-button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End GCash Modal -->

        <!-- Maya Modal with Blur Effect -->
        <div
            x-show="showMayaModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center"
            style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
        >
            <div
                class="absolute inset-0 bg-transparent"
                @click="showMayaModal = false"
            ></div>
            <div class="relative bg-white rounded-lg shadow-lg p-8 max-w-md w-full z-10">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold mb-2">Maya Payment</h2>
                    <p class="mb-4">Scan the QR Code below to pay your landlord.</p>
                    @if($mayaQrUrl)
                        <img src="{{ $mayaQrUrl }}" alt="Maya QR Code" class="mx-auto mb-4 h-40 w-40 object-contain border rounded" />
                    @else
                        <div class="text-red-500 mb-4">Maya QR code not available.</div>
                    @endif
                </div>
                <form method="POST" action="{{ route('transaction.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_method" value="maya">
                    <input type="hidden" name="bill_id" value="{{ $targetBill->id ?? '' }}">
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Amount Sent (₱) <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="amount_sent"
                            step="0.01"
                            min="{{ $targetBill->amount_due ?? 0 }}"
                            max="{{ $targetBill->amount_due ?? 0 }}"
                            value="{{ $targetBill->amount_due ?? 0 }}"
                            required
                            class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-lime-600"
                            placeholder="Enter amount sent"
                            oninput="this.setCustomValidity(this.value != '{{ $targetBill->amount_due ?? 0 }}' ? 'Amount sent must match the bill amount (₱{{ number_format($targetBill->amount_due ?? 0, 2) }})' : '')"
                            oninvalid="this.setCustomValidity('Amount sent must match the bill amount (₱{{ number_format($targetBill->amount_due ?? 0, 2) }})')"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Reference Number <span class="text-red-500">*</span></label>
                        <input type="text" name="reference_number" required class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-lime-600" placeholder="Enter reference number">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Your Maya Number <span class="text-red-500">*</span></label>
                        <input type="text" name="maya_number" required maxlength="11" class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-lime-600" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold w-full">
                            Upload Proof of Payment <span class="text-red-500">*</span>
                            <span class="text-gray-400 text-xs">(screenshot, photo, etc.)</span>
                        </label>
                        <input
                            type="file"
                            name="payment_photo"
                            accept="image/png, image/jpeg"
                            class="w-full mb-4 rounded-lg border border-gray-200 shadow-xs object-cover"
                            required
                            help="PNG or JPG (Max 2MB)"
                            onchange="if(this.files[0]?.size > 2097152){ alert('File size must be 2MB or less.'); this.value=''; }"
                        >
                        <span class="text-gray-400 text-xs block -mt-4">PNG or JPG (Max 2MB)</span>
                    </div>
                    <div class="flex justify-end gap-2">
                        <x-button type="button" variant="clean" @click="showMayaModal = false">Cancel</x-button>
                        <x-button type="submit" variant="primary">Finish Transaction</x-button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Maya Modal -->

        <!-- Cash Modal with Blur Effect -->
        <div
            x-show="showCashModal"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center"
            style="backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);"
        >
            <div
                class="absolute inset-0 bg-transparent"
                @click="showCashModal = false"
            ></div>
            <div class="relative bg-white rounded-lg shadow-lg p-8 max-w-md w-full z-10">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold mb-2">Cash Payment</h2>
                    <p class="mb-4">Upload a photo of your cash payment receipt or proof.</p>
                </div>
                <form method="POST" action="{{ route('transaction.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="payment_method" value="cash">
                    <input type="hidden" name="bill_id" value="{{ $targetBill->id ?? '' }}">
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">Amount Paid (₱) <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            name="amount_sent"
                            step="0.01"
                            min="{{ $targetBill->amount_due ?? 0 }}"
                            max="{{ $targetBill->amount_due ?? 0 }}"
                            value="{{ $targetBill->amount_due ?? 0 }}"
                            required
                            class="w-full border px-3 py-2 rounded focus:outline-none focus:ring focus:border-lime-600"
                            placeholder="Enter amount paid"
                            oninput="this.setCustomValidity(this.value != '{{ $targetBill->amount_due ?? 0 }}' ? 'Amount paid must match the bill amount (₱{{ number_format($targetBill->amount_due ?? 0, 2) }})' : '')"
                            oninvalid="this.setCustomValidity('Amount paid must match the bill amount (₱{{ number_format($targetBill->amount_due ?? 0, 2) }})')"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold w-full">
                            Upload Proof of Payment
                            <span class="text-gray-400 text-xs">(screenshot, photo, etc.)</span>
                        </label>
                        <input
                            type="file"
                            name="payment_photo"
                            accept="image/png, image/jpeg"
                            class="w-full mb-4 rounded-lg border border-gray-200 shadow-xs object-cover"
                            help="PNG or JPG (Max 2MB)"
                            onchange="if(this.files[0]?.size > 2097152){ alert('File size must be 2MB or less.'); this.value=''; }"
                        >
                        <span class="text-gray-400 text-xs block -mt-4">PNG or JPG (Max 2MB)</span>
                        <span class="text-xs text-gray-500 block mt-1">Proof of payment is optional for cash payments.</span>
                    </div>
                    <div class="flex justify-end gap-2">
                        <x-button type="button" variant="clean" @click="showCashModal = false">Cancel</x-button>
                        <x-button type="submit" variant="primary">Finish Transaction</x-button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Cash Modal -->
    </div>

    {{-- Flowbite Heroicon SVG symbols --}}
    <svg style="display: none;">
        <symbol id="icon-banknotes" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.75V7.5A2.25 2.25 0 0018.75 5.25H5.25A2.25 2.25 0 003 7.5v10.5A2.25 2.25 0 005.25 20.25h13.5A2.25 2.25 0 0021 18V15.75"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 15a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z"/>
        </symbol>
        <symbol id="icon-credit-card" viewBox="0 0 24 24">
            <rect width="20" height="14" x="2" y="5" rx="2" />
            <path d="M2 10h20" />
        </symbol>
        <symbol id="icon-cash" viewBox="0 0 24 24">
            <rect width="20" height="12" x="2" y="6" rx="2" />
            <circle cx="12" cy="12" r="3" />
        </symbol>
        <symbol id="icon-bank" viewBox="0 0 24 24">
            <path d="M3 10V6a1 1 0 011-1h16a1 1 0 011 1v4"/>
            <path d="M4 22h16M4 18h16M4 14h16"/>
            <path d="M10 10V4h4v6"/>
        </symbol>
        <symbol id="icon-currency-dollar" viewBox="0 0 24 24">
            <path d="M12 8V4m0 0a4 4 0 110 8h-1a4 4 0 100 8v-4m0 0a4 4 0 110-8h1a4 4 0 100 8"/>
        </symbol>
    </svg>
</x-app-layout>
