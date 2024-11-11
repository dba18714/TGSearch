<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;

class UpdateLinks extends Command
{
    protected $signature = 'links:verify-next';
    protected $description = 'Dispatch verification job for the next link needing verification';

    public function handle()
    {
        $startTime = time();
        $endTime = $startTime + (12 * 60 * 60); // 12 hours in seconds
        while (time() < $endTime) {
            $link = Link::dispatchNextVerificationJob();
            if (!$link) {
                $this->info("No more links to verify. Exiting.");
                break;
            }
            sleep(1);
            $this->info("Dispatched verification job for the next link.");
        }
    }
}