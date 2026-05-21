<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle profile picture update if a new one is uploaded
        if ($request->hasFile('profile_picture')) {
            $profilePath = $request->file('profile_picture')->store('profile_pictures', 'public');

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $data['profile_picture'] = $profilePath;
        }

        // If email has changed, reset verification
        if ($user->email !== $data['email']) {
            $user->email_verified_at = null;
        }

        $user->fill($data)->save();

        // Send new verification email if email changed
        if ($user->wasChanged('email')) {
            $user->sendEmailVerificationNotification();
        }

        session()->flash('toast.success', [
            'title' => 'Profile Updated',
            'content' => 'Your profile information has been successfully updated.',
        ]);

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
