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
        Schema::create('sertifikat', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('travel_id')->nullable();
            $table->unsignedBigInteger('cabang_id')->nullable();
            $table->string('nama_ppiu');
            $table->string('nama_kepala');
            $table->text('alamat');
            $table->date('berlaku_sampai');
            $table->date('tanggal_diterbitkan')->nullable();
            $table->date('tanggal_tandatangan')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('nomor_dokumen')->nullable();
            $table->string('qrcode_path')->nullable();
            $table->string('sertifikat_path')->nullable();
            $table->enum('jenis', ['PPIU', 'PIHK'])->default('PPIU');
            $table->enum('jenis_lokasi', ['pusat', 'cabang'])->default('pusat');
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('set null');
            $table->foreign('cabang_id')->references('id_cabang')->on('travel_cabang')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sertifikat');
    }
}; 