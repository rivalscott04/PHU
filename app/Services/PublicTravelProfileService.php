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
            'inspection_count' => (int) ($statistics['total_pengawasan'] ?? 0),
            'complaint_count' => (int) ($statistics['total_pengaduan'] ?? 0),
            'jamaah_count' => (int) ($statistics['total_jamaah'] ?? 0),
        ];
    }
}
