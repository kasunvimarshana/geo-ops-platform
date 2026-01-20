<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll require it into the
| script here so that we do not have to worry about manually loading any
| of our classes later on. It feels great to relax.
|
*/

require __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Bootstrap Laravel And Handle The Request
|--------------------------------------------------------------------------
|
| First, we need to get an application instance. This is pulled from the
| bootstrap/app.php file. For convenience, we've already included this in
| the Composer autoload files, so we just need to require it here.
|
*/

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
