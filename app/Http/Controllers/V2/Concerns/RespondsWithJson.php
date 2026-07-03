<?php

namespace App\Http\Controllers\V2\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Support\KabupatenScopeFilter;

trait RespondsWithJson
{
    protected function scopeFilters(Request $request, array $keys = []): array
    {
        $filters = $request->only($keys);
        $user = $request->user();

        if ($user->role === 'pengawas') {
            $filters = array_merge($filters, KabupatenScopeFilter::pengawasFilters($user));
        }

        if ($user->role === 'user') {
            $filters['travel_id'] = $user->travel_id;
        }

        return $filters;
    }

    protected function jsonSuccess(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function jsonError(string $message, int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}
