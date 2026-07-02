<?php

namespace App\Support;

use App\Models\TravelCompany;
use Illuminate\Http\Request;

class TravelAccess
{
    public static function authorize(Request $request, TravelCompany $travel): void
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'kabupaten' && $travel->kab_kota === $user->getKabupaten()) {
            return;
        }

        if ($user->role === 'user' && $user->travel_id === $travel->id) {
            return;
        }

        abort(403);
    }
}
