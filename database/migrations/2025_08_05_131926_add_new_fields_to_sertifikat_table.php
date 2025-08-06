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
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->date('tanggal_diterbitkan')->nullable()->after('alamat');
            $table->date('tanggal_tandatangan')->nullable()->after('tanggal_diterbitkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropColumn(['tanggal_diterbitkan', 'tanggal_tandatangan']);
        });
    }
};
