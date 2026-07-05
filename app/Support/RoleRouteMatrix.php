<?php

namespace App\Support;

use App\Models\TravelCompany;

/**
 * Matriks akses route V1 & V2 per role, dipakai seeder, PHPUnit, dan Playwright.
 */
final class RoleRouteMatrix
{
    /** @return list<array{role: string, email: string, password: string, label: string}> */
    public static function accounts(): array
    {
        return [
            ['role' => 'admin', 'email' => 'admin@phu.com', 'password' => 'admin123', 'label' => 'Super Admin'],
            ['role' => 'pimpinan', 'email' => 'pimpinan@phu.local', 'password' => 'password123', 'label' => 'Pimpinan Kanwil'],
            ['role' => 'pengawas', 'email' => 'pengawas.lombokbarat@phu.local', 'password' => 'password123', 'label' => 'Pengawas Lombok Barat'],
            ['role' => 'kabupaten', 'email' => 'kabupaten.lombokbarat@phu.com', 'password' => 'password123', 'label' => 'Admin Kabupaten'],
            ['role' => 'user', 'email' => 'lombokbarat.travel@phu.com', 'password' => 'password123', 'label' => 'User Travel'],
        ];
    }

    /** @return array<string, string> role => named route setelah login sukses */
    public static function loginRedirects(): array
    {
        return [
            'admin' => 'home',
            'pimpinan' => 'v2.dashboard',
            'pengawas' => 'v2.antrian.index',
            'kabupaten' => 'home',
            'user' => 'home',
        ];
    }

    /** @return list<string> */
    public static function allRouteNames(): array
    {
        $names = [];

        foreach (self::routeExpectations() as $routes) {
            foreach (array_keys($routes) as $name) {
                $names[$name] = true;
            }
        }

        foreach (self::loginRedirects() as $name) {
            $names[$name] = true;
        }

        return array_keys($names);
    }

    /**
     * @return array<string, array<string, int>> role => [routeName => expectedHttpStatus]
     */
    public static function routeExpectations(): array
    {
        $allow = 200;
        $deny = 403;

        return [
            'admin' => [
                'home' => $allow,
                'users.index' => $allow,
                'pengaduan' => $allow,
                'jamaah.umrah' => $allow,
                'travel' => $allow,
                'v2.dashboard' => $allow,
                'v2.antrian.index' => $allow,
                'v2.pengawasan.index' => $allow,
                'v2.monitoring.index' => $allow,
                'v2.monitoring.travel.pengaduan' => $allow,
                'v2.monitoring.kabupaten.pengaduan' => $allow,
                'v2.checklist.index' => $allow,
                'v2.followup.index' => $allow,
                'v2.risk.index' => $allow,
                'v2.compliance.index' => $allow,
                'v2.audit-log.index' => $allow,
                'v2.notifications.index' => $allow,
            ],
            'pimpinan' => [
                'home' => 302,
                'users.index' => $deny,
                'pengaduan' => $deny,
                'jamaah.umrah' => $allow,
                'travel' => $allow,
                'v2.dashboard' => $allow,
                'v2.antrian.index' => $deny,
                'v2.pengawasan.index' => $deny,
                'v2.monitoring.index' => $allow,
                'v2.monitoring.travel.pengaduan' => $allow,
                'v2.monitoring.kabupaten.pengaduan' => $allow,
                'v2.checklist.index' => $deny,
                'v2.followup.index' => $deny,
                'v2.risk.index' => $deny,
                'v2.compliance.index' => $deny,
                'v2.audit-log.index' => $deny,
                'v2.notifications.index' => $allow,
            ],
            'pengawas' => [
                'home' => 302,
                'users.index' => $deny,
                'pengaduan' => $deny,
                'jamaah.umrah' => $allow,
                'travel' => $allow,
                'v2.dashboard' => $allow,
                'v2.antrian.index' => $allow,
                'v2.pengawasan.index' => $allow,
                'v2.monitoring.index' => $allow,
                'v2.monitoring.travel.pengaduan' => $allow,
                'v2.monitoring.kabupaten.pengaduan' => $allow,
                'v2.checklist.index' => $deny,
                'v2.followup.index' => $allow,
                'v2.risk.index' => $allow,
                'v2.compliance.index' => $allow,
                'v2.audit-log.index' => $allow,
                'v2.notifications.index' => $allow,
            ],
            'kabupaten' => [
                'home' => $allow,
                'users.index' => $deny,
                'pengaduan' => $deny,
                'jamaah.umrah' => $allow,
                'travel' => $allow,
                'v2.dashboard' => $deny,
                'v2.antrian.index' => $deny,
                'v2.pengawasan.index' => $deny,
                'v2.monitoring.index' => $deny,
                'v2.checklist.index' => $deny,
                'v2.followup.index' => $deny,
                'v2.risk.index' => $deny,
                'v2.compliance.index' => $deny,
                'v2.audit-log.index' => $deny,
                'v2.notifications.index' => $allow,
            ],
            'user' => [
                'home' => $allow,
                'users.index' => $deny,
                'pengaduan' => $deny,
                'jamaah.umrah' => $allow,
                'travel' => $allow,
                'v2.dashboard' => $deny,
                'v2.antrian.index' => $deny,
                'v2.pengawasan.index' => $allow,
                'v2.monitoring.index' => $deny,
                'v2.checklist.index' => $deny,
                'v2.followup.index' => $allow,
                'v2.risk.index' => $allow,
                'v2.compliance.index' => $allow,
                'v2.audit-log.index' => $deny,
                'v2.notifications.index' => $allow,
            ],
        ];
    }

    /**
     * Parameter wajib untuk route yang punya placeholder URI (mis. {travel}).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function routeParameters(): array
    {
        $travelId = TravelCompany::where('kab_kota', 'Lombok Barat')->value('id')
            ?? TravelCompany::query()->value('id')
            ?? 1;

        return [
            'v2.monitoring.travel.pengaduan' => ['travel' => $travelId],
            'v2.monitoring.kabupaten.pengaduan' => ['kabupaten' => 'Lombok Barat'],
        ];
    }

    public static function urlForRoute(string $routeName): string
    {
        return route($routeName, self::routeParameters()[$routeName] ?? []);
    }

    /** @return array<string, mixed> */
    public static function toPlaywrightFixture(string $baseUrl): array
    {
        $paths = [];

        foreach (self::allRouteNames() as $routeName) {
            $paths[$routeName] = parse_url(self::urlForRoute($routeName), PHP_URL_PATH);
        }

        return [
            'baseUrl' => rtrim($baseUrl, '/'),
            'accounts' => self::accounts(),
            'loginRedirects' => self::loginRedirects(),
            'routeExpectations' => self::routeExpectations(),
            'paths' => $paths,
        ];
    }
}
