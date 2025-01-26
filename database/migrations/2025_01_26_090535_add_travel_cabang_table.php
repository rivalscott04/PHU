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
        Schema::create('travel_cabang', function (Blueprint $table) {
            $table->id('id_cabang');
            $table->string('SK_BA')->unique();
            $table->date('tanggal');
            $table->string('pimpinan_cabang');
            $table->string('alamat');
            $table->string('telepon');
            $table->timestamps();

            // Foreign key constraint to travel table
            $table->unsignedBigInteger('travel_id');
            $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('travel_cabang');
    }
};
