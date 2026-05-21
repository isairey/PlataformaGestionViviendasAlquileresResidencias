<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HomeRoom</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/images/logo.svg') }}">
    @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js'])
</head>
<body class="bg-[#E0FFE0] text-[#1b3a2b] font-sans">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 w-full z-50 navbar-gradient-bg" style="height: 80px;">
        <nav id="main-navbar"
            class="relative max-w-5xl mx-auto flex items-center h-full py-5 px-4 justify-center">
            <div class="flex items-center gap-8 mx-auto">
                <a href="{{ url()->current() }}" class="flex items-center">
                    <img src="{{ asset('assets/images/logo.svg') }}" alt="HomeRoom Logo"
                        class="h-14 w-auto object-contain whitespace-nowrap" style="max-width:190px; min-width:120px;" />
                </a>
                <x-a href="#home" variant="text" :uppercase="false" class="navbar-link text-[1.12rem] px-5 py-2" data-nav="home">Home</x-a>
                @if (Route::has('login'))
                    @auth
                        <x-a href="{{ url('/dashboard') }}" variant="text" :uppercase="false" class="navbar-link text-[1.12rem] px-5 py-2" data-nav="dashboard">Dashboard</x-a>
                    @else
                        <x-a href="{{ route('login') }}" variant="text" :uppercase="false" class="navbar-link text-[1.12rem] px-5 py-2" data-nav="login">Login</x-a>
                        @if (Route::has('register'))
                            <x-a href="{{ route('register') }}" variant="text" :uppercase="false" class="navbar-link text-[1.12rem] px-5 py-2" data-nav="register">Register</x-a>
                        @endif
                    @endauth
                @endif
            </div>
            <div id="navbar-underline" class="nav-underline absolute bottom-0 h-1 bg-[#55C58F] rounded transition-all" style="left:0;width:0;"></div>
        </nav>
    </header>
    <div class="pt-[80px]"></div>

    <section id="home" class="relative w-full" style="min-height:calc(100vh - 80px);">
        <img
            src="{{ Vite::asset('resources/assets/images/welcome-page-bg.jpg') }}"
            alt="Home Background"
            class="home-bg-image"
            draggable="false"
        />
        <div class="home-dim-overlay"></div>

        <div class="hero-section-flex" style="min-height:calc(100vh - 80px);">
            <div class="hero-text-col">
                <div class="hero-title">
                    HomeRoom: Simplifying Rentals, Empowering Connections
                </div>
                <div class="hero-description">
                    Effortlessly manage your rental journey with HomeRoom connecting tenants and landlords in one smart platform.
                </div>
                @if(Route::has('register') && !Auth::check())
                <div class="mt-8">
                    <a href="{{ route('register') }}"
                    class="getting-started-btn relative inline-flex items-center gap-2 px-7 py-3 border-2 border-lime-400 bg-white/30 backdrop-blur-md text-white text-lg font-semibold transition-all duration-300 overflow-hidden group shadow-xl"
                    id="gettingStartedBtn"
                    style="border-radius: 0;">
                        <span class="z-10 relative">Getting Started</span>
                        <svg class="w-5 h-5 ml-2 z-10 relative" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5"/>
                        </svg>
                        <span class="btn-hover-bg absolute inset-0 left-0 top-0 w-0 h-full bg-lime-400/90 transition-all duration-300 ease-out z-0 group-hover:w-full"></span>
                    </a>
                </div>
                @endif
            </div>
            <!-- Removed the hero-action-col containing the image slider -->
        </div>
    </section>
</body>
</html>
