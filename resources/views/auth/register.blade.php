<x-new-guest-layout title="Register - Step {{ $step }} of 3">
    <div class="text-center">
        <div class="flex items-center justify-center">
            <img src="{{ Vite::asset('resources/assets/images/logo.svg') }}" class="aspect-square size-32 -m-8" />
        </div>
        <h2 class="font-medium text-xl">Let's Get Started!</h2>
        <p class="text-sm text-gray-900">Create an account to join your property and get connected with your landlord</p>
    </div>

    <hr class="border-gray-200 my-4" />

    <!-- Progress Indicator -->
    <x-progress-tracker :step="$step" :labels="['Personal Information', 'Password', 'Room Code']" />


    <form method="POST" action="{{ route('register.store', ['step' => $step]) }}" enctype="multipart/form-data">
        @csrf

        @if ($step == 1)
            <!-- Step 1: Personal Information -->
            <div>
                <x-input.label for="name" :required="true">Name</x-input.label>
                <x-input.text id="name" type="text" name="name" :value="old('name', $data['name'] ?? '')" autofocus />
                <x-input.error for="name" />
            </div>

            <div class="mt-4">
                <x-input.label for="email" :required="true">Email</x-input.label>
                <x-input.text id="email" type="email" name="email" :value="old('email', $data['email'] ?? '')" />
                <x-input.error for="email" />
            </div>

            <div class="mt-4">
                <x-input.label for="profile_picture" :required="true">Profile Picture</x-input.label>
                @if (isset($data['profile_picture_path']) && $data['profile_picture_path'])
                    <div class="max-w-xs mb-4 mx-auto">
                        <img id="profile_frame" src="{{ asset('storage/' . $data['profile_picture_path']) }}"
                            alt="Current Profile Picture"
                            class="border border-gray-200 shadow-xs aspect-square w-full max-w-sm object-cover rounded-full" />
                    </div>
                    <x-input.classic-file id="profile_picture" name="profile_picture" accept="image/png,image/jpeg"
                        help="PNG or JPG (Max 2MB)" imgId="profile_frame" />
                @else
                    <x-input.file id="profile_picture" name="profile_picture" accept="image/png,image/jpeg"
                        :circle="true" help="PNG or JPG (Max 2MB)" />
                    <x-input.error for="profile_picture" />
                @endif
            </div>
        @elseif($step == 2)
            <!-- Step 2: Password -->
            <div>
                <x-input.label for="password" :required="true">Password</x-input.label>
                <x-input.text id="password" type="password" name="password" autofocus />
                <x-input.error for="password" />
            </div>

            <div class="mt-4">
                <x-input.label for="password_confirmation" :required="true">Confirm Password</x-input.label>
                <x-input.text id="password_confirmation" type="password" name="password_confirmation" />
                <x-input.error for="password_confirmation" />
            </div>
        @elseif($step == 3)
            <!-- Step 3: Room Code -->
            <div>
                <x-input.label for="code" :required="true">Room Code</x-input.label>
                <x-input.text id="code" type="text" name="code" :value="old('code', $data['code'] ?? '')" placeholder="ABC-123"
                    autofocus />
                <x-input.error for="code" />
                <p class="mt-2 text-sm text-gray-600">
                    Enter the room code provided by your landlord or property manager.
                </p>
            </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between mt-6">
            <div class="flex items-center space-x-4">
                @if ($step > 1)
                    <x-button type="button" variant="dark"
                        onclick="window.location.href='{{ route('register.back', ['step' => $step]) }}'">
                        <i class="ph-bold ph-arrow-left"></i>Back
                    </x-button>
                @else
                    <x-a href="{{ route('login') }}">Already Registered?</x-a>
                @endif
            </div>

            <x-button variant="primary">
                @if ($step == 3)
                    Submit
                @else
                    Next<i class="ph-bold ph-arrow-right"></i>
                @endif
            </x-button>
        </div>
    </form>
</x-new-guest-layout>
