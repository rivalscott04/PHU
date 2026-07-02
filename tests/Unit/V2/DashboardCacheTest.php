<?php

namespace Tests\Unit\V2;

use App\Support\DashboardCache;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardCacheTest extends TestCase
{
    public function test_monitoring_cache_key_is_stable(): void
    {
        $keyA = DashboardCache::monitoringKey('Lombok Barat', 5);
        $keyB = DashboardCache::monitoringKey('Lombok Barat', 5);

        $this->assertSame($keyA, $keyB);
        $this->assertNotSame($keyA, DashboardCache::monitoringKey('Lombok Tengah', 5));
    }

    public function test_flush_skips_when_using_array_cache_driver(): void
    {
        config(['cache.default' => 'array']);

        Cache::put(DashboardCache::KABUPATEN_OPTIONS_KEY, ['test'], 60);

        DashboardCache::flush();

        $this->assertSame(['test'], Cache::get(DashboardCache::KABUPATEN_OPTIONS_KEY));
    }
}
