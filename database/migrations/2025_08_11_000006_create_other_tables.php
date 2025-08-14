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
        // Create jamaah table
        Schema::create('jamaah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_id');
            $table->unsignedBigInteger('user_id');
            $table->string('nik', 16);
            $table->string('nama');
            $table->text('alamat');
            $table->string('nomor_hp', 13);
            $table->string('jenis_jamaah');
            $table->timestamps();

            $table->index(['travel_id']);
            $table->index(['user_id']);
            $table->index(['jenis_jamaah']);
        });

        // Create jamaah_haji_khusus table
        Schema::create('jamaah_haji_khusus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_id');
            $table->string('nama_lengkap');
            $table->string('no_ktp', 16);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat');
            $table->string('kota');
            $table->string('kecamatan')->nullable();
            $table->string('provinsi');
            $table->string('kode_pos', 5);
            $table->string('no_hp', 15);
            $table->string('email')->nullable();
            $table->string('nama_ayah');
            $table->string('pekerjaan');
            $table->string('pendidikan_terakhir');
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai']);
            $table->enum('pergi_haji', ['Belum', 'Sudah'])->nullable();
            $table->string('golongan_darah');
            $table->string('alergi')->nullable();
            $table->string('no_paspor')->nullable();
            $table->date('tanggal_berlaku_paspor')->nullable();
            $table->string('tempat_terbit_paspor')->nullable();
            $table->string('nomor_porsi')->nullable();
            $table->string('tahun_pendaftaran')->nullable();
            $table->enum('status_pendaftaran', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('catatan_khusus')->nullable();
            $table->string('dokumen_ktp')->nullable();
            $table->string('dokumen_kk')->nullable();
            $table->string('dokumen_paspor')->nullable();
            $table->string('dokumen_foto')->nullable();
            $table->string('surat_keterangan')->nullable();
            $table->string('bukti_setor_bank')->nullable();
            $table->enum('status_verifikasi_bukti', ['pending', 'verified', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('tanggal_verifikasi')->nullable();
            $table->timestamps();

            $table->index(['travel_id']);
            $table->index(['status_pendaftaran']);
            $table->index(['status_verifikasi_bukti']);
            $table->index(['verified_by']);
        });

        // Create bap table
        Schema::create('bap', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('jabatan');
            $table->string('ppiuname');
            $table->string('address_phone');
            $table->string('kab_kota');
            $table->integer('people');
            $table->integer('days');
            $table->decimal('price', 10, 2);
            $table->date('datetime');
            $table->string('airlines');
            $table->date('returndate');
            $table->string('airlines2');
            $table->string('pdf_file_path')->nullable();
            $table->enum('status', ['pending', 'diajukan', 'diproses', 'diterima'])->default('pending');
            $table->unsignedBigInteger('user_id');
            $table->string('nomor_surat')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['datetime']);
        });

        // Create pengaduan table
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('email_pelapor');
            $table->string('telepon_pelapor');
            $table->string('kabupaten_pelapor');
            $table->string('judul_pengaduan');
            $table->text('deskripsi_pengaduan');
            $table->string('kategori_pengaduan');
            $table->string('nama_travel')->nullable();
            $table->string('status_pengaduan')->default('pending');
            $table->text('tanggapan_pengaduan')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->string('pdf_output')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();

            $table->index(['status_pengaduan']);
            $table->index(['kategori_pengaduan']);
            $table->index(['processed_by']);
        });

        // Create pengunduran table
        Schema::create('pengunduran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_id');
            $table->string('nama_jamaah');
            $table->string('no_ktp', 16);
            $table->string('no_paspor')->nullable();
            $table->string('jenis_pengunduran');
            $table->text('alasan_pengunduran');
            $table->string('dokumen_pendukung')->nullable();
            $table->enum('status_pengunduran', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->index(['travel_id']);
            $table->index(['status_pengunduran']);
        });

        // Create sertifikat table
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
            $table->char('uuid', 36);
            $table->unsignedBigInteger('travel_id');
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->string('nama_ppiu');
            $table->string('nama_kepala');
            $table->text('alamat');
            $table->date('tanggal_diterbitkan');
            $table->date('tanggal_tandatangan');
            $table->string('nomor_surat');
            $table->string('nomor_dokumen');
            $table->string('qrcode_path')->nullable();
            $table->string('sertifikat_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('jenis')->default('PPIU');
            $table->enum('jenis_lokasi', ['pusat', 'cabang'])->default('pusat');
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamps();

            $table->unique('uuid');
            $table->index(['travel_id']);
            $table->index(['cabang_id']);
            $table->index(['jenis']);
            $table->index(['status']);
        });

        // Create sertifikat_settings table
        Schema::create('sertifikat_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penandatangan')->nullable();
            $table->string('nip_penandatangan')->nullable();
            $table->timestamps();
        });

        // Note: failed_jobs, password_reset_tokens, and personal_access_tokens tables are created by Laravel's default migrations

        // Add foreign key constraints
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
        });

        Schema::table('jamaah', function (Blueprint $table) {
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('bap', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('pengaduan', function (Blueprint $table) {
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('pengunduran', function (Blueprint $table) {
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
        });

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            $table->foreign('cabang_id')->references('id_cabang')->on('travel_cabang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraints first
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropForeign(['travel_id', 'cabang_id']);
        });

        Schema::table('pengunduran', function (Blueprint $table) {
            $table->dropForeign(['travel_id']);
        });

        Schema::table('pengaduan', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
        });

        Schema::table('bap', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            $table->dropForeign(['travel_id', 'verified_by']);
        });

        Schema::table('jamaah', function (Blueprint $table) {
            $table->dropForeign(['travel_id', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['travel_id']);
        });

        // Drop tables (excluding Laravel default tables)
        Schema::dropIfExists('sertifikat_settings');
        Schema::dropIfExists('sertifikat');
        Schema::dropIfExists('pengunduran');
        Schema::dropIfExists('pengaduan');
        Schema::dropIfExists('bap');
        Schema::dropIfExists('jamaah_haji_khusus');
        Schema::dropIfExists('jamaah');
    }
};
