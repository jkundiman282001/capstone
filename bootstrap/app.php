<?php

use Illuminate\Auth\AuthenticationException;
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
        // Exclude staff routes from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'staff/login',
            'staff/register',
            'staff/logout',
        ]);

        // Register auth middleware
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.staff' => \App\Http\Middleware\RedirectIfNotStaff::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'has.applied' => \App\Http\Middleware\CheckApplicationStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (\Illuminate\Http\Request $request, \Throwable $e) {
            if ($e instanceof AuthenticationException) {
                return $request->expectsJson();
            }

            return false;
        });

        $exceptions->render(function (AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect('/auth');
        });
    })->create();
