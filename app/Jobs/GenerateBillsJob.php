<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Tenant;
use App\Notifications\BillCreated;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateBillsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $today = now()->startOfDay();

        Tenant::all()->each(function (Tenant $tenant) use ($today) {
            $billingDay = Carbon::parse($tenant->move_in_date)->day;
            $daysInMonth = $today->daysInMonth;
            $expectedBillingDay = min($billingDay, $daysInMonth);

            // Only bill if today is on or after expected billing day
            if ($today->day < $expectedBillingDay) {
                return;
            }

            // Avoid duplicates by checking if a bill exists for this month
            $alreadyBilled = Bill::where('tenant_id', $tenant->id)
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->exists();

            if ($alreadyBilled) {
                return;
            }

            $dueDate = $today->copy()->addMonth();
            $dueDay = min($billingDay, $dueDate->daysInMonth);
            $dueDate->day = $dueDay;
            $dueDate = $dueDate->endOfDay();

            $bill = Bill::create([
                'tenant_id' => $tenant->id,
                'amount_due' => $tenant->room->rent_amount,
                'due_date' => $dueDate,
                'status' => 'unpaid',
            ]);

            $tenant->user->notify(new BillCreated($bill));
        });
    }
}
