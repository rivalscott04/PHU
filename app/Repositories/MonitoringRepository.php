<?php

namespace App\Repositories;

use App\Models\BAP;
use App\Models\Pengaduan;
use App\Models\TravelCompany;
use App\Models\User;
use App\Support\TravelMetrics;
use Illuminate\Support\Facades\Schema;

class MonitoringRepository
{
    public function getKpiSummary(?string $kabupaten = null, ?int $travelId = null): array
    {
        return TravelMetrics::monitoringSummary($kabupaten, $travelId);
    }

    public function getTravelMonitoringList(
        ?string $kabupaten = null,
        int $perPage = 15,
        ?int $travelId = null,
        ?string $jenisTravel = null,
        ?string $riskLevel = null,
        ?string $sort = null,
    ) {
        $query = TravelCompany::query()
            ->with(['riskScore'])
            ->withCount(['inspections', 'pengaduan'])
            ->withMax('inspections as last_inspection_at', 'created_at');

        if (Schema::hasTable('bap')) {
            $query->addSelect([
                'bap_pending_count' => BAP::query()
                    ->selectRaw('count(*)')
                    ->whereIn('status', ['diajukan', 'diproses', 'pending'])
                    ->whereIn('user_id', User::query()
                        ->select('id')
                        ->whereColumn('travel_id', 'travels.id')),
            ]);
        }

        $query
            ->when($kabupaten, fn ($q) => $q->where('kab_kota', $kabupaten))
            ->when($travelId, fn ($q) => $q->where('id', $travelId))
            ->when($jenisTravel, fn ($q) => $q->where('Status', $jenisTravel))
            ->when($riskLevel, fn ($q) => $q->whereHas(
                'riskScore',
                fn ($risk) => $risk->where('risk_level', $riskLevel)
            ));

        match ($sort) {
            'risk' => $query->orderByRaw(
                "(SELECT FIELD(risk_level, 'CRITICAL', 'HIGH', 'MEDIUM', 'LOW') FROM risk_scores WHERE risk_scores.travel_id = travels.id LIMIT 1) ASC"
            )->orderBy('Penyelenggara'),
            'pengaduan' => $query->orderByDesc('pengaduan_count')->orderBy('Penyelenggara'),
            'bap_pending' => $query->orderByDesc('bap_pending_count')->orderBy('Penyelenggara'),
            'inspection' => $query->orderBy('last_inspection_at')->orderBy('Penyelenggara'),
            default => $query->orderBy('Penyelenggara'),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    /** @return list<array<string, mixed>> */
    public function getTravelPengaduanList(TravelCompany $travel): array
    {
        return $travel->pengaduan()
            ->with('processedBy:id,nama,role,kabupaten')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Pengaduan $pengaduan) => $this->formatPengaduanItem($pengaduan))
            ->all();
    }

    /** @return list<array<string, mixed>> */
    public function getKabupatenPengaduanList(string $kabupaten): array
    {
        return Pengaduan::query()
            ->whereHas('travel', fn ($query) => $query->where('kab_kota', $kabupaten))
            ->with([
                'travel:id,Penyelenggara,kab_kota',
                'processedBy:id,nama,role,kabupaten',
            ])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Pengaduan $pengaduan) => $this->formatPengaduanItem($pengaduan, includeTravel: true))
            ->all();
    }

    /** @return array<string, mixed> */
    private function formatPengaduanItem(Pengaduan $pengaduan, bool $includeTravel = false): array
    {
        $item = [
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
        ];

        if ($includeTravel) {
            $item['travel_name'] = $pengaduan->travel?->Penyelenggara;
        }

        return $item;
    }
}
