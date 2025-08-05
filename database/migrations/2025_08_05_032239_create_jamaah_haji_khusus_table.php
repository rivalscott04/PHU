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
        Schema::create('jamaah_haji_khusus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_id');
            $table->string('nama_lengkap');
            $table->string('no_ktp', 16)->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('alamat');
            $table->string('kota');
            $table->string('provinsi');
            $table->string('kode_pos', 5);
            $table->string('no_hp', 15);
            $table->string('email')->nullable();
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('pekerjaan');
            $table->string('pendidikan_terakhir');
            $table->string('status_pernikahan');
            $table->string('golongan_darah');
            $table->string('riwayat_penyakit')->nullable();
            $table->string('alergi')->nullable();
            $table->string('no_paspor')->nullable();
            $table->date('tanggal_berlaku_paspor')->nullable();
            $table->string('tempat_terbit_paspor')->nullable();
            
            // Haji Khusus specific fields
            $table->string('nomor_porsi')->nullable();
            $table->date('tahun_pendaftaran')->nullable();
            $table->enum('status_pendaftaran', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('catatan_khusus')->nullable();
            $table->string('dokumen_ktp')->nullable();
            $table->string('dokumen_kk')->nullable();
            $table->string('dokumen_paspor')->nullable();
            $table->string('dokumen_foto')->nullable();
            $table->string('dokumen_medical_check')->nullable();
            
            $table->timestamps();
            
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jamaah_haji_khusus');
    }
};
