<?php

namespace Database\Seeders;

use App\Enums\PropertyType;
use App\Models\Bill;
use App\Models\Landlord;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // For initial run for production
        $user_password = 'password';
        $user = User::firstOrCreate(
            ['email' => env('DEFAULT_LANDLORD_EMAIL', 'landlord@gmail.com')],
            [
                'name' => 'Default Landlord',
                'email_verified_at' => now(),
                'password' => Hash::make($user_password),
                'role' => 'landlord',
                'profile_completed' => false,
            ]
        );

        $landlord = Landlord::firstOrCreate([
            'user_id' => $user->id,
        ]);

        // For quick testing, to be removed
        if (app()->isLocal()) {
            $property = Property::firstOrCreate([
                'landlord_id' => $landlord->id,
                'type' => PropertyType::DORM->value,
                'name' => 'Sample Property',
                'description' => 'A default seeded property',
                'address' => '123 Example St. Sample City',
            ]);

            $roomCode = generate_code();

            $room = Room::firstOrCreate([
                'code' => $roomCode,
            ], [
                'property_id' => $property->id,
                'name' => 'Room 001',
                'rent_amount' => 5000.00,
                'max_occupancy' => 3,
            ]);

            $user2_password = 'password';
            $user2 = User::firstOrCreate(['email' => 'tenant@gmail.com'], [
                'name' => 'Default Tenant',
                'email_verified_at' => now(),
                'password' => Hash::make($user2_password),
                'role' => 'tenant',
                'profile_completed' => true,
            ]);

            $tenant = Tenant::firstOrCreate([
                'user_id' => $user2->id,
                'room_id' => $room->id,
                'move_in_date' => now(),
            ]);

            // $bill = Bill::create([
            //     'tenant_id' => $tenant->id,
            //     'amount_due' => $tenant->room->rent_amount,
            //     'due_date' => now()->addMonth(),
            //     'status' => 'unpaid',
            // ]);

            // // tenant pays
            // $transaction = Transaction::create([
            //     'tenant_id' => $tenant->id,
            //     'bill_id' => $bill->id,
            //     'amount' => $bill->amount_due,
            //     'payment_method' => 'gcash',
            //     'payment_date' => now(),
            // ]);

            // // landlord confirms

            // $transaction->update([
            //     'status' => 'confirmed',
            //     'confirmed_at' => now(),
            // ]);

            // $transaction->bill->update([
            //     'status' => 'paid',
            // ]);

            $this->command->info("Seeded Room Code: {$room->code}");
        }

        // For Mocking the website
        if (app()->isLocal()) {
            $this->callWith(DefaultTestSeeder::class, ['landlord' => $landlord]);
        }
    }
}
