<?php

namespace App\Enums;

enum ChecklistInputType: string
{
    case Boolean = 'BOOLEAN';
    case Option = 'OPTION';
    case Number = 'NUMBER';
    case Text = 'TEXT';
    case File = 'FILE';
    case Photo = 'PHOTO';

    public function label(): string
    {
        return match ($this) {
            self::Boolean => 'Ya / Tidak',
            self::Option => 'Pilihan',
            self::Number => 'Angka',
            self::Text => 'Teks',
            self::File => 'Lampiran Berkas',
            self::Photo => 'Foto Bukti',
        };
    }

    public function hint(): string
    {
        return match ($this) {
            self::Boolean => 'Pertanyaan yang dijawab ya atau tidak, misalnya "Apakah izin masih berlaku?"',
            self::Option => 'Pilihan jawaban tetap, misalnya "Baik / Cukup / Kurang"',
            self::Number => 'Jawaban berupa angka, misalnya jumlah jamaah aktif',
            self::Text => 'Jawaban berupa teks bebas',
            self::File => 'Petugas menuliskan keterangan atau nama berkas bukti',
            self::Photo => 'Petugas menuliskan keterangan foto bukti lapangan',
        };
    }
}
