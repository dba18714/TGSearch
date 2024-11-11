<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;

class UpdateLinks extends Command
{
    protected $signature = 'links:verify-links';
    protected $description = 'Dispatch verification job for the next link needing verification';

    public function handle()
    {
        try {
            \Log::info("links:verify-links command started.");
            $result = Link::dispatchNextVerificationJob();
            if (!$result) {
                $this->info("No more links to verify. Exiting.");
                return;
            }
            $this->info("Dispatched verification job for the next link.");
        } catch (\Exception $e) {
            \Log::error("Error in links:verify-links command: " . $e->getMessage());
        }
    }
}
