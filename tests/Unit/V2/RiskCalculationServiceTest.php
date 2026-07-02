<?php

namespace Tests\Unit\V2;

use App\Enums\FindingSeverity;
use App\Enums\FindingStatus;
use App\Enums\InspectionStatus;
use App\Enums\InspectionType;
use App\Enums\RiskLevel;
use App\Models\Inspection;
use App\Models\InspectionFinding;
use App\Models\RiskScore;
use App\Models\TravelCompany;
use App\Models\User;
use App\Repositories\RiskRepository;
use App\Services\AuditLogService;
use App\Services\RiskCalculationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RiskCalculationServiceTest extends TestCase
{
    private RiskCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('migrate:fresh', [
            '--path' => [
                'database/migrations/2025_08_11_000001_create_admin_users_table.php',
                'database/migrations/2025_08_11_000002_create_travels_table.php',
                'database/migrations/2026_07_02_100004_create_pengawasan_table.php',
                'database/migrations/2026_07_02_100006_create_pengawasan_temuan_table.php',
                'database/migrations/2026_07_02_100010_create_risk_scores_table.php',
                'database/migrations/2026_07_02_100011_create_audit_logs_table.php',
            ],
        ]);

        $this->service = new RiskCalculationService(new RiskRepository(), new AuditLogService());

        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengadu');
            $table->unsignedBigInteger('travels_id')->nullable();
            $table->text('hal_aduan')->nullable();
            $table->string('berkas_aduan')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function test_complaint_score_follows_module_tiers(): void
    {
        $travel = TravelCompany::first();

        $breakdown = $this->service->getBreakdown($travel->id);
        $this->assertSame(0.0, $breakdown['scores']['complaint_score']);

        for ($i = 0; $i < 2; $i++) {
            \DB::table('pengaduan')->insert([
                'nama_pengadu' => 'Pengadu '.$i,
                'travels_id' => $travel->id,
                'hal_aduan' => 'Test aduan',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $breakdown = $this->service->getBreakdown($travel->id);
        $this->assertSame(10.0, $breakdown['scores']['complaint_score']);
    }

    public function test_inspection_and_followup_scores_use_severity_and_deadline(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        $inspectionId = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-4001',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ])->id;

        InspectionFinding::create([
            'inspection_id' => $inspectionId,
            'category' => 'Legalitas',
            'severity' => FindingSeverity::Critical,
            'title' => 'Kritis',
            'description' => 'Deskripsi',
            'recommendation' => 'Rekomendasi',
            'deadline' => now()->subDay(),
            'status' => FindingStatus::Open,
        ]);

        $breakdown = $this->service->getBreakdown($travel->id);

        $this->assertSame(20.0, $breakdown['scores']['inspection_score']);
        $this->assertSame(15.0, $breakdown['scores']['followup_score']);
        $this->assertSame(RiskLevel::Medium->value, $breakdown['risk_level']);
    }

    public function test_critical_total_score_and_recommendation(): void
    {
        $travel = TravelCompany::first();
        $user = User::first();

        $travel->update(['license_expiry' => now()->subDay()]);

        for ($i = 0; $i < 7; $i++) {
            \DB::table('pengaduan')->insert([
                'nama_pengadu' => 'Pengadu '.$i,
                'travels_id' => $travel->id,
                'hal_aduan' => 'Test aduan',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $inspectionId = Inspection::create([
            'travel_id' => $travel->id,
            'inspection_no' => 'PWG-2026-4002',
            'inspection_date' => now(),
            'inspection_type' => InspectionType::Routine,
            'status' => InspectionStatus::OnProgress,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ])->id;

        InspectionFinding::create([
            'inspection_id' => $inspectionId,
            'category' => 'Operasional',
            'severity' => FindingSeverity::Critical,
            'title' => 'Kritis 2',
            'description' => 'Deskripsi',
            'recommendation' => 'Rekomendasi',
            'deadline' => now()->subDays(3),
            'status' => FindingStatus::WaitingResponse,
        ]);

        InspectionFinding::create([
            'inspection_id' => $inspectionId,
            'category' => 'Legalitas',
            'severity' => FindingSeverity::Critical,
            'title' => 'Kritis 3',
            'description' => 'Deskripsi',
            'recommendation' => 'Rekomendasi',
            'deadline' => now()->subDay(),
            'status' => FindingStatus::Open,
        ]);

        $risk = $this->service->recalculateForTravel($travel->id);

        $this->assertGreaterThanOrEqual(76, $risk->total_score);
        $this->assertSame(RiskLevel::Critical, $risk->risk_level);
        $this->assertStringContainsString('Prioritas pengawasan', $this->service->getRecommendation($risk->risk_level->value));
    }

    public function test_recalculate_all_persists_scores(): void
    {
        $count = $this->service->recalculateAll(logAudit: false);

        $this->assertGreaterThan(0, $count);
        $this->assertGreaterThan(0, RiskScore::count());
    }
}
