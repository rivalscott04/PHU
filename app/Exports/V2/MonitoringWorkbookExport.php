<?php

namespace App\Exports\V2;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MonitoringWorkbookExport implements WithMultipleSheets
{
    public function __construct(
        private readonly array $kpi,
        private readonly ?string $kabupaten = null,
        private readonly ?int $travelId = null,
    ) {
    }

    public function sheets(): array
    {
        return [
            new MonitoringSummarySheet($this->kpi),
            new TravelMonitoringSheet($this->kabupaten, $this->travelId),
        ];
    }
}
