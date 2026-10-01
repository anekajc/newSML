<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo('/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Unexpected errors on jQuery AJAX calls from logged-in users: send the real
        // message + file:line back as JSON (instead of the bare "500 Server Error" page)
        // so it shows up in the browser console via the layouts' ajaxError handler
        // (resources/views/partials/ajax-error-logger.blade.php). Still logged to
        // storage/logs/laravel.log as usual. HTTP errors (404/419/...), auth redirects
        // and validation errors keep Laravel's default handling.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->ajax()
                || $e instanceof HttpExceptionInterface
                || $e instanceof AuthenticationException
                || $e instanceof ValidationException) {
                return null;
            }

            try {
                // May hit the SML DB if the user wasn't resolved yet; if that's what's
                // broken, fall back to the default response rather than throwing here.
                if (! Auth::check()) {
                    return null;
                }
            } catch (Throwable) {
                return null;
            }

            return response()->json([
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => str_replace(base_path().DIRECTORY_SEPARATOR, '', $e->getFile()),
                'line' => $e->getLine(),
            ], 500);
        });
    })->create();
