<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            // Pusat di luar NTB tidak terdata di sistem, jadi SK izinnya
            // diunggah sendiri oleh cabang.
            $table->string('dokumen_sk_pusat')->nullable()->after('dokumen_sk_du');
        });
    }

    public function down(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropColumn('dokumen_sk_pusat');
        });
    }
};
