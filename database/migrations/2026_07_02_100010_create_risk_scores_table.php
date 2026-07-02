<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')
                ->unique()
                ->constrained('travels')
                ->restrictOnDelete();
            $table->decimal('complaint_score', 5, 2)->default(0);
            $table->decimal('inspection_score', 5, 2)->default(0);
            $table->decimal('followup_score', 5, 2)->default(0);
            $table->decimal('certificate_score', 5, 2)->default(0);
            $table->decimal('bap_score', 5, 2)->default(0);
            $table->decimal('activity_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->enum('risk_level', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('LOW');
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->index('travel_id');
            $table->index('risk_level');
            $table->index('total_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
