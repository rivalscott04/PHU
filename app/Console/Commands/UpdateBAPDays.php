<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BAP;
use Carbon\Carbon;

class UpdateBAPDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bap:update-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing BAP records with days field';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baps = BAP::whereNull('days')->get();
        
        if ($baps->isEmpty()) {
            $this->info('No BAP records found that need updating.');
            return;
        }

        $this->info("Found {$baps->count()} BAP records to update.");

        foreach ($baps as $bap) {
            // Calculate days based on departure and return dates
            $departure = Carbon::parse($bap->datetime);
            $return = Carbon::parse($bap->returndate);
            $days = $departure->diffInDays($return);
            
            $bap->update(['days' => $days]);
            $this->line("Updated BAP ID {$bap->id}: {$days} days");
        }

        $this->info('Update completed!');
    }
}
