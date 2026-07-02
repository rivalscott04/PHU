<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan_temuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                ->constrained('pengawasan')
                ->cascadeOnDelete();
            $table->string('category');
            $table->enum('severity', ['MINOR', 'MAJOR', 'CRITICAL']);
            $table->string('title');
            $table->text('description');
            $table->text('recommendation');
            $table->date('deadline')->nullable();
            $table->enum('status', [
                'OPEN',
                'WAITING_RESPONSE',
                'FOLLOWUP_UPLOADED',
                'REVISION_REQUIRED',
                'VERIFIED',
                'CLOSED',
            ])->default('OPEN');
            $table->timestamps();

            $table->index('inspection_id');
            $table->index('category');
            $table->index('severity');
            $table->index('deadline');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan_temuan');
    }
};
