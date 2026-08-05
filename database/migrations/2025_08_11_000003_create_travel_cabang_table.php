<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_cabang', function (Blueprint $table) {
            $table->id('id_cabang');
            $table->string('Penyelenggara');
            $table->string('SK_BA')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('pimpinan_cabang');
            $table->text('alamat_cabang');
            $table->string('telepon');
            $table->string('kabupaten');
            $table->string('pusat')->nullable();
            $table->string('pimpinan_pusat');
            $table->text('alamat_pusat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_cabang');
    }
};
