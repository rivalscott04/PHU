<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('travels', function (Blueprint $table) {
            $table->id();
            $table->string('Penyelenggara');
            $table->string('Pusat');
            $table->date('Tanggal');
            $table->string('nilai_akreditasi')->nullable();
            $table->date('tanggal_akreditasi')->nullable();
            $table->string('lembaga_akreditasi')->nullable();
            $table->string('Pimpinan');
            $table->text('alamat_kantor_lama');
            $table->text('alamat_kantor_baru')->nullable();
            $table->string('Telepon', 20);
            $table->enum('Status', ['PIHK', 'PPIU']);
            $table->json('capabilities')->nullable();
            $table->boolean('can_haji')->default(false);
            $table->boolean('can_umrah')->default(true);
            $table->text('description')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('kab_kota');
            $table->timestamps();
        });

        // Create travel companies using firstOrCreate
        $travelCompanies = [
            [
                'Penyelenggara' => 'PT. Lombok Barat Travel',
                'Pusat' => 'Gerung',
                'Tanggal' => '2024-01-15',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-01-15',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Ahmad Suryadi',
                'alamat_kantor_lama' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'alamat_kantor_baru' => 'Jl. Raya Gerung No. 123, Gerung, Lombok Barat',
                'Telepon' => '0370-123456',
                'Status' => 'PPIU',
                'capabilities' => json_encode(['umrah']),
                'can_haji' => false,
                'can_umrah' => true,
                'description' => 'PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Lombok Barat',
            ],
            [
                'Penyelenggara' => 'PT. Lombok Tengah Travel',
                'Pusat' => 'Praya',
                'Tanggal' => '2024-01-20',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-01-20',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Budi Santoso',
                'alamat_kantor_lama' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'alamat_kantor_baru' => 'Jl. Raya Praya No. 456, Praya, Lombok Tengah',
                'Telepon' => '0370-234567',
                'Status' => 'PIHK',
                'capabilities' => json_encode(['haji', 'umrah', 'haji_khusus']),
                'can_haji' => true,
                'can_umrah' => true,
                'description' => 'PIHK - Penyelenggara Ibadah Haji Khusus (Haji & Umrah)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Lombok Tengah',
            ],
            [
                'Penyelenggara' => 'PT. Lombok Timur Travel',
                'Pusat' => 'Selong',
                'Tanggal' => '2024-01-25',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-01-25',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Siti Nurhaliza',
                'alamat_kantor_lama' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'alamat_kantor_baru' => 'Jl. Raya Selong No. 789, Selong, Lombok Timur',
                'Telepon' => '0370-345678',
                'Status' => 'PPIU',
                'capabilities' => json_encode(['umrah']),
                'can_haji' => false,
                'can_umrah' => true,
                'description' => 'PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Lombok Timur',
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Travel',
                'Pusat' => 'Sumbawa Besar',
                'Tanggal' => '2024-02-01',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-01',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Rudi Hartono',
                'alamat_kantor_lama' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'alamat_kantor_baru' => 'Jl. Raya Sumbawa Besar No. 321, Sumbawa Besar',
                'Telepon' => '0371-456789',
                'Status' => 'PIHK',
                'capabilities' => json_encode(['haji', 'umrah', 'haji_khusus']),
                'can_haji' => true,
                'can_umrah' => true,
                'description' => 'PIHK - Penyelenggara Ibadah Haji Khusus (Haji & Umrah)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Sumbawa',
            ],
            [
                'Penyelenggara' => 'PT. Sumbawa Barat Travel',
                'Pusat' => 'Taliwang',
                'Tanggal' => '2024-02-05',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-05',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Dewi Sartika',
                'alamat_kantor_lama' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'alamat_kantor_baru' => 'Jl. Raya Taliwang No. 654, Taliwang',
                'Telepon' => '0371-567890',
                'Status' => 'PPIU',
                'capabilities' => json_encode(['umrah']),
                'can_haji' => false,
                'can_umrah' => true,
                'description' => 'PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Sumbawa Barat',
            ],
            [
                'Penyelenggara' => 'PT. Dompu Travel',
                'Pusat' => 'Dompu',
                'Tanggal' => '2024-02-10',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-10',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Muhammad Rizki',
                'alamat_kantor_lama' => 'Jl. Raya Dompu No. 987, Dompu',
                'alamat_kantor_baru' => 'Jl. Raya Dompu No. 987, Dompu',
                'Telepon' => '0371-678901',
                'Status' => 'PPIU',
                'capabilities' => json_encode(['umrah']),
                'can_haji' => false,
                'can_umrah' => true,
                'description' => 'PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Dompu',
            ],
            [
                'Penyelenggara' => 'PT. Bima Travel',
                'Pusat' => 'Woha',
                'Tanggal' => '2024-02-15',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-15',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Ahmad Fauzi',
                'alamat_kantor_lama' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'alamat_kantor_baru' => 'Jl. Raya Woha No. 147, Woha, Bima',
                'Telepon' => '0371-789012',
                'Status' => 'PIHK',
                'capabilities' => json_encode(['haji', 'umrah', 'haji_khusus']),
                'can_haji' => true,
                'can_umrah' => true,
                'description' => 'PIHK - Penyelenggara Ibadah Haji Khusus (Haji & Umrah)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Bima',
            ],
            [
                'Penyelenggara' => 'PT. Mataram Travel',
                'Pusat' => 'Mataram',
                'Tanggal' => '2024-02-20',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-20',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Lina Marlina',
                'alamat_kantor_lama' => 'Jl. Pejanggik No. 258, Mataram',
                'alamat_kantor_baru' => 'Jl. Pejanggik No. 258, Mataram',
                'Telepon' => '0370-890123',
                'Status' => 'PIHK',
                'capabilities' => json_encode(['haji', 'umrah', 'haji_khusus']),
                'can_haji' => true,
                'can_umrah' => true,
                'description' => 'PIHK - Penyelenggara Ibadah Haji Khusus (Haji & Umrah)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Kota Mataram',
            ],
            [
                'Penyelenggara' => 'PT. Kota Bima Travel',
                'Pusat' => 'Bima',
                'Tanggal' => '2024-02-25',
                'nilai_akreditasi' => 'A',
                'tanggal_akreditasi' => '2024-02-25',
                'lembaga_akreditasi' => 'Kementerian Agama',
                'Pimpinan' => 'Nurul Hidayati',
                'alamat_kantor_lama' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'alamat_kantor_baru' => 'Jl. Soekarno-Hatta No. 369, Bima',
                'Telepon' => '0371-901234',
                'Status' => 'PPIU',
                'capabilities' => json_encode(['umrah']),
                'can_haji' => false,
                'can_umrah' => true,
                'description' => 'PPIU - Penyelenggara Perjalanan Ibadah Umrah (Umrah Only)',
                'license_number' => 'LIC-PT.-2025',
                'license_expiry' => '2027-08-11',
                'kab_kota' => 'Kota Bima',
            ],
        ];

        foreach ($travelCompanies as $travel) {
            DB::table('travels')->firstOrCreate(
                ['Penyelenggara' => $travel['Penyelenggara']],
                $travel
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travels');
    }
};
