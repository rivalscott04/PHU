<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\Jamaah;
use App\Models\JamaahHajiKhusus;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Http\Request;

final class JamaahExportScope
{
    /**
     * @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string}
     */
    public static function forJamaah(User $user, Request $request, string $jenis): array
    {
        // Filter yang sedang aktif di layar menang: yang diunduh harus persis
        // yang sedang dilihat.
        if (JamaahListingQuery::hasActiveFilter($request)) {
            return self::filteredJamaah($user, $request, $jenis);
        }

        if ($user->role === UserRole::User->value) {
            return self::singleTravelJamaah($user, $jenis);
        }

        $type = $request->string('type')->toString() ?: 'global';
        $travelId = $request->filled('travel_id') ? (int) $request->input('travel_id') : null;

        if ($user->role === UserRole::Kabupaten->value) {
            if ($type === 'travel' && $travelId) {
                return self::specificTravelJamaah($jenis, $travelId, $user);
            }

            return self::groupedJamaahInKabupaten($user, $jenis);
        }

        if ($type === 'travel' && $travelId) {
            return self::specificTravelJamaah($jenis, $travelId, $user);
        }

        return self::globalGroupedJamaah($jenis);
    }

    /**
     * @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string}
     */
    public static function forHajiKhusus(User $user, Request $request): array
    {
        if (JamaahListingQuery::hasActiveFilter($request)) {
            return self::filteredHajiKhusus($user, $request);
        }

        if ($user->role === UserRole::User->value) {
            return self::singleTravelHajiKhusus($user);
        }

        $type = $request->string('type')->toString() ?: 'global';
        $travelId = $request->filled('travel_id') ? (int) $request->input('travel_id') : null;

        if ($user->role === UserRole::Kabupaten->value) {
            if ($type === 'travel' && $travelId) {
                return self::specificTravelHajiKhusus($travelId, $user);
            }

            return self::groupedHajiKhususInKabupaten($user);
        }

        if ($type === 'travel' && $travelId) {
            return self::specificTravelHajiKhusus($travelId, $user);
        }

        return self::globalGroupedHajiKhusus();
    }

