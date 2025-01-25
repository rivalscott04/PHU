<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bap', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('jabatan');
            $table->string('ppiuname');
            $table->string('address_phone');
            $table->string('kab_kota');
            $table->integer('people');
            $table->string('package');
            $table->integer('days');
            $table->decimal('price', 10, 2);
            $table->date('datetime');
            $table->string('airlines');
            $table->time('time');
            $table->date('returndate');
            $table->string('airlines2');
            $table->time('times2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bap');
    }
}
