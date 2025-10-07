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
            // Add cabang_id column for cabang travel users
            $table->unsignedBigInteger('cabang_id')->nullable()->after('travel_id');
            
            // Add index for cabang_id
            $table->index(['cabang_id']);
        });

        // Add foreign key constraint for cabang_id
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('cabang_id')->references('id_cabang')->on('travel_cabang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['cabang_id']);
            
            // Drop column and index
            $table->dropIndex(['cabang_id']);
            $table->dropColumn('cabang_id');
        });
    }
};