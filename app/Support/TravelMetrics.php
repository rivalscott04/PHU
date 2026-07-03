<?php

namespace App\Support;

use App\Models\BAP;
use App\Models\CabangTravel;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\Jamaah;
use App\Models\JamaahHajiKhusus;
use App\Models\Pengaduan;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Support\SchemaTables;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class TravelMetrics
{
    /** @return array{total_ppiu: int, total_pihk: int} */
    public static function travelTypeCounts(Builder $travelScope): array
    {
        $row = (clone $travelScope)
            ->selectRaw("SUM(CASE WHEN Status = 'PPIU' THEN 1 ELSE 0 END) as total_ppiu")
            ->selectRaw("SUM(CASE WHEN Status = 'PIHK' THEN 1 ELSE 0 END) as total_pihk")
            ->first();

        return [
            'total_ppiu' => (int) ($row->total_ppiu ?? 0),
            'total_pihk' => (int) ($row->total_pihk ?? 0),
        ];
    }

    /** @param  Collection<int, int>|Builder  $travelScope */
    public static function activeFindingsCount(Collection|Builder $travelScope): int
    {
        if (! SchemaTables::has('pengawasan_temuan')) {
            return 0;
        }

        return InspectionFinding::query()
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->when(
                $travelScope instanceof Builder,
                fn ($q) => $q->whereIn('pengawasan.travel_id', $travelScope),
                fn ($q) => $q->whereIn('pengawasan.travel_id', $travelScope)
            )
            ->whereNotIn('pengawasan_temuan.status', ['CLOSED', 'VERIFIED'])
            ->count();
    }

    /** @param  Collection<int, int>|Builder  $travelScope */
    public static function highRiskCount(Collection|Builder $travelScope): int
    {
        return RiskScore::query()
            ->when(
                $travelScope instanceof Builder,
                fn ($q) => $q->whereIn('travel_id', $travelScope),
                fn ($q) => $q->whereIn('travel_id', $travelScope)
            )
            ->whereIn('risk_level', ['HIGH', 'CRITICAL'])
            ->count();
    }

    /** @param  Collection<int, int>|Builder  $travelScope */
    public static function runningInspectionsCount(Collection|Builder $travelScope, ?Carbon $start = null, ?Carbon $end = null): int
    {
        return Inspection::query()
            ->when(
                $travelScope instanceof Builder,
                fn ($q) => $q->whereIn('travel_id', $travelScope),
                fn ($q) => $q->whereIn('travel_id', $travelScope)
            )
            ->whereIn('status', ['SCHEDULED', 'ON_PROGRESS', 'WAITING_FOLLOWUP', 'FOLLOWUP_UPLOADED'])
            ->when($start && $end, fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->count();
    }

    /**
     * @param  Collection<int, int>  $travelIds
     * @return array<string, int>
     */
    public static function dashboardPeriodCounts(Collection $travelIds, Carbon $start, Carbon $end, ?string $kabupaten = null): array
    {
        if ($travelIds->isEmpty()) {
            return self::emptyDashboardPeriodCounts();
        }

        $travelScope = TravelCompany::query()->whereIn('id', $travelIds);
        $typeCounts = self::travelTypeCounts($travelScope);

        $jamaahStats = (object) ['total' => 0, 'total_umrah' => 0];
        if (Schema::hasTable('jamaah')) {
            $jamaahStats = Jamaah::query()
                ->whereIn('travel_id', $travelIds)
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN jenis_jamaah = 'umrah' THEN 1 ELSE 0 END) as total_umrah")
                ->first();
        }

        $bapStats = (object) ['total' => 0, 'pending' => 0];
        if (Schema::hasTable('bap')) {
            $bapQuery = BAP::query()
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('user_id', function ($sub) use ($travelIds) {
                    $sub->select('id')->from('users')->whereIn('travel_id', $travelIds);
                });

            $bapStats = (clone $bapQuery)
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
                ->first();
        }

        return [
            ...$typeCounts,
            'total_cabang' => Schema::hasTable('travel_cabang')
                ? CabangTravel::query()
                    ->when($kabupaten, fn ($q) => $q->where('kabupaten', $kabupaten))
                    ->count()
                : 0,
            'total_jamaah' => (int) ($jamaahStats->total ?? 0),
            'total_jamaah_umrah' => (int) ($jamaahStats->total_umrah ?? 0),
            'total_jamaah_haji_khusus' => Schema::hasTable('jamaah_haji_khusus')
                ? JamaahHajiKhusus::query()
                    ->whereIn('travel_id', $travelIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count()
                : 0,
            'total_bap' => (int) ($bapStats->total ?? 0),
            'bap_pending' => (int) ($bapStats->pending ?? 0),
            'pengawasan_berjalan' => self::runningInspectionsCount($travelIds, $start, $end),
            'temuan_aktif' => self::activeFindingsCount($travelIds),
            'total_pengaduan' => Schema::hasTable('pengaduan')
                ? Pengaduan::query()
                    ->whereIn('travels_id', $travelIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count()
                : 0,
            'travel_risiko_tinggi' => self::highRiskCount($travelIds),
        ];
    }

    public static function travelScopeBuilder(?string $kabupaten = null, ?int $travelId = null): Builder
    {
        return TravelCompany::query()
            ->when($kabupaten, fn ($q) => $q->where('kab_kota', $kabupaten))
            ->when($travelId, fn ($q) => $q->where('id', $travelId))
            ->select('id');
    }

    /**
     * @return array<string, int>
     */
    public static function monitoringSummary(?string $kabupaten = null, ?int $travelId = null): array
    {
        $travelScope = self::travelScopeBuilder($kabupaten, $travelId);

        $travelStats = TravelCompany::query()
            ->when($kabupaten, fn ($q) => $q->where('kab_kota', $kabupaten))
            ->when($travelId, fn ($q) => $q->where('id', $travelId))
            ->selectRaw('COUNT(*) as total_travel')
            ->selectRaw("SUM(CASE WHEN Status = 'PPIU' THEN 1 ELSE 0 END) as total_ppiu")
            ->selectRaw("SUM(CASE WHEN Status = 'PIHK' THEN 1 ELSE 0 END) as total_pihk")
            ->first();

        $jamaahStats = (object) ['total' => 0, 'total_haji_khusus' => 0];
        if (Schema::hasTable('jamaah')) {
            $jamaahStats = Jamaah::query()
                ->whereIn('travel_id', $travelScope)
                ->selectRaw('COUNT(*) as total')
                ->first();
        }

        if (Schema::hasTable('jamaah_haji_khusus')) {
            $jamaahStats->total_haji_khusus = JamaahHajiKhusus::query()
                ->whereIn('travel_id', $travelScope)
                ->count();
        }

        return [
            'total_travel' => (int) ($travelStats->total_travel ?? 0),
            'total_ppiu' => (int) ($travelStats->total_ppiu ?? 0),
            'total_pihk' => (int) ($travelStats->total_pihk ?? 0),
            'total_cabang' => Schema::hasTable('travel_cabang')
                ? CabangTravel::query()
                    ->when($kabupaten, fn ($q) => $q->where('kabupaten', $kabupaten))
                    ->count()
                : 0,
            'total_jamaah' => (int) ($jamaahStats->total ?? 0),
            'total_jamaah_haji_khusus' => (int) ($jamaahStats->total_haji_khusus ?? 0),
            'total_pengaduan' => Schema::hasTable('pengaduan')
                ? Pengaduan::query()->whereIn('travels_id', $travelScope)->count()
                : 0,
            'pengawasan_berjalan' => self::runningInspectionsCount($travelScope),
            'temuan_aktif' => self::activeFindingsCount($travelScope),
            'travel_risiko_tinggi' => self::highRiskCount($travelScope),
        ];
    }

    /** @return array<string, int> */
    private static function emptyDashboardPeriodCounts(): array
    {
        return [
            'total_ppiu' => 0,
            'total_pihk' => 0,
            'total_cabang' => 0,
            'total_jamaah' => 0,
            'total_jamaah_umrah' => 0,
            'total_jamaah_haji_khusus' => 0,
            'total_bap' => 0,
            'bap_pending' => 0,
            'pengawasan_berjalan' => 0,
            'temuan_aktif' => 0,
            'total_pengaduan' => 0,
            'travel_risiko_tinggi' => 0,
        ];
    }
}
