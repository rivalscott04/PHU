<?php

namespace App\Support;

use App\Enums\UserRole;

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
            'perlu_perhatian' => ['label' => 'Perlu Perhatian', 'icon' => 'bx-error', 'color' => '#f46a6a'],
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

    /**
     * @param  array<string, int|float>  $summary
     * @return array{
     *     layout: string,
     *     sections: list<array{
     *         key: string,
     *         title: ?string,
     *         cards: list<array{
     *             key: string,
     *             label: string,
     *             icon: string,
     *             value: int|float,
     *             hint?: string,
     *             url?: ?string,
     *             tone?: string,
     *             composite?: list<string>
     *         }>
     *     }>
     * }
     */
    public static function formatForRole(string $role, array $summary, array $query = []): array
    {
        return match ($role) {
            UserRole::Pimpinan->value => self::executiveLayout($summary, $query),
            UserRole::Pengawas->value => self::pengawasLayout($summary, $query),
            default => self::operationalLayout($summary, $query, $role),
        };
    }

    /**
     * @param  array<string, int|float>  $summary
     * @return array{layout: string, sections: list<array<string, mixed>>}
     */
    private static function executiveLayout(array $summary, array $query): array
    {
        $attention = (int) ($summary['temuan_aktif'] ?? 0)
            + (int) ($summary['travel_risiko_tinggi'] ?? 0);

        return [
            'layout' => 'executive',
            'summary' => $summary,
            'sections' => [
                [
                    'key' => 'prioritas',
                    'title' => null,
                    'cards' => [
                        self::card('perlu_perhatian', $attention, [
                            'hint' => 'Temuan aktif + travel berisiko tinggi',
                            'url' => self::travelUrl($query, ['risk_level' => 'HIGH', 'sort' => 'risk']),
                            'tone' => $attention > 0 ? 'danger' : 'default',
                            'composite' => ['temuan_aktif', 'travel_risiko_tinggi'],
                        ]),
                        self::card('total_pengaduan', $summary, [
                            'hint' => 'Klik untuk urutkan travel dengan pengaduan terbanyak',
                            'url' => self::travelUrl($query, ['sort' => 'pengaduan']),
                            'tone' => ((int) ($summary['total_pengaduan'] ?? 0)) > 0 ? 'warning' : 'default',
                        ]),
                        self::card('total_travel', $summary, [
                            'url' => self::travelUrl($query),
                        ]),
                        self::card('total_jamaah', $summary, [
                            'url' => self::travelUrl($query),
                        ]),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, int|float>  $summary
     * @return array{layout: string, sections: list<array<string, mixed>>}
     */
    private static function pengawasLayout(array $summary, array $query): array
    {
        return [
            'layout' => 'pengawas',
            'summary' => $summary,
            'sections' => [
                [
                    'key' => 'prioritas',
                    'title' => 'Prioritas Pengawasan',
                    'cards' => [
                        self::card('pengawasan_berjalan', $summary, [
                            'hint' => 'Pemeriksaan sedang berlangsung',
                            'url' => self::moduleUrl(UserRole::Pengawas->value, 'pengawasan', $query),
                        ]),
                        self::card('temuan_aktif', $summary, [
                            'hint' => 'Temuan belum ditutup',
                            'url' => self::moduleUrl(UserRole::Pengawas->value, 'followup', $query),
                            'tone' => ((int) ($summary['temuan_aktif'] ?? 0)) > 0 ? 'danger' : 'default',
                        ]),
                        self::card('total_pengaduan', $summary, [
                            'hint' => 'Pengaduan masyarakat perlu ditindak',
                            'url' => self::travelUrl($query, ['sort' => 'pengaduan']),
                            'tone' => ((int) ($summary['total_pengaduan'] ?? 0)) > 0 ? 'warning' : 'default',
                        ]),
                        self::card('travel_risiko_tinggi', $summary, [
                            'hint' => 'Travel berstatus HIGH atau CRITICAL',
                            'url' => self::moduleUrl(UserRole::Pengawas->value, 'risk', $query, ['risk_level' => 'HIGH']),
                            'tone' => ((int) ($summary['travel_risiko_tinggi'] ?? 0)) > 0 ? 'warning' : 'default',
                        ]),
                    ],
                ],
                [
                    'key' => 'konteks',
                    'title' => 'Konteks Wilayah',
                    'cards' => [
                        self::card('total_travel', $summary, [
                            'url' => self::travelUrl($query),
                        ]),
                        self::card('total_jamaah', $summary, [
                            'url' => self::travelUrl($query),
                        ]),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, int|float>  $summary
     * @return array{layout: string, sections: list<array<string, mixed>>}
     */
    private static function operationalLayout(array $summary, array $query, string $role): array
    {
        return [
            'layout' => 'operational',
            'summary' => $summary,
            'sections' => [
                [
                    'key' => 'profile',
                    'title' => 'Profil Travel',
                    'cards' => [
                        self::card('total_travel', $summary, [
                            'url' => self::travelUrl($query),
                        ]),
                        self::card('total_jamaah', $summary, [
                            'url' => self::travelUrl($query),
                        ]),
                    ],
                ],
                [
                    'key' => 'operational',
                    'title' => 'Pengawasan & Risiko',
                    'cards' => [
                        self::card('total_pengaduan', $summary, [
                            'hint' => 'Klik untuk lihat travel dengan pengaduan terbanyak',
                            'url' => self::travelUrl($query, ['sort' => 'pengaduan']),
                            'tone' => ((int) ($summary['total_pengaduan'] ?? 0)) > 0 ? 'warning' : 'default',
                        ]),
                        self::card('pengawasan_berjalan', $summary, [
                            'hint' => 'Pemeriksaan yang sedang berlangsung',
                            'url' => self::moduleUrl($role, 'pengawasan', $query),
                        ]),
                        self::card('temuan_aktif', $summary, [
                            'hint' => 'Temuan yang belum ditutup',
                            'url' => self::moduleUrl($role, 'followup', $query),
                            'tone' => ((int) ($summary['temuan_aktif'] ?? 0)) > 0 ? 'danger' : 'default',
                        ]),
                        self::card('travel_risiko_tinggi', $summary, [
                            'hint' => 'Travel berstatus HIGH atau CRITICAL',
                            'url' => self::moduleUrl($role, 'risk', $query, ['risk_level' => 'HIGH']),
                            'tone' => ((int) ($summary['travel_risiko_tinggi'] ?? 0)) > 0 ? 'warning' : 'default',
                        ]),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, int|float>|int|float  $summaryOrValue
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private static function card(string $key, array|int|float $summaryOrValue, array $options = []): array
    {
        $definition = self::definitions()[$key] ?? [
            'label' => str_replace('_', ' ', $key),
            'icon' => 'bx-bar-chart',
            'color' => '#556ee6',
        ];

        $value = is_array($summaryOrValue)
            ? (int) ($summaryOrValue[$key] ?? 0)
            : (int) $summaryOrValue;

        return [
            'key' => $key,
            'label' => $definition['label'],
            'icon' => $definition['icon'],
            'value' => $value,
            'hint' => $options['hint'] ?? null,
            'url' => $options['url'] ?? null,
            'tone' => $options['tone'] ?? 'default',
            'composite' => $options['composite'] ?? null,
        ];
    }

    /** @param  array<string, mixed>  $extra */
    private static function travelUrl(array $query, array $extra = []): ?string
    {
        if (! RouteAccess::canAccessRoute(auth()->user(), 'v2.monitoring.travel')) {
            return null;
        }

        return route('v2.monitoring.travel', array_merge($query, $extra));
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private static function moduleUrl(string $role, string $module, array $query, array $extra = []): ?string
    {
        $user = auth()->user();

        return match ($module) {
            'pengawasan' => RouteAccess::canAccessRoute($user, 'v2.pengawasan.index')
                ? route('v2.pengawasan.index')
                : self::travelUrl($query, ['sort' => 'inspection']),
            'followup' => RouteAccess::canAccessRoute($user, 'v2.followup.index')
                ? route('v2.followup.index')
                : self::travelUrl($query),
            'risk' => RouteAccess::canAccessRoute($user, 'v2.risk.index')
                ? route('v2.risk.index')
                : self::travelUrl($query, $extra),
            default => self::travelUrl($query, $extra),
        };
    }
}
