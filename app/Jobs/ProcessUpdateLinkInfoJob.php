<?php

namespace App\Jobs;

use App\Models\Link;
use App\Services\TelegramCrawlerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessUpdateLinkInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     */
    public $tries = 3;

    /**
     * 任务可以执行的最大秒数
     */
    public $timeout = 30;

    public function __construct(
        protected Link $link
    ) {}

    /**
     * Execute the job.
     * 必须使用模型的 $link->dispatchUpdateJob(); 方法派遣本任务，以防止队列执行失败时，任务被重新调度。
     */
    public function handle(TelegramCrawlerService $crawler): void
    {
        Cache::lock('telegram-crawler', 1)->block(20, function () use ($crawler) {
            $data = $crawler->crawl($this->link->url);
            if ($data) {
                $data['verified_at'] = now();
                $this->link->update($data);
            }
        });
    }

    /**
     * 处理失败的任务
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Link update job failed', [
            'link_id' => $this->link->id,
            'url' => $this->link->url,
            'error' => $exception->getMessage(),
        ]);
    }
}
