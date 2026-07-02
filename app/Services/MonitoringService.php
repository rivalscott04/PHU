<?php

namespace App\Services;

use App\Repositories\MonitoringRepository;
use App\Support\DashboardCache;
use Illuminate\Support\Facades\Cache;

class MonitoringService
{
    private const CACHE_TTL_SECONDS = 300;

    public function __construct(
        private readonly MonitoringRepository $monitoringRepository,
    ) {
    }

    public function getKpiSummary(?string $kabupaten = null, ?int $travelId = null): array
    {
        $cacheKey = DashboardCache::monitoringKey($kabupaten, $travelId);

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($kabupaten, $travelId) {
            return $this->monitoringRepository->getKpiSummary($kabupaten, $travelId);
        });
    }

    public function getKpiCards(?string $kabupaten = null, ?int $travelId = null): array
    {
        return \App\Support\MonitoringKpiCards::format(
            $this->getKpiSummary($kabupaten, $travelId)
        );
    }

    public function getTravelList(?string $kabupaten = null, int $perPage = 15, ?int $travelId = null)
    {
        return $this->monitoringRepository->getTravelMonitoringList($kabupaten, $perPage, $travelId);
    }
}
