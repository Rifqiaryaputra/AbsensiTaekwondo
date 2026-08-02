<?php

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\JadwalService;

$svc = app(JadwalService::class);
$now = now();

echo 'Now: '.$now->toDateTimeString().PHP_EOL;
echo 'toTimeString: '.$now->toTimeString().PHP_EOL;
echo 'hariNama: '.$svc->hariNama($now).PHP_EOL;
echo 'Carbon locale: '.Carbon\Carbon::getLocale().PHP_EOL;
echo '--- jadwal ---'.PHP_EOL;
foreach (App\Models\Jadwal::all() as $j) {
    echo $j->id.' | '.$j->hari.' | '.$j->jam_start.' - '.$j->jam_close.PHP_EOL;
}
echo '--- active ---'.PHP_EOL;
$active = $svc->getActiveJadwal($now);
echo $active ? 'ACTIVE: '.$active->id.' '.$active->hari.' '.$active->jam_start.'-'.$active->jam_close : 'NONE'.PHP_EOL;
