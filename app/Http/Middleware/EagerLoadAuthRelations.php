<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EagerLoadAuthRelations
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ($user->travel_id || $user->cabang_id)) {
            $user->loadMissing(['travel', 'cabang']);
        }

        return $next($request);
    }
}
