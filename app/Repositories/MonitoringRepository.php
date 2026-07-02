<?php

namespace App\Repositories;

use App\Models\TravelCompany;
use App\Support\TravelMetrics;

class MonitoringRepository
{
    public function getKpiSummary(?string $kabupaten = null, ?int $travelId = null): array
    {
        return TravelMetrics::monitoringSummary($kabupaten, $travelId);
    }

    public function getTravelMonitoringList(?string $kabupaten = null, int $perPage = 15, ?int $travelId = null)
    {
        return TravelCompany::query()
            ->with(['riskScore'])
            ->withCount(['inspections', 'pengaduan'])
            ->when($kabupaten, fn ($q) => $q->where('kab_kota', $kabupaten))
            ->when($travelId, fn ($q) => $q->where('id', $travelId))
            ->orderBy('Penyelenggara')
            ->paginate($perPage);
    }
}
