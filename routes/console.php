<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily fine update at midnight
Schedule::command('loans:update-fines')
    ->daily()
    ->at('00:00')
    ->description('Update fines for overdue loans daily');
