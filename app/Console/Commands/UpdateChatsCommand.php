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
        $generaSettings = app(GeneralSettings::class);
        $items_per_update = $generaSettings->items_per_update;

        // try {
        Log::info("chats:verify-chats command started.111");

        $dispatched_count = 0;
        try {
            for ($i = 0; $i < $items_per_update; $i++) {
                $dispatched_count++;
                $result = Chat::dispatchNextVerificationJob();
                if (!$result) {
                    $this->info("No more chats to verify. Exiting.");
                }
                $result = Message::dispatchNextVerificationJob();
                if (!$result) {
                    $this->info("No more messages to verify. Exiting.");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error in dispatchNextVerificationJob: " . $e->getMessage());
        }

        Log::info('::dispatchNextVerificationJob() $dispatched_count: ' . $dispatched_count);

        // $result = Chat::dispatchNextAuditJob();
        // if (!$result) {
        //     $this->info("No more chats to verify. Exiting.");
        // }
        // $result = Message::dispatchNextAuditJob();
        // if (!$result) {
        //     $this->info("No more messages to verify. Exiting.");
        // }

        $this->info("Dispatched verification job for the next chat.");
        // } catch (\Exception $e) {
        //     Log::error("Error in chats:verify-chats command: " . $e->getMessage());
        // }
    }
}
