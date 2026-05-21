<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $transaction = Transaction::create([
        //     'tenant_id' => 1,
        //     'bill_id' => 1,
        //     'amount' => 5000,
        //     'payment_method' => 'gcash',
        //     'payment_date' => now(),
        // ]);

        // $bill = Bill::find(1);

        $tenant = Tenant::find(2);
        $this->command->info('Tenant Outstanding Balance: '.$tenant->outstandingBalance());

        // $transaction = Transaction::find(1);

        // $transaction->update([
        //     'status' => 'confirmed',
        //     'confirmed_at' => now(),
        // ]);

        // $transaction->bill->update([
        //     'status' => 'paid',
        // ]);
    }
}
