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
        Schema::table('users', function (Blueprint $table) {
            // Remove username column
            $table->dropColumn('username');
            
            // Remove firstname and lastname columns
            $table->dropColumn(['firstname', 'lastname']);
            
            // Add new columns
            $table->string('nama')->after('id');
            $table->string('nomor_hp')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['nama', 'nomor_hp']);
            
            // Add back old columns
            $table->string('username')->after('id');
            $table->string('firstname')->nullable()->after('username');
            $table->string('lastname')->nullable()->after('firstname');
        });
    }
};
