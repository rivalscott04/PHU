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
        
        // 1. AdminSeeder (already done in migration 000001)
        echo "1. Admin user already created in migration 000001...\n";
        
        // 2. TravelCompanySeeder (already done in migration 000002)
        echo "2. Travel companies already created in migration 000002...\n";
        
        // 3. UpdateTravelCapabilitiesSeeder
        echo "3. Running UpdateTravelCapabilitiesSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'UpdateTravelCapabilitiesSeeder']);
        
        // 4. KabupatenSeeder (already done in migration 000004)
        echo "4. Kabupaten users already created in migration 000004...\n";
        
        // 5. TravelUserSeeder (already done in migration 000005)
        echo "5. Travel users already created in migration 000005...\n";
        
        // 6. CabangTravelSeeder (already done in migration 000003)
        echo "6. Travel cabang already created in migration 000003...\n";
        
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
        // The data will be removed when the tables are dropped
    }
};
