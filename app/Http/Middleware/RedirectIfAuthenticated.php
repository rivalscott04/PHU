<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Redirect berdasarkan role
                switch ($user->role) {
                    case 'admin':
                    case 'kabupaten':
                    case 'user':
                        return redirect()->route('home');
                    case 'pengawas':
                        return redirect()->route('v2.antrian.index');
                    case 'pimpinan':
                        return redirect()->route('v2.dashboard');
                    default:
                        return redirect('/');
                }
            }
        }

        return $next($request);
    }
}
