<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// Shared hosting: saat index.php ditempatkan di document root terpisah
// (mis. public_html), public_path() harus menunjuk ke direktori ini.
// Jika core Laravel berada di luar document root, sesuaikan juga path
// require autoload/bootstrap di atas ke lokasi core aplikasi.
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
