<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->string('travel_token', 20)->nullable()->after('nomor_surat');
            $table->string('kanwil_token', 20)->nullable()->after('travel_token');
        });
    }

    public function down(): void
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->dropColumn(['travel_token', 'kanwil_token']);
        });
    }
};
