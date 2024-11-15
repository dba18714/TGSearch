<?php

namespace App\Jobs;

use App\Models\Owner;
use App\Services\GoogleCustomSearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class ProcessGoogleCustomSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数
     */
    public $tries = 1;

    /**
     * 任务可以执行的最大秒数
     */
    public $timeout = 30;

    /**
     * 搜索关键词
     */
    protected string $search;

    /**
     * Create a new job instance.
     */
    public function __construct(string $search)
    {
        $this->search = $search;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleCustomSearchService $googleSearchService): void
    {
        Log::info('Starting Google custom search job', [
            'search' => $this->search
        ]);

        $executed = RateLimiter::attempt(
            'google-custom-search',  // 限流器的key
            100,                     // 每天允许100次
            function () use ($googleSearchService) {
                Log::info('Starting Google custom search job', [
                    'search' => $this->search
                ]);

                $results = $googleSearchService->search($this->search);
                $this->processSearchResults($results);
            },
            60 * 60 * 24           // 24小时后重置
        );

        if (!$executed) {
            $seconds = RateLimiter::availableIn('google-custom-search');
            Log::warning('Daily Google custom search limit reached', [
                'search' => $this->search,
                'available_in_seconds' => $seconds,
                'available_in_hours' => round($seconds / 3600, 2)
            ]);
        }
    }

    /**
     * Process the search results
     */
    protected function processSearchResults(array $results): void
    {
        foreach ($results as $item) {
            // 跳过 web.t.me 的链接
            if (str_starts_with($item['link'], 'https://web.t.me')) {
                continue;
            }

            $username = extract_telegram_username_by_url($item['link']);
            $message_id = extract_telegram_message_id_by_url($item['link']);

            if ($username) {
                $owner = Owner::firstOrCreate(
                    ['username' => $username]
                );
                $owner->dispatchUpdateJob();
                if ($message_id) {
                    $message = $owner->messages()->firstOrCreate(
                        ['original_id' => $message_id]
                    );
                    $message->dispatchUpdateJob();
                }
            }
        }
    }

    /**
     * 处理失败的任务
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Google custom search job failed', [
            'search' => $this->search,
            'error' => $exception->getMessage(),
        ]);
    }
}
