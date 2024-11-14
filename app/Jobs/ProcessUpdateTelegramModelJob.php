<?php

namespace App\Jobs;

use App\Enums\CrawlTelegramType;
use App\Models\Owner;
use App\Services\TelegramCrawlerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessUpdateTelegramModelJob implements ShouldQueue
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
        protected Model $model
    ) {}

    /**
     * Execute the job.
     * 必须使用模型的 $model->dispatchUpdateJob(); 方法派遣本任务，以防止队列执行失败时，任务被重新调度。
     */
    public function handle(TelegramCrawlerService $crawler): void
    {
        Cache::lock('telegram-crawler', 1)->block(20, function () use ($crawler) {

            // 获取模型类名
            $model_class_name = class_basename($this->model);
            $data = $crawler->crawl($this->model->url);
            if (!$data) return;

            if ($model_class_name == 'Owner') {
                $this->model->update([
                    'verified_at' => now(),
                    'name' => $data['name'],
                    'introduction' => $data['introduction'],
                    'member_count' => $data['member_count'],
                    'type' => $data['type'],
                    'is_valid' => $data['is_valid'],
                ]);
            } elseif ($model_class_name == 'Message') {
                \Log::info('Update message url: ' . $this->model->url);
                \Log::info('Update message data: ', $data);

                $new_data['verified_at'] = now();
                if ($data['message'] !== null) $new_data['text'] = $data['message'];
                if ($data['view_count'] !== null) $new_data['view_count'] = $data['view_count'];
                $new_data['is_valid'] = $data['is_valid'];
                $this->model->update($new_data);
            } else {
                throw new \Exception('Unknown model class name: ' . $model_class_name);
            }
        });
    }
    public function handle2(TelegramCrawlerService $crawler): void
    {
        Cache::lock('telegram-crawler', 1)->block(20, function () use ($crawler) {

            // 获取模型类名
            $modelClassName = class_basename($this->model);
            $data = $crawler->crawl($this->model->url, $modelClassName);
            if ($data) {
                // TODO 删除空键
                $data['verified_at'] = now();
                $this->model->update($data);
            }
        });
    }

    /**
     * 处理失败的任务
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Owner update job failed', [
            'model_class_name' => class_basename($this->model),
            'model_id' => $this->model->id,
            'url' => $this->model->url,
            'error' => $exception->getMessage(),
        ]);
    }
}
