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
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->dropForeign(['travel_id']);
            $table->dropColumn('travel_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_cabang', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_id');
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
        });
    }
};
