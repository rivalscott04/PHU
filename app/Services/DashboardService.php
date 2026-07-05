<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use App\Support\DashboardExecutive;
use App\Support\DashboardFilter;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const CACHE_TTL_SECONDS = 300;

    public function __construct(
        private readonly DashboardRepository $dashboardRepository,
    ) {
    }

    public function getOverview(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('overview'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            return [
                'stats' => $this->dashboardRepository->getKpiStats($filter),
                'charts' => $this->dashboardRepository->getCharts($filter),
                'rankings' => $this->dashboardRepository->getRankings($filter),
                'timeline' => $this->dashboardRepository->getTimeline($filter),
                'warnings' => $this->dashboardRepository->getEarlyWarnings($filter),
                'heatmap' => $this->dashboardRepository->getKabupatenHeatmap($filter),
                'filters' => [
                    'kabupaten_options' => $this->dashboardRepository->getKabupatenOptions(),
                    'tahun' => $filter->tahun ?? (int) now()->year,
                    'bulan' => $filter->bulan ?? (int) now()->month,
                ],
            ];
        });
    }

    public function getStatistics(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('statistics'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            return $this->dashboardRepository->getKpiStats($filter);
        });
    }

    public function getCharts(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('charts'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            return $this->dashboardRepository->getCharts($filter);
        });
    }

    public function getRankings(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('rankings'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            return $this->dashboardRepository->getRankings($filter);
        });
    }

    public function getTimeline(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('timeline'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            return $this->dashboardRepository->getTimeline($filter);
        });
    }

    public function getWarnings(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('warnings'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            return $this->dashboardRepository->getEarlyWarnings($filter);
        });
    }

    public function getHeatmap(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('heatmap'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            return $this->dashboardRepository->getKabupatenHeatmap($filter);
        });
    }

    /** @return array<string, mixed> */
    public function getExecutive(DashboardFilter $filter): array
    {
        return Cache::remember($filter->cacheKey('executive_v4'), self::CACHE_TTL_SECONDS, function () use ($filter) {
            $stats = $this->dashboardRepository->getKpiStats($filter);
            $insights = $this->dashboardRepository->getExecutiveInsights($filter);
            $summary = DashboardExecutive::buildSummaryPoints(
                $stats,
                $insights['completion_rates'],
                $insights['intervention_priorities'],
                $filter,
                $insights['coverage_gaps'],
            );

            return [
                ...$insights,
                'summary' => $summary,
                'summary_text' => DashboardExecutive::summaryToPlainText($summary),
            ];
        });
    }
}
