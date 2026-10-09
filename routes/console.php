<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reservations:mark-no-shows')
    ->dailyAt('01:00')
    ->withoutOverlapping();

Schedule::command('hotel:send-operational-alerts')
    ->dailyAt('06:00')
    ->withoutOverlapping();

Schedule::command('mpesa:reconcile-pending --minutes=2')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->onOneServer();

Schedule::command('backup:run --only-db')
    ->dailyAt('02:00')
    ->withoutOverlapping(120)
    ->onOneServer()
    ->environments(['production']);

Schedule::command('backup:clean')
    ->dailyAt('02:30')
    ->withoutOverlapping(120)
    ->onOneServer()
    ->environments(['production']);
