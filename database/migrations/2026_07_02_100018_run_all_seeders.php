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
        echo "Running seeders (after all schema migrations)...\n";

        echo "1. Admin user already created in migration 000001\n";
        echo "2. Travel companies already created in migration 000002\n";
        echo "3. Travel cabang already created in migration 000003\n";
        echo "4. Kabupaten users already created in migration 000004\n";
        echo "5. Travel users already created in migration 000005\n";

        echo "6. Running UpdateTravelCapabilitiesSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'UpdateTravelCapabilitiesSeeder']);

        echo "7. Running BAPSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'BAPSeeder']);

        echo "8. Running JamaahUmrahSeeder...\n";
        Artisan::call('db:seed', ['--class' => 'JamaahUmrahSeeder']);

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
