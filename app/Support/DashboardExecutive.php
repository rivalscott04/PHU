<?php

namespace App\Support;

use App\Models\TravelCompany;
use Carbon\Carbon;

final class DashboardExecutive
{
    /**
     * @param  array<string, array{label?: string, value?: int|float, trend?: float, direction?: string}>  $stats
     * @param  array<string, array{total: int, selesai?: int, pending?: int, percent: float, label: string}>  $completion
     * @param  list<array<string, mixed>>  $priorities
     * @param  list<array<string, mixed>>  $coverageGaps
     * @return array{period: string, points: list<array{label: string, text: string, tone: string}>}
     */
    public static function buildSummaryPoints(
        array $stats,
        array $completion,
        array $priorities,
        DashboardFilter $filter,
        array $coverageGaps = [],
    ): array {
        $bulan = $filter->bulan ?? (int) now()->month;
        $tahun = $filter->tahun ?? (int) now()->year;
        $period = Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y');

        $points = [];

        self::appendOperationalQueuePoints($points, $filter);
        self::appendJamaahPoints($points, $stats);

        $pengaduanValue = (int) (($stats['total_pengaduan'] ?? [])['value'] ?? 0);
        if ($pengaduanValue > 0) {
            $pengaduanPct = (int) round($completion['pengaduan']['percent'] ?? 0);
            $points[] = [
                'label' => 'Pengaduan',
                'text' => "{$pengaduanValue} kasus, penyelesaian {$pengaduanPct}%",
                'tone' => $pengaduanPct < 50 ? 'warning' : 'default',
            ];
        }

        $baBerangkat = $completion['ba_pemberangkatan'] ?? [];
        $baBerangkatTotal = (int) ($baBerangkat['total'] ?? 0);
        if ($baBerangkatTotal > 0) {
            $baBerangkatPct = (int) round($baBerangkat['percent'] ?? 0);
            $points[] = [
                'label' => 'BA Pemberangkatan',
                'text' => "{$baBerangkatPct}% disetujui ({$baBerangkat['selesai']} dari {$baBerangkatTotal} pengajuan)",
                'tone' => $baBerangkatPct < 75 ? 'warning' : 'default',
            ];
        }

        $bapPending = (int) (($stats['bap_pending'] ?? [])['value'] ?? 0);
        if ($bapPending > 0) {
            $points[] = [
                'label' => 'BA Pemberangkatan (Periode)',
                'text' => "{$bapPending} pengajuan menunggu pada periode filter",
                'tone' => 'warning',
            ];
        }

        $baPemeriksaan = $completion['pengawasan'] ?? [];
        $baPemeriksaanTotal = (int) ($baPemeriksaan['total'] ?? 0);
        if ($baPemeriksaanTotal > 0) {
            $baPemeriksaanPct = (int) round($baPemeriksaan['percent'] ?? 0);
            $points[] = [
                'label' => 'BA Pemeriksaan',
                'text' => "{$baPemeriksaanPct}% selesai ({$baPemeriksaan['selesai']} dari {$baPemeriksaanTotal} pemeriksaan)",
                'tone' => $baPemeriksaanPct < 75 ? 'warning' : 'default',
            ];
        }

        $pengawasanBerjalan = (int) (($stats['pengawasan_berjalan'] ?? [])['value'] ?? 0);
        if ($pengawasanBerjalan > 0) {
            $points[] = [
                'label' => 'Pengawasan Berjalan',
                'text' => "{$pengawasanBerjalan} pemeriksaan masih berlangsung",
                'tone' => 'info',
            ];
        }

        $highRisk = (int) (($stats['travel_risiko_tinggi'] ?? [])['value'] ?? 0);
        if ($highRisk > 0) {
            $points[] = [
                'label' => 'Risiko Tinggi',
                'text' => "{$highRisk} travel perlu perhatian khusus",
                'tone' => 'danger',
            ];
        }

        $temuanAktif = (int) (($stats['temuan_aktif'] ?? [])['value'] ?? 0);
        if ($temuanAktif > 0) {
            $temuanPct = (int) round($completion['temuan']['percent'] ?? 0);
            $points[] = [
                'label' => 'Temuan Aktif',
                'text' => "{$temuanAktif} temuan terbuka, penyelesaian keseluruhan {$temuanPct}%",
                'tone' => 'warning',
            ];
        }

        $criticalCount = count(array_filter($priorities, fn (array $row) => ($row['urgency'] ?? '') === 'critical'));
        if ($criticalCount > 0) {
            $points[] = [
                'label' => 'Intervensi Segera',
                'text' => "{$criticalCount} penyelenggara memerlukan tindakan segera",
                'tone' => 'danger',
            ];
        }

        if ($coverageGaps !== []) {
            $points[] = [
                'label' => 'Coverage Pengawasan',
                'text' => count($coverageGaps).' travel belum diawasi dalam 12 bulan terakhir',
                'tone' => 'warning',
            ];
        }

        if ($points === []) {
            $points[] = [
                'label' => 'Kondisi Umum',
                'text' => 'Dalam batas normal, tidak ada isu kritis yang teridentifikasi',
                'tone' => 'success',
            ];
        }

        return [
            'period' => $period,
            'points' => $points,
        ];
    }

    /**
     * @param  array{period: string, points: list<array{label: string, text: string, tone: string}>}  $summary
     */
    public static function summaryToPlainText(array $summary): string
    {
        $lines = array_map(
            fn (array $point) => "{$point['label']}: {$point['text']}",
            $summary['points'] ?? []
        );

        return 'Ringkasan periode '.($summary['period'] ?? '').'. '.implode('. ', $lines).'.';
    }

