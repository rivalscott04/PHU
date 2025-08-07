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

        // Check password change for both 'user' and 'kabupaten' roles
        if ($user && in_array($user->role, ['user', 'kabupaten']) && !$user->is_password_changed) {
            return redirect()->route('user.changePassword')->with('warning', 'Anda harus mengganti password default Anda.');
        }

        return $next($request);
    }
}
