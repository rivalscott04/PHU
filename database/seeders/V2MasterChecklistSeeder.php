<?php

namespace Database\Seeders;

use App\Models\Checklist;
use App\Models\ChecklistCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class V2MasterChecklistSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('master_checklist_categories')) {
            $this->command->warn('V2 checklist seeder dilewati: tabel master checklist belum ada. Jalankan migrate dulu.');

            return;
        }

        $categories = [
            [
                'name' => 'Legalitas',
                'sort_order' => 1,
                'items' => [
                    ['code' => 'LEG-001', 'title' => 'Izin Operasional Aktif', 'input_type' => 'BOOLEAN', 'weight' => 15],
                    ['code' => 'LEG-002', 'title' => 'Akreditasi Masih Berlaku', 'input_type' => 'BOOLEAN', 'weight' => 10],
                ],
            ],
            [
                'name' => 'Operasional',
                'sort_order' => 2,
                'items' => [
                    ['code' => 'OPS-001', 'title' => 'Kantor Aktif Beroperasi', 'input_type' => 'BOOLEAN', 'weight' => 10],
                    ['code' => 'OPS-002', 'title' => 'Jumlah Jamaah Aktif', 'input_type' => 'NUMBER', 'weight' => 8],
                ],
            ],
            [
                'name' => 'Keuangan',
                'sort_order' => 3,
                'items' => [
                    ['code' => 'FIN-001', 'title' => 'Laporan Keuangan Tersedia', 'input_type' => 'BOOLEAN', 'weight' => 7],
                    ['code' => 'FIN-002', 'title' => 'Rekening Escrow Sesuai Ketentuan', 'input_type' => 'BOOLEAN', 'weight' => 10],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = ChecklistCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                [
                    'description' => "Kategori checklist {$categoryData['name']}",
                    'sort_order' => $categoryData['sort_order'],
                    'is_active' => true,
                ]
            );

            foreach ($categoryData['items'] as $index => $item) {
                Checklist::updateOrCreate(
                    ['code' => $item['code']],
                    [
                        'category_id' => $category->id,
                        'title' => $item['title'],
                        'description' => null,
                        'input_type' => $item['input_type'],
                        'weight' => $item['weight'],
                        'required' => true,
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('V2 master checklist seeded.');
    }
}
