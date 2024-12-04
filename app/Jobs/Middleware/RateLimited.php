<?php

namespace App\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;

class RateLimited
{
    /**
     * 处理队列任务
     *
     * @param  \Closure(object): void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        \Log::info('Starting RateLimited Middleware for Google custom search job');

        $executed = RateLimiter::attempt(
            'google-custom-search',  // 限流器的key
            100,                     // 24小时允许100次
            function () use ($next, $job) {
                \Log::info('Starting RateLimited Middleware for $next($job);' . time());
                $next($job);
            },
            60 * 60 * 24           // 24小时后重置
        );

        if (!$executed) {
            $seconds = RateLimiter::availableIn('google-custom-search');
            $job->release($seconds);
        }
    }
}
