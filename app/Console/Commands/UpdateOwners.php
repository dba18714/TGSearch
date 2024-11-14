<?php

namespace App\Console\Commands;

use App\Models\Owner;
use Illuminate\Console\Command;

class UpdateOwners extends Command
{
    protected $signature = 'owners:verify-owners';
    protected $description = 'Dispatch verification job for the next owner needing verification';

    public function handle()
    {
        try {
            \Log::info("owners:verify-owners command started.");
            $result = Owner::dispatchNextVerificationJob();
            if (!$result) {
                $this->info("No more owners to verify. Exiting.");
                return;
            }
            $this->info("Dispatched verification job for the next owner.");
        } catch (\Exception $e) {
            \Log::error("Error in owners:verify-owners command: " . $e->getMessage());
        }
    }
}
