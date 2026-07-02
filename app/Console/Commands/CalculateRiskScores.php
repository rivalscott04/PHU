<?php

namespace App\Console\Commands;

use App\Services\AuditLogService;
use App\Services\RiskCalculationService;
use Illuminate\Console\Command;

class CalculateRiskScores extends Command
{
    protected $signature = 'risk:calculate {--travel= : Hitung ulang untuk travel ID tertentu}';

    protected $description = 'Hitung ulang risk score seluruh PPIU/PIHK berdasarkan indikator operasional';

    public function handle(RiskCalculationService $riskCalculationService, AuditLogService $auditLogService): int
    {
        $travelId = $this->option('travel');

        if ($travelId) {
            $risk = $riskCalculationService->recalculateForTravel((int) $travelId);
            $this->info("Risk travel #{$travelId} dihitung: {$risk->total_score} ({$risk->risk_level?->value})");

            return self::SUCCESS;
        }

        $this->info('Memulai perhitungan risk score...');
        $count = $riskCalculationService->recalculateAll(logAudit: false);

        $auditLogService->log(
            'risk',
            'recalculate',
            "menghitung ulang skor risiko otomatis untuk {$count} perusahaan travel"
        );

        $this->info("Selesai. {$count} travel diperbarui.");

        return self::SUCCESS;
    }
}
