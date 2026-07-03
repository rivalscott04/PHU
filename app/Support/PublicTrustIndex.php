<?php

namespace App\Support;

use App\Models\RiskScore;
use App\Models\TravelCompany;
use Carbon\Carbon;

class PublicTrustIndex
{
    /** @return array<string, mixed> */
    public static function fromRiskScore(?RiskScore $risk): array
    {
        if (! $risk || $risk->total_score === null) {
            return self::empty();
        }

        $score = max(0, min(100, (int) round(100 - (float) $risk->total_score)));

        return [
            'score' => $score,
            'label' => self::labelForScore($score),
            'short_label' => self::shortLabelForScore($score),
            'color' => self::colorForScore($score),
            'bg_class' => self::bgClassForScore($score),
            'stars' => self::starsForScore($score),
            'description' => self::descriptionForScore($score),
            'updated_at' => $risk->last_calculated_at,
            'has_data' => true,
        ];
    }

    /** @return array<string, mixed> */
    public static function empty(): array
    {
        return [
            'score' => null,
            'label' => 'Belum Tersedia',
            'short_label' => 'Belum ada data',
            'color' => '#94a3b8',
            'bg_class' => 'secondary',
            'stars' => 0,
            'description' => 'Indeks kepercayaan untuk travel ini belum tersedia.',
            'updated_at' => null,
            'has_data' => false,
        ];
    }

    /**
     * Sinyal positif yang aman ditampilkan ke masyarakat umum.
     *
     * @param  array<string, mixed>  $stats
     * @return array<int, array{icon: string, title: string, detail: string, tone: string}>
     */
    public static function buildPublicSignals(TravelCompany $travel, array $stats): array
    {
        $signals = [];

        if ($travel->getLicenseStatus() === 'Active') {
            $expiry = $travel->license_expiry
                ? 'Berlaku sampai '.$travel->license_expiry->translatedFormat('d F Y')
                : 'Izin operasional masih aktif';
            $signals[] = [
                'icon' => 'fa-shield-halved',
                'title' => 'Izin Operasional Aktif',
                'detail' => $expiry,
                'tone' => 'success',
            ];
        } elseif ($travel->getLicenseStatus() === 'Expired') {
            $signals[] = [
                'icon' => 'fa-triangle-exclamation',
                'title' => 'Izin Perlu Diperpanjang',
                'detail' => 'Periksa status izin terbaru sebelum memilih travel.',
                'tone' => 'warning',
            ];
        }

        if ($travel->nilai_akreditasi) {
            $signals[] = [
                'icon' => 'fa-award',
                'title' => 'Memiliki Akreditasi',
                'detail' => 'Nilai akreditasi: '.$travel->nilai_akreditasi,
                'tone' => 'success',
            ];
        }

        $inspectionCount = (int) ($stats['total_pengawasan'] ?? 0);
        if ($inspectionCount > 0) {
            $signals[] = [
                'icon' => 'fa-clipboard-check',
                'title' => 'Telah Diawasi Kanwil',
                'detail' => 'Tercatat '.$inspectionCount.' kali pengawasan resmi.',
                'tone' => 'info',
            ];
        }

        $complaintCount = (int) ($stats['total_pengaduan'] ?? 0);
        $signals[] = [
            'icon' => 'fa-comments',
            'title' => 'Pengaduan Masyarakat',
            'detail' => self::complaintLabel($complaintCount),
            'tone' => self::complaintTone($complaintCount),
        ];

        $jamaahCount = (int) ($stats['total_jamaah'] ?? 0);
        if ($jamaahCount > 0) {
            $signals[] = [
                'icon' => 'fa-users',
                'title' => 'Jamaah Terlayani',
                'detail' => number_format($jamaahCount).' jamaah tercatat di sistem.',
                'tone' => 'info',
            ];
        }

        return $signals;
    }

    public static function labelForScore(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Sangat Dipercaya',
            $score >= 60 => 'Dipercaya',
            $score >= 40 => 'Perlu Dicek',
            default => 'Kurang Dipercaya',
        };
    }

    public static function shortLabelForScore(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Sangat baik',
            $score >= 60 => 'Cukup baik',
            $score >= 40 => 'Perlu dicek',
            default => 'Rendah',
        };
    }

    public static function colorForScore(int $score): string
    {
        return match (true) {
            $score >= 80 => '#1acc8d',
            $score >= 60 => '#50a5f1',
            $score >= 40 => '#f1b44c',
            default => '#f46a6a',
        };
    }

    public static function bgClassForScore(int $score): string
    {
        return match (true) {
            $score >= 80 => 'success',
            $score >= 60 => 'info',
            $score >= 40 => 'warning',
            default => 'danger',
        };
    }

    public static function starsForScore(int $score): int
    {
        return match (true) {
            $score >= 80 => 5,
            $score >= 60 => 4,
            $score >= 40 => 3,
            $score >= 20 => 2,
            default => 1,
        };
    }

    public static function descriptionForScore(int $score): string
    {
        return match (true) {
            $score >= 80 => 'Travel ini memiliki catatan kepatuhan yang baik berdasarkan data pengawasan Kanwil.',
            $score >= 60 => 'Secara umum travel ini dapat dipertimbangkan, namun tetap periksa detail layanan.',
            $score >= 40 => 'Ada beberapa hal yang perlu Anda perhatikan sebelum memilih travel ini.',
            default => 'Disarankan untuk membandingkan dengan travel lain atau menghubungi Kanwil untuk informasi lebih lanjut.',
        };
    }

    public static function formattedUpdatedAt(?Carbon $updatedAt): ?string
    {
        return $updatedAt?->translatedFormat('d F Y');
    }

    private static function complaintLabel(int $count): string
    {
        return match (true) {
            $count === 0 => 'Belum ada pengaduan tercatat.',
            $count <= 3 => 'Pengaduan tercatat masih sedikit.',
            default => 'Terdapat beberapa pengaduan yang perlu diperhatikan.',
        };
    }

    private static function complaintTone(int $count): string
    {
        return match (true) {
            $count === 0 => 'success',
            $count <= 3 => 'info',
            default => 'warning',
        };
    }
}
