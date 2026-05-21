<?php

use App\Jobs\GenerateBillsJob;
use App\Jobs\UpdateOverdueBillsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:clean-temp-storage public --force')->daily();

Schedule::job(new GenerateBillsJob)->everyTenSeconds();
Schedule::job(new UpdateOverdueBillsJob)->everyTenSeconds();
