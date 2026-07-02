<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class DashboardCache
{
    public const KABUPATEN_OPTIONS_KEY = 'dashboard.kabupaten_options';

    public static function flush(): void
    {
        if (config('cache.default') === 'array') {
            return;
        }

        Cache::forget(self::KABUPATEN_OPTIONS_KEY);
        Cache::flush();
    }

    public static function monitoringKey(?string $kabupaten, ?int $travelId): string
    {
        return 'monitoring.kpi.'.md5(json_encode([$kabupaten, $travelId]));
    }

    public static function forgetMonitoring(?string $kabupaten = null, ?int $travelId = null): void
    {
        Cache::forget(self::monitoringKey($kabupaten, $travelId));
    }
}
