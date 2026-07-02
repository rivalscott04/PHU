<?php

namespace Tests\Unit\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\FollowupStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\Checklist;
use App\Models\ChecklistCategory;
use App\Models\ChecklistOption;
use App\Models\Followup;
use App\Models\FollowupLog;
use App\Models\Inspection;
use App\Models\InspectionChecklist;
use App\Models\InspectionFinding;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2025_08_11_000002_create_travels_table.php',
                'database/migrations/2026_07_02_100001_create_master_checklist_categories_table.php',
                'database/migrations/2026_07_02_100002_create_master_checklists_table.php',
                'database/migrations/2026_07_02_100003_create_master_checklist_options_table.php',
                'database/migrations/2026_07_02_100004_create_pengawasan_table.php',
                'database/migrations/2026_07_02_100005_create_pengawasan_checklists_table.php',
                'database/migrations/2026_07_02_100006_create_pengawasan_temuan_table.php',
                'database/migrations/2026_07_02_100007_create_pengawasan_photos_table.php',
                'database/migrations/2026_07_02_100008_create_pengawasan_followups_table.php',
                'database/migrations/2026_07_02_100009_create_pengawasan_followup_logs_table.php',
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
            ],
        ]);
    }

    public function test_checklist_category_relationships(): void
    {
        $category = ChecklistCategory::create([
            'name' => 'Legalitas',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $checklist = Checklist::create([
            'category_id' => $category->id,
            'code' => 'LEG-001',
            'title' => 'Izin Operasional',
            'input_type' => 'BOOLEAN',
            'weight' => 10,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ChecklistOption::create([
            'checklist_id' => $checklist->id,
            'label' => 'Ya',
            'value' => 'yes',
            'score' => 100,
            'sort_order' => 1,
        ]);

        $category->load('checklists.options');

        $this->assertCount(1, $category->checklists);
        $this->assertSame('LEG-001', $category->checklists->first()->code);
        $this->assertCount(1, $category->checklists->first()->options);
        $this->assertSame($category->id, $checklist->fresh()->category->id);
    }

    public function test_inspection_relationship_chain(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        $inspection = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-0002',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::Draft,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $category = ChecklistCategory::create([
            'name' => 'Operasional',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $masterChecklist = Checklist::create([
            'category_id' => $category->id,
            'code' => 'OPS-001',
            'title' => 'Kantor Aktif',
            'input_type' => 'BOOLEAN',
            'weight' => 7,
            'required' => true,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        InspectionChecklist::create([
            'inspection_id' => $inspection->id,
            'master_checklist_id' => $masterChecklist->id,
            'answer' => 'Ya',
            'score' => 100,
        ]);

        $finding = InspectionFinding::create([
            'inspection_id' => $inspection->id,
            'category' => 'Operasional',
            'severity' => FindingSeverity::Major,
            'title' => 'Kantor tidak aktif',
            'description' => 'Kantor tampak tidak beroperasi.',
            'recommendation' => 'Aktifkan kembali kantor operasional.',
            'deadline' => now()->addDays(14),
            'status' => FindingStatus::Open,
        ]);

        $followup = Followup::create([
            'finding_id' => $finding->id,
            'description' => 'Foto kantor aktif diunggah.',
            'status' => FollowupStatus::Submitted,
            'submitted_at' => now(),
        ]);

        FollowupLog::create([
            'followup_id' => $followup->id,
            'status' => FollowupStatus::Submitted->value,
            'description' => 'Upload bukti tindak lanjut.',
            'created_by' => $user->id,
        ]);

        RiskScore::create([
            'travel_id' => $travel->id,
            'total_score' => 55,
            'risk_level' => RiskLevel::Medium,
            'last_calculated_at' => now(),
        ]);

        $inspection->load(['travel', 'checklists.masterChecklist', 'findings.followups.logs']);
        $travel->load(['inspections', 'riskScore']);

        $this->assertSame($travel->id, $inspection->travel->id);
        $this->assertCount(1, $inspection->checklists);
        $this->assertSame('OPS-001', $inspection->checklists->first()->masterChecklist->code);
        $this->assertCount(1, $inspection->findings);
        $this->assertCount(1, $inspection->findings->first()->followups);
        $this->assertCount(1, $inspection->findings->first()->followups->first()->logs);
        $this->assertCount(1, $travel->inspections);
        $this->assertSame(RiskLevel::Medium, $travel->riskScore->risk_level);
    }
}
