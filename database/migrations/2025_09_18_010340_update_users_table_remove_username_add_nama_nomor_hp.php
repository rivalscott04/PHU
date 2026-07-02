<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama')->nullable()->after('id');
            $table->string('nomor_hp')->nullable()->after('email');
        });

        foreach (DB::table('users')->get() as $user) {
            $nama = trim(($user->firstname ?? '').' '.($user->lastname ?? ''));
            if ($nama === '') {
                $nama = $user->username ?? 'User PHU';
            }

            DB::table('users')->where('id', $user->id)->update(['nama' => $nama]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn(['firstname', 'lastname']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn(['nama', 'nomor_hp']);
            
            // Add back old columns
            $table->string('username')->after('id');
            $table->string('firstname')->nullable()->after('username');
            $table->string('lastname')->nullable()->after('firstname');
        });
    }
};