    /**
     * Unduhan mengikuti filter daftar. Satu travel menghasilkan file tunggal,
     * hasil pencarian lintas travel tetap dikelompokkan per travel.
     *
     * @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string}
     */
    private static function filteredJamaah(User $user, Request $request, string $jenis): array
    {
        $jamaah = JamaahListingQuery::build($jenis, $request, $user)->get();

        if ($jamaah->isEmpty()) {
            return self::fail('Tidak ada data jamaah yang cocok dengan filter aktif.');
        }

        if ($request->filled('travel_id')) {
            return self::ok(false, $jamaah, $jamaah->first()->travel);
        }

        return self::ok(true, $jamaah->groupBy('travel_id'), null);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function singleTravelJamaah(User $user, string $jenis): array
    {
        if (! $user->travel_id) {
            return self::fail('Travel tidak ditemukan untuk akun Anda.');
        }

        $jamaah = Jamaah::query()
            ->where('jenis_jamaah', $jenis)
            ->where('travel_id', $user->travel_id)
            ->with('travel')
            ->orderBy('nama')
            ->get();

        if ($jamaah->isEmpty()) {
            return self::fail('Tidak ada data jamaah untuk diunduh.');
        }

        return self::ok(false, $jamaah, $jamaah->first()->travel);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function specificTravelJamaah(string $jenis, int $travelId, User $user): array
    {
        $travel = TravelCompany::findOrFail($travelId);
        KabupatenResourceGuard::authorizeTravel($user, $travel);

        $jamaah = Jamaah::query()
            ->where('jenis_jamaah', $jenis)
            ->where('travel_id', $travelId)
            ->with('travel')
            ->orderBy('nama')
            ->get();

        if ($jamaah->isEmpty()) {
            return self::fail('Tidak ada data jamaah untuk PPIU ini.');
        }

        return self::ok(false, $jamaah, $travel);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function groupedJamaahInKabupaten(User $user, string $jenis): array
    {
        $query = Jamaah::query()
            ->where('jenis_jamaah', $jenis)
            ->with('travel');

        KabupatenScopeFilter::applyOnTravelRelation($query, KabupatenScopeFilter::filtersForUser($user));

        $grouped = $query->get()->groupBy('travel_id');

        if ($grouped->isEmpty()) {
            return self::fail('Tidak ada data jamaah untuk diunduh.');
        }

        return self::ok(true, $grouped, null);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function globalGroupedJamaah(string $jenis): array
    {
        $grouped = Jamaah::query()
            ->where('jenis_jamaah', $jenis)
            ->with('travel')
            ->get()
            ->groupBy('travel_id');

        if ($grouped->isEmpty()) {
            return self::fail('Tidak ada data jamaah untuk diunduh.');
        }

        return self::ok(true, $grouped, null);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function filteredHajiKhusus(User $user, Request $request): array
    {
        $jamaah = JamaahListingQuery::buildHajiKhusus($request, $user)->orderBy('nama_lengkap')->get();

        if ($jamaah->isEmpty()) {
            return self::fail('Tidak ada data jamaah haji khusus yang cocok dengan filter aktif.');
        }

        if ($request->filled('travel_id')) {
            return self::ok(false, $jamaah, $jamaah->first()->travel);
        }

        return self::ok(true, $jamaah->groupBy('travel_id'), null);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function singleTravelHajiKhusus(User $user): array
    {
        if (! $user->travel_id) {
            return self::fail('Travel tidak ditemukan untuk akun Anda.');
        }

        $jamaah = JamaahHajiKhusus::query()
            ->where('travel_id', $user->travel_id)
            ->with('travel')
            ->orderBy('nama')
            ->get();

        if ($jamaah->isEmpty()) {
            return self::fail('Tidak ada data jamaah haji khusus untuk diunduh.');
        }

        return self::ok(false, $jamaah, $jamaah->first()->travel);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function specificTravelHajiKhusus(int $travelId, User $user): array
    {
        $travel = TravelCompany::findOrFail($travelId);
        KabupatenResourceGuard::authorizeTravel($user, $travel);

        $jamaah = JamaahHajiKhusus::query()
            ->where('travel_id', $travelId)
            ->with('travel')
            ->orderBy('nama')
            ->get();

        if ($jamaah->isEmpty()) {
            return self::fail('Tidak ada data jamaah haji khusus untuk PIHK ini.');
        }

        return self::ok(false, $jamaah, $travel);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function groupedHajiKhususInKabupaten(User $user): array
    {
        $query = JamaahHajiKhusus::query()->with('travel');
        KabupatenScopeFilter::applyOnTravelRelation($query, KabupatenScopeFilter::filtersForUser($user));

        $grouped = $query->get()->groupBy('travel_id');

        if ($grouped->isEmpty()) {
            return self::fail('Tidak ada data jamaah haji khusus untuk diunduh.');
        }

        return self::ok(true, $grouped, null);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function globalGroupedHajiKhusus(): array
    {
        $grouped = JamaahHajiKhusus::query()
            ->with('travel')
            ->get()
            ->groupBy('travel_id');

        if ($grouped->isEmpty()) {
            return self::fail('Tidak ada data jamaah haji khusus untuk diunduh.');
        }

        return self::ok(true, $grouped, null);
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: ?string} */
    private static function ok(bool $isGlobal, mixed $data, ?TravelCompany $travel): array
    {
        return [
            'isGlobal' => $isGlobal,
            'data' => $data,
            'travel' => $travel,
            'error' => null,
        ];
    }

    /** @return array{isGlobal: bool, data: mixed, travel: ?TravelCompany, error: string} */
    private static function fail(string $message): array
    {
        return [
            'isGlobal' => false,
            'data' => collect(),
            'travel' => null,
            'error' => $message,
        ];
    }
}
