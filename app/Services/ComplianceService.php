<?php

namespace App\Services;

use App\Models\TravelCompany;
use App\Repositories\ComplianceRepository;
use App\Support\TravelMetrics;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ComplianceService
{
    private const CACHE_TTL_SECONDS = 300;

    public function __construct(
        private readonly ComplianceRepository $complianceRepository,
    ) {
    }

    public function getProfile(int $travelId): array
    {
        $cacheKey = "compliance.profile.{$travelId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($travelId) {
            $travel = $this->complianceRepository->findTravel($travelId);

            if (! $travel) {
                return [];
            }

            return [
                'travel' => $travel,
                'statistics' => $this->complianceRepository->getStatistics($travel),
                'inspection_history' => $this->complianceRepository->getInspectionHistory($travelId),
                'recommendations' => $this->buildRecommendations($travel),
            ];
        });
    }

    /**
     * Ringan untuk halaman publik — tanpa riwayat inspeksi & rekomendasi internal.
     *
     * @return array{travel?: TravelCompany, statistics?: array<string, mixed>}
     */
    public function getPublicProfile(int $travelId, ?TravelCompany $travel = null): array
    {
        $cacheKey = "compliance.public-profile.{$travelId}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($travelId, $travel) {
            if ($travel === null || $travel->id !== $travelId) {
                $travel = $this->complianceRepository->findTravel($travelId);
            } elseif (! $travel->relationLoaded('riskScore')) {
                $travel->load('riskScore');
            }

            if (! $travel) {
                return [];
            }

            return [
                'travel' => $travel,
                'statistics' => $this->complianceRepository->getStatistics($travel),
            ];
        });
    }

    /** @return array<int, string> */
    private function buildRecommendations(TravelCompany $travel): array
    {
        $recommendations = [];

        if ($travel->isLicenseExpired()) {
            $recommendations[] = 'Segera perpanjang izin operasional travel.';
        }

        if ($travel->riskScore && in_array($travel->riskScore->risk_level?->value ?? $travel->riskScore->risk_level, ['HIGH', 'CRITICAL'], true)) {
            $recommendations[] = 'Travel berada pada kategori risiko tinggi, perlu pengawasan intensif.';
        }

        $activeFindings = TravelMetrics::activeFindingsCount(
            Collection::make([$travel->id])
        );

        if ($activeFindings > 0) {
            $recommendations[] = "Terdapat {$activeFindings} temuan aktif yang perlu ditindaklanjuti.";
        }

        return $recommendations;
    }
}
