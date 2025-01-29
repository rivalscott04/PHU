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

        if (auth()->check() && auth()->user()->role === 'kabupaten' || auth()->user()->role === 'admin') {
            return $next($request);
        }
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
