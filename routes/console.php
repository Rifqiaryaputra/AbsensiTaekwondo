<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-rekap status Alfa untuk jadwal yang sesi absensinya sudah ditutup.
// Daftarkan perintah auto-alfa agar berjalan setiap menit
Schedule::command('absen:auto-alfa')->everyMinute();
