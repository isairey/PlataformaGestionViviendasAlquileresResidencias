<x-new-guest-layout title="Login">
    <div class="text-center">
        <div class="flex items-center justify-center">
            <img src="{{ Vite::asset('resources/assets/images/logo.svg') }}" class="aspect-square size-32 -m-8" />
        </div>
        <h2 class="font-medium text-xl">Welcome back!</h2>
        <p class="text-sm text-gray-900">Please enter your details to log in</p>
    </div>

    <hr class="border-gray-200 my-4" />

    <form method="POST" action="{{ route('login') }}">
        {{-- <div class="mb-4">
            <x-input.date maxDate="{{ iso_to_us(now()->toDateString()) }}" :withButtons="true" name="birth_date"
                :value="old('birth_date')" placeholder="Select Date" />
        </div> --}}

        @csrf
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <div>
            <x-input.label for="email" :required="true">Email</x-input.label>
            <x-input.text id="email" type="email" name="email" :value="old('email')" autofocus />
            <x-input.error for="email" />
        </div>

        <div class="mt-4">
            <x-input.label for="password" :required="true">Password</x-input.label>
            <x-input.text id="password" type="password" name="password" />
            <x-input.error for="password" />
        </div>


        <div class="mt-4 flex items-center justify-between">
            <x-input.label for="remember_me">
                <x-input.checkbox id="remember_me" name="remember" />
                <span class="ms-1 text-sm text-gray-600">Remember Me</span>
            </x-input.label>
            @if (Route::has('password.request'))
                <x-a href="{{ route('password.request') }}">Forgot your password?</x-a>
            @endif
        </div>

        <div class="flex items-center mt-4">
            <x-button class="w-full">Log In</x-button>
        </div>

        <p class="text-center mt-4 text-sm text-gray-900">Don't have an account yet? <x-a
                href="{{ route('register') }}">Sign Up</x-a></p>
    </form>
</x-new-guest-layout>
