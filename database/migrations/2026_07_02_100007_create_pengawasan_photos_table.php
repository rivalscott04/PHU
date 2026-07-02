<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengawasan_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                ->constrained('pengawasan')
                ->cascadeOnDelete();
            $table->string('photo');
            $table->string('caption')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('inspection_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengawasan_photos');
    }
};
