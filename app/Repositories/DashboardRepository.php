<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Support\AuditLogNarrator;
use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\InspectionStatus;
use App\Enums\RiskLevel;
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
            'total_bap' => 'Total BA Pemberangkatan',
            'bap_pending' => 'BA Pemberangkatan Pending',
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
            'risk_distribution' => $this->labelRiskDistribution(
                $this->getRiskDistribution($filter->kabupaten, $filter->travelId)
            ),
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
                    'message' => "{$staleBap} BA Pemberangkatan pending lebih dari 7 hari.",
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
                'message' => "{$highRisk} travel memiliki risk score tinggi (≥80).",
            ];
        }

        return $warnings;
    }

    /** @return array<string, mixed> */
    public function getExecutiveInsights(DashboardFilter $filter): array
    {
        $completion = $this->getCompletionRates($filter);

        return [
            'completion_rates' => $completion,
            'intervention_priorities' => $this->getInterventionPriorities($filter),
            'kabupaten_scorecard' => $this->getKabupatenScorecard($filter),
            'coverage_gaps' => $this->getCoverageGaps($filter),
        ];
    }

    /** @return array<string, array{total: int, selesai?: int, pending?: int, percent: float, label: string}> */
    public function getCompletionRates(DashboardFilter $filter): array
    {
        $travelIds = $this->travelIdsFor($filter);
        $closedStatuses = [FindingStatus::Closed->value, FindingStatus::Verified->value];

        $temuanTotal = InspectionFinding::query()
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->whereIn('pengawasan.travel_id', $travelIds)
            ->count();

        $temuanSelesai = InspectionFinding::query()
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->whereIn('pengawasan.travel_id', $travelIds)
            ->whereIn('pengawasan_temuan.status', $closedStatuses)
            ->count();

        $pengaduanTotal = 0;
        $pengaduanSelesai = 0;
        if (Schema::hasTable('pengaduan')) {
            $pengaduanTotal = Pengaduan::query()
                ->whereIn('travels_id', $travelIds)
                ->count();
            $pengaduanSelesai = Pengaduan::query()
                ->whereIn('travels_id', $travelIds)
                ->where('status', 'completed')
                ->count();
        }

        $bapTotal = 0;
        $bapDisetujui = 0;
        if (Schema::hasTable('bap')) {
            $bapQuery = BAP::query()
                ->whereIn('user_id', function ($sub) use ($travelIds) {
                    $sub->select('id')->from('users')->whereIn('travel_id', $travelIds);
                });
            $bapTotal = (clone $bapQuery)->count();
            $bapDisetujui = (clone $bapQuery)->where('status', 'diterima')->count();
        }

        $pengawasanTotal = Inspection::query()
            ->whereIn('travel_id', $travelIds)
            ->where('status', '!=', InspectionStatus::Cancelled->value)
            ->count();

        $pengawasanSelesai = Inspection::query()
            ->whereIn('travel_id', $travelIds)
            ->whereIn('status', [InspectionStatus::Verified->value, InspectionStatus::Closed->value])
            ->count();

        return [
            'temuan' => [
                'label' => 'Penyelesaian Temuan',
                'total' => $temuanTotal,
                'selesai' => $temuanSelesai,
                'percent' => $this->completionPercent($temuanSelesai, $temuanTotal),
            ],
            'pengaduan' => [
                'label' => 'Penyelesaian Pengaduan',
                'total' => $pengaduanTotal,
                'selesai' => $pengaduanSelesai,
                'percent' => $this->completionPercent($pengaduanSelesai, $pengaduanTotal),
            ],
            'ba_pemberangkatan' => [
                'label' => 'BA Pemberangkatan Disetujui',
                'total' => $bapTotal,
                'selesai' => $bapDisetujui,
                'percent' => $this->completionPercent($bapDisetujui, $bapTotal),
            ],
            'pengawasan' => [
                'label' => 'Penyelesaian Pengawasan',
                'total' => $pengawasanTotal,
                'selesai' => $pengawasanSelesai,
                'percent' => $this->completionPercent($pengawasanSelesai, $pengawasanTotal),
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    public function getInterventionPriorities(DashboardFilter $filter, int $limit = 15): array
    {
        $priorities = [];
        $travelQuery = $this->travelQuery($filter);

        (clone $travelQuery)
            ->whereNotNull('license_expiry')
            ->whereDate('license_expiry', '<=', now()->addDays(30))
            ->orderBy('license_expiry')
            ->limit(8)
            ->get(['id', 'Penyelenggara', 'kab_kota', 'license_expiry'])
            ->each(function (TravelCompany $travel) use (&$priorities) {
                $days = (int) now()->startOfDay()->diffInDays($travel->license_expiry, false);
                $urgency = $days <= 7 ? 'critical' : ($days <= 14 ? 'high' : 'medium');

                $priorities[] = [
                    'travel' => $travel->Penyelenggara,
                    'travel_id' => $travel->id,
                    'kabupaten' => $travel->kab_kota,
                    'issue' => $days < 0
                        ? 'Izin/sertifikat sudah habis '.abs($days).' hari lalu'
                        : "Izin/sertifikat habis dalam {$days} hari",
                    'urgency' => $days < 0 ? 'critical' : $urgency,
                    'category' => 'sertifikat',
                ];
            });

        RiskScore::query()
            ->join('travels', 'travels.id', '=', 'risk_scores.travel_id')
            ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                $filter->applyTravelKabKota($q, 'travels.kab_kota');
            })
            ->when($filter->travelId, fn ($q) => $q->where('risk_scores.travel_id', $filter->travelId))
            ->where(function ($q) {
                $q->where('risk_scores.total_score', '>=', 80)
                    ->orWhereIn('risk_scores.risk_level', [RiskLevel::High->value, RiskLevel::Critical->value]);
            })
            ->select('risk_scores.*', 'travels.Penyelenggara as travel_name', 'travels.kab_kota')
            ->orderByDesc('risk_scores.total_score')
            ->limit(8)
            ->get()
            ->each(function ($risk) use (&$priorities) {
                $level = RiskLevel::tryFrom($risk->risk_level?->value ?? $risk->risk_level);
                $priorities[] = [
                    'travel' => $risk->travel_name,
                    'travel_id' => $risk->travel_id,
                    'kabupaten' => $risk->kab_kota,
                    'issue' => 'Skor risiko '.(int) $risk->total_score.' ('.($level?->label() ?? 'Tinggi').')',
                    'urgency' => ($risk->risk_level?->value ?? $risk->risk_level) === RiskLevel::Critical->value ? 'critical' : 'high',
                    'category' => 'risiko',
                ];
            });

        InspectionFinding::query()
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->join('travels', 'travels.id', '=', 'pengawasan.travel_id')
            ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                $filter->applyTravelKabKota($q, 'travels.kab_kota');
            })
            ->whereIn('pengawasan_temuan.status', [
                FindingStatus::Open->value,
                FindingStatus::WaitingResponse->value,
                FindingStatus::RevisionRequired->value,
            ])
            ->whereIn('pengawasan_temuan.severity', [FindingSeverity::Major->value, FindingSeverity::Critical->value])
            ->selectRaw('travels.id as travel_id, travels.Penyelenggara as travel_name, travels.kab_kota, COUNT(*) as open_count')
            ->groupBy('travels.id', 'travels.Penyelenggara', 'travels.kab_kota')
            ->orderByDesc('open_count')
            ->limit(8)
            ->get()
            ->each(function ($row) use (&$priorities) {
                $priorities[] = [
                    'travel' => $row->travel_name,
                    'travel_id' => $row->travel_id,
                    'kabupaten' => $row->kab_kota,
                    'issue' => "{$row->open_count} temuan berat/sedang belum selesai",
                    'urgency' => 'high',
                    'category' => 'temuan',
                ];
            });

        if (Schema::hasTable('pengaduan')) {
            Pengaduan::query()
                ->join('travels', 'travels.id', '=', 'pengaduan.travels_id')
                ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                    $filter->applyTravelKabKota($q, 'travels.kab_kota');
                })
                ->whereIn('pengaduan.status', ['pending', 'in_progress'])
                ->selectRaw('travels.id as travel_id, travels.Penyelenggara as travel_name, travels.kab_kota, COUNT(*) as open_count')
                ->groupBy('travels.id', 'travels.Penyelenggara', 'travels.kab_kota')
                ->having('open_count', '>=', 2)
                ->orderByDesc('open_count')
                ->limit(5)
                ->get()
                ->each(function ($row) use (&$priorities) {
                    $priorities[] = [
                        'travel' => $row->travel_name,
                        'travel_id' => $row->travel_id,
                        'kabupaten' => $row->kab_kota,
                        'issue' => "{$row->open_count} pengaduan belum selesai",
                        'urgency' => 'medium',
                        'category' => 'pengaduan',
                    ];
                });
        }

        $urgencyOrder = ['critical' => 0, 'high' => 1, 'medium' => 2];
        usort($priorities, function (array $a, array $b) use ($urgencyOrder) {
            $left = $urgencyOrder[$a['urgency'] ?? 'medium'] ?? 3;
            $right = $urgencyOrder[$b['urgency'] ?? 'medium'] ?? 3;

            return $left <=> $right;
        });

        $seen = [];
        $deduped = [];
        foreach ($priorities as $item) {
            $key = ($item['travel_id'] ?? 0).'|'.($item['category'] ?? '');
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $deduped[] = $item;
        }

        return array_slice($deduped, 0, $limit);
    }

    /** @return list<array<string, mixed>> */
    public function getKabupatenScorecard(DashboardFilter $filter): array
    {
        $kabupatens = $this->getKabupatenOptions();
        $period = $this->resolvePeriod($filter);
        $rows = [];

        foreach ($kabupatens as $kabupaten) {
            if (! $filter->matchesKabupaten($kabupaten)) {
                continue;
            }

            $scopedFilter = new DashboardFilter(
                kabupaten: $kabupaten,
                tahun: $filter->tahun,
                bulan: $filter->bulan,
                jenisTravel: $filter->jenisTravel,
                riskLevel: $filter->riskLevel,
                travelId: $filter->travelId,
                kabupatens: $filter->kabupatens,
            );

            $travelIds = $this->travelIdsFor($scopedFilter);
            if ($travelIds->isEmpty()) {
                continue;
            }

            $totalTravel = $travelIds->count();
            $pengawasan = Inspection::query()
                ->whereIn('travel_id', $travelIds)
                ->whereBetween('created_at', [$period['start'], $period['end']])
                ->count();

            $temuanAktif = InspectionFinding::query()
                ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
                ->whereIn('pengawasan.travel_id', $travelIds)
                ->whereNotIn('pengawasan_temuan.status', [FindingStatus::Closed->value, FindingStatus::Verified->value])
                ->count();

            $pengaduan = Schema::hasTable('pengaduan')
                ? Pengaduan::query()->whereIn('travels_id', $travelIds)->count()
                : 0;

            $avgRisk = (float) RiskScore::query()
                ->whereIn('travel_id', $travelIds)
                ->avg('total_score');

            $bapPending = 0;
            if (Schema::hasTable('bap')) {
                $bapPending = BAP::query()
                    ->where('status', 'pending')
                    ->whereIn('user_id', function ($sub) use ($travelIds) {
                        $sub->select('id')->from('users')->whereIn('travel_id', $travelIds);
                    })
                    ->count();
            }

            $rows[] = [
                'kabupaten' => $kabupaten,
                'total_travel' => $totalTravel,
                'pengawasan' => $pengawasan,
                'temuan_aktif' => $temuanAktif,
                'pengaduan' => $pengaduan,
                'avg_risk' => round($avgRisk, 1),
                'bap_pending' => $bapPending,
            ];
        }

        usort($rows, fn (array $a, array $b) => $b['temuan_aktif'] <=> $a['temuan_aktif']
            ?: $b['pengaduan'] <=> $a['pengaduan']);

        return $rows;
    }

    /** @return list<array<string, mixed>> */
    public function getCoverageGaps(DashboardFilter $filter, int $limit = 12): array
    {
        $travelIds = $this->travelIdsFor($filter);
        $cutoff = now()->subMonths(12);

        $lastInspections = Inspection::query()
            ->whereIn('travel_id', $travelIds)
            ->selectRaw('travel_id, MAX(created_at) as last_inspection')
            ->groupBy('travel_id')
            ->pluck('last_inspection', 'travel_id');

        return TravelCompany::query()
            ->whereIn('id', $travelIds)
            ->get(['id', 'Penyelenggara', 'kab_kota'])
            ->map(function (TravelCompany $travel) use ($lastInspections, $cutoff) {
                $last = $lastInspections[$travel->id] ?? null;
                $lastCarbon = $last ? Carbon::parse($last) : null;
                $isGap = $lastCarbon === null || $lastCarbon->lt($cutoff);

                return [
                    'travel' => $travel->Penyelenggara,
                    'travel_id' => $travel->id,
                    'kabupaten' => $travel->kab_kota,
                    'last_inspection' => $lastCarbon?->format('d/m/Y'),
                    'months_ago' => $lastCarbon ? (int) $lastCarbon->diffInMonths(now()) : null,
                    'is_gap' => $isGap,
                ];
            })
            ->filter(fn (array $row) => $row['is_gap'])
            ->sortBy(fn (array $row) => $row['last_inspection'] ? ($row['months_ago'] ?? 0) : -1)
            ->take($limit)
            ->values()
            ->all();
    }

    private function completionPercent(int $done, int $total): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($done / $total) * 100, 1);
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
        $counts = Inspection::query()
            ->join('travels', 'travels.id', '=', 'pengawasan.travel_id')
            ->when($filter->hasKabupatenRestriction(), function ($q) use ($filter) {
                $filter->applyTravelKabKota($q, 'travels.kab_kota');
            })
            ->when($filter->travelId, fn ($q) => $q->where('pengawasan.travel_id', $filter->travelId))
            ->selectRaw('travels.kab_kota as kabupaten, COUNT(*) as total')
            ->groupBy('travels.kab_kota')
            ->pluck('total', 'kabupaten');

        $entries = [];

        foreach (\App\Support\NtbKabupatenMap::centroids() as $kabupaten => $coords) {
            if (! $filter->matchesKabupaten($kabupaten)) {
                continue;
            }

            $entries[] = [
                'kabupaten' => $kabupaten,
                'total' => (int) ($counts[$kabupaten] ?? 0),
            ];
        }

        return [
            'labels' => array_column($entries, 'kabupaten'),
            'series' => array_column($entries, 'total'),
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
            'labels' => array_map(fn (FindingSeverity $case) => $case->label(), FindingSeverity::cases()),
            'series' => array_map(
                fn (FindingSeverity $case) => (int) ($rows[$case->value] ?? 0),
                FindingSeverity::cases()
            ),
        ];
    }

    /** @param  array<string, int>  $raw */
    private function labelRiskDistribution(array $raw): array
    {
        $labeled = [];

        foreach ($raw as $level => $total) {
            $enum = RiskLevel::tryFrom($level);
            $labeled[$enum?->label() ?? $level] = $total;
        }

        return $labeled;
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
