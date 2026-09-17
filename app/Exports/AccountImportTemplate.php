<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountImportTemplate implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return ['nama', 'email', 'nomor_hp', 'travel_company'];
    }

    public function array(): array
    {
        return [];
    }
}
