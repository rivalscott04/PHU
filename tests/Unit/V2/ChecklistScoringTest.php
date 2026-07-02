<?php

namespace Tests\Unit\V2;

use App\Enums\ChecklistInputType;
use App\Models\Checklist;
use App\Models\ChecklistCategory;
use App\Models\ChecklistOption;
use App\Models\InspectionChecklist;
use App\Support\ChecklistScoring;
use Tests\TestCase;

class ChecklistScoringTest extends TestCase
{
    public function test_boolean_score_uses_weight_when_answer_is_yes(): void
    {
        $master = new Checklist([
            'input_type' => ChecklistInputType::Boolean,
            'weight' => 15,
        ]);

        $this->assertSame(15, ChecklistScoring::scoreItem($master, '1'));
        $this->assertSame(0, ChecklistScoring::scoreItem($master, '0'));
    }

    public function test_option_score_uses_option_weight(): void
    {
        $category = ChecklistCategory::make(['name' => 'Operasional']);
        $master = new Checklist([
            'input_type' => ChecklistInputType::Option,
            'weight' => 10,
        ]);
        $master->setRelation('options', collect([
            new ChecklistOption(['label' => 'Baik', 'value' => 'good', 'score' => 10]),
            new ChecklistOption(['label' => 'Kurang', 'value' => 'bad', 'score' => 2]),
        ]));

        $this->assertSame(10, ChecklistScoring::scoreItem($master, 'good'));
        $this->assertSame(2, ChecklistScoring::scoreItem($master, 'bad'));
    }

    public function test_overall_score_is_weighted_percentage(): void
    {
        $booleanMaster = new Checklist(['input_type' => ChecklistInputType::Boolean, 'weight' => 10]);
        $optionMaster = new Checklist(['input_type' => ChecklistInputType::Option, 'weight' => 10]);
        $optionMaster->setRelation('options', collect([
            new ChecklistOption(['label' => 'Baik', 'value' => 'good', 'score' => 10]),
        ]));

        $items = collect([
            tap(new InspectionChecklist(['answer' => '1', 'score' => 10]), fn ($item) => $item->setRelation('masterChecklist', $booleanMaster)),
            tap(new InspectionChecklist(['answer' => 'good', 'score' => 10]), fn ($item) => $item->setRelation('masterChecklist', $optionMaster)),
        ]);

        $this->assertSame(100.0, ChecklistScoring::overallScore($items));
    }

    public function test_format_answer_uses_human_labels(): void
    {
        $this->assertSame('Ya', ChecklistScoring::formatAnswer('1', ChecklistInputType::Boolean));
        $this->assertSame('Tidak', ChecklistScoring::formatAnswer('0', ChecklistInputType::Boolean));
        $this->assertSame('Belum diisi', ChecklistScoring::formatAnswer(null, ChecklistInputType::Boolean));
    }
}
