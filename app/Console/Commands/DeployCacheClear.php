<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

class DeployCacheClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:cache-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'cache-clear';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 来自：https://github.com/blade-ui-kit/blade-icons#caching
        // Process::run('php artisan icons:clear', function (string $type, string $output) {
        //     echo $output;
        // })->throw();

        Process::run('php artisan route:clear', function (string $type, string $output) {
            echo $output;
        })->throw();
        Process::run('php artisan view:clear', function (string $type, string $output) {
            echo $output;
        })->throw();
        Process::run('php artisan config:clear', function (string $type, string $output) {
            echo $output;
        })->throw();
        Process::run('php artisan cache:clear', function (string $type, string $output) {
            echo $output;
        })->throw();
        Process::run('php artisan optimize:clear', function (string $type, string $output) {
            echo $output;
        })->throw();
    }
}
