<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop unnecessary columns first
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropColumn([
                'email_pelapor',
                'telepon_pelapor', 
                'kabupaten_pelapor',
                'judul_pengaduan',
                'kategori_pengaduan',
                'nama_travel',
                'status_pengaduan',
                'tanggapan_pengaduan'
            ]);
        });
        
        // Add travels_id foreign key
        Schema::table('pengaduan', function (Blueprint $table) {
            $table->unsignedBigInteger('travels_id')->after('nama_pelapor')->nullable();
            $table->foreign('travels_id')->references('id')->on('travels');
        });
        
        // Rename columns using raw SQL for MariaDB compatibility
        DB::statement('ALTER TABLE pengaduan CHANGE nama_pelapor nama_pengadu VARCHAR(255)');
        DB::statement('ALTER TABLE pengaduan CHANGE deskripsi_pengaduan hal_aduan TEXT');
        DB::statement('ALTER TABLE pengaduan CHANGE file_lampiran berkas_aduan VARCHAR(255)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            // Reverse the changes
            $table->dropForeign(['travels_id']);
            $table->dropColumn('travels_id');
            
            $table->renameColumn('nama_pengadu', 'nama_pelapor');
            $table->renameColumn('hal_aduan', 'deskripsi_pengaduan');
            $table->renameColumn('berkas_aduan', 'file_lampiran');
            
            // Add back the dropped columns
            $table->string('email_pelapor')->after('nama_pelapor');
            $table->string('telepon_pelapor')->after('email_pelapor');
            $table->string('kabupaten_pelapor')->after('telepon_pelapor');
            $table->string('judul_pengaduan')->after('kabupaten_pelapor');
            $table->string('kategori_pengaduan')->after('deskripsi_pengaduan');
            $table->string('nama_travel')->nullable()->after('kategori_pengaduan');
            $table->string('status_pengaduan')->default('pending')->after('nama_travel');
            $table->text('tanggapan_pengaduan')->nullable()->after('status_pengaduan');
        });
    }
};