<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TravelCompany;

class UpdateTravelCapabilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $travelCompanies = TravelCompany::all();

        foreach ($travelCompanies as $travel) {
            // Set default capabilities based on status
            $travel->setDefaultCapabilities();
            
            // Add additional information
            $travel->description = $travel->getTravelTypeDescription();
            $travel->license_number = 'LIC-' . strtoupper(substr($travel->Penyelenggara, 0, 3)) . '-' . date('Y');
            $travel->license_expiry = now()->addYears(2);
            
            $travel->save();
        }

        $this->command->info('Travel companies updated with new capabilities!');
        $this->command->info('PIHK companies can now handle both Haji and Umrah');
        $this->command->info('PPIU companies can handle Umrah only');
    }
} 