<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_id')->nullable();
            $table->string('username');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'kabupaten', 'user'])->default('user');
            $table->string('kabupaten')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postal')->nullable();
            $table->text('about')->nullable();
            $table->boolean('is_password_changed')->default(false);
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['travel_id']);
            $table->index(['role']);
        });

        // Create admin user using firstOrCreate
        DB::table('users')->firstOrCreate(
            ['email' => 'admin@phu.com'],
            [
                'username' => 'admin',
                'firstname' => 'Super',
                'lastname' => 'Admin',
                'email' => 'admin@phu.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'travel_id' => null,
                'address' => 'Jl. Admin No. 1',
                'city' => 'Jakarta',
                'country' => 'Indonesia',
                'postal' => '12345',
                'about' => 'Super Administrator untuk sistem PHU',
                'is_password_changed' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
