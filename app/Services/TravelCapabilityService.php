<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TravelCapabilityService
{
    /**
     * Get available menu items for current user
     */
    public static function getAvailableMenus()
    {
        $user = Auth::user();
        $menus = [];

        if ($user->role === UserRole::Admin->value) {
            $menus = [
                'dashboard' => true,
                'jamaah_umrah' => true,
                'jamaah_haji_khusus' => true,
                'bap' => true,
                'pengaduan' => true,
                'keberangkatan' => true,
                'pengunduran' => false, // disabled: schema mismatch, re-enable after migration fix
                'travel_management' => true,
                'cabang_travel' => true,
                'user_management' => true,
                'sertifikat' => true,
            ];
        } elseif ($user->role === UserRole::Kabupaten->value) {
            $menus = [
                'dashboard' => true,
                'jamaah_umrah' => true,
                'jamaah_haji_khusus' => true,
                'bap' => true,
                'pengaduan' => true,
                'keberangkatan' => true,
                'pengunduran' => false, // disabled: schema mismatch, re-enable after migration fix
                'travel_management' => true,
                'cabang_travel' => true,
                'user_management' => false,
                'sertifikat' => true,
            ];
        } elseif ($user->role === UserRole::Pengawas->value) {
            $menus = [
                'dashboard' => false,
                'jamaah_umrah' => false,
                'jamaah_haji_khusus' => false,
                'bap' => false,
                'pengaduan' => false,
                'keberangkatan' => false,
                'pengunduran' => false,
                'travel_management' => false,
                'cabang_travel' => false,
                'user_management' => false,
                'sertifikat' => false,
            ];
        } elseif ($user->role === UserRole::Pimpinan->value) {
            $menus = [
                'dashboard' => false,
                'jamaah_umrah' => false,
                'jamaah_haji_khusus' => false,
                'bap' => false,
                'pengaduan' => false,
                'keberangkatan' => false,
                'pengunduran' => false,
                'travel_management' => false,
                'cabang_travel' => false,
                'user_management' => false,
                'sertifikat' => false,
            ];
        } elseif ($user->role === UserRole::User->value) {
            $travel = $user->travel;

            $menus = [
                'dashboard' => true,
                'jamaah_umrah' => $travel ? $travel->canHandleUmrah() : false,
                'jamaah_haji_khusus' => $travel ? $travel->canHandleHajiKhusus() : false,
                'bap' => true,
                'travel_packages' => true,
                'pengaduan' => false,
                'keberangkatan' => true,
                'pengunduran' => false, // disabled: schema mismatch, re-enable after migration fix
                'travel_management' => false,
                'cabang_travel' => false,
                'user_management' => false,
                'sertifikat' => false,
            ];
        }

        return $menus;
    }

    /**
     * Check if user can access specific feature
     */
    public static function canAccess($feature)
    {
        $menus = self::getAvailableMenus();

        return $menus[$feature] ?? false;
    }

    /**
     * Get travel company capabilities
     */
    public static function getTravelCapabilities(TravelCompany $travel)
    {
        return [
            'can_haji' => $travel->canHandleHaji(),
            'can_umrah' => $travel->canHandleUmrah(),
            'can_haji_khusus' => $travel->canHandleHajiKhusus(),
            'status' => $travel->Status,
            'description' => $travel->getTravelTypeDescription(),
            'services' => $travel->getAvailableServices(),
            'license_status' => $travel->getLicenseStatus(),
            'license_expired' => $travel->isLicenseExpired(),
        ];
    }

    /**
     * Get travel type options for forms
     */
    public static function getTravelTypeOptions()
    {
        return [
            'PPIU' => 'PPIU: Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)',
            'PIHK' => 'PIHK: Penyelenggara Ibadah Haji Khusus (Haji & Umrah)',
        ];
    }

    /**
     * Get service options
     */
    public static function getServiceOptions()
    {
        return [
            'umrah' => 'Umrah',
            'haji' => 'Haji',
            'haji_khusus' => 'Haji Khusus',
        ];
    }

    /**
     * Validate travel capabilities
     */
    public static function validateCapabilities($status, $canHaji, $canUmrah)
    {
        $errors = [];

        if ($status === 'PPIU' && $canHaji) {
            $errors[] = 'PPIU travel companies can only handle Umrah services.';
        }

        if ($status === 'PIHK' && ! $canHaji) {
            $errors[] = 'PIHK travel companies must be able to handle Haji services.';
        }

        if (! $canHaji && ! $canUmrah) {
            $errors[] = 'Travel company must be able to handle at least one service (Haji or Umrah).';
        }

        return $errors;
    }

    /**
     * Get menu items for sidebar
     *
     * @return list<array<string, mixed>>
     */
    public static function getSidebarMenus(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        return match ($user->role) {
            UserRole::Pimpinan->value => self::pimpinanSidebarMenus(),
            UserRole::Pengawas->value => self::pengawasSidebarMenus(),
            UserRole::User->value => self::travelSidebarMenus($user),
            UserRole::Kabupaten->value => self::kabupatenSidebarMenus(),
            default => self::adminSidebarMenus(),
        };
    }

    /** @return list<array<string, mixed>> */
    private static function pimpinanSidebarMenus(): array
    {
        return [
            self::link('Dashboard Pengawasan', 'v2.dashboard', 'bx bx-bar-chart-alt-2'),
            self::link('Monitoring NTB', 'v2.monitoring.index', 'bx bx-radar'),
            self::link('Unduh Laporan', 'v2.export.dashboard', 'bx bxs-file-pdf'),
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function pengawasSidebarMenus(): array
    {
        return [
            self::link('Antrian Kerja', 'v2.antrian.index', 'bx bx-list-check', badge: 'antrian'),
            self::link('Monitoring Wilayah', 'v2.monitoring.index', 'bx bx-radar'),
            [
                'name' => 'Pemeriksaan',
                'icon' => 'bx bx-search-alt',
                'hasSubmenu' => true,
                'items' => [
                    self::subItem('Daftar Pemeriksaan', 'v2.pengawasan.index', hint: 'Inspeksi lapangan PPIU'),
                    self::subItem('Buat Pemeriksaan', 'v2.pengawasan.create'),
                    self::subItem('Tindak Lanjut Temuan', 'v2.followup.index', badge: 'followup_verify'),
                ],
            ],
            [
                'name' => 'Analisis',
                'icon' => 'bx bx-line-chart',
                'hasSubmenu' => true,
                'items' => [
                    self::subItem('Skor Risiko', 'v2.risk.index'),
                    self::subItem('Profil Kepatuhan', 'v2.compliance.index'),
                ],
            ],
            self::link('Log Aktivitas', 'v2.audit-log.index', 'bx bx-history'),
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function travelSidebarMenus(User $user): array
    {
        $menus = [
            self::link('Beranda', 'home', 'bx bx-home-circle'),
        ];

        $travel = $user->travel;
        $jamaahItems = [];
        if ($travel?->canHandleUmrah()) {
            $jamaahItems[] = self::subItem('Jamaah Umrah', 'jamaah.umrah');
        }
        if ($travel?->canHandleHajiKhusus()) {
            $jamaahItems[] = self::subItem('Jamaah Haji Khusus', 'jamaah.haji-khusus.index');
        }

        if ($jamaahItems !== []) {
            $menus[] = [
                'name' => 'Data Jamaah',
                'icon' => 'bx bx-user-circle',
                'hasSubmenu' => true,
                'items' => $jamaahItems,
            ];
        }

        $menus[] = [
            'name' => 'Keberangkatan',
            'icon' => 'bx bx-calendar-event',
            'hasSubmenu' => true,
            'items' => [
                self::subItem('BA Pemberangkatan', 'bap', hint: 'Pengajuan keberangkatan jamaah'),
                self::subItem('Paket Umrah Saya', 'travel.packages', hint: 'Katalog harga paket untuk pengajuan BA'),
                self::subItem('Jadwal Keberangkatan', 'keberangkatan'),
            ],
        ];

        $kanwilItems = [
            self::subItem('Tindak Lanjut Temuan', 'v2.followup.index', badge: 'followup_action'),
            self::subItem('Profil Kepatuhan Saya', 'v2.compliance.index'),
        ];

        $menus[] = [
            'name' => 'Tugas dari Kanwil',
            'icon' => 'bx bx-task',
            'hasSubmenu' => true,
            'items' => $kanwilItems,
        ];

        $menus[] = [
            'name' => 'Sertifikat',
            'icon' => 'bx bx-award',
            'hasSubmenu' => true,
            'items' => [
                self::subItem('Sertifikat Saya', 'travel.certificates'),
            ],
        ];

        return $menus;
    }

    /** @return list<array<string, mixed>> */
    private static function kabupatenSidebarMenus(): array
    {
        return [
            self::link('Beranda', 'home', 'bx bx-home-circle', badge: 'home_queues'),
            [
                'name' => 'Tugas Wilayah',
                'icon' => 'bx bx-calendar-event',
                'hasSubmenu' => true,
                'items' => [
                    self::subItem('BA Pemberangkatan', 'bap', badge: 'bap_pending', hint: 'Persetujuan keberangkatan jamaah'),
                    self::subItem('Pengaduan', 'pengaduan', badge: 'pengaduan_open'),
                    self::subItem('Jadwal Keberangkatan', 'keberangkatan'),
                ],
            ],
            [
                'name' => 'Data Travel',
                'icon' => 'bx bx-buildings',
                'hasSubmenu' => true,
                'items' => [
                    self::subItem('PPIU Pusat', 'travel'),
                    self::subItem('PPIU Cabang', 'cabang.travel'),
                    self::subItem('Sertifikat', 'sertifikat.index'),
                ],
            ],
            [
                'name' => 'Data Jamaah',
                'icon' => 'bx bx-user-circle',
                'hasSubmenu' => true,
                'items' => [
                    self::subItem('Jamaah Umrah', 'jamaah.umrah'),
                    self::subItem('Jamaah Haji Khusus', 'jamaah.haji-khusus.index'),
                ],
            ],
            self::link('Profil Kepatuhan Wilayah', 'v2.compliance.index', 'bx bx-check-shield'),
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function adminSidebarMenus(): array
    {
        return [
            self::link('Beranda', 'home', 'bx bx-home-circle', badge: 'home_queues'),
            self::link('Antrian Kerja', 'v2.antrian.index', 'bx bx-list-check', badge: 'antrian'),
            [
                'name' => 'Pengawasan',
                'icon' => 'bx bx-analyse',
                'hasSubmenu' => true,
                'groups' => self::pengawasanGroups(forPengawas: true, forAdmin: true, forTravel: false, includeAntrian: false),
            ],
            [
                'name' => 'Operasional',
                'icon' => 'bx bx-calendar-event',
                'hasSubmenu' => true,
                'items' => [
                    self::subItem('BA Pemberangkatan', 'bap', badge: 'bap_pending', hint: 'Persetujuan keberangkatan jamaah'),
                    self::subItem('Jadwal Keberangkatan', 'keberangkatan'),
                    self::subItem('Pengaduan', 'pengaduan', badge: 'pengaduan_open'),
                    self::subItem('Registrasi Travel', 'travel', params: ['filter' => 'pending'], badge: 'registration_pending'),
                ],
            ],
            [
                'name' => 'Data & Master',
                'icon' => 'bx bx-data',
                'hasSubmenu' => true,
                'items' => [
                    self::subItem('PPIU Pusat', 'travel'),
                    self::subItem('PPIU Cabang', 'cabang.travel'),
                    self::subItem('Jamaah Umrah', 'jamaah.umrah'),
                    self::subItem('Jamaah Haji Khusus', 'jamaah.haji-khusus.index'),
                    self::subItem('Sertifikat PPIU', 'sertifikat.index'),
                    self::subItem('Kelola Pengguna', 'users.index'),
                    self::subItem('Kontak Support', 'settings.support.edit'),
                    self::subItem('Atur Checklist', 'v2.checklist.index'),
                ],
            ],
        ];
    }

    /**
     * @return list<array{label: string, items: list<array<string, mixed>>}>
     */
    private static function pengawasanGroups(
        bool $forPengawas,
        bool $forAdmin,
        bool $forTravel,
        bool $includeAntrian = true,
    ): array {
        $groups = [];

        if ($includeAntrian && ($forPengawas || $forAdmin)) {
            $groups[] = [
                'label' => 'Antrian',
                'items' => [
                    self::subItem('Antrian Kerja', 'v2.antrian.index', badge: 'antrian'),
                ],
            ];
        }

        $groups = array_merge($groups, [
            [
                'label' => 'Ringkasan',
                'items' => [
                    self::subItem('Dashboard Pengawasan', 'v2.dashboard'),
                    self::subItem('Monitoring PPIU', 'v2.monitoring.index'),
                ],
            ],
            [
                'label' => 'Operasional',
                'items' => array_values(array_filter([
                    ($forPengawas || $forAdmin)
                        ? self::subItem('BA Pemeriksaan', 'v2.pengawasan.index', hint: 'Inspeksi lapangan PPIU')
                        : null,
                    self::subItem(
                        'Tindak Lanjut Temuan',
                        'v2.followup.index',
                        badge: $forTravel ? 'followup_action' : 'followup_verify',
                    ),
                ])),
            ],
            [
                'label' => 'Analisis',
                'items' => array_values(array_filter([
                    ($forPengawas || $forAdmin)
                        ? self::subItem('Skor Risiko', 'v2.risk.index')
                        : null,
                    self::subItem('Profil Kepatuhan PPIU', 'v2.compliance.index'),
                ])),
            ],
            [
                'label' => 'Pengaturan',
                'items' => array_values(array_filter([
                    $forAdmin ? self::subItem('Atur Checklist', 'v2.checklist.index') : null,
                    ($forPengawas || $forAdmin) ? self::subItem('Log Aktivitas', 'v2.audit-log.index') : null,
                ])),
            ],
        ]);

        return array_values(array_filter(
            $groups,
            fn (array $group) => ($group['items'] ?? []) !== []
        ));
    }

    /** @param  array<string, mixed>  $params */
    private static function link(
        string $name,
        string $route,
        string $icon,
        array $params = [],
        ?string $badge = null,
    ): array {
        return [
            'name' => $name,
            'route' => $route,
            'icon' => $icon,
            'params' => $params,
            'badge' => $badge,
            'visible' => true,
        ];
    }

    /** @param  array<string, mixed>  $params */
    private static function subItem(
        string $name,
        string $route,
        array $params = [],
        ?string $hint = null,
        ?string $badge = null,
    ): array {
        return [
            'name' => $name,
            'route' => $route,
            'params' => $params,
            'hint' => $hint,
            'badge' => $badge,
            'visible' => true,
        ];
    }
}
