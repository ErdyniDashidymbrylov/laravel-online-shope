<?php

declare(strict_types=1);

use App\Jobs\GenerateSalesReportsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::call(fn () => GenerateSalesReportsJob::dispatchSync())
    ->name('generate-sales-reports')
    ->everyTwoMinutes()
    ->withoutOverlapping(2);
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
