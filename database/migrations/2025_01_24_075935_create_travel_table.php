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
            $table->string('No');
            $table->string('Penyelenggara');
            $table->string('Pusat');
            $table->date('Tanggal');
            $table->string('Jml_Akreditasi', 1);
            $table->date('tanggal_akreditasi');
            $table->string('lembaga_akreditasi')->nullable();
            $table->string('Pimpinan');
            $table->text('alamat_kantor_lama');
            $table->text('alamat_kantor_baru')->nullable();
            $table->string('Telepon', 20);
            $table->enum('Status', ['PIHK', 'PPIU']);
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
