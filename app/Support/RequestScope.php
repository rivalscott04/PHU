<?php

namespace App\Support;

use Illuminate\Http\Request;

class RequestScope
{
    public function __construct(
        public readonly ?string $kabupaten = null,
        public readonly ?array $kabupatens = null,
        public readonly ?int $travelId = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $user = $request->user();

        if ($user->role === 'kabupaten') {
            return new self(kabupaten: NtbKabupatenMap::normalize($user->kabupaten));
        }

        if ($user->role === 'pengawas') {
            $scoped = $user->getScopedKabupatens();

            if ($scoped === null) {
                return new self(kabupaten: $request->get('kabupaten'));
            }

            if (count($scoped) === 1) {
                return new self(kabupaten: $scoped[0]);
            }

            return new self(kabupatens: $scoped);
        }

        if ($user->role === 'user') {
            // travelId null membuat penyaring when() di repositori lenyap dan
            // seluruh data NTB terbaca. Akun tanpa travel_id ditolak di sini.
            abort_unless($user->travel_id, 403, 'Akun ini tidak punya akses ke data pengawasan.');

            return new self(travelId: $user->travel_id);
        }

        return new self(kabupaten: $request->get('kabupaten'));
    }

    public function hasKabupatenRestriction(): bool
    {
        return $this->kabupaten !== null || ! empty($this->kabupatens);
    }

    /** @return array<string, mixed> */
    public function toFilterArray(): array
    {
        if (! empty($this->kabupatens)) {
            return ['kabupatens' => $this->kabupatens];
        }

        if ($this->kabupaten) {
            return ['kabupaten' => $this->kabupaten];
        }

        return [];
    }
}
