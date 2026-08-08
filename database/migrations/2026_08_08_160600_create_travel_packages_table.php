<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_id')->constrained('travels')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->unsignedSmallInteger('days')->nullable();
            $table->string('default_airline')->nullable();
            $table->text('service_notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['travel_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_packages');
    }
};
