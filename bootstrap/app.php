<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'IsAdmin' => App\Http\Middleware\IsAdmin::class,
            'IsDokter' => App\Http\Middleware\IsDokter::class,
            'IsPerawat' => App\Http\Middleware\IsPerawat::class,
            'IsResepsionis' => App\Http\Middleware\IsResepsionis::class,
            'IsPemilik' => App\Http\Middleware\IsPemilik::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
