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
    ];

    /** @var array<string, string> */
    private const ACTION_VERBS = [
        'create' => 'menambahkan',
        'update' => 'mengubah',
        'upload' => 'mengunggah',
        'approve' => 'menyetujui',
        'revision' => 'meminta revisi pada',
        'recalculate' => 'menghitung ulang',
        'delete' => 'menghapus',
        'download' => 'mengunduh',
        'export' => 'mengekspor',
        'login' => 'masuk ke',
        'logout' => 'keluar dari',
        'close' => 'menutup',
        'reject' => 'menolak',
    ];

    /** @return array{actor: string, actor_role: string, category: string, summary: string, detail: ?string} */
    public function present(AuditLog $log): array
    {
        $actor = $this->actorName($log->user);
        $actorRole = $this->actorRoleLabel($log->user);
        $category = self::MODULE_LABELS[$log->module] ?? ucfirst($log->module);
        $detail = $this->cleanDetail($log->description);
        $summary = $this->buildSummary($actor, $log->module, $log->action, $detail);

        return [
            'actor' => $actor,
            'actor_role' => $actorRole,
            'category' => $category,
            'summary' => $summary,
            'detail' => $detail !== '' ? $detail : null,
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
            'user' => 'Penyelenggara Travel',
            default => 'Pengguna',
        };
    }

    private function buildSummary(string $actor, string $module, string $action, string $detail): string
    {
        if ($detail !== '') {
            if ($this->startsWithActorVerb($detail)) {
                return "{$actor} {$detail}";
            }

            if ($module === 'auth') {
                return "{$actor} {$detail}";
            }

            return "{$actor} {$detail}";
        }

        $verb = self::ACTION_VERBS[$action] ?? $action;
        $object = match ($module) {
            'pengawasan' => 'data pengawasan',
            'followup' => 'tindak lanjut',
            'checklist' => 'daftar periksa',
            'risk' => 'penilaian risiko',
            'auth' => 'sistem',
            default => 'data',
        };

        if ($module === 'auth') {
            return "{$actor} {$verb} {$object}";
        }

        return "{$actor} {$verb} {$object}";
    }

    private function cleanDetail(?string $description): string
    {
        $detail = trim((string) $description);

        return rtrim($detail, '.');
    }

    private function startsWithActorVerb(string $detail): bool
    {
        $lower = strtolower($detail);

        foreach (self::ACTION_VERBS as $verb) {
            if (str_starts_with($lower, $verb.' ')) {
                return true;
            }
        }

        return false;
    }
}
