<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * 定义应用中的命令调度
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('links:verify-next')
            ->everyMinute()
            ->withoutOverlapping();
    }
}