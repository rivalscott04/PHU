<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\Jamaah;
use App\Models\JamaahHajiKhusus;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Satu sumber kebenaran untuk scope + filter daftar jamaah, dipakai bersama
 * oleh tampilan tabel dan unduhan supaya file yang diunduh selalu sama dengan
 * yang sedang dilihat.
 */
final class JamaahListingQuery
{
    /** @return Builder<Jamaah> */
    public static function build(string $jenis, Request $request, User $user): Builder
    {
        $query = Jamaah::query()
            ->where('jenis_jamaah', $jenis)
            ->with('travel');

        self::applyScope($query, $user);
        self::applyFilters($query, $request);

        return $query->orderBy('nama');
    }

    /** @return Builder<JamaahHajiKhusus> */
    public static function buildHajiKhusus(Request $request, User $user): Builder
    {
        $query = JamaahHajiKhusus::query()->with('travel');

        self::applyHajiKhususScope($query, $user);
        self::applyTravelFilter($query, $request);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('no_ktp', 'like', "%{$search}%")
                    ->orWhere('no_paspor', 'like', "%{$search}%")
                    ->orWhere('nomor_porsi', 'like', "%{$search}%")
                    ->orWhereHas('travel', function ($travelQuery) use ($search) {
                        $travelQuery->where('Penyelenggara', 'like', "%{$search}%")
                            ->orWhere('kab_kota', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->byStatus($request->string('status')->toString());
        }

        return $query;
    }

    public static function hasActiveFilter(Request $request): bool
    {
        return $request->filled('search')
            || $request->filled('travel_id')
            || $request->filled('status');
    }

    /**
     * Travel yang boleh dilihat user ini dan benar-benar punya jamaah jenis
     * tersebut, jadi filter tidak pernah menawarkan pilihan kosong.
     *
     * @return Collection<int, TravelCompany>
     */
    public static function travelOptions(string $jenis, User $user): Collection
    {
        if ($user->role === UserRole::User->value) {
            return collect();
        }

        $query = TravelCompany::query()
            ->whereHas('jamaah', fn ($q) => $q->where('jenis_jamaah', $jenis))
            ->withCount(['jamaah as jamaah_count' => fn ($q) => $q->where('jenis_jamaah', $jenis)]);

        if ($user->role === UserRole::Kabupaten->value) {
            KabupatenScopeFilter::applyOnColumn(
                $query,
                KabupatenScopeFilter::filtersForUser($user),
                'kab_kota'
            );
        }

        return $query->orderBy('Penyelenggara')->get();
    }

    /** @return Collection<int, TravelCompany> */
    public static function travelOptionsHajiKhusus(User $user): Collection
    {
        if ($user->role === UserRole::User->value) {
            return collect();
        }

        $query = TravelCompany::query()
            ->whereHas('jamaahHajiKhusus')
            ->withCount('jamaahHajiKhusus as jamaah_count');

        if ($user->role === UserRole::Kabupaten->value) {
            KabupatenScopeFilter::applyOnColumn(
                $query,
                KabupatenScopeFilter::filtersForUser($user),
                'kab_kota'
            );
        }

        return $query->orderBy('Penyelenggara')->get();
    }

    /** @param  Builder<Jamaah>  $query */
    private static function applyScope(Builder $query, User $user): void
    {
        if ($user->role === UserRole::User->value) {
            OperatorScope::apply($query, $user);

            return;
        }

        if ($user->role === UserRole::Kabupaten->value) {
            KabupatenScopeFilter::applyOnTravelRelation($query, KabupatenScopeFilter::filtersForUser($user));

            return;
        }

        if ($user->role !== UserRole::Admin->value) {
            $query->whereRaw('1 = 0');
        }
    }

    /** @param  Builder<JamaahHajiKhusus>  $query */
    private static function applyHajiKhususScope(Builder $query, User $user): void
    {
        if ($user->role === UserRole::User->value) {
            // Dulu filter travel dilewati kalau user->travel kosong, dan yang
            // tersisa hanya penyaring wilayah. Untuk PIC cabang itu berarti
            // jamaah haji khusus milik travel lain di kabupaten yang sama ikut
            // terlihat. Kepemilikan sekarang dinilai eksplisit.
            OperatorScope::apply($query, $user);

            return;
        }

        if ($user->role === UserRole::Kabupaten->value) {
            KabupatenScopeFilter::applyOnTravelRelation($query, KabupatenScopeFilter::filtersForUser($user));

            return;
        }

        if ($user->role !== UserRole::Admin->value) {
            $query->whereRaw('1 = 0');
        }
    }

    /**
     * Scope peran selalu dipasang lebih dulu, jadi travel_id di luar wilayah
     * user hanya menghasilkan nol baris, bukan kebocoran data.
     */
    private static function applyTravelFilter(Builder $query, Request $request): void
    {
        if ($request->filled('travel_id')) {
            $query->where('travel_id', (int) $request->input('travel_id'));
        }
    }

    /** @param  Builder<Jamaah>  $query */
    private static function applyFilters(Builder $query, Request $request): void
    {
        self::applyTravelFilter($query, $request);

        if (! $request->filled('search')) {
            return;
        }

        $search = $request->string('search')->toString();

        $query->where(function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
                ->orWhere('nik', 'like', "%{$search}%")
                ->orWhere('nomor_hp', 'like', "%{$search}%")
                ->orWhere('alamat', 'like', "%{$search}%")
                ->orWhereHas('travel', function ($travelQuery) use ($search) {
                    $travelQuery->where('Penyelenggara', 'like', "%{$search}%")
                        ->orWhere('kab_kota', 'like', "%{$search}%");
                });
        });
    }
}
