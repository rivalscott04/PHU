<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bap', function (Blueprint $table) {
            if (! Schema::hasColumn('bap', 'package')) {
                $table->string('package')->nullable()->after('people');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bap', function (Blueprint $table) {
            if (Schema::hasColumn('bap', 'package')) {
                $table->dropColumn('package');
            }
        });
    }
};
