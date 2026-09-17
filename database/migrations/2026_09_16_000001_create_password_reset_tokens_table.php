<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penyimpanan token set password bawaan Laravel. Tokennya disimpan dalam bentuk
 * hash oleh broker, dan dihapus begitu dipakai, jadi tabel ini tidak pernah
 * menyimpan sesuatu yang bisa langsung dipakai orang lain.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
