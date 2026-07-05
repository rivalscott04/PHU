<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bap_jamaah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bap_id');
            $table->unsignedBigInteger('jamaah_id');
            $table->timestamps();

            $table->unique(['bap_id', 'jamaah_id']);
            $table->index(['jamaah_id']);
            $table->foreign('bap_id')->references('id')->on('bap')->cascadeOnDelete();
            $table->foreign('jamaah_id')->references('id')->on('jamaah')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bap_jamaah');
    }
};
