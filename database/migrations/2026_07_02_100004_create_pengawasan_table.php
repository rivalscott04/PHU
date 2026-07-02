<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')
                ->constrained('travels')
                ->restrictOnDelete();
            $table->string('inspection_no')->unique();
            $table->date('inspection_date');
            $table->enum('inspection_type', ['ROUTINE', 'SPOT_CHECK', 'COMPLAINT_BASED', 'SPECIAL'])->default('ROUTINE');
            $table->decimal('overall_score', 5, 2)->nullable();
            $table->enum('status', [
                'DRAFT',
                'SCHEDULED',
                'ON_PROGRESS',
                'WAITING_FOLLOWUP',
                'FOLLOWUP_UPLOADED',
                'VERIFIED',
                'CLOSED',
                'CANCELLED',
            ])->default('DRAFT');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index('travel_id');
            $table->index('inspection_date');
            $table->index('status');
            $table->index('inspection_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan');
    }
};
