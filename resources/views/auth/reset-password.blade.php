<x-guest-layout title="Reset Password">
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email Address -->
        <div>
            <x-input.label for="email" :required="true">Email</x-input.label>
            <x-input.text id="email" type="email" name="email" :value="old('email', $email)" autofocus />
            <x-input.error for="email" />
        </div>

        <div class="mt-4">
            <x-input.label for="password" :required="true">New Password</x-input.label>
            <x-input.text id="password" type="password" name="password" />
            <x-input.error for="password" />
        </div>

        <div class="mt-4">
            <x-input.label for="password_confirmation" :required="true">Confirm Password</x-input.label>
            <x-input.text id="password_confirmation" type="password" name="password_confirmation" />
            <x-input.error for="password_confirmation" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-button>Reset Password</x-button>
        </div>
    </form>
</x-guest-layout>
