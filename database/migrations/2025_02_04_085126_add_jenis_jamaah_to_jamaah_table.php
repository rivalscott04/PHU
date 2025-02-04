<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('jamaah', function (Blueprint $table) {
            $table->string('jenis_jamaah')->after('nomor_hp'); // Akan berisi 'haji' atau 'umrah'
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('jamaah', function (Blueprint $table) {
            $table->dropColumn('jenis_jamaah');
        });
    }
};
