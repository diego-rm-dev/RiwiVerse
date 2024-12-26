<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('fix:publication-dates', function () {
    \App\Models\Publications::whereNull('date')->update([
        'date' => now(),
    ]);

    $this->info('Fechas corregidas.');
});