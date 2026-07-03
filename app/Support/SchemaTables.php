<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class SchemaTables
{
    /** @var array<string, bool> */
    private static array $cache = [];

    private static bool $booted = false;

    /** @var list<string> */
    private const KNOWN_TABLES = [
        'jamaah',
        'pengaduan',
        'sertifikat',
        'bap',
        'pengawasan',
        'pengawasan_temuan',
        'pengawasan_followups',
        'travel_cabang',
        'jamaah_haji_khusus',
        'audit_logs',
    ];

    public static function has(string $table): bool
    {
        self::boot();

        if (array_key_exists($table, self::$cache)) {
            return self::$cache[$table];
        }

        return self::$cache[$table] = Schema::hasTable($table);
    }

    private static function boot(): void
    {
        if (self::$booted) {
            return;
        }

        self::$booted = true;

        $rows = DB::select(
            'SELECT TABLE_NAME AS table_name FROM information_schema.tables WHERE table_schema = ? AND TABLE_NAME IN ('.implode(', ', array_fill(0, count(self::KNOWN_TABLES), '?')).')',
            array_merge([DB::connection()->getDatabaseName()], self::KNOWN_TABLES)
        );

        $existing = [];
        foreach ($rows as $row) {
            $existing[strtolower((string) $row->table_name)] = true;
        }

        foreach (self::KNOWN_TABLES as $table) {
            self::$cache[$table] = isset($existing[strtolower($table)]);
        }
    }
}
