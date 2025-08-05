<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KabupatenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        $user = auth()->user();
        
        // Allow access if user is kabupaten or admin
        if ($user->role === 'kabupaten' || $user->role === 'admin') {
            return $next($request);
        }
        
        // Allow access if impersonating and the original user was admin or kabupaten
        if (app('impersonate')->isImpersonating()) {
            $originalUser = app('impersonate')->getImpersonator();
            if ($originalUser && in_array($originalUser->role, ['admin', 'kabupaten'])) {
                return $next($request);
            }
        }
        
        // For travel users (role 'user'), allow access to dashboard
        if ($user->role === 'user') {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
