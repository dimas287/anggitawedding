<?php

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
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\PreventDuplicateRequest::class);
        
        $middleware->web(append: [
            \App\Http\Middleware\CheckGlobalMaintenance::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'profile.complete' => \App\Http\Middleware\EnsureProfileComplete::class,
            'log.admin' => \App\Http\Middleware\LogAdminActivity::class,
            'invitation.maintenance' => \App\Http\Middleware\CheckInvitationMaintenance::class,
            'global.maintenance' => \App\Http\Middleware\CheckGlobalMaintenance::class,
            'honeypot' => \App\Http\Middleware\AntiSpamHoneypot::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
