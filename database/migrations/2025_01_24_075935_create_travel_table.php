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
        Schema::create('travel', function (Blueprint $table) {
            $table->id();
            $table->string('penyelenggara');
            $table->string('nomor_sk');
            $table->date('tanggal_sk');
            $table->string('akreditasi', 1);
            $table->date('tanggal_akreditasi');
            $table->string('lembaga_akreditasi')->nullable();
            $table->string('pimpinan');
            $table->text('alamat_kantor_lama');
            $table->text('alamat_kantor_baru')->nullable();
            $table->string('telepon', 20);
            $table->enum('status', ['diajukan', 'diproses', 'diterima']);
            $table->string('kab_kota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('travel');
    }
};
