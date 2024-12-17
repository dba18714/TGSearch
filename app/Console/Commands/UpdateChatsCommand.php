<?php

namespace App\Console\Commands;

use App\Models\Chat;
use App\Models\Message;
use App\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateChatsCommand extends Command
{
    protected $signature = 'chats:verify-chats';
    protected $description = 'Dispatch verification job for the next chat needing verification';

    public function handle()
    {
        $settings = app(GeneralSettings::class);
        $itemsPerUpdate = $settings->itemsPerUpdate;

        // try {
        Log::info("chats:verify-chats command started.");

        for ($i = 0; $i < $itemsPerUpdate; $i++) {
            $result = Chat::dispatchNextVerificationJob();
            if (!$result) {
                $this->info("No more chats to verify. Exiting.");
                return;
            }
            $result = Message::dispatchNextVerificationJob();
            if (!$result) {
                $this->info("No more messages to verify. Exiting.");
                return;
            }
        }

        $result = Chat::dispatchNextAuditJob();
        if (!$result) {
            $this->info("No more chats to verify. Exiting.");
            return;
        }
        $result = Message::dispatchNextAuditJob();
        if (!$result) {
            $this->info("No more messages to verify. Exiting.");
            return;
        }

        $this->info("Dispatched verification job for the next chat.");
        // } catch (\Exception $e) {
        //     Log::error("Error in chats:verify-chats command: " . $e->getMessage());
        // }
    }
}
