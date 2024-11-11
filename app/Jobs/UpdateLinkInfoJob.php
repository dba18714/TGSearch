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

class UpdateLinkInfoJob implements ShouldQueue
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
        try {
            $crawledData = $crawler->crawl($this->link->url);

            if (!empty($crawledData['name']) || !empty($crawledData['member_count']) || !empty($crawledData['introduction'])) {
                $this->link->update([
                    'name' => $crawledData['name'] ?? $this->link->name,
                    'member_count' => $crawledData['member_count'] ?? $this->link->member_count,
                    'introduction' => $crawledData['introduction'] ?? $this->link->introduction,
                ]);

                Log::info('Link info updated successfully', [
                    'link_id' => $this->link->id,
                    'url' => $this->link->url,
                ]);
            } else {
                Log::warning('No data crawled for link', [
                    'link_id' => $this->link->id,
                    'url' => $this->link->url,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update link info', [
                'link_id' => $this->link->id,
                'url' => $this->link->url,
                'error' => $e->getMessage(),
            ]);

            // 重新抛出异常，让队列系统知道任务失败
            throw $e;
        }
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