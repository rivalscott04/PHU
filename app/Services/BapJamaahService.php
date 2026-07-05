<?php

namespace App\Services;

use App\Models\BAP;
use App\Models\Jamaah;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BapJamaahService
{
    /** @var list<string> */
    private const ACTIVE_STATUSES = ['pending', 'diajukan', 'diproses'];

    /**
     * @return \Illuminate\Database\Eloquent\Builder<Jamaah>
     */
    private function baseQueryForForm(User $user, ?TravelCompany $travel = null)
    {
        $query = Jamaah::query()->with('travel')->orderBy('nama');

        if ($user->role === 'user' && $travel) {
            $jenis = $travel->Status === 'PIHK' ? 'haji' : 'umrah';
            $query->where('travel_id', $travel->id)->where('jenis_jamaah', $jenis);
        } elseif ($user->role === 'kabupaten') {
            $query->whereHas('travel', function ($q) use ($user) {
                $q->where('kab_kota', $user->kabupaten);
            });
        }

        return $query;
    }

    public function countForForm(User $user, ?TravelCompany $travel = null): int
    {
        return $this->baseQueryForForm($user, $travel)->count();
    }

    /**
     * @param  array{search?: string, ppiuname?: string, per_page?: int, ignore_bap_id?: int|null}  $filters
     */
    public function paginateForPicker(User $user, ?TravelCompany $travel, array $filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->baseQueryForForm($user, $travel);

        if (! empty($filters['ppiuname'])) {
            $query->whereHas('travel', function ($q) use ($filters) {
                $q->where('Penyelenggara', $filters['ppiuname']);
            });
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        return $query->paginate($filters['per_page'] ?? 15)->withQueryString();
    }

    /**
     * @param  array{ppiuname?: string, ignore_bap_id?: int|null}  $filters
     * @return list<int>
     */
    public function availableIdsForPicker(User $user, ?TravelCompany $travel, array $filters = []): array
    {
        $query = $this->baseQueryForForm($user, $travel);

        if (! empty($filters['ppiuname'])) {
            $query->whereHas('travel', function ($q) use ($filters) {
                $q->where('Penyelenggara', $filters['ppiuname']);
            });
        }

        $busy = $this->busyJamaahIds($filters['ignore_bap_id'] ?? null);

        if ($busy !== []) {
            $query->whereNotIn('id', $busy);
        }

        return $query->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * @return Collection<int, Jamaah>
     */
    public function listForForm(User $user, ?TravelCompany $travel = null): Collection
    {
        return $this->baseQueryForForm($user, $travel)->get();
    }

    /**
     * @return list<int>
     */
    public function busyJamaahIds(?int $ignoreBapId = null): array
    {
        $query = DB::table('bap_jamaah')
            ->join('bap', 'bap.id', '=', 'bap_jamaah.bap_id')
            ->whereIn('bap.status', self::ACTIVE_STATUSES);

        if ($ignoreBapId) {
            $query->where('bap.id', '!=', $ignoreBapId);
        }

        return $query->pluck('bap_jamaah.jamaah_id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * @param  list<int|string>  $jamaahIds
     * @return Collection<int, Jamaah>
     */
    public function validateSelection(array $jamaahIds, User $user, string $ppiuname, ?int $ignoreBapId = null): Collection
    {
        $jamaahIds = array_values(array_unique(array_filter(array_map('intval', $jamaahIds))));

        if ($jamaahIds === []) {
            throw ValidationException::withMessages([
                'jamaah_ids' => 'Pilih minimal satu jamaah untuk keberangkatan ini.',
            ]);
        }

        $jamaah = Jamaah::with('travel')->whereIn('id', $jamaahIds)->get();

        if ($jamaah->count() !== count($jamaahIds)) {
            throw ValidationException::withMessages([
                'jamaah_ids' => 'Data jamaah tidak valid.',
            ]);
        }

        $travelUser = $user->travel_id ? TravelCompany::find($user->travel_id) : null;

        foreach ($jamaah as $row) {
            if ($user->role === 'user') {
                if ($row->travel_id !== $user->travel_id) {
                    throw ValidationException::withMessages([
                        'jamaah_ids' => 'Jamaah yang dipilih bukan milik travel Anda.',
                    ]);
                }

                if ($travelUser) {
                    $expectedJenis = $travelUser->Status === 'PIHK' ? 'haji' : 'umrah';
                    if ($row->jenis_jamaah !== $expectedJenis) {
                        throw ValidationException::withMessages([
                            'jamaah_ids' => 'Jenis jamaah tidak sesuai izin travel.',
                        ]);
                    }
                }
            }

            if ($user->role === 'kabupaten' && $row->travel?->kab_kota !== $user->kabupaten) {
                throw ValidationException::withMessages([
                    'jamaah_ids' => 'Jamaah di luar wilayah kabupaten Anda.',
                ]);
            }

            if ($row->travel?->Penyelenggara !== $ppiuname) {
                throw ValidationException::withMessages([
                    'jamaah_ids' => 'Jamaah harus berasal dari PPIU yang dipilih.',
                ]);
            }
        }

        $busy = $this->busyJamaahIds($ignoreBapId);
        if (array_intersect($jamaahIds, $busy) !== []) {
            throw ValidationException::withMessages([
                'jamaah_ids' => 'Beberapa jamaah sudah terdaftar di pengajuan aktif lain.',
            ]);
        }

        return $jamaah;
    }

    /**
     * @param  list<int|string>  $jamaahIds
     */
    public function sync(BAP $bap, array $jamaahIds, User $user): int
    {
        $selected = $this->validateSelection($jamaahIds, $user, $bap->ppiuname, $bap->id);
        $bap->jamaah()->sync($selected->pluck('id')->all());

        return $selected->count();
    }
}
