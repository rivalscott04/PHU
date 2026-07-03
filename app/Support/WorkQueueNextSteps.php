<?php

namespace App\Support;

use App\Enums\WorkQueueType;
use App\Models\SupervisionWorkQueue;
use App\Models\TravelCompany;

class WorkQueueNextSteps
{
    /**
     * @return array{
     *     title: string,
     *     hint: string,
     *     steps: list<string>,
     *     actions: list<array{label: string, url: string, style: string, icon: string}>
     * }
     */
    public static function forQueueItem(SupervisionWorkQueue $item): array
    {
        return match ($item->type) {
            WorkQueueType::VerifikasiFollowup => [
                'title' => 'Cara menyelesaikan',
                'hint' => 'Buka bukti perbaikan travel, lalu setujui atau minta revisi di halaman verifikasi.',
                'steps' => [
                    'Klik Buka untuk melihat lampiran dan keterangan travel',
                    'Periksa apakah bukti sesuai rekomendasi temuan',
                    'Klik Setujui atau Minta Revisi',
                    'Antrian selesai otomatis setelah disetujui',
                ],
                'actions' => [],
            ],
            WorkQueueType::RisikoTinggi => [
                'title' => 'Cara menyelesaikan',
                'hint' => 'Buka detail skor risiko, ambil tindakan sesuai rekomendasi, lalu kembali ke antrian dan klik Selesai.',
                'steps' => [
                    'Klik Buka untuk melihat skor dan indikator penyebab',
                    'Gunakan tombol tindakan di halaman risiko (pemeriksaan / kepatuhan)',
                    'Kembali ke Antrian Kerja dan klik Selesai',
                ],
                'actions' => $item->travel_id ? self::riskActions((int) $item->travel_id) : [],
            ],
            WorkQueueType::Pengaduan => [
                'title' => 'Cara menyelesaikan',
                'hint' => 'Buka detail pengaduan, tindaklanjuti keluhan, lalu tutup pengaduan hingga status Selesai.',
                'steps' => [
                    'Klik Buka untuk membaca isi pengaduan',
                    'Koordinasikan penyelesaian dengan travel terkait',
                    'Ubah status pengaduan menjadi Selesai',
                    'Antrian selesai otomatis saat pengaduan ditutup',
                ],
                'actions' => [],
            ],
            WorkQueueType::DeadlineTemuan => [
                'title' => 'Cara menyelesaikan',
                'hint' => 'Buka BA Pemeriksaan, pastikan temuan yang melewati deadline segera ditindaklanjuti.',
                'steps' => [
                    'Klik Buka untuk melihat temuan di BA Pemeriksaan',
                    'Koordinasikan perbaikan atau verifikasi tindak lanjut',
                    'Kembali ke Antrian Kerja dan klik Selesai',
                ],
                'actions' => [],
            ],
        };
    }

    /**
     * @return array{
     *     title: string,
     *     hint: string,
     *     steps: list<string>,
     *     actions: list<array{label: string, url: string, style: string, icon: string}>
     * }
     */
    public static function forRiskDetail(TravelCompany $travel, string $riskLevel): array
    {
        $level = strtoupper($riskLevel);

        $hint = match ($level) {
            'CRITICAL', 'HIGH' => 'Prioritas tinggi, segera jadwalkan pemeriksaan dan pantau kepatuhan travel ini.',
            'MEDIUM' => 'Masukkan ke monitoring intensif dan pertimbangkan pemeriksaan jika diperlukan.',
            default => 'Risiko masih terkendali. Pantau kepatuhan secara berkala.',
        };

        $steps = match ($level) {
            'CRITICAL', 'HIGH' => [
                'Baca rekomendasi dan breakdown indikator di bawah',
                'Jadwalkan BA Pemeriksaan untuk travel ini',
                'Pantau profil kepatuhan hingga risiko turun',
                'Kembali ke Antrian Kerja lalu klik Selesai',
            ],
            'MEDIUM' => [
                'Baca rekomendasi dan breakdown indikator',
                'Pantau kepatuhan travel di Profil Kepatuhan',
                'Jadwalkan pemeriksaan bila indikator memburuk',
                'Kembali ke Antrian Kerja lalu klik Selesai',
            ],
            default => [
                'Baca rekomendasi dan breakdown indikator',
                'Pantau kepatuhan secara berkala',
                'Kembali ke Antrian Kerja lalu klik Selesai',
            ],
        };

        return [
            'title' => 'Langkah Selanjutnya',
            'hint' => $hint,
            'steps' => $steps,
            'actions' => self::riskActions($travel->id, $level),
        ];
    }

    /**
     * @return list<array{label: string, url: string, style: string, icon: string}>
     */
    private static function riskActions(int $travelId, ?string $riskLevel = null): array
    {
        $level = strtoupper($riskLevel ?? '');
        $scheduleStyle = in_array($level, ['CRITICAL', 'HIGH'], true) ? 'primary' : 'outline-primary';

        return [
            [
                'label' => 'Jadwalkan Pemeriksaan',
                'url' => route('v2.pengawasan.create', ['travel_id' => $travelId]),
                'style' => $scheduleStyle,
                'icon' => 'bx-calendar-plus',
            ],
            [
                'label' => 'Profil Kepatuhan',
                'url' => route('v2.compliance.show', $travelId),
                'style' => 'outline-primary',
                'icon' => 'bx-shield-quarter',
            ],
            [
                'label' => 'Monitoring PPIU',
                'url' => route('v2.monitoring.index'),
                'style' => 'outline-secondary',
                'icon' => 'bx-radar',
            ],
            [
                'label' => 'Kembali ke Antrian',
                'url' => route('v2.antrian.index', ['type' => WorkQueueType::RisikoTinggi->value]),
                'style' => 'outline-success',
                'icon' => 'bx-list-check',
            ],
        ];
    }
}
