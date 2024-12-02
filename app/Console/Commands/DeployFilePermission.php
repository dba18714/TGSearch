<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;

class DeployFilePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:file-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'file-permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Process::run('sudo chmod -R 0757 storage', function (string $type, string $output) {
            echo $output;
        })->throw();
        Process::run('sudo chmod -R 0757 bootstrap/cache', function (string $type, string $output) {
            echo $output;
        })->throw();
    }
}
