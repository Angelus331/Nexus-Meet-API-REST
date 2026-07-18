<?php

use App\Console\Commands\NotificarEventosProximos;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tareas programadas
|--------------------------------------------------------------------------
| Requiere el cron real apuntando a "php artisan schedule:run" cada minuto
| (ver Fase 7 de la segunda guía) — sin eso, esto nunca se ejecuta solo.
*/
Schedule::command(NotificarEventosProximos::class)->everyThirtyMinutes();