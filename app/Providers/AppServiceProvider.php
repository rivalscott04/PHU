<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Carbon::setLocale('id');

        if (! app()->isProduction() && ! config('app.e2e_testing')) {
            Model::preventLazyLoading();
        }

        $this->configureImpersonateSessionGuard();
        $this->configureRateLimiting();
    }

    /**
     * lab404/laravel-impersonate registers a custom session guard that calls
     * setRequest() with a possibly-null refresh() result. Laravel 11's
     * SessionGuard::setRequest() is non-nullable, which TypeErrors and 500s.
     */
    protected function configureImpersonateSessionGuard(): void
    {
        $auth = $this->app['auth'];

        $auth->extend('session', function ($app, $name, array $config) use ($auth) {
            $provider = $auth->createUserProvider($config['provider'] ?? null);

            $guard = new \Lab404\Impersonate\Guard\SessionGuard(
                $name,
                $provider,
                $app['session.store'],
            );

            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($app['cookie']);
            }

            if (method_exists($guard, 'setDispatcher')) {
                $guard->setDispatcher($app['events']);
            }

            if (method_exists($guard, 'setRequest')) {
                if ($app->bound('request')) {
                    $request = $app->refresh('request', $guard, 'setRequest');
                    if ($request !== null) {
                        $guard->setRequest($request);
                    }
                } else {
                    $app->rebinding('request', function ($app, $request) use ($guard) {
                        $guard->setRequest($request);
                    });
                }
            }

            return $guard;
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            if (app()->environment('local') && config('app.e2e_testing')) {
                return Limit::none();
            }

            $key = trim((string) $request->input('email_or_phone', $request->ip()));

            // Samakan format nomor supaya +62 812... dan 0812-... tidak dapat jatah percobaan terpisah.
            if (preg_match('/^[0-9+\-\s().]+$/', $key)) {
                $key = \App\Models\User::normalizeNomorHp($key);
            } else {
                $key = mb_strtolower($key);
            }

            return Limit::perMinute(5)->by($request->ip().'|'.$key);
        });

        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        RateLimiter::for('sensitive', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });
    }
}
