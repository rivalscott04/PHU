<?php

namespace App\Services;

use App\Exports\V2\MonitoringWorkbookExport;
use App\Exports\V2\TravelMonitoringExport;
use App\Repositories\InspectionRepository;
use App\Support\DashboardFilter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
        private readonly DashboardService $dashboardService,
        private readonly InspectionRepository $inspectionRepository,
        private readonly MonitoringService $monitoringService,
    ) {
    }

    public function exportTravel(?string $kabupaten, ?int $travelId, string $format = 'xlsx'): BinaryFileResponse
    {
        $format = $this->normalizeSpreadsheetFormat($format);
        $export = new TravelMonitoringExport($kabupaten, $travelId);
        $filename = 'daftar-travel-'.now()->format('Y-m-d').'.'.$format;

        $this->logExport("mengekspor daftar travel ke {$this->formatLabel($format)}");

        return Excel::download($export, $filename, $this->excelWriterType($format));
    }

    public function exportMonitoring(?string $kabupaten, ?int $travelId, string $format = 'xlsx'): BinaryFileResponse
    {
        $format = $this->normalizeSpreadsheetFormat($format);
        $kpi = $this->monitoringService->getKpiSummary($kabupaten, $travelId);
        $filename = 'monitoring-'.now()->format('Y-m-d').'.'.$format;

        $this->logExport("mengekspor ringkasan monitoring ke {$this->formatLabel($format)}");

        if ($format === 'csv') {
            return Excel::download(
                new TravelMonitoringExport($kabupaten, $travelId),
                $filename,
                ExcelFormat::CSV
            );
        }

        return Excel::download(
            new MonitoringWorkbookExport($kpi, $kabupaten, $travelId),
            $filename,
            ExcelFormat::XLSX
        );
    }

    public function exportPengawasan(array $filters): \Illuminate\Http\Response
    {
        $inspections = $this->inspectionRepository->listForExport($filters);
        $filename = 'laporan-pengawasan-'.now()->format('Y-m-d').'.pdf';

        $this->logExport('mengekspor laporan pengawasan ke PDF');

        return Pdf::loadView('v2.export.pengawasan', [
            'inspections' => $inspections,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'filterSummary' => $this->buildPengawasanFilterSummary($filters),
        ])
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function exportDashboard(DashboardFilter $filter): \Illuminate\Http\Response
    {
        $overview = $this->dashboardService->getOverview($filter);
        $filename = 'dashboard-pengawasan-'.now()->format('Y-m-d').'.pdf';

        $this->logExport('mengekspor ringkasan dashboard ke PDF');

        return Pdf::loadView('v2.export.dashboard', [
            'stats' => $overview['stats'] ?? [],
            'rankings' => $overview['rankings'] ?? [],
            'warnings' => $overview['warnings'] ?? [],
            'executive' => $this->dashboardService->getExecutive($filter),
            'filters' => $overview['filters'] ?? [],
            'generatedAt' => now()->format('d/m/Y H:i'),
            'periodLabel' => $this->buildPeriodLabel($filter),
        ])
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    /** @param array<string, mixed> $filters */
    private function buildPengawasanFilterSummary(array $filters): string
    {
        $parts = [];

        if (! empty($filters['kabupaten'])) {
            $parts[] = 'Kabupaten: '.$filters['kabupaten'];
        }
        if (! empty($filters['status'])) {
            $parts[] = 'Status: '.$filters['status'];
        }
        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $parts[] = 'Periode: '.($filters['date_from'] ?? '...').' s/d '.($filters['date_to'] ?? '...');
        }

        return $parts === [] ? 'Semua data pengawasan' : implode(' | ', $parts);
    }

    private function buildPeriodLabel(DashboardFilter $filter): string
    {
        $bulan = $filter->bulan ? Str::padLeft((string) $filter->bulan, 2, '0', STR_PAD_LEFT) : null;

        if ($filter->tahun && $bulan) {
            return "Periode {$bulan}/{$filter->tahun}";
        }

        if ($filter->tahun) {
            return "Tahun {$filter->tahun}";
        }

        return 'Semua periode';
    }

    private function normalizeSpreadsheetFormat(string $format): string
    {
        return match (strtolower($format)) {
            'csv' => 'csv',
            'xlsx', 'excel' => 'xlsx',
            default => 'xlsx',
        };
    }

    private function excelWriterType(string $format): string
    {
        return $format === 'csv' ? ExcelFormat::CSV : ExcelFormat::XLSX;
    }

    private function formatLabel(string $format): string
    {
        return match ($format) {
            'csv' => 'CSV',
            default => 'Excel',
        };
    }

    private function logExport(string $description): void
    {
        $this->auditLogService->log('export', 'export', $description);
    }
}
