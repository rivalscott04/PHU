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
            // Filter di repositori dipasang dengan when(), jadi nilai null
            // membuat penyaringnya lenyap dan seluruh data pengawasan NTB ikut
            // terbaca. Akun travel yang tidak punya travel_id harus ditolak,
            // bukan dibiarkan lewat tanpa penyaring.
            //
            // Pengawasan menyasar pemegang izin, dan datanya milik pusat. PIC
            // cabang tidak diberi akses ke sini.
            abort_unless($user->travel_id, 403, 'Akun ini tidak punya akses ke data pengawasan.');

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
