<?php

/*
|--------------------------------------------------------------------------
| SWA Media Account Portal
|--------------------------------------------------------------------------
| 
| This is the entry point for the SWA Media Account Portal application.
| This application is built with PHP/Laravel and provides user account
| management functionality including authentication, profile management,
| and security settings.
|
*/

// Define the application start time
define('LARAVEL_START', microtime(true));

// Check if the application is in maintenance mode
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap the Laravel application
$app = require_once __DIR__.'/../bootstrap/app.php';

// Handle the incoming request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);