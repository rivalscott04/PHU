<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Run seeders in proper order using Artisan commands
        echo "Running seeders in proper order...\n";
        
        // 1. AdminSeeder
        echo "1. Running AdminSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'AdminSeeder']);
        
        // 2. TravelCompanySeeder
        echo "2. Running TravelCompanySeeder...\n";
        Artisan::call('db:seed', ['--class' => 'TravelCompanySeeder']);
        
        // 3. UpdateTravelCapabilitiesSeeder
        echo "3. Running UpdateTravelCapabilitiesSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'UpdateTravelCapabilitiesSeeder']);
        
        // 4. KabupatenSeeder
        echo "4. Running KabupatenSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'KabupatenSeeder']);
        
        // 5. TravelUserSeeder
        echo "5. Running TravelUserSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'TravelUserSeeder']);
        
        // 6. CabangTravelSeeder
        echo "6. Running CabangTravelSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'CabangTravelSeeder']);
        
        // 7. BAPSeeder
        echo "7. Running BAPSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'BAPSeeder']);
        
        // 8. JamaahUmrahSeeder
        echo "8. Running JamaahUmrahSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'JamaahUmrahSeeder']);
        
        // 9. JamaahHajiKhususSeeder
        echo "9. Running JamaahHajiKhususSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'JamaahHajiKhususSeeder']);
        
        echo "All seeders completed successfully!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is for seeding, no rollback needed
    }
};
