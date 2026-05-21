{{--
    maxDate: latest selectable date (MM/DD/YYYY) or null
    minDate: earliest selectable date (MM/DD/YYYY) or null
    withButtons: show "Today" and "Clear" buttons
    autoHide: hide picker after selection
--}}

@props([
    'maxDate' => null,
    'minDate' => null,
    'withButtons' => true,
    'autoHide' => true,
])

@php
    $applyButtons = $withButtons ? 'datepicker-buttons datepicker-autoselect-today' : '';
    $applyAutoHide = $autoHide ? 'datepicker-autohide' : '';
    $applyMaxDate = $maxDate ? "datepicker-max-date=\"{$maxDate}\"" : '';
    $applyMinDate = $minDate ? "datepicker-min-date=\"{$minDate}\"" : '';
@endphp

<div class="relative">
    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
        <i class="ph-bold ph-calendar-blank"></i>
    </div>
    <input datepicker datepicker-format="mm/dd/yyyy" {!! $applyMaxDate !!} {!! $applyMinDate !!} {{ $applyButtons }}
        {{ $applyAutoHide }}" type="text"
        {{ $attributes->merge([
            'class' =>
                'bg-gray-50 border border-gray-300 text-gray-900 rounded-md focus:ring-lime-600 focus:border-lime-600 block w-full ps-10 p-2.5',
        ]) }}>
</div>
