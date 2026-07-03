<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Support\AuditLogNarrator;
use App\Models\BAP;
use App\Models\Followup;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\Jamaah;
use App\Models\Pengaduan;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Support\DashboardFilter;
use App\Support\TravelMetrics;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardRepository
{
    private ?string $cachedTravelIdsKey = null;

    /** @var \Illuminate\Support\Collection<int, int>|null */
    private ?\Illuminate\Support\Collection $cachedTravelIds = null;

    public function __construct(
        private readonly MonitoringRepository $monitoringRepository,
        private readonly RiskRepository $riskRepository,
    ) {
    }

    public function getKpiStats(DashboardFilter $filter): array
    {
        $travelIds = $this->travelIdsFor($filter);
        $period = $this->resolvePeriod($filter);

        $current = TravelMetrics::dashboardPeriodCounts($travelIds, $period['start'], $period['end'], $filter->kabupaten);
        $previous = TravelMetrics::dashboardPeriodCounts($travelIds, $period['prev_start'], $period['prev_end'], $filter->kabupaten);

        $labels = [
            'total_ppiu' => 'Total PPIU',
            'total_pihk' => 'Total PIHK',
            'total_cabang' => 'Total Cabang',
            'total_jamaah' => 'Total Jamaah',
            'total_jamaah_umrah' => 'Jamaah Umrah',
            'total_jamaah_haji_khusus' => 'Jamaah Haji Khusus',
            'total_bap' => 'Total BAP',
            'bap_pending' => 'BAP Pending',
            'pengawasan_berjalan' => 'Pengawasan Berjalan',
            'temuan_aktif' => 'Temuan Aktif',
            'total_pengaduan' => 'Pengaduan',
            'travel_risiko_tinggi' => 'Travel Risiko Tinggi',
        ];

        $cards = [];
        foreach ($labels as $key => $label) {
            $value = $current[$key] ?? 0;
            $prev = $previous[$key] ?? 0;
            $cards[$key] = [
                'label' => $label,
                'value' => $value,
                'trend' => $this->calculateTrend($value, $prev),
                'direction' => $this->trendDirection($value, $prev),
            ];
        }

        return $cards;
    }

    public function getCharts(DashboardFilter $filter): array
    {
        $travelIds = $this->travelIdsFor($filter);
        $year = $filter->tahun ?? (int) now()->year;

        return [
            'jamaah_monthly' => $this->getJamaahMonthlyChart($travelIds, $year),
            'keberangkatan_monthly' => $this->getKeberangkatanMonthlyChart($travelIds, $year),
            'pengaduan_category' => $this->getPengaduanCategoryChart($travelIds),
            'pengawasan_kabupaten' => $this->getPengawasanKabupatenChart($filter),
            'risk_distribution' => $this->getRiskDistribution($filter->kabupaten, $filter->travelId),
            'temuan_severity' => $this->getTemuanSeverityChart($travelIds),
        ];
    }

    public function getRankings(DashboardFilter $filter): array
    {
        $travelIds = $this->travelIdsFor($filter);

        return [
            'risk' => $this->getRiskRankingFiltered($filter, 10),
            'jamaah' => $this->getTopTravelsByJamaah($travelIds, 10),
            'pengaduan' => $this->getTopTravelsByPengaduan($travelIds, 10),
            'pengawasan' => $this->getTopTravelsByPengawasan($travelIds, 10),
            'kabupaten' => $this->getTopKabupaten($filter, 10),
        ];
    }

    public function getTimeline(DashboardFilter $filter, int $limit = 15): array
    {
        $events = collect();
        $travelIds = $this->travelIdsFor($filter);

        Inspection::query()
            ->with('travel')
            ->whereIn('travel_id', $travelIds)
            ->latest()
            ->limit($limit)
            ->get()
            ->each(function (Inspection $inspection) use ($events) {
                $events->push([
                    'at' => $inspection->created_at,
                    'type' => 'pengawasan',
                    'title' => 'Pengawasan dibuat',
                    'description' => "{$inspection->travel?->Penyelenggara}, {$inspection->inspection_no}",
                    'url' => route('v2.pengawasan.show', $inspection),
                ]);
            });

        if (Schema::hasTable('pengawasan_followups')) {
            Followup::query()
                ->with(['finding.inspection.travel'])
                ->whereHas('finding.inspection', fn ($q) => $q->whereIn('travel_id', $travelIds))
                ->latest('submitted_at')
                ->limit($limit)
                ->get()
                ->each(function (Followup $followup) use ($events) {
                    $events->push([
                        'at' => $followup->submitted_at ?? $followup->created_at,
                        'type' => 'followup',
                        'title' => 'Tindak lanjut diunggah',
                        'description' => $followup->finding?->inspection?->travel?->Penyelenggara.', '.$followup->finding?->title,
                        'url' => route('v2.followup.show', $followup),
                    ]);
                });
        }

        if (Schema::hasTable('audit_logs')) {
            $narrator = app(AuditLogNarrator::class);

            AuditLog::query()
                ->with('user')
                ->whereIn('module', ['pengawasan', 'followup', 'risk'])
                ->latest('created_at')
                ->limit($limit)
                ->get()
                ->each(function (AuditLog $log) use ($events, $narrator) {
                    $narrative = $narrator->present($log);
                    $events->push([
                        'at' => $log->created_at,
                        'type' => $log->module,
                        'title' => $narrative['category'],
                        'description' => $narrative['summary'],
                        'url' => route('v2.audit-log.show', $log),
                    ]);
                });
        }

        return $events
            ->filter(fn ($e) => $e['at'] !== null)
            ->sortByDesc('at')
            ->take($limit)
            ->map(fn ($e) => [
                ...$e,
                'at' => Carbon::parse($e['at'])->toIso8601String(),
                'relative' => Carbon::parse($e['at'])->diffForHumans(),
            ])
            ->values()
            ->all();
    }

    public function getEarlyWarnings(DashboardFilter $filter): array
    {
        $warnings = [];
        $travelQuery = $this->travelQuery($filter);
        $travelIds = $this->travelIdsFor($filter);

        $expiringCerts = (clone $travelQuery)
            ->whereNotNull('license_expiry')
            ->whereDate('license_expiry', '<=', now()->addDays(30))
            ->count();

        if ($expiringCerts > 0) {
            $warnings[] = [
                'level' => 'critical',
                'icon' => '🔴',
                'message' => "{$expiringCerts} travel memiliki izin/sertifikat akan habis dalam 30 hari.",
            ];
        }

        if (Schema::hasTable('bap')) {
            $staleBap = BAP::query()
                ->where('status', 'pending')
                ->where('created_at', '<', now()->subDays(7))
                ->when($travelIds->isNotEmpty(), function ($q) use ($travelIds) {
                    $q->whereIn('user_id', function ($sub) use ($travelIds) {
                        $sub->select('id')->from('users')->whereIn('travel_id', $travelIds);
                    });
                })
                ->count();

            if ($staleBap > 0) {
                $warnings[] = [
                    'level' => 'warning',
                    'icon' => '🟠',
                    'message' => "{$staleBap} BAP pending lebih dari 7 hari.",
                ];
            }
        }

        $waitingFollowup = InspectionFinding::query()
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->whereIn('pengawasan.travel_id', $travelIds)
            ->whereIn('pengawasan_temuan.status', ['OPEN', 'WAITING_RESPONSE', 'REVISION_REQUIRED'])
            ->count();

        if ($waitingFollowup > 0) {
            $warnings[] = [
                'level' => 'caution',
                'icon' => '🟡',
                'message' => "{$waitingFollowup} temuan belum ditindaklanjuti travel.",
            ];
        }

        $highRisk = RiskScore::query()
            ->whereIn('travel_id', $travelIds)
            ->where(function ($q) {
                $q->where('total_score', '>=', 80)
                    ->orWhere('risk_level', 'CRITICAL');
            })
            ->count();

        if ($highRisk > 0) {
            $warnings[] = [
                'level' => 'critical',
                'icon' => '🔴',
                'message' => "{$highRisk} travel memiliki risk score tinggi (≥80).",
            ];
        }

        return $warnings;
    }

    public function getOverviewStats(?string $kabupaten = null): array
    {
        return $this->monitoringRepository->getKpiSummary($kabupaten);
    }

    public function getRiskRanking(int $limit = 5, ?string $kabupaten = null): Collection
    {
        return $this->riskRepository->getRanking($limit, $kabupaten);
    }

    public function getRecentInspections(int $limit = 10, ?string $kabupaten = null): Collection
    {
        return Inspection::query()
            ->with(['travel', 'creator'])
            ->when($kabupaten, fn ($q) => $q->whereHas('travel', fn ($travel) => $travel->where('kab_kota', $kabupaten)))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getActiveFindings(int $limit = 10, ?string $kabupaten = null): Collection
    {
        return InspectionFinding::query()
            ->with(['inspection.travel'])
            ->whereNotIn('status', ['CLOSED', 'VERIFIED'])
            ->when($kabupaten, fn ($q) => $q->whereHas('inspection.travel', fn ($travel) => $travel->where('kab_kota', $kabupaten)))
            ->orderBy('deadline')
            ->limit($limit)
            ->get();
    }

    public function getRiskDistribution(?string $kabupaten = null, ?int $travelId = null): array
    {
        return RiskScore::query()
            ->when($kabupaten, fn ($q) => $q->whereHas('travel', fn ($travel) => $travel->where('kab_kota', $kabupaten)))
            ->when($travelId, fn ($q) => $q->where('travel_id', $travelId))
            ->selectRaw('risk_level, COUNT(*) as total')
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level')
            ->toArray();
    }

    public function getKabupatenOptions(): array
    {
        return Cache::remember(
            \App\Support\DashboardCache::KABUPATEN_OPTIONS_KEY,
            300,
            fn () => TravelCompany::query()
                ->select('kab_kota')
                ->distinct()
                ->orderBy('kab_kota')
                ->pluck('kab_kota')
                ->all()
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function getKabupatenHeatmap(DashboardFilter $filter): array
    {
        $period = $this->resolvePeriod($filter);

        $travelCounts = $this->travelQuery($filter)
            ->selectRaw('kab_kota, COUNT(*) as total')
            ->groupBy('kab_kota')
            ->pluck('total', 'kab_kota');

        $pengawasanCounts = Inspection::query()
            ->join('travels', 'travels.id', '=', 'pengawasan.travel_id')
            ->when($filter->jenisTravel, fn ($q) => $q->where('travels.Status', $filter->jenisTravel))
            ->when($filter->travelId, fn ($q) => $q->where('pengawasan.travel_id', $filter->travelId))
            ->whereBetween('pengawasan.inspection_date', [$period['start'], $period['end']])
            ->selectRaw('travels.kab_kota as kabupaten, COUNT(*) as total')
            ->groupBy('travels.kab_kota')
            ->pluck('total', 'kabupaten');

        $temuanCounts = InspectionFinding::query()
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->join('travels', 'travels.id', '=', 'pengawasan.travel_id')
            ->when($filter->jenisTravel, fn ($q) => $q->where('travels.Status', $filter->jenisTravel))
            ->when($filter->travelId, fn ($q) => $q->where('pengawasan.travel_id', $filter->travelId))
            ->whereNotIn('pengawasan_temuan.status', ['CLOSED', 'VERIFIED'])
            ->selectRaw('travels.kab_kota as kabupaten, COUNT(*) as total')
            ->groupBy('travels.kab_kota')
            ->pluck('total', 'kabupaten');

        $riskAverages = RiskScore::query()
            ->join('travels', 'travels.id', '=', 'risk_scores.travel_id')
            ->when($filter->jenisTravel, fn ($q) => $q->where('travels.Status', $filter->jenisTravel))
            ->when($filter->travelId, fn ($q) => $q->where('risk_scores.travel_id', $filter->travelId))
            ->when($filter->riskLevel, fn ($q) => $q->where('risk_scores.risk_level', $filter->riskLevel))
            ->selectRaw('travels.kab_kota as kabupaten, ROUND(AVG(risk_scores.total_score), 1) as avg_score')
            ->groupBy('travels.kab_kota')
            ->pluck('avg_score', 'kabupaten');

        $regions = [];

        foreach (\App\Support\NtbKabupatenMap::centroids() as $kabupaten => $coords) {
            if (! $filter->matchesKabupaten($kabupaten)) {
                continue;
            }

            $pengawasan = (int) ($pengawasanCounts[$kabupaten] ?? 0);

            $regions[] = [
                'kabupaten' => $kabupaten,
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'travel' => (int) ($travelCounts[$kabupaten] ?? 0),
                'pengawasan' => $pengawasan,
                'temuan_aktif' => (int) ($temuanCounts[$kabupaten] ?? 0),
                'avg_risk' => (float) ($riskAverages[$kabupaten] ?? 0),
                'intensity' => $pengawasan,
            ];
        }

        return $regions;
    }

    private function travelQuery(DashboardFilter $filter): Builder
    {
        return TravelCompany::query()
            ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                $filter->applyTravelKabKota($q);
            })
            ->when($filter->jenisTravel, fn ($q) => $q->where('Status', $filter->jenisTravel))
            ->when($filter->travelId, fn ($q) => $q->where('id', $filter->travelId));
    }

    /** @return \Illuminate\Support\Collection<int, int> */
    private function travelIdsFor(DashboardFilter $filter): \Illuminate\Support\Collection
    {
        $key = $filter->cacheKey('travel_ids');

        if ($this->cachedTravelIdsKey === $key && $this->cachedTravelIds !== null) {
            return $this->cachedTravelIds;
        }

        $this->cachedTravelIdsKey = $key;
        $this->cachedTravelIds = $this->travelQuery($filter)->pluck('id');

        return $this->cachedTravelIds;
    }

    private function resolvePeriod(DashboardFilter $filter): array
    {
        $year = $filter->tahun ?? (int) now()->year;
        $month = $filter->bulan ?? (int) now()->month;

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $prevStart = $start->copy()->subMonth()->startOfMonth();
        $prevEnd = $start->copy()->subMonth()->endOfMonth();

        return [
            'start' => $start,
            'end' => $end,
            'prev_start' => $prevStart,
            'prev_end' => $prevEnd,
        ];
    }

    private function calculateTrend(int|float $current, int|float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function trendDirection(int|float $current, int|float $previous): string
    {
        if ($current > $previous) {
            return 'up';
        }
        if ($current < $previous) {
            return 'down';
        }

        return 'flat';
    }

    /** @param \Illuminate\Support\Collection<int, int> $travelIds */
    private function getJamaahMonthlyChart($travelIds, int $year): array
    {
        $labels = [];
        $series = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create($year, $m, 1)->format('M');
            $series[] = 0;
        }

        if (! Schema::hasTable('jamaah')) {
            return compact('labels', 'series');
        }

        $monthExpr = $this->monthExpression('created_at');

        $rows = Jamaah::query()
            ->selectRaw("{$monthExpr} as bulan, COUNT(*) as total")
            ->whereIn('travel_id', $travelIds)
            ->whereYear('created_at', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        for ($m = 1; $m <= 12; $m++) {
            $series[$m - 1] = (int) ($rows[$m] ?? 0);
        }

        return compact('labels', 'series');
    }

    /** @param \Illuminate\Support\Collection<int, int> $travelIds */
    private function getKeberangkatanMonthlyChart($travelIds, int $year): array
    {
        $labels = [];
        $series = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[] = Carbon::create($year, $m, 1)->format('M');
            $series[] = 0;
        }

        if (! Schema::hasTable('bap')) {
            return compact('labels', 'series');
        }

        $monthExpr = $this->monthExpression('created_at');

        $query = BAP::query()
            ->selectRaw("{$monthExpr} as bulan, COUNT(*) as total")
            ->whereYear('created_at', $year);

        if ($travelIds->isNotEmpty()) {
            $query->whereIn('user_id', function ($sub) use ($travelIds) {
                $sub->select('id')->from('users')->whereIn('travel_id', $travelIds);
            });
        }

        $rows = $query->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');

        for ($m = 1; $m <= 12; $m++) {
            $series[$m - 1] = (int) ($rows[$m] ?? 0);
        }

        return compact('labels', 'series');
    }

    /** @param \Illuminate\Support\Collection<int, int> $travelIds */
    private function getPengaduanCategoryChart($travelIds): array
    {
        if (! Schema::hasTable('pengaduan')) {
            return ['labels' => [], 'series' => []];
        }

        $statusLabels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
        ];

        $rows = Pengaduan::query()
            ->selectRaw('status, COUNT(*) as total')
            ->whereIn('travels_id', $travelIds)
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->map(fn ($row) => $statusLabels[$row->status] ?? $row->status)->all(),
            'series' => $rows->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    private function getPengawasanKabupatenChart(DashboardFilter $filter): array
    {
        $rows = Inspection::query()
            ->join('travels', 'travels.id', '=', 'pengawasan.travel_id')
            ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                $filter->applyTravelKabKota($q, 'travels.kab_kota');
            })
            ->when($filter->travelId, fn ($q) => $q->where('pengawasan.travel_id', $filter->travelId))
            ->selectRaw('travels.kab_kota as kabupaten, COUNT(*) as total')
            ->groupBy('travels.kab_kota')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'labels' => $rows->pluck('kabupaten')->all(),
            'series' => $rows->pluck('total')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    /** @param \Illuminate\Support\Collection<int, int> $travelIds */
    private function getTemuanSeverityChart($travelIds): array
    {
        $rows = InspectionFinding::query()
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->whereIn('pengawasan.travel_id', $travelIds)
            ->selectRaw('pengawasan_temuan.severity as severity, COUNT(*) as total')
            ->groupBy('pengawasan_temuan.severity')
            ->pluck('total', 'severity');

        return [
            'labels' => ['MINOR', 'MAJOR', 'CRITICAL'],
            'series' => [
                (int) ($rows['MINOR'] ?? 0),
                (int) ($rows['MAJOR'] ?? 0),
                (int) ($rows['CRITICAL'] ?? 0),
            ],
        ];
    }

    private function getRiskRankingFiltered(DashboardFilter $filter, int $limit): array
    {
        return RiskScore::query()
            ->join('travels', 'travels.id', '=', 'risk_scores.travel_id')
            ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                $filter->applyTravelKabKota($q, 'travels.kab_kota');
            })
            ->when($filter->travelId, fn ($q) => $q->where('risk_scores.travel_id', $filter->travelId))
            ->when($filter->riskLevel, fn ($q) => $q->where('risk_scores.risk_level', $filter->riskLevel))
            ->select('risk_scores.*', 'travels.Penyelenggara as travel_name')
            ->orderByDesc('risk_scores.total_score')
            ->limit($limit)
            ->get()
            ->map(fn ($risk) => [
                'travel' => $risk->travel_name,
                'travel_id' => $risk->travel_id,
                'total_score' => (float) $risk->total_score,
                'risk_level' => $risk->risk_level?->value ?? $risk->risk_level,
            ])
            ->all();
    }

    private function monthExpression(string $column): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', {$column}) AS INTEGER)"
            : "MONTH({$column})";
    }

    /** @param \Illuminate\Support\Collection<int, int> $travelIds */
    private function getTopTravelsByJamaah($travelIds, int $limit): array
    {
        if (! Schema::hasTable('jamaah')) {
            return [];
        }

        return Jamaah::query()
            ->join('travels', 'travels.id', '=', 'jamaah.travel_id')
            ->selectRaw('jamaah.travel_id, travels.Penyelenggara as travel_name, COUNT(*) as total')
            ->whereIn('jamaah.travel_id', $travelIds)
            ->groupBy('jamaah.travel_id', 'travels.Penyelenggara')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'travel' => $row->travel_name,
                'travel_id' => $row->travel_id,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    /** @param \Illuminate\Support\Collection<int, int> $travelIds */
    private function getTopTravelsByPengaduan($travelIds, int $limit): array
    {
        if (! Schema::hasTable('pengaduan')) {
            return [];
        }

        return Pengaduan::query()
            ->join('travels', 'travels.id', '=', 'pengaduan.travels_id')
            ->selectRaw('pengaduan.travels_id as travel_id, travels.Penyelenggara as travel_name, COUNT(*) as total')
            ->whereIn('pengaduan.travels_id', $travelIds)
            ->groupBy('pengaduan.travels_id', 'travels.Penyelenggara')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'travel' => $row->travel_name,
                'travel_id' => $row->travel_id,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    /** @param \Illuminate\Support\Collection<int, int> $travelIds */
    private function getTopTravelsByPengawasan($travelIds, int $limit): array
    {
        return Inspection::query()
            ->join('travels', 'travels.id', '=', 'pengawasan.travel_id')
            ->selectRaw('pengawasan.travel_id, travels.Penyelenggara as travel_name, COUNT(*) as total')
            ->whereIn('pengawasan.travel_id', $travelIds)
            ->groupBy('pengawasan.travel_id', 'travels.Penyelenggara')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'travel' => $row->travel_name,
                'travel_id' => $row->travel_id,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    private function getTopKabupaten(DashboardFilter $filter, int $limit): array
    {
        return Inspection::query()
            ->join('travels', 'travels.id', '=', 'pengawasan.travel_id')
            ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                $filter->applyTravelKabKota($q, 'travels.kab_kota');
            })
            ->selectRaw('travels.kab_kota as kabupaten, COUNT(*) as total')
            ->groupBy('travels.kab_kota')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'kabupaten' => $row->kabupaten,
                'total' => (int) $row->total,
            ])
            ->all();
    }
}
