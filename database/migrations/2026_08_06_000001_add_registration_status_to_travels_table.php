<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->string('registration_status', 20)->default('approved')->after('kab_kota');
            $table->text('registration_notes')->nullable()->after('registration_status');
            $table->timestamp('verified_at')->nullable()->after('registration_notes');
            $table->foreignId('verified_by')->nullable()->after('verified_at')->constrained('users')->nullOnDelete();
        });

        DB::table('travels')->whereNull('registration_status')->update([
            'registration_status' => 'approved',
        ]);
    }

    public function down(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['registration_status', 'registration_notes', 'verified_at']);
        });
    }
};
