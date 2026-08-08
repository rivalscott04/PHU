<?php

use App\Models\Checklist;
use App\Support\ChecklistCodeGenerator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('master_checklists')) {
            return;
        }

        Checklist::query()
            ->with('category')
            ->where('code', 'like', '%-%')
            ->orderBy('id')
            ->each(function (Checklist $checklist): void {
                $category = $checklist->category;

                if ($category === null) {
                    return;
                }

                if (! preg_match('/-(\d+)$/', $checklist->code, $matches)) {
                    return;
                }

                $abbrev = ChecklistCodeGenerator::categoryAbbrev($category->name);
                $period = $checklist->created_at?->format('Ym') ?? now()->format('Ym');
                $newCode = sprintf('%s%s%03d', $abbrev, $period, (int) $matches[1]);

                if ($newCode === $checklist->code) {
                    return;
                }

                if (Checklist::query()->where('code', $newCode)->whereKeyNot($checklist->id)->exists()) {
                    $newCode = ChecklistCodeGenerator::generate($category, $checklist->created_at);
                }

                $checklist->update(['code' => $newCode]);
            });
    }

    public function down(): void
    {
        // Irreversible: old dash codes are not restored.
    }
};
