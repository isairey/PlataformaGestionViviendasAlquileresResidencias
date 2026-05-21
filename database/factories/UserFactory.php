<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $domain = fake()->randomElement(['gmail.com', 'yahoo.com', 'outlook.com']);

        $seedDir = resource_path('seeds/profile_pictures');
        $files = glob($seedDir.'/*.*');
        $originalFile = fake()->randomElement($files);

        $extension = pathinfo($originalFile, PATHINFO_EXTENSION);
        $filename = Str::uuid().'.'.$extension;

        // Read the file content
        $contents = file_get_contents($originalFile);

        Storage::disk('public')->put('profile_pictures/'.$filename, $contents);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->userName().'@'.$domain,
            'profile_picture' => 'profile_pictures/'.$filename,
            'email_verified_at' => now(),
            'role' => 'tenant',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function landlord(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'landlord',
        ]);
    }

    public function tenant(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'tenant',
            'profile_completed' => true,
        ]);
    }
}