    /** @deprecated Use buildSummaryPoints + summaryToPlainText */
    public static function buildSummary(
        array $stats,
        array $completion,
        array $priorities,
        DashboardFilter $filter,
        array $coverageGaps = [],
    ): string {
        return self::summaryToPlainText(
            self::buildSummaryPoints($stats, $completion, $priorities, $filter, $coverageGaps)
        );
    }

    /** @param  list<array{label: string, text: string, tone: string}>  $points */
    private static function appendOperationalQueuePoints(array &$points, DashboardFilter $filter): void
    {
        $registrationPending = HomeCommandCenter::countRegistrationPending($filter->kabupaten);
        if ($registrationPending > 0) {
            $points[] = [
                'label' => 'Registrasi Travel',
                'text' => "{$registrationPending} pendaftaran mandiri menunggu verifikasi Kanwil",
                'tone' => $registrationPending > 3 ? 'danger' : 'warning',
            ];
        }

        $bapPending = HomeCommandCenter::countBapPendingForScope($filter->kabupaten);
        if ($bapPending > 0) {
            $points[] = [
                'label' => 'BA Pemberangkatan Menunggu',
                'text' => "{$bapPending} pengajuan menunggu persetujuan Kanwil/Kabupaten",
                'tone' => 'warning',
            ];
        }
    }

    /** @param  list<array{label: string, text: string, tone: string}>  $points */
    private static function appendJamaahPoints(array &$points, array $stats): void
    {
        $umrah = $stats['total_jamaah_umrah'] ?? [];
        $hajiKhusus = $stats['total_jamaah_haji_khusus'] ?? [];
        $ppiu = $stats['total_ppiu'] ?? [];
        $pihk = $stats['total_pihk'] ?? [];

        $umrahValue = (int) ($umrah['value'] ?? 0);
        $hajiValue = (int) ($hajiKhusus['value'] ?? 0);
        $ppiuCount = (int) ($ppiu['value'] ?? 0);
        $pihkCount = (int) ($pihk['value'] ?? 0);

        $points[] = [
            'label' => 'Jamaah Umrah (PPIU)',
            'text' => $umrahValue > 0
                ? self::formatJamaahLine($umrahValue, $umrah, $ppiuCount)
                : self::formatEmptyJamaahLine('PPIU', $ppiuCount),
            'tone' => 'default',
        ];

        $points[] = [
            'label' => 'Jamaah Haji Khusus (PIHK)',
            'text' => $hajiValue > 0
                ? self::formatJamaahLine($hajiValue, $hajiKhusus, $pihkCount)
                : self::formatEmptyJamaahLine('PIHK', $pihkCount),
            'tone' => 'default',
        ];
    }

    /** @param  array{value?: int|float, trend?: float, direction?: string}  $stat */
    private static function formatJamaahLine(int $count, array $stat, int $penyelenggaraCount): string
    {
        $suffix = $penyelenggaraCount > 0
            ? ", {$penyelenggaraCount} penyelenggara aktif"
            : '';

        return "{$count} tercatat".self::trendText($stat).$suffix;
    }

    private static function formatEmptyJamaahLine(string $jenis, int $penyelenggaraCount): string
    {
        $suffix = $penyelenggaraCount > 0
            ? " ({$penyelenggaraCount} penyelenggara {$jenis} terdaftar)"
            : '';

        return "belum ada jamaah periode ini{$suffix}";
    }

    /** @param  array{value?: int|float, trend?: float, direction?: string}  $stat */
    private static function trendText(array $stat): string
    {
        $trend = (float) ($stat['trend'] ?? 0);
        $direction = $stat['direction'] ?? 'flat';

        return match ($direction) {
            'up' => ", naik {$trend}% vs bulan lalu",
            'down' => ", turun {$trend}% vs bulan lalu",
            default => ', stabil vs bulan lalu',
        };
    }

    public static function urgencyLabel(string $urgency): string
    {
        return match ($urgency) {
            'critical' => 'Segera',
            'high' => 'Prioritas',
            default => 'Perlu Perhatian',
        };
    }

    public static function urgencyBadge(string $urgency): string
    {
        return match ($urgency) {
            'critical' => 'light text-danger border border-danger',
            'high' => 'light text-body border',
            default => 'light text-muted border',
        };
    }

    public static function warningIconClass(string $level): string
    {
        return match ($level) {
            'critical' => 'bx-error-circle text-danger',
            default => 'bx-info-circle text-muted',
        };
    }

    /** @return array{cardClass: string, badgeClass: string, badgeLabel: string} */
    public static function completionRateStatus(float $percent): array
    {
        if ($percent >= 75) {
            return ['cardClass' => '', 'badgeClass' => '', 'badgeLabel' => ''];
        }

        if ($percent >= 50) {
            return [
                'cardClass' => '',
                'badgeClass' => 'badge bg-light text-muted border',
                'badgeLabel' => 'Perlu ditingkatkan',
            ];
        }

        return [
            'cardClass' => 'border-start border-danger border-2',
            'badgeClass' => 'badge bg-light text-danger border border-danger',
            'badgeLabel' => 'Rendah',
        ];
    }

    public static function pointDotClass(string $tone): string
    {
        return ($tone === 'danger') ? 'bg-danger' : 'bg-secondary opacity-50';
    }

    public static function pointTextClass(string $tone): string
    {
        return 'text-muted';
    }

    /** @return array{class: string, label: string}|null */
    public static function pointStatusBadge(string $tone): ?array
    {
        return match ($tone) {
            'danger' => ['class' => 'badge bg-light text-danger border border-danger', 'label' => 'Segera'],
            default => null,
        };
    }

    /** @deprecated Use pointDotClass, pointTextClass, and pointStatusBadge */
    public static function pointToneClass(string $tone): string
    {
        return self::pointTextClass($tone);
    }
}
