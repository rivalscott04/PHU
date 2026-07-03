<?php

namespace App\Repositories;

use App\Models\BAP;
use App\Models\Followup;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\Jamaah;
use App\Models\Pengaduan;
use App\Models\Sertifikat;
use App\Models\TravelCompany;
use App\Support\SchemaTables;
use App\Support\TravelMetrics;
use Illuminate\Support\Facades\DB;

class ComplianceRepository
{
    public function __construct(
        private readonly RiskRepository $riskRepository,
        private readonly InspectionRepository $inspectionRepository,
    ) {
    }

    private function tableExists(string $table): bool
    {
        return SchemaTables::has($table);
    }

    public function findTravel(int $travelId): ?TravelCompany
    {
        return TravelCompany::with(['riskScore'])->find($travelId);
    }

    public function getStatistics(TravelCompany $travel): array
    {
        $travelId = $travel->id;
        $counts = $this->aggregateTravelCounts($travel);

        return [
            'total_jamaah' => $counts['total_jamaah'],
            'total_pengaduan' => $counts['total_pengaduan'],
            'total_bap' => $counts['total_bap'],
            'total_sertifikat' => $counts['total_sertifikat'],
            'total_pengawasan' => $counts['total_pengawasan'],
            'temuan_aktif' => $counts['temuan_aktif'],
            'risk_score' => $travel->relationLoaded('riskScore')
                ? $travel->riskScore
                : $this->riskRepository->findByTravelId($travelId),
            'travel_type' => $travel->Status,
            'kabupaten' => $travel->kab_kota,
        ];
    }

    public function getInspectionHistory(int $travelId, int $perPage = 10)
    {
        return $this->inspectionRepository->paginate([
            'travel_id' => $travelId,
        ], $perPage);
    }

    public function getFollowupSummary(int $travelId): array
    {
        return Followup::query()
            ->join('pengawasan_temuan', 'pengawasan_temuan.id', '=', 'pengawasan_followups.finding_id')
            ->join('pengawasan', 'pengawasan.id', '=', 'pengawasan_temuan.inspection_id')
            ->where('pengawasan.travel_id', $travelId)
            ->selectRaw('pengawasan_followups.status as status, COUNT(*) as total')
            ->groupBy('pengawasan_followups.status')
            ->pluck('total', 'status')
            ->map(fn ($count) => (int) $count)
            ->toArray();
    }

    /** @return array<string, int> */
    private function aggregateTravelCounts(TravelCompany $travel): array
    {
        $travelId = $travel->id;
        $counts = [
            'total_jamaah' => 0,
            'total_pengaduan' => 0,
            'total_bap' => 0,
            'total_sertifikat' => 0,
            'total_pengawasan' => 0,
            'temuan_aktif' => 0,
        ];

        if ($this->tableExists('jamaah') && $this->tableExists('pengaduan') && $this->tableExists('sertifikat')) {
            $row = DB::selectOne('
                SELECT
                    (SELECT COUNT(*) FROM jamaah WHERE travel_id = ?) AS total_jamaah,
                    (SELECT COUNT(*) FROM pengaduan WHERE travels_id = ?) AS total_pengaduan,
                    (SELECT COUNT(*) FROM sertifikat WHERE travel_id = ?) AS total_sertifikat
            ', [$travelId, $travelId, $travelId]);

            $counts['total_jamaah'] = (int) ($row->total_jamaah ?? 0);
            $counts['total_pengaduan'] = (int) ($row->total_pengaduan ?? 0);
            $counts['total_sertifikat'] = (int) ($row->total_sertifikat ?? 0);
        } else {
            if ($this->tableExists('jamaah')) {
                $counts['total_jamaah'] = Jamaah::where('travel_id', $travelId)->count();
            }
            if ($this->tableExists('pengaduan')) {
                $counts['total_pengaduan'] = Pengaduan::where('travels_id', $travelId)->count();
            }
            if ($this->tableExists('sertifikat')) {
                $counts['total_sertifikat'] = Sertifikat::where('travel_id', $travelId)->count();
            }
        }

        if ($this->tableExists('bap')) {
            $counts['total_bap'] = BAP::where('ppiuname', $travel->Penyelenggara)->count();
        }

        $inspectionStats = Inspection::query()
            ->where('travel_id', $travelId)
            ->selectRaw('COUNT(*) as total_pengawasan')
            ->first();

        $counts['total_pengawasan'] = (int) ($inspectionStats->total_pengawasan ?? 0);
        $counts['temuan_aktif'] = TravelMetrics::activeFindingsCount(
            collect([$travelId])
        );

        return $counts;
    }
}
