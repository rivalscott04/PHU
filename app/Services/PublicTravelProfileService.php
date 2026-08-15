<?php

namespace App\Services;

use App\Models\TravelCompany;
use App\Support\PublicTrustIndex;

class PublicTravelProfileService
{
    public function __construct(
        private readonly ComplianceService $complianceService,
    ) {
    }

    /** @return array<string, mixed> */
    public function getProfile(TravelCompany|int $travelOrId): array
    {
        $travel = $travelOrId instanceof TravelCompany ? $travelOrId : null;
        $travelId = $travel?->id ?? $travelOrId;

        $profile = $this->complianceService->getPublicProfile($travelId, $travel);

        if ($profile === []) {
            return [];
        }

        /** @var TravelCompany $travel */
        $travel = $profile['travel'];
        $statistics = $profile['statistics'];
        $trust = PublicTrustIndex::fromRiskScore($statistics['risk_score'] ?? null);

        return [
            'travel' => $travel,
            'trust' => $trust,
            'signals' => PublicTrustIndex::buildPublicSignals($travel, $statistics),
            'cabang' => $this->approvedCabang($travel),
            'inspection_count' => (int) ($statistics['total_pengawasan'] ?? 0),
            'complaint_count' => (int) ($statistics['total_pengaduan'] ?? 0),
            'jamaah_count' => (int) ($statistics['total_jamaah'] ?? 0),
        ];
    }

    /**
     * Hanya cabang yang sudah disetujui Kanwil yang boleh tampil di publik.
     * Kolomnya dipilih satu per satu, bukan select all, supaya dokumen
     * pendaftaran dan catatan verifikasi internal tidak ikut terbawa keluar.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\CabangTravel>
     */
    private function approvedCabang(TravelCompany $travel)
    {
        return $travel->cabang()
            ->approved()
            ->orderBy('kabupaten')
            ->orderBy('Penyelenggara')
            ->get([
                'id_cabang',
                'travel_id',
                'Penyelenggara',
                'kabupaten',
                'alamat_cabang',
                'telepon',
                'pimpinan_cabang',
            ]);
    }
}
