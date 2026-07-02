<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan_followup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('followup_id')
                ->constrained('pengawasan_followups')
                ->cascadeOnDelete();
            $table->string('status');
            $table->text('description')->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('followup_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan_followup_logs');
    }
};
