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
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->string('pusat', 255)->nullable()->change();
            $table->string('SK_BA', 255)->nullable()->change();
            $table->date('tanggal')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->string('pusat', 255)->nullable(false)->change();
            $table->string('SK_BA', 255)->nullable(false)->change();
            $table->date('tanggal')->nullable(false)->change();
        });
    }
};
