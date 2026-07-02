<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestScope
{
    public function __construct(
        public readonly ?string $kabupaten = null,
        public readonly ?int $travelId = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $user = $request->user();

        if ($user->role === 'kabupaten') {
            return new self(kabupaten: $user->getKabupaten());
        }

        if ($user->role === 'user') {
            return new self(travelId: $user->travel_id);
        }

        return new self(kabupaten: $request->get('kabupaten'));
    }
}
