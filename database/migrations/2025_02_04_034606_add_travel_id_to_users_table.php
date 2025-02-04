<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom user_id sebagai unsignedBigInteger (tipe data untuk foreign key)
            $table->unsignedBigInteger('travel_id')->nullable()->after('id');

            // Tambahkan foreign key constraint
            $table->foreign('travel_id')
                ->references('id')
                ->on('travels')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign(['travel_id']);

            // Hapus kolom user_id
            $table->dropColumn('travel_id');
        });
    }
};
