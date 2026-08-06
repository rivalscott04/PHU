<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogNarrator
{
    /** @var array<string, string> */
    private const MODULE_LABELS = [
        'pengawasan' => 'Pengawasan',
        'followup' => 'Tindak Lanjut',
        'checklist' => 'Daftar Periksa',
        'risk' => 'Penilaian Risiko',
        'auth' => 'Akses Sistem',
        'export' => 'Ekspor Data',
        'antrian' => 'Antrian Kerja',
    ];

    /** @return array{actor: string, actor_role: string, category: string, summary: string, detail: ?string} */
    public function present(AuditLog $log): array
    {
        $actor = $this->actorName($log->user);
        $actorRole = $this->actorRoleLabel($log->user);
        $category = self::MODULE_LABELS[$log->module] ?? ucfirst($log->module);
        $rawDetail = $this->cleanDetail($log->description);
        $rawDetail = $this->stripLeadingActor($rawDetail, $actor);
        $reference = $this->extractReference($rawDetail);
        $summary = $this->humanize($rawDetail, $log->module);

        return [
            'actor' => $actor,
            'actor_role' => $actorRole,
            'category' => $category,
            'summary' => $summary,
            'detail' => $reference,
        ];
    }

    public function actorName(?User $user): string
    {
        if (! $user) {
            return 'Sistem';
        }

        $name = trim((string) ($user->nama ?? ''));
        if ($name === '') {
            $name = trim(((string) ($user->firstname ?? '')).' '.((string) ($user->lastname ?? '')));
        }
        if ($name === '') {
            $name = (string) ($user->username ?? $user->email ?? 'Pengguna');
        }

        return $name;
    }

    public function actorRoleLabel(?User $user): string
    {
        if (! $user) {
            return 'Otomatis';
        }

        return match ($user->role) {
            'admin' => 'Admin Kanwil',
            'kabupaten' => 'Admin Kabupaten',
            'pengawas' => 'Pengawas',
            'user' => 'Penyelenggara Travel',
            default => 'Pengguna',
        };
    }

    private function humanize(string $detail, string $module): string
    {
        if ($detail === '') {
            return match ($module) {
                'auth' => 'Akses sistem',
                'pengawasan' => 'Memperbarui data pemeriksaan',
                'followup' => 'Memperbarui tindak lanjut',
                default => 'Memperbarui data',
            };
        }

        $text = $detail;

        $text = preg_replace('/\bPWG\d+\b/', '', $text) ?? $text;
        $text = preg_replace('/\bpengaduan\s*#\d+\b/i', 'pengaduan', $text) ?? $text;
        $text = preg_replace('/\s*\([A-Z0-9_-]{2,}\)\s*/', ' ', $text) ?? $text;
        $text = preg_replace('/:\s*\d+\s+poin\s*\([^)]+\)/i', '', $text) ?? $text;

        $phrases = [
            'menjadwalkan pengawasan baru  untuk' => 'menjadwalkan pemeriksaan ke',
            'menjadwalkan pengawasan baru untuk' => 'menjadwalkan pemeriksaan ke',
            'menjadwalkan pemeriksaan  untuk' => 'menjadwalkan pemeriksaan ke',
            'menjadwalkan pemeriksaan untuk' => 'menjadwalkan pemeriksaan ke',
            'memperbarui jadwal pengawasan' => 'memperbarui jadwal pemeriksaan',
            'memperbarui jadwal pemeriksaan' => 'memperbarui jadwal pemeriksaan',
            'mencatat temuan baru' => 'mencatat masalah',
            'memperbarui temuan' => 'memperbarui masalah',
            'pada pengawasan' => 'pada pemeriksaan',
            'pada pemeriksaan' => 'pada pemeriksaan travel',
            'mengisi daftar periksa pengawasan' => 'mengisi pertanyaan pemeriksaan',
            'mengisi daftar periksa pemeriksaan' => 'mengisi pertanyaan pemeriksaan',
            'menyelesaikan pemeriksaan' => 'menyelesaikan pemeriksaan travel',
            'menghitung ulang skor risiko otomatis untuk' => 'menghitung ulang skor risiko untuk',
            'menghitung ulang skor risiko untuk' => 'memperbarui skor risiko untuk',
            'menambahkan pengaduan  ke antrian kerja pengawasan' => 'menambahkan pengaduan ke antrian kerja',
            'menambahkan pengaduan ke antrian kerja pengawasan' => 'menambahkan pengaduan ke antrian kerja',
        ];

        foreach ($phrases as $from => $to) {
            $text = str_ireplace($from, $to, $text);
        }

        $text = preg_replace('/\s{2,}/', ' ', $text) ?? $text;
        $text = trim($text, " \t\n\r\0\x0B.,");
        $text = rtrim($text, '.');

        return $this->sentenceCase($text);
    }

    private function extractReference(string $detail): ?string
    {
        $references = [];

        if (preg_match('/\b(PWG\d+)\b/', $detail, $matches)) {
            $references[] = 'Nomor pemeriksaan: '.$matches[1];
        }

        if (preg_match('/\bpengaduan\s*#(\d+)\b/i', $detail, $matches)) {
            $references[] = 'Nomor pengaduan: '.$matches[1];
        }

        if ($references === []) {
            return null;
        }

        return implode(' · ', $references);
    }

    private function stripLeadingActor(string $detail, string $actor): string
    {
        if ($actor === 'Sistem' || $detail === '') {
            return $detail;
        }

        $lowerDetail = strtolower($detail);
        $lowerActor = strtolower($actor);

        if (str_starts_with($lowerDetail, $lowerActor.' ')) {
            return ltrim(substr($detail, strlen($actor)));
        }

        return $detail;
    }

    private function cleanDetail(?string $description): string
    {
        return rtrim(trim((string) $description), '.');
    }

    private function sentenceCase(string $text): string
    {
        if ($text === '') {
            return $text;
        }

        return mb_strtoupper(mb_substr($text, 0, 1)).mb_substr($text, 1);
    }
}
