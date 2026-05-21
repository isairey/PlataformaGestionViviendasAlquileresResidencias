<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Storage;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Landlord>
 */
class LandlordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // GCash QR
        $gcashSeedDir = resource_path('seeds/gcash_qr');
        $gcashFiles = glob($gcashSeedDir.'/*.*');
        $gcashOriginalFile = fake()->randomElement($gcashFiles);
        $gcashExtension = pathinfo($gcashOriginalFile, PATHINFO_EXTENSION);
        $gcashFilename = Str::uuid().'.'.$gcashExtension;
        $gcashContents = file_get_contents($gcashOriginalFile);
        Storage::disk('public')->put('qr_codes/'.$gcashFilename, $gcashContents);

        // Maya QR
        $mayaSeedDir = resource_path('seeds/maya_qr');
        $mayaFiles = glob($mayaSeedDir.'/*.*');
        $mayaOriginalFile = fake()->randomElement($mayaFiles);
        $mayaExtension = pathinfo($mayaOriginalFile, PATHINFO_EXTENSION);
        $mayaFilename = Str::uuid().'.'.$mayaExtension;
        $mayaContents = file_get_contents($mayaOriginalFile);
        Storage::disk('public')->put('qr_codes/'.$mayaFilename, $mayaContents);

        return [
            'user_id' => User::factory()->landlord(),
            'gcash_qr' => "qr_codes/$gcashFilename",
            'maya_qr' => "qr_codes/$mayaFilename",
        ];
    }
}
