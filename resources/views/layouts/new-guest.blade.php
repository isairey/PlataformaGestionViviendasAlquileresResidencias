@props(['title' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ page_title($title ?? '') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ Vite::asset('resources/assets/images/logo.svg') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-outfit text-gray-900 antialiased">
    <div class="min-h-screen bg-gray-100 flex items-center justify-center py-6">
        <div
            class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg mb-8 border-l-4 border-lime-800">
            {{ $slot }}
        </div>
    </div>

    <x-toast-container />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    @stack('scripts')
</body>

</html>
