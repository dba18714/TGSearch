<?php

namespace App\Console\Commands;

use SergiX44\Nutgram\RunningMode\Polling;
use SergiX44\Nutgram\Nutgram;
use Illuminate\Console\Command;

class RunTelegramBotCommand extends Command
{
    protected $signature = 'app:telegram:run {--timeout=10 : The polling timeout in seconds}';
    protected $description = 'Run the Telegram bot with custom timeout';

    public function handle(Nutgram $bot)
    {
        $timeout = (int)$this->option('timeout');
        
        $this->info("Starting Telegram bot with {$timeout}s timeout...");

        $bot->setRunningMode(Polling::class, [
            'timeout' => $timeout,
        ]);

        $bot->run();
    }
}