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
            return new self(kabupaten: $user->kabupaten);
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
