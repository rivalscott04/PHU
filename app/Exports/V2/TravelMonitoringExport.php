<?php

namespace App\Exports\V2;

use App\Models\TravelCompany;
use App\Support\KanwilContact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class TravelMonitoringExport implements FromQuery, WithEvents, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?string $kabupaten = null,
        private readonly ?int $travelId = null,
    ) {
    }

    public function query(): Builder
    {
        $query = TravelCompany::query()
            ->with(['riskScore'])
            ->when($this->kabupaten, fn ($q) => $q->where('kab_kota', $this->kabupaten))
            ->when($this->travelId, fn ($q) => $q->where('id', $this->travelId))
            ->orderBy('Penyelenggara');

        $counts = ['inspections'];
        if (Schema::hasTable('pengaduan')) {
            $counts[] = 'pengaduan';
        }

        return $query->withCount($counts);
    }

    public function headings(): array
    {
        return [
            'Penyelenggara',
            'Kabupaten/Kota',
            'Jenis',
            'Pimpinan',
            'Telepon',
            'Skor Risiko',
            'Tingkat Risiko',
            'Jumlah Pengawasan',
            'Jumlah Pengaduan',
            'Masa Berlaku Izin',
        ];
    }

    /** @param  TravelCompany  $travel */
    public function map($travel): array
    {
        return [
            $travel->Penyelenggara,
            $travel->kab_kota,
            $travel->Status,
            $travel->Pimpinan,
            $travel->Telepon,
            $travel->riskScore?->total_score ?? 0,
            $travel->riskScore?->risk_level?->value ?? $travel->riskScore?->risk_level ?? '-',
            $travel->inspections_count ?? 0,
            $travel->pengaduan_count ?? 0,
            $travel->license_expiry?->format('d/m/Y') ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $sheet->insertNewRowBefore(1, 2);
                $sheet->setCellValue('A1', KanwilContact::exportSourceLabel());
                $sheet->setCellValue('A2', 'Dicetak: '.now()->format('d/m/Y H:i'));
            },
        ];
    }
}
