<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SergiX44\Nutgram\Nutgram;

class TestTelegramCommand extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Test Telegram bot connection';

    public function handle(Nutgram $bot)
    {
        $this->info('Testing Telegram connection...');
        
        try {
            $me = $bot->getMe();
            $this->info('Connection successful!');
            $this->info('Bot info:');
            $this->table(
                ['ID', 'Username', 'First Name'],
                [[
                    $me->id,
                    $me->username,
                    $me->first_name,
                ]]
            );
        } catch (\Exception $e) {
            $this->error('Connection failed!');
            $this->error($e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}