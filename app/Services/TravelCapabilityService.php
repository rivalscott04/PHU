<?php

namespace App\Services;

use App\Models\TravelCompany;
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

        // Admin and Kabupaten always have access to all menus
        if (in_array($user->role, ['admin', 'kabupaten'])) {
            $menus = [
                'dashboard' => true,
                'jamaah_umrah' => true,
                'jamaah_haji_khusus' => true,
                'bap' => true,
                'pengaduan' => true,
                'keberangkatan' => true,
                'pengunduran' => true,
                'travel_management' => $user->role === 'admin',
                'cabang_travel' => true,
                'user_management' => $user->role === 'admin',
            ];
        } else {
            // Travel user - check based on travel company capabilities
            $travel = $user->travel;
            
            if ($travel) {
                $menus = [
                    'dashboard' => true,
                    'jamaah_umrah' => $travel->canHandleUmrah(),
                    'jamaah_haji_khusus' => $travel->canHandleHajiKhusus(),
                    'bap' => true,
                    'pengaduan' => true,
                    'keberangkatan' => true,
                    'pengunduran' => true,
                    'travel_management' => false,
                    'cabang_travel' => false,
                    'user_management' => false,
                ];
            }
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
            'PPIU' => 'PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)',
            'PIHK' => 'PIHK - Penyelenggara Ibadah Haji Khusus (Haji & Umrah)',
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

        // PPIU can only do Umrah
        if ($status === 'PPIU' && $canHaji) {
            $errors[] = 'PPIU travel companies can only handle Umrah services.';
        }

        // PIHK can do both Haji and Umrah
        if ($status === 'PIHK' && !$canHaji) {
            $errors[] = 'PIHK travel companies must be able to handle Haji services.';
        }

        // Must have at least one service
        if (!$canHaji && !$canUmrah) {
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
        $menus = [];

        // Dashboard
        $menus[] = [
            'name' => 'Dashboard',
            'route' => 'home',
            'icon' => 'bx bx-home-circle',
            'badge' => '04',
            'visible' => true,
        ];

        // Master Travel (Admin only)
        if ($user->role === 'admin') {
            $menus[] = [
                'name' => 'Master Travel',
                'items' => [
                    [
                        'name' => 'Data Travel',
                        'route' => 'travel',
                        'icon' => 'bx bxs-plane-alt',
                        'visible' => true,
                    ],
                    [
                        'name' => 'Data Cabang Travel',
                        'route' => 'cabang.travel',
                        'icon' => 'bx bxs-business',
                        'visible' => true,
                    ],
                    [
                        'name' => 'Akun Travel',
                        'route' => 'travels',
                        'icon' => 'bx bx-user-plus',
                        'visible' => true,
                    ],
                ],
            ];
        }

        // Travel Services
        $travelServices = [
            [
                'name' => 'Data BAP',
                'route' => 'bap',
                'icon' => 'bx bx-list-ul',
                'visible' => true,
            ],
        ];

        // Add Jamaah menus based on capabilities
        if (in_array($user->role, ['admin', 'kabupaten'])) {
            $travelServices[] = [
                'name' => 'Jamaah Umrah',
                'route' => 'jamaah.umrah',
                'icon' => 'bx bxs-group',
                'visible' => true,
            ];
            $travelServices[] = [
                'name' => 'Jamaah Haji Khusus',
                'route' => 'jamaah.haji-khusus.index',
                'icon' => 'bx bxs-star',
                'visible' => true,
            ];
        } else {
            $travel = $user->travel;
            if ($travel) {
                if ($travel->canHandleUmrah()) {
                    $travelServices[] = [
                        'name' => 'Jamaah Umrah',
                        'route' => 'jamaah.umrah',
                        'icon' => 'bx bxs-group',
                        'visible' => true,
                    ];
                }
                if ($travel->canHandleHajiKhusus()) {
                    $travelServices[] = [
                        'name' => 'Jamaah Haji Khusus',
                        'route' => 'jamaah.haji-khusus.index',
                        'icon' => 'bx bxs-star',
                        'visible' => true,
                    ];
                }
            }
        }

        $menus[] = [
            'name' => 'Travel',
            'items' => $travelServices,
        ];

        // Other menus
        $menus[] = [
            'name' => 'Other Services',
            'items' => [
                [
                    'name' => 'Pengaduan',
                    'route' => 'pengaduan',
                    'icon' => 'bx bx-envelope',
                    'visible' => true,
                ],
                [
                    'name' => 'Keberangkatan',
                    'route' => 'keberangkatan',
                    'icon' => 'bx bx-calendar',
                    'visible' => true,
                ],
                [
                    'name' => 'Pengunduran',
                    'route' => in_array($user->role, ['admin', 'kabupaten']) ? 'pengunduran' : 'pengunduran.create',
                    'icon' => 'bx bx-send',
                    'visible' => true,
                ],
            ],
        ];

        return $menus;
    }
} 