<?php

namespace App\Exports\Concerns;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
 * Sel nomor (SK, NIB, telepon) wajib teks. Kalau dibiarkan angka,
 * Excel menampilkannya sebagai notasi ilmiah dan digit belakang hilang.
 */
trait ExportsTravelSheet
{
    protected int $rowNumber = 0;

    /** @return list<string> */
    abstract protected function textColumns(): array;

    public function bindValue(Cell $cell, $value): bool
    {
        if (in_array($cell->getColumn(), $this->textColumns(), true)) {
            $cell->setValueExplicit($value === null ? '' : (string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    protected function nextRowNumber(): int
    {
        return ++$this->rowNumber;
    }

    protected function teks(mixed $value): string
    {
        if ($value === null) {
            return '-';
        }

        $text = trim((string) $value);

        return $text === '' ? '-' : $text;
    }

    protected function tanggal(mixed $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('d/m/Y');
        }

        return '-';
    }
}
