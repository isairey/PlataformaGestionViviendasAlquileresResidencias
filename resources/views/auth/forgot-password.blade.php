<x-guest-layout title="Forgot Password">
    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset
        link that will allow you to choose a new one.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input.label for="email" :required="true">Email</x-input.label>
            <x-input.text id="email" type="email" name="email" :value="old('email')" autofocus />
            <x-input.error for="email" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-button variant="primary">
                Email Password Reset Link
            </x-button>
        </div>
    </form>
</x-guest-layout>
