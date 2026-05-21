<?php

namespace App\Jobs;

use App\Enums\BillStatus;
use App\Models\Bill;
use App\Notifications\BillOverdue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateOverdueBillsJob implements ShouldQueue
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

        Bill::where('status', 'unpaid')
            ->where('due_date', '<', $today)
            ->get()
            ->each(function (Bill $bill) {
                $bill->update(['status' => BillStatus::OVERDUE->value]);

                if ($bill->tenant && $bill->tenant->user) {
                    $bill->tenant->user->notify(new BillOverdue($bill));
                }
            });

    }
}
