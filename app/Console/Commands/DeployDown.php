<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class DeployDown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'down';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rand = Str::random(8);
        if (Process::run("php artisan down --secret=\"$rand\"")->seeInOutput('Application is now in maintenance mode.')) {
            $this->info("访问 https://example.com/$rand 可绕过维护模式");
        }
    }
}
