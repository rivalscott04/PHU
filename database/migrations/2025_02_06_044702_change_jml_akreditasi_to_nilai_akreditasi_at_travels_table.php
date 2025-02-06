<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('travels', function (Blueprint $table) {
            DB::statement('ALTER TABLE travels CHANGE Jml_Akreditasi nilai_akreditasi VARCHAR(255)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travels', function (Blueprint $table) {
            DB::statement('ALTER TABLE travels CHANGE nilai_akreditasi Jml_Akreditasi VARCHAR(255)');
        });
    }
};
