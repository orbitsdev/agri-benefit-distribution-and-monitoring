<?php

use App\Http\Middleware\EnsureIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'Excel' => Maatwebsite\Excel\Facades\Excel::class,
            'DNS1D' => Milon\Barcode\Facades\DNS1DFacade::class,
            'DNS2D' => Milon\Barcode\Facades\DNS2DFacade::class,
             Maatwebsite\Excel\ExcelServiceProvider::class,
        ]);


        $middleware->alias([
            'is-admin'=> \App\Http\Middleware\EnsureIsAdmin::class,
            'is-super-admin'=> \App\Http\Middleware\EnsureIsSuperAdmin::class,
            // 'distribution-is-not-locked'=> \App\Http\Middleware\EnsureDistributionIsUnlocked::class,
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })

    ->create();
