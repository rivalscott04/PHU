<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
    public function handle(Request $request, Closure $next)
    {
        // Never redirect the password-change or logout endpoints themselves
        if ($request->routeIs('user.changePassword', 'user.updatePassword', 'logout')) {
            return $next($request);
        }

        // Skip password check if impersonating
        if (app('impersonate')->isImpersonating()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user && in_array($user->role, ['user', 'kabupaten', 'pengawas', 'pimpinan'], true) && ! $user->is_password_changed) {
            return redirect()->route('user.changePassword')->with('warning', 'Anda harus mengganti password default Anda.');
        }

        return $next($request);
    }
}
