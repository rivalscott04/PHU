<?php

namespace App\Support;

use App\Enums\UserRole;

class DashboardKpiCards
{
    /**
     * @param  array<string, array{label: string, value: int|float, trend?: int|float, direction?: string}>  $stats
     * @return array{layout: string, stats: array<string, mixed>, sections: list<array<string, mixed>>}
     */
    public static function formatForRole(string $role, array $stats): array
    {
        return match ($role) {
            UserRole::Pimpinan->value => [
                'layout' => 'none',
                'stats' => $stats,
                'sections' => [],
            ],
            UserRole::Pengawas->value => self::pengawasLayout($stats),
            default => self::adminLayout($stats),
        };
    }

    /**
     * @param  array<string, array{label: string, value: int|float, trend?: int|float, direction?: string}>  $stats
     * @return array{layout: string, stats: array<string, mixed>, sections: list<array<string, mixed>>}
     */
    private static function adminLayout(array $stats): array
    {
        return [
            'layout' => 'admin',
            'stats' => $stats,
            'sections' => [
                [
                    'key' => 'profile',
                    'title' => 'Profil & Volume',
                    'cards' => [
                        self::compositeCard('penyelenggara', 'Penyelenggara', 'bx-buildings', $stats, ['total_ppiu', 'total_pihk'], [
                            'hint' => 'PPIU dan PIHK terdaftar',
                            'url' => self::routeIf('v2.monitoring.travel'),
                            'breakdown' => [
                                ['key' => 'total_ppiu', 'label' => 'PPIU'],
                                ['key' => 'total_pihk', 'label' => 'PIHK'],
                                ['key' => 'total_cabang', 'label' => 'Cabang'],
                            ],
                        ]),
                        self::card('total_jamaah', $stats, [
                            'url' => self::routeIf('jamaah.umrah'),
                            'breakdown' => [
                                ['key' => 'total_jamaah_umrah', 'label' => 'Umrah'],
                                ['key' => 'total_jamaah_haji_khusus', 'label' => 'Haji Khusus'],
                            ],
                        ]),
                    ],
                ],
                [
                    'key' => 'operational',
                    'title' => 'Operasional & Pengawasan',
                    'cards' => [
                        self::card('bap_pending', $stats, [
                            'hint' => 'BA pemberangkatan menunggu persetujuan',
                            'url' => self::routeIf('travel', ['filter' => 'bap']),
                            'tone' => self::toneFor($stats, 'bap_pending'),
                        ]),
                        self::card('pengawasan_berjalan', $stats, [
                            'url' => self::routeIf('v2.pengawasan.index'),
                        ]),
                        self::card('temuan_aktif', $stats, [
                            'url' => self::routeIf('v2.followup.index'),
                            'tone' => self::toneFor($stats, 'temuan_aktif', 'danger'),
                        ]),
                        self::card('total_pengaduan', $stats, [
                            'url' => self::routeIf('v2.monitoring.index'),
                            'tone' => self::toneFor($stats, 'total_pengaduan', 'warning'),
                        ]),
                        self::card('travel_risiko_tinggi', $stats, [
                            'url' => self::routeIf('v2.risk.index'),
                            'tone' => self::toneFor($stats, 'travel_risiko_tinggi', 'warning'),
                        ]),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, array{label: string, value: int|float, trend?: int|float, direction?: string}>  $stats
     * @return array{layout: string, stats: array<string, mixed>, sections: list<array<string, mixed>>}
     */
    private static function pengawasLayout(array $stats): array
    {
        return [
            'layout' => 'pengawas',
            'stats' => $stats,
            'sections' => [
                [
                    'key' => 'prioritas',
                    'title' => 'Prioritas Pengawasan',
                    'cards' => [
                        self::card('pengawasan_berjalan', $stats, [
                            'hint' => 'Pemeriksaan sedang berlangsung',
                            'url' => self::routeIf('v2.pengawasan.index'),
                        ]),
                        self::card('temuan_aktif', $stats, [
                            'hint' => 'Temuan belum ditutup',
                            'url' => self::routeIf('v2.followup.index'),
                            'tone' => self::toneFor($stats, 'temuan_aktif', 'danger'),
                        ]),
                        self::card('total_pengaduan', $stats, [
                            'hint' => 'Pengaduan masyarakat perlu ditindak',
                            'url' => self::routeIf('v2.monitoring.index'),
                            'tone' => self::toneFor($stats, 'total_pengaduan', 'warning'),
                        ]),
                        self::card('travel_risiko_tinggi', $stats, [
                            'hint' => 'Travel berstatus HIGH atau CRITICAL',
                            'url' => self::routeIf('v2.risk.index'),
                            'tone' => self::toneFor($stats, 'travel_risiko_tinggi', 'warning'),
                        ]),
                    ],
                ],
                [
                    'key' => 'konteks',
                    'title' => 'Konteks Wilayah',
                    'cards' => [
                        self::compositeCard('penyelenggara', 'Penyelenggara', 'bx-buildings', $stats, ['total_ppiu', 'total_pihk'], [
                            'url' => self::routeIf('v2.monitoring.travel'),
                            'breakdown' => [
                                ['key' => 'total_ppiu', 'label' => 'PPIU'],
                                ['key' => 'total_pihk', 'label' => 'PIHK'],
                            ],
                        ]),
                        self::card('total_jamaah', $stats, [
                            'url' => self::routeIf('jamaah.umrah'),
                            'breakdown' => [
                                ['key' => 'total_jamaah_haji_khusus', 'label' => 'Haji Khusus'],
                            ],
                        ]),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, array{label: string, value: int|float, trend?: int|float, direction?: string}>  $stats
     * @param  list<string>  $parts
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private static function compositeCard(
        string $key,
        string $label,
        string $icon,
        array $stats,
        array $parts,
        array $options = [],
    ): array {
        $value = 0;
        foreach ($parts as $part) {
            $value += (int) ($stats[$part]['value'] ?? 0);
        }

        return [
            'key' => $key,
            'label' => $label,
            'icon' => $icon,
            'value' => $value,
            'hint' => $options['hint'] ?? null,
            'url' => $options['url'] ?? null,
            'tone' => $options['tone'] ?? 'default',
            'composite' => $parts,
            'breakdown' => $options['breakdown'] ?? [],
            'trend' => null,
            'direction' => null,
        ];
    }

    /**
     * @param  array<string, array{label: string, value: int|float, trend?: int|float, direction?: string}>  $stats
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private static function card(string $key, array $stats, array $options = []): array
    {
        $stat = $stats[$key] ?? ['label' => str_replace('_', ' ', $key), 'value' => 0];

        return [
            'key' => $key,
            'label' => $stat['label'] ?? str_replace('_', ' ', $key),
            'icon' => self::iconFor($key),
            'value' => (int) ($stat['value'] ?? 0),
            'hint' => $options['hint'] ?? null,
            'url' => $options['url'] ?? null,
            'tone' => $options['tone'] ?? 'default',
            'breakdown' => $options['breakdown'] ?? [],
            'trend' => $stat['trend'] ?? null,
            'direction' => $stat['direction'] ?? null,
        ];
    }

    /** @param  array<string, array{label: string, value: int|float}>  $stats */
    private static function toneFor(array $stats, string $key, string $activeTone = 'warning'): string
    {
        return ((int) ($stats[$key]['value'] ?? 0)) > 0 ? $activeTone : 'default';
    }

    private static function iconFor(string $key): string
    {
        return match ($key) {
            'total_ppiu', 'penyelenggara' => 'bx-buildings',
            'total_pihk' => 'bx-map-alt',
            'total_cabang' => 'bx-git-branch',
            'total_jamaah', 'total_jamaah_umrah' => 'bx-group',
            'total_jamaah_haji_khusus' => 'bx-user-pin',
            'total_bap', 'bap_pending' => 'bx-file',
            'pengawasan_berjalan' => 'bx-search-alt',
            'temuan_aktif' => 'bx-error-circle',
            'total_pengaduan' => 'bx-message-square-dots',
            'travel_risiko_tinggi' => 'bx-shield-quarter',
            default => 'bx-bar-chart',
        };
    }

    /** @param  array<string, mixed>  $params */
    private static function routeIf(string $routeName, array $params = []): ?string
    {
        $user = auth()->user();
        if (! $user || ! RouteAccess::canAccessRoute($user, $routeName, $params)) {
            return null;
        }

        return route($routeName, $params);
    }
}
