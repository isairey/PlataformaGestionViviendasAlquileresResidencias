<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Delete Account
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting
            your account, please download any data or information that you wish to retain.
        </p>
    </header>

    <x-button variant="danger" x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Delete Account</x-button>


    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                Are you sure you want to delete your account?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter
                your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mt-6">
                <x-input.label for="password" class="sr-only">Password</x-input.label>
                <x-input.text id="password" type="password" name="password" class="w-9/12" placeholder="Password" />
                <x-input.error for="password" bag="userDeletion" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-button variant="clear" type="button" x-on:click="$dispatch('close')">
                    Cancel
                </x-button>

                <x-button variant="danger" class="ms-3">
                    Delete Account
                </x-button>
            </div>
        </form>
    </x-modal>
</section>
