<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'pimpinan', 'kabupaten', 'pengawas', 'user') NOT NULL DEFAULT 'user'"
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::table('users')->where('role', 'pimpinan')->update(['role' => 'admin']);

        DB::statement(
            "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kabupaten', 'pengawas', 'user') NOT NULL DEFAULT 'user'"
        );
    }
};
