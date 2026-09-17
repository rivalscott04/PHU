<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('nip', 18)->nullable()->unique()->after('nama');
            $table->string('jabatan')->nullable()->after('nip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['nip']);
            $table->dropColumn(['nip', 'jabatan']);
        });
    }
};
