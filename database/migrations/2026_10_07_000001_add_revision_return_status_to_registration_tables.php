<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            // Status antrean yang dituju setelah PIC mengirim perbaikan.
            $table->string('revision_return_status', 20)->nullable()->after('registration_notes');
        });

        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->string('revision_return_status', 20)->nullable()->after('registration_notes');
        });
    }

    public function down(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->dropColumn('revision_return_status');
        });

        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropColumn('revision_return_status');
        });
    }
};
