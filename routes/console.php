<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('risk:calculate')
    ->dailyAt('00:30')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/risk-calculate.log'));

// Sertifikat PPIU berakhir setiap 1 Januari. Dijalankan harian agar hari yang
// terlewat karena server mati tetap tersusul; penandanya mencegah kiriman ganda.
Schedule::command('sertifikat:reminder-kadaluarsa')
    ->dailyAt('07:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/sertifikat-kadaluarsa.log'));

Schedule::command('followup:send-deadline-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/deadline-reminders.log'));
