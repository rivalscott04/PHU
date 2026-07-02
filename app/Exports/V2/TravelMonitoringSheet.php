<?php

namespace App\Exports\V2;

use Maatwebsite\Excel\Concerns\WithTitle;

class TravelMonitoringSheet extends TravelMonitoringExport implements WithTitle
{
    public function title(): string
    {
        return 'Daftar Travel';
    }
}
