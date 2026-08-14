<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                ->constrained('pengawasan')
                ->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('inspection_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan_status_logs');
    }
};
