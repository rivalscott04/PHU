<?php

namespace App\Exports;

use App\Enums\BapStatus;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BapExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(private readonly Collection $records)
    {
    }

    public function collection(): Collection
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Surat',
            'Nama PIC',
            'Jabatan',
            'PPIU',
            'Nomor HP',
            'Kabupaten Kota',
            'Tanggal Berangkat',
            'Jumlah Jamaah',
            'Harga per Orang',
            'Status',
        ];
    }

    public function map($bap): array
    {
        static $row = 0;
        $row++;

        $status = BapStatus::tryFrom((string) $bap->status)?->label() ?? (string) $bap->status;

        return [
            $row,
            $bap->nomor_surat ?? '',
            $bap->name ?? '',
            $bap->jabatan ?? '',
            $bap->ppiuname ?? '',
            $bap->address_phone ?? '',
            $bap->kab_kota ?? '',
            $bap->datetime ? date('d/m/Y', strtotime($bap->datetime)) : '',
            $bap->people ?? '',
            $bap->price ?? '',
            $status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
