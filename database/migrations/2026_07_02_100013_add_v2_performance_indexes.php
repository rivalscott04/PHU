<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('travels')) {
            Schema::table('travels', function (Blueprint $table) {
                $table->index('kab_kota', 'idx_travels_kab_kota');
                $table->index('Status', 'idx_travels_status');
                $table->index('license_expiry', 'idx_travels_license_expiry');
            });
        }

        if (Schema::hasTable('pengawasan')) {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->index(['travel_id', 'status'], 'idx_pengawasan_travel_status');
            });
        }

        if (Schema::hasTable('pengawasan_temuan')) {
            Schema::table('pengawasan_temuan', function (Blueprint $table) {
                $table->index(['status', 'deadline'], 'idx_temuan_status_deadline');
            });
        }

        if (Schema::hasTable('pengawasan_followups')) {
            Schema::table('pengawasan_followups', function (Blueprint $table) {
                $table->index('submitted_at', 'idx_followups_submitted_at');
            });
        }

        if (Schema::hasTable('risk_scores')) {
            Schema::table('risk_scores', function (Blueprint $table) {
                $table->index(['risk_level', 'total_score'], 'idx_risk_level_score');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->index(['module', 'created_at'], 'idx_audit_module_created');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('travels')) {
            Schema::table('travels', function (Blueprint $table) {
                $table->dropIndex('idx_travels_kab_kota');
                $table->dropIndex('idx_travels_status');
                $table->dropIndex('idx_travels_license_expiry');
            });
        }

        if (Schema::hasTable('pengawasan')) {
            Schema::table('pengawasan', function (Blueprint $table) {
                $table->dropIndex('idx_pengawasan_travel_status');
            });
        }

        if (Schema::hasTable('pengawasan_temuan')) {
            Schema::table('pengawasan_temuan', function (Blueprint $table) {
                $table->dropIndex('idx_temuan_status_deadline');
            });
        }

        if (Schema::hasTable('pengawasan_followups')) {
            Schema::table('pengawasan_followups', function (Blueprint $table) {
                $table->dropIndex('idx_followups_submitted_at');
            });
        }

        if (Schema::hasTable('risk_scores')) {
            Schema::table('risk_scores', function (Blueprint $table) {
                $table->dropIndex('idx_risk_level_score');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropIndex('idx_audit_module_created');
            });
        }
    }
};
