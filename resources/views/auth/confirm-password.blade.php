<x-guest-layout title="Confirm Password">
    <div class="mb-4 text-sm text-gray-600">
        This is a secure area of the application. Please confirm your password before continuing.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div class="mt-4">
            <x-input.label for="password" :required="true">Password</x-input.label>
            <x-input.text id="password" type="password" name="password" />
            <x-input.error for="password" />
        </div>

        <div class="flex justify-end mt-4">
            <x-button>Confirm</x-button>
        </div>
    </form>
</x-guest-layout>
