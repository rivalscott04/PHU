<?php

namespace App\Exports\V2;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonitoringSummarySheet implements FromArray, WithTitle
{
    /** @param array<string, int> $kpi */
    public function __construct(
        private readonly array $kpi,
    ) {
    }

    public function array(): array
    {
        $labels = [
            'total_travel' => 'Total Penyelenggara',
            'total_ppiu' => 'PPIU',
            'total_pihk' => 'PIHK',
            'total_cabang' => 'Cabang Travel',
            'total_jamaah' => 'Total Jamaah',
            'total_jamaah_haji_khusus' => 'Jamaah Haji Khusus',
            'total_pengaduan' => 'Pengaduan',
            'pengawasan_berjalan' => 'Pengawasan Berjalan',
            'temuan_aktif' => 'Temuan Aktif',
            'travel_risiko_tinggi' => 'Travel Berisiko Tinggi',
        ];

        $rows = [['Indikator', 'Nilai']];
        foreach ($labels as $key => $label) {
            $rows[] = [$label, $this->kpi[$key] ?? 0];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}
