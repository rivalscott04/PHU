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
            $table->text('alamat_pusat')->change();
            $table->text('alamat_cabang')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->string('alamat_pusat');
            $table->string('alamat_cabang');
        });
    }
};
