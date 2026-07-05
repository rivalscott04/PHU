<?php

namespace App\Repositories;

use App\Models\Pengaduan;
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

    /** @return list<array<string, mixed>> */
    public function getTravelPengaduanList(TravelCompany $travel): array
    {
        return $travel->pengaduan()
            ->with('processedBy:id,nama,role,kabupaten')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Pengaduan $pengaduan) => [
                'id' => $pengaduan->id,
                'nama_pengadu' => $pengaduan->nama_pengadu,
                'hal_aduan' => $pengaduan->hal_aduan,
                'status' => $pengaduan->status,
                'status_label' => $pengaduan->getStatusLabel(),
                'status_badge' => match ($pengaduan->status) {
                    'pending' => 'warning',
                    'in_progress' => 'info',
                    'completed' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary',
                },
                'admin_notes' => $pengaduan->admin_notes,
                'has_berkas' => (bool) $pengaduan->berkas_aduan,
                'created_at' => $pengaduan->created_at?->format('d/m/Y H:i'),
                'completed_at' => $pengaduan->completed_at?->format('d/m/Y H:i'),
                'processed_by' => $pengaduan->processedBy?->pengaduanHandlerLabel(),
            ])
            ->all();
    }
}
