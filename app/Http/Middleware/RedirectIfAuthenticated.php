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
                        return redirect()->route('home');
                    case 'kabupaten':
                        return redirect()->route('home');
                    case 'user':
                        return redirect()->route('bap');
                    default:
                        return redirect('/');
                }
            }
        }

        return $next($request);
    }
}
