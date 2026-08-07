<?php

use App\Helpers\ExceptionMessageHelper;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/v2.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'kabupaten' => \App\Http\Middleware\KabupatenMiddleware::class,
            'kabupaten.access' => \App\Http\Middleware\KabupatenAccessMiddleware::class,
            'password.changed' => \App\Http\Middleware\CheckPasswordChanged::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\EagerLoadAuthRelations::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (AuthorizationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not found.'], 404);
            }

            if ($request->isMethod('GET') && $request->is('v2/pengawasan/*')) {
                return response()->view('errors::404', [], 404);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Anda tidak memiliki akses untuk aksi ini, atau pengawasan sudah ditutup.');
        });

        $exceptions->renderable(function (QueryException|PDOException $e, Request $request) {
            Log::error('Database error', ['exception' => $e]);

            $message = ExceptionMessageHelper::forUser($e);

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            if ($request->isMethod('GET')) {
                return response()->view('errors.500', [], 500);
            }

            return redirect()->back()->withInput()->with('error', $message);
        });
    })
    ->create();
