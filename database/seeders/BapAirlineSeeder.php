<?php

namespace Database\Seeders;

use App\Models\BapAirline;
use Illuminate\Database\Seeder;

class BapAirlineSeeder extends Seeder
{
    /**
     * Maskapai yang umum dipakai travel Indonesia di form BA (haji/umrah).
     * Fokus operator domestik + Saudia (resmi pengangkut haji dari Indonesia).
     */
    public function run(): void
    {
        $airlines = [
            'Garuda Indonesia',
            'Lion Air',
            'Batik Air',
            'Citilink',
            'AirAsia',
            'Saudia Airlines',
        ];

        foreach ($airlines as $index => $name) {
            BapAirline::updateOrCreate(
                ['name' => $name],
                ['sort_order' => $index + 1, 'is_active' => true]
            );
        }

        BapAirline::query()
            ->whereNotIn('name', $airlines)
            ->delete();

        $this->command?->info('Maskapai BA siap: '.count($airlines).' entri (fokus Indonesia).');
    }
}
