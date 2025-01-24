<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('AdminMiddleware executed for user: ' . auth()->id());
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }
        \Log::info('Unauthorized access attempt by user: ' . auth()->id());
        return redirect('/')->with('error', 'Unauthorized access.');
    }
}
