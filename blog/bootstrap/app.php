<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Appended, so it sees the 404 the router produced.
        $middleware->web(append: [
            \App\Http\Middleware\ServeRedirects::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RecordPageView::class,
        ]);

        // There is no login page: readers sign in through a provider. Sending
        // a guest back to the comments shows them the buttons that do exist.
        $middleware->redirectGuestsTo(fn ($request) => url()->previous().'#comments');
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
