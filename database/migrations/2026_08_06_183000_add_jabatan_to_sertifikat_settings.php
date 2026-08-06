<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat_settings', function (Blueprint $table) {
            $table->string('jabatan_penandatangan')->nullable()->after('nip_penandatangan');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat_settings', function (Blueprint $table) {
            $table->dropColumn('jabatan_penandatangan');
        });
    }
};
