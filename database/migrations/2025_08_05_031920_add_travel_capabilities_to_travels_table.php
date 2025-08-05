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
        Schema::table('travels', function (Blueprint $table) {
            // Add capabilities columns for better maintainability
            $table->json('capabilities')->nullable()->after('Status');
            $table->boolean('can_haji')->default(false)->after('capabilities');
            $table->boolean('can_umrah')->default(true)->after('can_haji');
            $table->text('description')->nullable()->after('can_umrah');
            $table->string('license_number')->nullable()->after('description');
            $table->date('license_expiry')->nullable()->after('license_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->dropColumn([
                'capabilities',
                'can_haji',
                'can_umrah',
                'description',
                'license_number',
                'license_expiry'
            ]);
        });
    }
};
