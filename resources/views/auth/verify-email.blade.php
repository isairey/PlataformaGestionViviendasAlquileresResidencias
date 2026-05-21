<x-guest-layout title="Verify Email">
    <div class="mb-4 text-sm text-center text-gray-600">
        To keep things secure, we've sent a confirmation link to your email. Just click it to verify your address!
        Didn't get it? No worries — we'll gladly send a new one.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-button variant="primary">Resend Verification Email</x-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-lime
                -600">
                Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
