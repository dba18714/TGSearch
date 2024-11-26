<?php

namespace App\Console\Commands;

use App\Models\Entity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateEntities extends Command
{
    protected $signature = 'entities:verify-entities';
    protected $description = 'Dispatch verification job for the next entity needing verification';

    public function handle()
    {
        try {
            Log::info("entities:verify-entities command started.");
            $result = Entity::dispatchNextVerificationJob();
            if (!$result) {
                $this->info("No more entities to verify. Exiting.");
                return;
            }
            $this->info("Dispatched verification job for the next entity.");
        } catch (\Exception $e) {
            Log::error("Error in entities:verify-entities command: " . $e->getMessage());
        }
    }
}
