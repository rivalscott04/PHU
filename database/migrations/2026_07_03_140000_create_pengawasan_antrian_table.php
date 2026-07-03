<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan_antrian', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->unsignedTinyInteger('priority')->default(50);
            $table->unsignedBigInteger('travel_id')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('reference_type', 60);
            $table->unsignedBigInteger('reference_id');
            $table->string('title');
            $table->text('summary')->nullable();
            $table->string('action_url', 500);
            $table->string('status', 20)->default('open');
            $table->timestamp('due_at')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['type', 'reference_type', 'reference_id'], 'pengawasan_antrian_unique_ref');
            $table->index(['kabupaten', 'status']);
            $table->index(['status', 'priority']);
            $table->foreign('travel_id')->references('id')->on('travels')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan_antrian');
    }
};
