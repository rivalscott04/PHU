<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('pengaduan', 'status')) {
                $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('pending');
            }
            
            if (!Schema::hasColumn('pengaduan', 'pdf_output')) {
                $table->string('pdf_output')->nullable();
            }
            
            if (!Schema::hasColumn('pengaduan', 'admin_notes')) {
                $table->text('admin_notes')->nullable();
            }
            
            if (!Schema::hasColumn('pengaduan', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
            
            if (!Schema::hasColumn('pengaduan', 'processed_by')) {
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            // Remove columns if they exist
            if (Schema::hasColumn('pengaduan', 'status')) {
                $table->dropColumn('status');
            }
            
            if (Schema::hasColumn('pengaduan', 'pdf_output')) {
                $table->dropColumn('pdf_output');
            }
            
            if (Schema::hasColumn('pengaduan', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
            
            if (Schema::hasColumn('pengaduan', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            
            if (Schema::hasColumn('pengaduan', 'processed_by')) {
                $table->dropForeign(['processed_by']);
                $table->dropColumn('processed_by');
            }
        });
    }
};
