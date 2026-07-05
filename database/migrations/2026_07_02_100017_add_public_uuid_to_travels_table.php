<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->uuid('public_uuid')->nullable()->unique()->after('id');
        });

        DB::table('travels')
            ->whereNull('public_uuid')
            ->orderBy('id')
            ->lazyById()
            ->each(function (object $row): void {
                DB::table('travels')
                    ->where('id', $row->id)
                    ->update(['public_uuid' => (string) Str::uuid()]);
            });
    }

    public function down(): void
    {
        Schema::table('travels', function (Blueprint $table) {
            $table->dropUnique(['public_uuid']);
            $table->dropColumn('public_uuid');
        });
    }
};
