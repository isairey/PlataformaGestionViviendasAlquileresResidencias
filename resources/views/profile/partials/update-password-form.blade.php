<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Update Password
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input.label for="update_password_current_password">Current Password</x-input.label>
            <x-input.text id="update_password_current_password" type="password" name="current_password" />
            <x-input.error for="current_password" bag="updatePassword" />
        </div>


        <div>
            <x-input.label for="update_password_password">New Password</x-input.label>
            <x-input.text id="update_password_password" type="password" name="password" />
            <x-input.error for="password" bag="updatePassword" />
        </div>

        <div>
            <x-input.label for="update_password_password_confirmation">Confirm Password</x-input.label>
            <x-input.text id="update_password_password_confirmation" type="password" name="password_confirmation" />
            <x-input.error for="password_confirmation" bag="updatePassword" />
        </div>

        <div class="flex items-center gap-4">
            <x-button variant="primary">Save</x-button>

        </div>
    </form>
</section>
