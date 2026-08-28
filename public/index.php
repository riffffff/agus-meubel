<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
| Shared hosting: file Laravel ada di luar public_html
| Path: /home/username/agusmeubel/
| public_html/ berisi isi dari folder public/ Laravel
|
| Sesuaikan path __DIR__.'/../vendor' dengan lokasi install kamu.
| Contoh jika struktur hosting:
|   /home/username/agusmeubel/       <- root Laravel
|   /home/username/public_html/      <- isi folder public/
|
| Maka path di bawah sudah benar karena index.php ada di public_html
| dan Laravel root ada satu level di atas (/../agusmeubel)
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
