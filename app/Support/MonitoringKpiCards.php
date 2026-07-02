<?php

namespace App\Support;

class MonitoringKpiCards
{
    /** @return array<string, array{label: string, icon: string, color: string}> */
    public static function definitions(): array
    {
        return [
            'total_travel' => ['label' => 'Total Travel', 'icon' => 'bx-buildings', 'color' => '#556ee6'],
            'total_ppiu' => ['label' => 'Total PPIU', 'icon' => 'bx-map', 'color' => '#34c38f'],
            'total_pihk' => ['label' => 'Total PIHK', 'icon' => 'bx-map-alt', 'color' => '#50a5f1'],
            'total_cabang' => ['label' => 'Total Cabang', 'icon' => 'bx-git-branch', 'color' => '#74788d'],
            'total_jamaah' => ['label' => 'Total Jamaah', 'icon' => 'bx-group', 'color' => '#556ee6'],
            'total_jamaah_haji_khusus' => ['label' => 'Jamaah Haji Khusus', 'icon' => 'bx-user-pin', 'color' => '#f1b44c'],
            'total_pengaduan' => ['label' => 'Total Pengaduan', 'icon' => 'bx-message-square-dots', 'color' => '#f46a6a'],
            'pengawasan_berjalan' => ['label' => 'Pengawasan Berjalan', 'icon' => 'bx-search-alt', 'color' => '#556ee6'],
            'temuan_aktif' => ['label' => 'Temuan Aktif', 'icon' => 'bx-error-circle', 'color' => '#f46a6a'],
            'travel_risiko_tinggi' => ['label' => 'Travel Risiko Tinggi', 'icon' => 'bx-shield-quarter', 'color' => '#f1b44c'],
        ];
    }

    /** @param array<string, int|float> $summary */
    public static function format(array $summary): array
    {
        $cards = [];

        foreach (self::definitions() as $key => $definition) {
            $cards[$key] = [
                ...$definition,
                'value' => $summary[$key] ?? 0,
            ];
        }

        return $cards;
    }
}
