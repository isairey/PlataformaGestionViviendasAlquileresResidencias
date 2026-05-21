<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view for a specific step.
     */
    public function create(Request $request, int $step = 1): View|RedirectResponse
    {
        // Ensure step is valid
        if ($step < 1 || $step > 3) {
            return redirect()->route('register', ['step' => 1]);
        }

        // For steps 2 and 3, ensure previous steps are completed
        if ($step > 1 && ! $this->isPreviousStepCompleted($step - 1)) {
            return redirect()->route('register', ['step' => $step - 1]);
        }

        return view('auth.register', [
            'step' => $step,
            'data' => session('registration_data', []),
        ]);
    }

    /**
     * Handle form submission for each step.
     */
    public function store(Request $request, int $step = 1): RedirectResponse
    {
        // Validate current step
        $validatedData = $this->validateStep($request, $step);

        // Store validated data in session
        $registrationData = session('registration_data', []);
        $registrationData = array_merge($registrationData, $validatedData);
        session(['registration_data' => $registrationData]);

        // Mark step as completed
        $completedSteps = session('completed_steps', []);
        $completedSteps[] = $step;
        session(['completed_steps' => array_unique($completedSteps)]);

        // If this is the final step, create the user
        if ($step === 3) {
            return $this->finalizeRegistration($registrationData);
        }

        // Redirect to next step
        return redirect()->route('register', ['step' => $step + 1]);
    }

    /**
     * Handle going back to previous step.
     */
    public function back(int $step): RedirectResponse
    {
        if ($step > 1) {
            return redirect()->route('register', ['step' => $step - 1]);
        }

        return redirect()->route('register', ['step' => 1]);
    }

    /**
     * Validate data for specific step.
     */
    private function validateStep(Request $request, int $step): array
    {
        switch ($step) {
            case 1:
                $registrationData = session('registration_data', []);
                $hasExistingImage = isset($registrationData['profile_picture_path']);

                $rules = [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'lowercase', 'email:strict,dns', 'max:255', 'unique:'.User::class],
                ];

                // Only require profile_picture if we don't already have one
                if (! $hasExistingImage) {
                    $rules['profile_picture'] = ['required', 'image', 'mimes:jpeg,png', 'max:2048'];
                } else {
                    $rules['profile_picture'] = ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'];
                }

                $validated = $request->validate($rules, [
                    'profile_picture.max' => 'The profile picture must not be larger than 2MB.',
                ]);

                // Handle file upload if provided
                if ($request->hasFile('profile_picture')) {
                    // Clean up old temp file if exists
                    if ($hasExistingImage) {
                        Storage::disk('public')->delete($registrationData['profile_picture_path']);
                    }

                    $tempPath = $request->file('profile_picture')->store('temp/profile_pictures', 'public');
                    $validated['profile_picture_path'] = $tempPath;
                    unset($validated['profile_picture']); // Remove the file object
                } elseif ($hasExistingImage) {
                    // Keep the existing image path
                    $validated['profile_picture_path'] = $registrationData['profile_picture_path'];
                }

                return $validated;

            case 2:
                return $request->validate([
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ]);

            case 3:
                return $request->validate([
                    'code' => ['required', 'regex:/^[A-Z0-9]{3}-[A-Z0-9]{3}$/', 'exists:rooms,code'],
                ], [], [
                    'code' => 'room code',
                ]);

            default:
                return [];
        }
    }

    /**
     * Check if previous step is completed.
     */
    private function isPreviousStepCompleted(int $step): bool
    {
        $completedSteps = session('completed_steps', []);

        return in_array($step, $completedSteps);
    }

    /**
     * Finalize registration and create user.
     */
    private function finalizeRegistration(array $data): RedirectResponse
    {
        // Handle profile picture - move from temp to final location
        $profilePath = null;
        if (isset($data['profile_picture_path'])) {
            $tempPath = $data['profile_picture_path']; // e.g., temp/profile_pictures/xyz.jpg
            $filename = basename($tempPath);
            $finalPath = 'profile_pictures/'.$filename;

            // Move file from temp to final location
            Storage::disk('public')->move($tempPath, $finalPath);
            $profilePath = $finalPath;
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'profile_picture' => $profilePath,
            'profile_completed' => true,
        ]);

        // Create tenant relationship
        $room = Room::where('code', $data['code'])->firstOrFail();
        Tenant::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'move_in_date' => now(),
        ]);

        // Clear registration session data
        session()->forget(['registration_data', 'completed_steps']);

        // Clean up any remaining temp files
        $this->cleanupTempFiles();

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Clean up temporary files.
     */
    private function cleanupTempFiles(): void
    {
        $registrationData = session('registration_data', []);
        if (isset($registrationData['profile_picture_path'])) {
            Storage::disk('public')->delete($registrationData['profile_picture_path']);
        }
    }
}
