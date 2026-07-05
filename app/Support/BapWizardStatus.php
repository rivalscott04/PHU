<?php

namespace App\Support;

use App\Models\BAP;

final class BapWizardStatus
{
    /**
     * @return array{label: string, class: string}
     */
    public static function travelBadge(BAP $bap): array
    {
        if ($bap->status === 'pending') {
            if (! $bap->pdf_file_path) {
                return ['label' => 'Perlu PDF', 'class' => 'bg-secondary text-white'];
            }

            return ['label' => 'Siap Diajukan', 'class' => 'bg-info text-dark'];
        }

        return match ($bap->status) {
            'diajukan' => ['label' => 'Menunggu Persetujuan', 'class' => 'bg-primary text-white'],
            'diproses' => ['label' => 'Sedang Ditinjau', 'class' => 'bg-warning text-dark'],
            'diterima' => ['label' => 'Disetujui', 'class' => 'bg-success text-white'],
            default => ['label' => ucfirst($bap->status), 'class' => 'bg-secondary text-white'],
        };
    }

    public static function wizardStep(BAP $bap): ?int
    {
        if ($bap->status !== 'pending') {
            return null;
        }

        return $bap->pdf_file_path ? 3 : 2;
    }

    public static function wizardRouteName(BAP $bap): ?string
    {
        return match (self::wizardStep($bap)) {
            2 => 'bap.wizard.upload',
            3 => 'bap.wizard.review',
            default => null,
        };
    }

    /**
     * @return array{label: string, class: string, hint: string}
     */
    public static function detailMeta(BAP $bap): array
    {
        if ($bap->status === 'pending') {
            if (! $bap->pdf_file_path) {
                return [
                    'label' => 'Draft',
                    'class' => 'bg-secondary text-white',
                    'hint' => 'Data sudah disimpan. Lengkapi upload PDF melalui wizard pengajuan.',
                ];
            }

            return [
                'label' => 'Draft — Siap Diajukan',
                'class' => 'bg-info text-dark',
                'hint' => 'Data dan PDF sudah lengkap. Ajukan melalui wizard sebelum menunggu persetujuan.',
            ];
        }

        return match ($bap->status) {
            'diajukan' => [
                'label' => 'Menunggu Persetujuan',
                'class' => 'bg-primary text-white',
                'hint' => 'Pengajuan sudah dikirim ke Kabupaten/Kanwil dan menunggu peninjauan.',
            ],
            'diproses' => [
                'label' => 'Sedang Ditinjau',
                'class' => 'bg-warning text-dark',
                'hint' => 'Pengajuan sedang ditinjau. Pantau halaman ini untuk pembaruan status.',
            ],
            'diterima' => [
                'label' => 'Disetujui',
                'class' => 'bg-success text-white',
                'hint' => 'Keberangkatan disetujui. Anda dapat mencetak BA Pemberangkatan.',
            ],
            default => [
                'label' => ucfirst($bap->status),
                'class' => 'bg-secondary text-white',
                'hint' => '',
            ],
        };
    }

    /** @return list<string> */
    public static function approverStatusOptions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'pending' => ['pending', 'diajukan', 'diproses', 'diterima'],
            'diajukan' => ['diajukan', 'diproses', 'diterima'],
            'diproses' => ['diproses', 'diterima'],
            'diterima' => ['diterima'],
            default => [$currentStatus],
        };
    }

    public static function approverStatusLabel(string $status): string
    {
        return match ($status) {
            'pending' => 'Draft',
            'diajukan' => 'Diajukan',
            'diproses' => 'Diproses',
            'diterima' => 'Diterima',
            default => ucfirst($status),
        };
    }
}
