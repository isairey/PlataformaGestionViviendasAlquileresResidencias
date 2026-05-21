<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Profile Information
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Update your account's profile information and email address.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input.label for="name">Name</x-input.label>
            <x-input.text id="name" type="text" name="name" :value="old('name', $user->name)" />
            <x-input.error for="name" />
        </div>

        <div>
            <x-input.label for="email">Email</x-input.label>
            <x-input.text id="email" type="email" name="email" :value="old('email', $user->email)" />
            <x-input.error for="email" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Your email address is unverified.

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-lime-700">
                            Click here to re-send the verification email.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            A new verification link has been sent to your email address.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input.label for="profile_picture">Profile Picture</x-input.label>
            @if ($user->profile_picture)
                <div class="max-w-xs mb-4 mx-auto">
                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" alt="Profile Picture"
                        class="border border-gray-200 shadow-xs aspect-square w-full max-w-sm object-cover rounded-full" />
                </div>
                <x-input.classic-file id="profile_picture" name="profile_picture" accept="image/png,image/jpeg"
                    help="PNG or JPG (Max 2MB)" />
            @else
                <x-input.file id="profile_picture" name="profile_picture" accept="image/png,image/jpeg"
                    :circle="true" help="PNG or JPG (Max 2MB)" />
            @endif
            <x-input.error for="profile_picture" />
        </div>

        <div class="flex items-center gap-4">
            <x-button variant="primary">Save</x-button>
        </div>
    </form>
</section>
