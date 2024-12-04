<?php

namespace App\Jobs;

use App\Jobs\Middleware\RateLimited;
use App\Models\Chat;
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
    public $tries = 2;

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

    public function middleware(): array
    {
        return [new RateLimited];
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleCustomSearchService $googleSearchService): void
    {
        Log::info('Starting Google custom search job', [
            'search' => $this->search
        ]);

        $results = $googleSearchService->search($this->search);
        $this->processSearchResults($results);
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
                $chat = Chat::firstOrCreate(
                    ['username' => $username],
                    [
                        'source_str' => $item['link'],
                    ]
                );
                $chat->dispatchUpdateJob();
                if ($message_id) {
                    $message = $chat->messages()->firstOrCreate(
                        ['source_id' => $message_id],
                        [
                            'source_str' => $item['link'],
                        ]
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
