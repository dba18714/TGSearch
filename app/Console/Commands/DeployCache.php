<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

class DeployCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 来自：https://github.com/blade-ui-kit/blade-icons#caching
        Process::run('php artisan icons:cache', function (string $type, string $output) {
            echo $output;
        })->throw();

        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache'); // view:cache 会导致命令行退出并报错：“Latest compiled component path not found.”

        // 必须执行这个，否则“/livewire/livewire.min.js?id=02b08710”会返回404
        Process::run('php artisan livewire:publish --assets', function (string $type, string $output) {
            echo $output;
        })->throw();

        $this->call('filament:optimize');
        $this->call('optimize');
    }
}
