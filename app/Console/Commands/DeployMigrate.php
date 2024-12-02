<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeployMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrate';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line('测试数据库连接...');
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            abort(500, '数据库连接失败');
        }
        $this->info('数据库连接成功');

        $this->line('执行数据库迁移...');
        $this->call('migrate', [
            '--force' => true,
        ]);
        $this->info('数据库迁移成功');
    }
}
