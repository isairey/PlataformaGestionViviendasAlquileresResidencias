<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Storage;

class LandlordSetupController extends Controller
{
    public function edit(Request $request, int $step = 1)
    {
        if ($step < 1 || $step > 3) {
            return redirect()->route('landlord.setup.edit', ['step' => 1]);
        }

        if ($step > 1 && ! $this->isPreviousStepCompleted($step - 1)) {
            return redirect()->route('landlord.setup.edit', ['step' => $step - 1]);
        }

        return view('landlord.setup', [
            'step' => $step,
            'data' => session('landlord_setup_data', []),
        ]);
    }

    public function update(Request $request, int $step = 1)
    {
        $validated = $this->validateStep($request, $step);
        $data = array_merge(session('landlord_setup_data', []), $validated);
        session(['landlord_setup_data' => $data]);

        $steps = session('landlord_completed_steps', []);
        $steps[] = $step;
        session(['landlord_completed_steps' => array_unique($steps)]);

        if ($step === 3) {
            return $this->finalizeSetup($data);
        }

        return redirect()->route('landlord.setup.edit', ['step' => $step + 1]);
    }

    public function back(int $step)
    {
        if ($step > 1) {
            return redirect()->route('landlord.setup.edit', ['step' => $step - 1]);
        }

        return redirect()->route('landlord.setup.edit', ['step' => 1]);
    }

    private function validateStep(Request $request, int $step): array
    {
        $data = session('landlord_setup_data', []);
        $hasImage = isset($data['profile_picture_path']);
        $hasGcash = isset($data['gcash_qr_path']);
        $hasMaya = isset($data['maya_qr_path']);

        switch ($step) {
            case 1:
                $rules = [
                    'name' => ['required', 'string', 'max:255'],
                    'profile_picture' => [$hasImage ? 'nullable' : 'required', 'image', 'mimes:jpeg,png', 'max:2048'],
                ];
                $validated = $request->validate($rules);

                if ($request->hasFile('profile_picture')) {
                    if ($hasImage) {
                        Storage::disk('public')->delete($data['profile_picture_path']);
                    }
                    $path = $request->file('profile_picture')->store('temp/profile_pictures', 'public');
                    $validated['profile_picture_path'] = $path;
                    unset($validated['profile_picture']);
                } elseif ($hasImage) {
                    $validated['profile_picture_path'] = $data['profile_picture_path'];
                }

                return $validated;

            case 2:
                return $request->validate([
                    'password' => ['required', 'confirmed', Password::defaults()],
                ]);

            case 3:
                $rules = [
                    'gcash_qr' => [$hasGcash ? 'nullable' : 'image', 'mimes:jpeg,png', 'max:2048'],
                    'maya_qr' => [$hasMaya ? 'nullable' : 'image', 'mimes:jpeg,png', 'max:2048'],
                ];
                $validated = $request->validate($rules);

                if ($request->hasFile('gcash_qr')) {
                    if ($hasGcash) {
                        Storage::disk('public')->delete($data['gcash_qr_path']);
                    }
                    $path = $request->file('gcash_qr')->store('temp/qr_codes', 'public');
                    $validated['gcash_qr_path'] = $path;
                    unset($validated['gcash_qr']);
                } elseif ($hasGcash) {
                    $validated['gcash_qr_path'] = $data['gcash_qr_path'];
                }

                if ($request->hasFile('maya_qr')) {
                    if ($hasMaya) {
                        Storage::disk('public')->delete($data['maya_qr_path']);
                    }
                    $path = $request->file('maya_qr')->store('temp/qr_codes', 'public');
                    $validated['maya_qr_path'] = $path;
                    unset($validated['maya_qr']);
                } elseif ($hasMaya) {
                    $validated['maya_qr_path'] = $data['maya_qr_path'];
                }

                return $validated;

            default:
                return [];
        }
    }

    private function isPreviousStepCompleted(int $step): bool
    {
        return in_array($step, session('landlord_completed_steps', []));
    }

    private function finalizeSetup(array $data)
    {
        $user = auth()->user();

        // Move profile picture
        if (isset($data['profile_picture_path'])) {
            $filename = basename($data['profile_picture_path']);
            $finalPath = 'profile_pictures/'.$filename;
            Storage::disk('public')->move($data['profile_picture_path'], $finalPath);
            $user->profile_picture = $finalPath;
        }

        $user->name = $data['name'];
        $user->password = Hash::make($data['password']);
        $user->profile_completed = true;
        $user->save();

        // Move landlord QR codes
        $landlord = $user->landlord;

        if (isset($data['gcash_qr_path'])) {
            $filename = basename($data['gcash_qr_path']);
            $finalPath = 'qr_codes/'.$filename;
            Storage::disk('public')->move($data['gcash_qr_path'], $finalPath);
            $landlord->gcash_qr = $finalPath;
        }

        if (isset($data['maya_qr_path'])) {
            $filename = basename($data['maya_qr_path']);
            $finalPath = 'qr_codes/'.$filename;
            Storage::disk('public')->move($data['maya_qr_path'], $finalPath);
            $landlord->maya_qr = $finalPath;
        }

        $landlord->save();

        // Cleanup
        session()->forget(['landlord_setup_data', 'landlord_completed_steps']);

        session()->flash('toast.success', [
            'title' => 'Setup Complete',
            'content' => 'Your landlord profile has been successfully set up.',
        ]);

        return redirect()->route('dashboard')->with('success', 'Setup complete!');
    }
}
