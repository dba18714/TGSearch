<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Utils\Helper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DeployInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:install 
    {--DB_DATABASE= : Database name}
    {--DB_USERNAME= : Database username}
    {--DB_PASSWORD= : Database password}

    {--ADMIN_EMAIL= : Admin email}
    {--ADMIN_PASSWORD= : Admin password}

    {--MEILISEARCH_KEY=A4300576306E42D7 : MEILISEARCH KEY}
        ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'install';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!file_exists(base_path() . '/.env')) {
            if (!copy(base_path() . '/.env.example', base_path() . '/.env')) {
                abort(500, '复制环境文件失败，请检查目录权限');
            }
        }

        $this->call('deploy:down');
        if (!env('APP_KEY')) {
            $this->line('生成APP_KEY...');
            $exitCode = $this->call('key:generate');
            if ($exitCode) {
                abort(500, 'key:generate 执行失败');
            }
            $this->info('APP_KEY生成成功');
        }

        $DB_DATABASE = $this->option('DB_DATABASE') ?: $this->ask('请输入数据库名');
        $DB_USERNAME = $this->option('DB_USERNAME') ?: $this->ask('请输入数据库用户名');
        $DB_PASSWORD = $this->option('DB_PASSWORD') ?: $this->ask('请输入数据库密码');
        $SCOUT_QUEUE = 'true';
        $SCOUT_DRIVER = 'meilisearch';
        $MEILISEARCH_KEY = $this->option('MEILISEARCH_KEY') ?: $this->ask('请输入 MEILISEARCH_KEY');
        saveToEnv([
            'QUEUE_CONNECTION' => 'redis',

            'DB_CONNECTION' => 'mysql',
            'DB_DATABASE' => $DB_DATABASE,
            'DB_USERNAME' => $DB_USERNAME,
            'DB_PASSWORD' => $DB_PASSWORD,
            'SCOUT_QUEUE' => $SCOUT_QUEUE,
            'SCOUT_DRIVER' => $SCOUT_DRIVER,
            'MEILISEARCH_KEY' => $MEILISEARCH_KEY,
        ]);
        $this->call('deploy:cache-clear');

        $exitCode = $this->call('deploy:migrate');
        if ($exitCode) {
            abort(500, 'deploy:migrate 执行失败');
        }

        $this->call('storage:link');

        $this->line('注册管理员账号...');
        $email = '';
        while (!$email) {
            $email = $this->option('ADMIN_EMAIL') ?: $this->ask('请设置管理员邮箱');
            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'email']
            );
            if ($validator->fails()) {
                $this->error('邮箱格式错误，请重试');
                $email = '';
            }
        }
        $password = '';
        while (!$password) {
            $password = $this->option('ADMIN_PASSWORD') ?: $this->ask('请设置管理员密码');
            if (strlen($password) < 6) {
                $this->error('管理员密码长度最小为6位字符');
                $password = '';
            }
        }
        if ($this->registerAdmin($email, $password)) {
            $this->info('管理员账号注册成功');
        } else {
            $this->info("管理员 {$email} 已存在，已跳过注册");
        }

        $this->call('deploy:cache');
        $this->call('deploy:file-permission');
        $this->call('scout:flush', ['model' => 'App\Models\UnifiedSearch']);
        $this->call('scout:sync-index-settings');
        $this->call('horizon:terminate');

        Process::run('php artisan up')->throw();

        $this->info('一切就绪');
        $this->info("管理员邮箱：{$email}");
        $this->info("管理员密码：{$password}");

        $this->info("首页：//your-domain.com");
        $this->info("管理面板：//your-domain.com/admin");

        return 0;
    }

    private function registerAdmin($email, $password)
    {
        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' =>  Str::random(6),
                'password' => Hash::make($password),
                'is_admin' => 1,
            ]
        );
        if ($user->wasRecentlyCreated) {
            return true;
        } 
        return false;
    }
}
