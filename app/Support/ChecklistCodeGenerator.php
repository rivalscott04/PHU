<?php

namespace App\Support;

use App\Models\Checklist;
use App\Models\ChecklistCategory;
use Illuminate\Support\Carbon;

class ChecklistCodeGenerator
{
    /** @var array<string, string> */
    private const CATEGORY_ABBREVS = [
        'legalitas' => 'LEG',
        'operasional' => 'OPS',
        'keuangan' => 'FIN',
    ];

    public static function generate(ChecklistCategory $category, ?Carbon $at = null): string
    {
        $at ??= now();
        $abbrev = self::categoryAbbrev($category->name);
        $period = $at->format('Ym');
        $prefix = $abbrev.$period;

        $latest = Checklist::query()
            ->where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->value('code');

        $sequence = 1;
        if ($latest !== null && str_starts_with($latest, $prefix) && strlen($latest) === strlen($prefix) + 3) {
            $sequence = (int) substr($latest, -3) + 1;
        }

        return sprintf('%s%03d', $prefix, $sequence);
    }

    public static function categoryAbbrev(string $categoryName): string
    {
        $normalized = strtolower(trim($categoryName));

        if (isset(self::CATEGORY_ABBREVS[$normalized])) {
            return self::CATEGORY_ABBREVS[$normalized];
        }

        $letters = preg_replace('/[^A-Za-z]/', '', $categoryName) ?? '';

        return strtoupper(substr($letters, 0, 3)) ?: 'CHK';
    }
}
