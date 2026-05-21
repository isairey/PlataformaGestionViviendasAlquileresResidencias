<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;

class LandlordProfileController extends Controller
{
    public function update(Request $request)
    {
        $landlord = auth()->user()->landlord;

        if (! $landlord) {
            abort(403, 'Only landlords can access this endpoint.');
        }

        $data = $request->validate([
            'gcash_qr' => ['image', 'mimes:jpeg,png', 'max:2048'],
            'maya_qr' => ['image', 'mimes:jpeg,png', 'max:2048'],
        ], [
            'gcash_qr.max' => 'The GCash QR image must not be larger than 2MB.',
            'maya_qr.max' => 'The Maya QR image must not be larger than 2MB.',
        ]);

        if ($request->hasFile('gcash_qr')) {
            $data['gcash_qr'] = $request->file('gcash_qr')->store('qr_codes', 'public');

            if ($landlord->gcash_qr && Storage::disk('public')->exists($landlord->gcash_qr)) {
                Storage::disk('public')->delete($landlord->gcash_qr);
            }
        }

        if ($request->hasFile('maya_qr')) {
            $data['maya_qr'] = $request->file('maya_qr')->store('qr_codes', 'public');

            if ($landlord->maya_qr && Storage::disk('public')->exists($landlord->maya_qr)) {
                Storage::disk('public')->delete($landlord->maya_qr);
            }
        }

        $landlord->fill($data)->save();

        session()->flash('toast.success', [
            'title' => 'QR Codes Updated',
            'content' => 'Your payment QR codes have been successfully saved.',
        ]);

        return redirect()->route('profile.edit');
    }
}
