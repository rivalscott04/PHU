<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToBapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->enum('status', ['pending', 'diajukan', 'diproses', 'diterima'])->default('pending')->after('pdf_file_path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
