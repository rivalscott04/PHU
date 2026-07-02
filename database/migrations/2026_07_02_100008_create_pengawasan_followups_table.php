<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan_followups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')
                ->constrained('pengawasan_temuan')
                ->cascadeOnDelete();
            $table->text('description');
            $table->string('attachment')->nullable();
            $table->enum('status', [
                'SUBMITTED',
                'PENDING',
                'REVISION_REQUIRED',
                'VERIFIED',
                'REJECTED',
                'CLOSED',
            ])->default('SUBMITTED');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('finding_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan_followups');
    }
};
