

运行 Horizon
php artisan horizon

你可以使用 Artisan 命令 horizon:pause 和 horizon:continue 来暂停和指示任务继续运行：

php artisan horizon:pause

php artisan horizon:continue


你可以使用 Artisan 命令 horizon:terminate 优雅地终止机器上的主 Horizon 进程。Horizon 会等当前正在处理的所有任务都完成后退出：

php artisan horizon:terminate


使用 Horizon 时，应使用 horizon:clear 命令从队列中清除作业，而不是使用 queue:clear 命令。

sail artisan horizon:clear


sail php artisan scout:flush "App\Models\Entity" && sail php artisan scout:flush "App\Models\Message" && sail artisan migrate:fresh --seed

## 启动长轮训
sail artisan nutgram:run

## 测试 Telegram bot 
sail artisan telegram:test