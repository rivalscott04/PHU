<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                ->constrained('pengawasan')
                ->cascadeOnDelete();
            $table->foreignId('master_checklist_id')
                ->constrained('master_checklists')
                ->restrictOnDelete();
            $table->text('answer')->nullable();
            $table->unsignedSmallInteger('score')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('inspection_id');
            $table->index('master_checklist_id');
            $table->unique(['inspection_id', 'master_checklist_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan_checklists');
    }
};
