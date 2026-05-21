@props(['for', 'all' => false, 'bag' => 'default'])

@php
    $errorBag = $errors->getBag($bag);
    $messages = $errorBag->get($for);
@endphp

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1 text-sm text-red-600 space-y-1']) }}>
        @foreach ($all ? (array) $messages : [is_array($messages) ? $messages[0] : $messages] as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
