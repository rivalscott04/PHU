<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
    }

    public function down(): void
    {
        Schema::dropIfExists('travels');
    }
};
