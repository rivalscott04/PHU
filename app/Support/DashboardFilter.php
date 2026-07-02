<?php

namespace App\Support;

use Illuminate\Http\Request;

class DashboardFilter
{
    public function __construct(
        public readonly ?string $kabupaten = null,
        public readonly ?int $tahun = null,
        public readonly ?int $bulan = null,
        public readonly ?string $jenisTravel = null,
        public readonly ?string $riskLevel = null,
        public readonly ?int $travelId = null,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $scope = RequestScope::fromRequest($request);

        return new self(
            kabupaten: $scope->kabupaten,
            tahun: $request->filled('tahun') ? (int) $request->get('tahun') : null,
            bulan: $request->filled('bulan') ? (int) $request->get('bulan') : null,
            jenisTravel: $request->get('jenis_travel'),
            riskLevel: $request->get('risk_level'),
            travelId: $scope->travelId,
        );
    }

    public function cacheKey(string $suffix): string
    {
        return 'dashboard.'.md5(json_encode([
            $suffix,
            $this->kabupaten,
            $this->tahun,
            $this->bulan,
            $this->jenisTravel,
            $this->riskLevel,
            $this->travelId,
        ]));
    }
}
