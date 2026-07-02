<?php

namespace App\Support;

use App\Enums\ChecklistInputType;
use App\Models\Checklist;
use App\Models\InspectionChecklist;
use Illuminate\Support\Collection;

class ChecklistScoring
{
    public static function scoreItem(Checklist $master, ?string $answer): ?int
    {
        if ($answer === null || $answer === '') {
            return null;
        }

        return match ($master->input_type) {
            ChecklistInputType::Boolean => self::isTruthy($answer) ? $master->weight : 0,
            ChecklistInputType::Option => self::optionScore($master, $answer),
            ChecklistInputType::Number, ChecklistInputType::Text, ChecklistInputType::File, ChecklistInputType::Photo => null,
        };
    }

    public static function overallScore(Collection $items): ?float
    {
        $scorable = $items->filter(function (InspectionChecklist $item) {
            $type = $item->masterChecklist?->input_type;

            return $type === ChecklistInputType::Boolean || $type === ChecklistInputType::Option;
        });

        if ($scorable->isEmpty()) {
            return null;
        }

        $totalWeight = $scorable->sum(fn (InspectionChecklist $item) => $item->masterChecklist?->weight ?? 0);

        if ($totalWeight <= 0) {
            return null;
        }

        $earned = $scorable->sum(fn (InspectionChecklist $item) => $item->score ?? 0);

        return round(($earned / $totalWeight) * 100, 2);
    }

    public static function formatAnswer(?string $answer, ChecklistInputType $type, ?Collection $options = null): string
    {
        if ($answer === null || $answer === '') {
            return 'Belum diisi';
        }

        return match ($type) {
            ChecklistInputType::Boolean => self::isTruthy($answer) ? 'Ya' : 'Tidak',
            ChecklistInputType::Option => $options?->firstWhere('value', $answer)?->label ?? $answer,
            default => $answer,
        };
    }

    private static function isTruthy(string $answer): bool
    {
        return in_array(strtolower($answer), ['1', 'yes', 'ya', 'true'], true);
    }

    private static function optionScore(Checklist $master, string $answer): int
    {
        $option = $master->relationLoaded('options')
            ? $master->options->firstWhere('value', $answer)
            : $master->options()->where('value', $answer)->first();

        return $option?->score ?? 0;
    }
}
