<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfFilePathToBapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bap', function (Blueprint $table) {
            $table->string('pdf_file_path')->nullable()->after('airlines2');
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
            $table->dropColumn('pdf_file_path');
        });
    }
}
