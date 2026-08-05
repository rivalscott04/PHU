<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->string('dokumen_sk')->nullable()->after('registration_notes');
            $table->string('dokumen_akreditasi')->nullable()->after('dokumen_sk');
        });
    }

    public function down(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->dropColumn(['dokumen_sk', 'dokumen_akreditasi']);
        });
    }
};
