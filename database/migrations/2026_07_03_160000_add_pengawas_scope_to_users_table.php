<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('pengawas_scope', 20)->nullable()->after('kabupaten');
            $table->json('pengawas_kabupatens')->nullable()->after('pengawas_scope');
        });

        DB::table('users')
            ->where('role', 'pengawas')
            ->whereNull('pengawas_scope')
            ->update(['pengawas_scope' => 'single']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['pengawas_scope', 'pengawas_kabupatens']);
        });
    }
};
