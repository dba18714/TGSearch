<?php

namespace App\Console\Commands;

use App\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateChatsCommand extends Command
{
    protected $signature = 'chats:verify-chats';
    protected $description = 'Dispatch verification job for the next chat needing verification';

    public function handle()
    {
        try {
            Log::info("chats:verify-chats command started.");
            $result = Chat::dispatchNextVerificationJob();
            if (!$result) {
                $this->info("No more chats to verify. Exiting.");
                return;
            }
            $this->info("Dispatched verification job for the next chat.");
        } catch (\Exception $e) {
            Log::error("Error in chats:verify-chats command: " . $e->getMessage());
        }
    }
}
