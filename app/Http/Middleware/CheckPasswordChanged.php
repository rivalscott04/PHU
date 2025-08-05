<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPasswordChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Log::info('CheckPasswordChanged middleware hit');
        $user = Auth::user();

        // Skip password check if impersonating
        if (app('impersonate')->isImpersonating()) {
            return $next($request);
        }

        if ($user && $user->role === 'user' && !$user->is_password_changed) {
            return redirect()->route('user.changePassword')->with('warning', 'Anda harus mengganti password default Anda.');
        }

        return $next($request);
    }
}
