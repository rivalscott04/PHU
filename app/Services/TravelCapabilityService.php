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
                'pengunduran' => true,
                'travel_management' => true,
                'cabang_travel' => true,
                'user_management' => true,
                'sertifikat' => true,
            ];
        } elseif ($user->role === UserRole::Kabupaten->value) {
            $menus = [
                'dashboard' => true,
                'jamaah_umrah' => false,
                'jamaah_haji_khusus' => false,
                'bap' => true,
                'pengaduan' => false,
                'keberangkatan' => true,
                'pengunduran' => true,
                'travel_management' => false,
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
                'pengaduan' => false,
                'keberangkatan' => true,
                'pengunduran' => true,
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
     */
    public static function getSidebarMenus()
    {
        $user = Auth::user();

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
        return [[
            'name' => 'Ringkasan Eksekutif',
            'icon' => 'bx bx-bar-chart-alt-2',
            'hasSubmenu' => true,
            'items' => [
                ['name' => 'Dashboard Pengawasan', 'route' => 'v2.dashboard', 'visible' => true],
                ['name' => 'Monitoring PPIU', 'route' => 'v2.monitoring.index', 'visible' => true],
            ],
        ]];
    }

    /** @return list<array<string, mixed>> */
    private static function pengawasSidebarMenus(): array
    {
        return [[
            'name' => 'Pengawasan Digital',
            'icon' => 'bx bx-analyse',
            'hasSubmenu' => true,
            'groups' => self::pengawasanGroups(forPengawas: true, forAdmin: false, forTravel: false),
        ]];
    }

    /** @return list<array<string, mixed>> */
    private static function travelSidebarMenus(User $user): array
    {
        $menus = [[
            'name' => 'Beranda',
            'route' => 'home',
            'icon' => 'bx bx-home-circle',
            'visible' => true,
        ]];

        $travel = $user->travel;
        $jamaahItems = [];
        if ($travel?->canHandleUmrah()) {
            $jamaahItems[] = ['name' => 'Jamaah Umrah', 'route' => 'jamaah.umrah', 'visible' => true];
        }
        if ($travel?->canHandleHajiKhusus()) {
            $jamaahItems[] = ['name' => 'Jamaah Haji Khusus', 'route' => 'jamaah.haji-khusus.index', 'visible' => true];
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
            'icon' => 'bx bx-cog',
            'hasSubmenu' => true,
            'items' => [
                ['name' => 'BA Pemberangkatan', 'route' => 'bap', 'visible' => true],
                ['name' => 'Jadwal Keberangkatan', 'route' => 'keberangkatan', 'visible' => true],
            ],
        ];

        $menus[] = [
            'name' => 'Sertifikat',
            'icon' => 'bx bx-award',
            'hasSubmenu' => true,
            'items' => [
                ['name' => 'Sertifikat Saya', 'route' => 'travel.certificates', 'visible' => true],
            ],
        ];

        $pengawasanGroups = self::pengawasanGroups(forPengawas: false, forAdmin: false, forTravel: true);
        if ($pengawasanGroups !== []) {
            $menus[] = [
                'name' => 'Tugas Pengawasan',
                'icon' => 'bx bx-task',
                'hasSubmenu' => true,
                'groups' => $pengawasanGroups,
            ];
        }

        return $menus;
    }

    /** @return list<array<string, mixed>> */
    private static function kabupatenSidebarMenus(): array
    {
        return [
            [
                'name' => 'Beranda',
                'route' => 'home',
                'icon' => 'bx bx-home-circle',
                'visible' => true,
            ],
            [
                'name' => 'Master Data',
                'icon' => 'bx bx-data',
                'hasSubmenu' => true,
                'items' => [
                    ['name' => 'Data PPIU Cabang', 'route' => 'cabang.travel', 'visible' => true],
                ],
            ],
            [
                'name' => 'Keberangkatan',
                'icon' => 'bx bx-cog',
                'hasSubmenu' => true,
                'items' => [
                    ['name' => 'BA Pemberangkatan', 'route' => 'bap', 'visible' => true],
                    ['name' => 'Jadwal Keberangkatan', 'route' => 'keberangkatan', 'visible' => true],
                    ['name' => 'Pengunduran', 'route' => 'pengunduran', 'visible' => true],
                ],
            ],
            [
                'name' => 'Sertifikat',
                'icon' => 'bx bx-award',
                'hasSubmenu' => true,
                'items' => [
                    ['name' => 'Sertifikat', 'route' => 'sertifikat.index', 'visible' => true],
                ],
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private static function adminSidebarMenus(): array
    {
        $menus = [[
            'name' => 'Beranda',
            'route' => 'home',
            'icon' => 'bx bx-home-circle',
            'visible' => true,
        ]];

        $menus[] = [
            'name' => 'Master Data',
            'icon' => 'bx bx-data',
            'hasSubmenu' => true,
            'items' => [
                ['name' => 'Data PPIU Pusat', 'route' => 'travel', 'visible' => true],
                ['name' => 'Data PPIU Cabang', 'route' => 'cabang.travel', 'visible' => true],
                ['name' => 'Kelola Pengguna', 'route' => 'users.index', 'visible' => true],
            ],
        ];

        $menus[] = [
            'name' => 'Data Jamaah',
            'icon' => 'bx bx-user-circle',
            'hasSubmenu' => true,
            'items' => [
                ['name' => 'Jamaah Umrah', 'route' => 'jamaah.umrah', 'visible' => true],
                ['name' => 'Jamaah Haji Khusus', 'route' => 'jamaah.haji-khusus.index', 'visible' => true],
            ],
        ];

        $menus[] = [
            'name' => 'Keberangkatan',
            'icon' => 'bx bx-cog',
            'hasSubmenu' => true,
            'items' => [
                ['name' => 'BA Pemberangkatan', 'route' => 'bap', 'visible' => true],
                ['name' => 'Jadwal Keberangkatan', 'route' => 'keberangkatan', 'visible' => true],
                ['name' => 'Pengunduran', 'route' => 'pengunduran', 'visible' => true],
                ['name' => 'Pengaduan', 'route' => 'pengaduan', 'visible' => true],
            ],
        ];

        $menus[] = [
            'name' => 'Sertifikat',
            'icon' => 'bx bx-award',
            'hasSubmenu' => true,
            'items' => [
                ['name' => 'Sertifikat PPIU', 'route' => 'sertifikat.index', 'visible' => true],
            ],
        ];

        $pengawasanGroups = self::pengawasanGroups(forPengawas: true, forAdmin: true, forTravel: false);
        if ($pengawasanGroups !== []) {
            $menus[] = [
                'name' => 'Pengawasan Digital',
                'icon' => 'bx bx-analyse',
                'hasSubmenu' => true,
                'groups' => $pengawasanGroups,
            ];
        }

        return $menus;
    }

    /**
     * @return list<array{label: string, items: list<array{name: string, route: string, visible: bool}>}>
     */
    private static function pengawasanGroups(bool $forPengawas, bool $forAdmin, bool $forTravel): array
    {
        $groups = [
            [
                'label' => 'Antrian',
                'items' => [
                    [
                        'name' => 'Antrian Kerja',
                        'route' => 'v2.antrian.index',
                        'visible' => $forPengawas || $forAdmin,
                    ],
                ],
            ],
            [
                'label' => 'Ringkasan',
                'items' => [
                    [
                        'name' => 'Dashboard Pengawasan',
                        'route' => 'v2.dashboard',
                        'visible' => $forPengawas || $forAdmin,
                    ],
                    [
                        'name' => 'Monitoring PPIU',
                        'route' => 'v2.monitoring.index',
                        'visible' => $forPengawas || $forAdmin,
                    ],
                ],
            ],
            [
                'label' => 'Operasional',
                'items' => [
                    [
                        'name' => 'BA Pemeriksaan',
                        'route' => 'v2.pengawasan.index',
                        'visible' => $forPengawas || $forAdmin,
                    ],
                    [
                        'name' => 'Tindak Lanjut Temuan',
                        'route' => 'v2.followup.index',
                        'visible' => $forPengawas || $forAdmin || $forTravel,
                    ],
                ],
            ],
            [
                'label' => 'Analisis',
                'items' => [
                    [
                        'name' => 'Skor Risiko',
                        'route' => 'v2.risk.index',
                        'visible' => $forPengawas || $forAdmin || $forTravel,
                    ],
                    [
                        'name' => 'Profil Kepatuhan PPIU',
                        'route' => 'v2.compliance.index',
                        'visible' => $forPengawas || $forAdmin || $forTravel,
                    ],
                ],
            ],
            [
                'label' => 'Pengaturan',
                'items' => [
                    [
                        'name' => 'Atur Checklist',
                        'route' => 'v2.checklist.index',
                        'visible' => $forAdmin,
                    ],
                    [
                        'name' => 'Log Aktivitas',
                        'route' => 'v2.audit-log.index',
                        'visible' => $forPengawas || $forAdmin,
                    ],
                ],
            ],
        ];

        return array_values(array_filter(
            $groups,
            fn (array $group) => collect($group['items'])->contains(fn (array $item) => $item['visible'])
        ));
    }
}
