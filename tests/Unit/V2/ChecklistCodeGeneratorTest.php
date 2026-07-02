<?php

namespace Tests\Unit\V2;

use App\Models\Checklist;
use App\Models\ChecklistCategory;
use App\Support\ChecklistCodeGenerator;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ChecklistCodeGeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2026_07_02_100001_create_master_checklist_categories_table.php',
                'database/migrations/2026_07_02_100002_create_master_checklists_table.php',
            ],
        ]);
    }
    public function test_generates_code_with_category_month_and_sequence(): void
    {
        $category = new ChecklistCategory(['name' => 'Legalitas']);

        $code = ChecklistCodeGenerator::generate($category, now()->setDate(2026, 7, 3));

        $this->assertSame('LEG202607001', $code);
    }

    public function test_increments_sequence_within_same_category_and_month(): void
    {
        $category = ChecklistCategory::create([
            'name' => 'Operasional',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Checklist::create([
            'category_id' => $category->id,
            'code' => 'OPS202607001',
            'title' => 'Item pertama',
            'input_type' => 'BOOLEAN',
            'weight' => 5,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $code = ChecklistCodeGenerator::generate($category, now()->setDate(2026, 7, 3));

        $this->assertSame('OPS202607002', $code);
    }

    public function test_uses_known_category_abbreviation(): void
    {
        $this->assertSame('FIN', ChecklistCodeGenerator::categoryAbbrev('Keuangan'));
        $this->assertSame('OPS', ChecklistCodeGenerator::categoryAbbrev('Operasional'));
    }
}
