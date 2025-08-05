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
        
        // Check if user is authenticated
        if (!auth()->check()) {
            return abort(403, 'Unauthorized access.');
        }

        $user = auth()->user();
        
        // Allow access if user is admin
        if ($user->role === 'admin') {
            return $next($request);
        }
        
        // Allow access if impersonating and the original user was admin
        if (app('impersonate')->isImpersonating()) {
            $originalUser = app('impersonate')->getImpersonator();
            if ($originalUser && $originalUser->role === 'admin') {
                return $next($request);
            }
        }
        
        \Log::info('Unauthorized access attempt by user: ' . auth()->id());
        return abort(403, 'Unauthorized access.');
    }
}

