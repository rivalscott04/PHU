<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('master_checklist_categories')
                ->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('input_type', ['BOOLEAN', 'OPTION', 'NUMBER', 'TEXT', 'FILE', 'PHOTO']);
            $table->unsignedTinyInteger('weight')->default(1);
            $table->boolean('required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_checklists');
    }
};
