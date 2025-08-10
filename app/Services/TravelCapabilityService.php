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

        // Admin has access to all menus
        if ($user->role === 'admin') {
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
                'sertifikat' => true, // Admin can create certificates
            ];
        } else if ($user->role === 'kabupaten') {
            // Kabupaten has limited access
            $menus = [
                'dashboard' => true,
                'jamaah_umrah' => false,
                'jamaah_haji_khusus' => false,
                'bap' => true, // Kabupaten can access BAP
                'pengaduan' => false,
                'keberangkatan' => true,
                'pengunduran' => true,
                'travel_management' => false,
                'cabang_travel' => true,
                'user_management' => false, // Kabupaten cannot manage users
                'sertifikat' => true, // Kabupaten can access certificates for impersonation testing
            ];
        } else if ($user->role === 'user') {
            // Travel user - check based on travel company capabilities
            $travel = $user->travel;
            
            $menus = [
                'dashboard' => true,
                'jamaah_umrah' => $travel ? $travel->canHandleUmrah() : false,
                'jamaah_haji_khusus' => $travel ? $travel->canHandleHajiKhusus() : false,
                'bap' => true, // Travel users always have access to BAP
                'pengaduan' => false, // Travel users cannot access pengaduan
                'keberangkatan' => true,
                'pengunduran' => true,
                'travel_management' => false,
                'cabang_travel' => false,
                'user_management' => false,
                'sertifikat' => false, // Travel users cannot create certificates
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

        // Master Data (Admin only)
        if ($user->role === 'admin') {
            $menus[] = [
                'name' => 'Master Data',
                'icon' => 'bx bx-data',
                'hasSubmenu' => true,
                'items' => [
                                           [
                           'name' => 'Data PPIU Pusat',
                           'route' => 'travel',
                           'icon' => 'bx bxs-plane-alt',
                           'visible' => true,
                       ],
                       [
                           'name' => 'Data PPIU Cabang',
                           'route' => 'cabang.travel',
                           'icon' => 'bx bxs-business',
                           'visible' => true,
                       ],
                    [
                        'name' => 'User Kabupaten',
                        'route' => 'kabupaten.index',
                        'icon' => 'bx bx-user-circle',
                        'visible' => true,
                    ],
                                           [
                           'name' => 'User PPIU',
                           'route' => 'travels.index',
                           'icon' => 'bx bx-user-plus',
                           'visible' => true,
                       ],
                ],
            ];
        }

        // Master Data (Kabupaten only - simplified accordion)
        if ($user->role === 'kabupaten') {
            $menus[] = [
                'name' => 'Master Data',
                'icon' => 'bx bx-data',
                'hasSubmenu' => true,
                'items' => [
                    [
                        'name' => 'Data PPIU Cabang',
                        'route' => 'cabang.travel',
                        'icon' => 'bx bxs-business',
                        'visible' => true,
                    ],
                ],
            ];
        }

        // Data Jamaah (Accordion)
        $jamaahItems = [];
        
        // Add Data BAP for admin, kabupaten, and travel users
        if (in_array($user->role, ['admin', 'kabupaten', 'user'])) {
            $jamaahItems[] = [
                'name' => 'Data BAP',
                'route' => 'bap',
                'icon' => 'bx bx-list-ul',
                'visible' => true,
            ];
        }

        // Add Jamaah menus based on capabilities
        if ($user->role === 'admin') {
            $jamaahItems[] = [
                'name' => 'Jamaah Umrah',
                'route' => 'jamaah.umrah',
                'icon' => 'bx bxs-group',
                'visible' => true,
            ];
            $jamaahItems[] = [
                'name' => 'Jamaah Haji Khusus',
                'route' => 'jamaah.haji-khusus.index',
                'icon' => 'bx bxs-star',
                'visible' => true,
            ];
        } else if ($user->role === 'user') {
            $travel = $user->travel;
            if ($travel) {
                if ($travel->canHandleUmrah()) {
                    $jamaahItems[] = [
                        'name' => 'Jamaah Umrah',
                        'route' => 'jamaah.umrah',
                        'icon' => 'bx bxs-group',
                        'visible' => true,
                    ];
                }
                if ($travel->canHandleHajiKhusus()) {
                    $jamaahItems[] = [
                        'name' => 'Jamaah Haji Khusus',
                        'route' => 'jamaah.haji-khusus.index',
                        'icon' => 'bx bxs-star',
                        'visible' => true,
                    ];
                }
            }
        }

        if (!empty($jamaahItems)) {
            $menus[] = [
                'name' => 'Data Jamaah',
                'icon' => 'bx bx-user-circle',
                'hasSubmenu' => true,
                'items' => $jamaahItems,
            ];
        }

        // Layanan (Accordion)
        $layananItems = [];
        
        $layananItems[] = [
            'name' => 'Keberangkatan',
            'route' => 'keberangkatan',
            'icon' => 'bx bx-calendar',
            'visible' => true,
        ];
        
        if ($user->role === 'admin') {
            $layananItems[] = [
                'name' => 'Pengunduran',
                'route' => 'pengunduran',
                'icon' => 'bx bx-send',
                'visible' => true,
            ];
        }

        if ($user->role === 'admin') {
            $layananItems[] = [
                'name' => 'Pengaduan',
                'route' => 'pengaduan',
                'icon' => 'bx bx-envelope',
                'visible' => true,
            ];
        }

        $menus[] = [
            'name' => 'Layanan',
            'icon' => 'bx bx-cog',
            'hasSubmenu' => true,
            'items' => $layananItems,
        ];

        // Sertifikat (Accordion)
        $sertifikatItems = [];
        
        if ($user->role === 'admin') {
            $sertifikatItems[] = [
                'name' => 'Sertifikat PPIU',
                'route' => 'sertifikat.index',
                'icon' => 'bx bx-award',
                'visible' => true,
            ];
        }
        
        if ($user->role === 'user') {
            $sertifikatItems[] = [
                'name' => 'Sertifikat Saya',
                'route' => 'travel.certificates',
                'icon' => 'bx bx-award',
                'visible' => true,
            ];
        }
        
        if ($user->role === 'kabupaten') {
            $sertifikatItems[] = [
                'name' => 'Sertifikat',
                'route' => 'sertifikat.index',
                'icon' => 'bx bx-award',
                'visible' => true,
            ];
        }

        if (!empty($sertifikatItems)) {
            $menus[] = [
                'name' => 'Sertifikat',
                'icon' => 'bx bx-award',
                'hasSubmenu' => true,
                'items' => $sertifikatItems,
            ];
        }

        return $menus;
    }
} 