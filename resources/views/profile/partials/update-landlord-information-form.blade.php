<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Landlord Information
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Update your account's profile information and email address.
        </p>
    </header>


    <form method="post" action="{{ route('landlord.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="mt-4">
            <x-input.label for="gcash_qr">GCash QR</x-input.label>
            @if ($user->landlord->gcash_qr)
                <div
                    class="w-full mb-4 rounded-lg border border-gray-200 shadow-xs object-cover flex justify-center items-center">
                    <img src={{ $user->landlord->gcash_qr }} class="rounded-lg" />
                </div>
                <x-input.classic-file id="gcash_qr" name="gcash_qr" accept="image/png,image/jpeg"
                    help="PNG or JPG (Max 2MB)" />
            @else
                <x-input.file id="gcash_qr" name="gcash_qr" accept="image/jpeg,image/png"
                    help="PNG or JPG (Max 2MB)"></x-input.file>
            @endif
            <x-input.error for="gcash_qr" />
        </div>

        <div class="mt-4">
            <x-input.label for="maya_qr">Maya QR</x-input.label>
            @if ($user->landlord->maya_qr)
                <div
                    class="w-full mb-4 rounded-lg border border-gray-200 shadow-xs object-cover flex justify-center items-center">
                    <img src={{ $user->landlord->maya_qr }} class="rounded-lg" />
                </div>
                <x-input.classic-file id="maya_qr" name="maya_qr" accept="image/png,image/jpeg"
                    help="PNG or JPG (Max 2MB)" />
            @else
                <x-input.file id="maya_qr" name="maya_qr" accept="image/jpeg,image/png"
                    help="PNG or JPG (Max 2MB)"></x-input.file>
            @endif

            <x-input.error for="maya_qr" />
        </div>

        <div class="flex items-center gap-4">
            <x-button variant="primary">Save</x-button>
        </div>
    </form>
</section>
