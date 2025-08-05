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
        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            // First, update status_pernikahan to be an enum with proper values
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai'])->change();
            
            // Remove the duplicate status_kawin field
            $table->dropColumn('status_kawin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            // Add back the status_kawin field
            $table->enum('status_kawin', ['Belum Menikah', 'Menikah', 'Cerai'])->nullable()->after('pergi_haji');
            
            // Revert status_pernikahan back to string
            $table->string('status_pernikahan')->change();
        });
    }
};
