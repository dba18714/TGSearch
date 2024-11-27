<?php

namespace App\Jobs;

use App\Enums\CrawlTelegramType;
use App\Models\Chat;
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
    public $tries = 1;

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
            if ($model_class_name == 'Chat') {
                $data = $crawler->crawl($this->model->username);
                if (!$data) return;
                if ($data['is_valid'] && $data['name'] === null) {
                    $model_json = $this->model->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    $data_json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    throw new \Exception("Telegram 爬虫返回的数据有误: model: {$model_json} data: {$data_json}");
                }

                if ($data['is_valid']) {
                    $new_data['name'] = $data['name'];
                    $new_data['introduction'] = $data['introduction'];
                    $new_data['member_count'] = $data['member_count'];
                    if ($data['photo_count'] !== null) {
                        $new_data['photo_count'] = $data['photo_count'];
                    }
                    if ($data['video_count'] !== null) {
                        $new_data['video_count'] = $data['video_count'];
                    }
                    if ($data['file_count'] !== null) {
                        $new_data['file_count'] = $data['file_count'];
                    }
                    if ($data['link_count'] !== null) {
                        $new_data['link_count'] = $data['link_count'];
                    }
                    $new_data['type'] = $data['type'];
                }
                $new_data['verified_at'] = now();
                $new_data['is_valid'] = $data['is_valid'];
                $this->model->update($new_data);
            } elseif ($model_class_name == 'Message') {
                $data = $crawler->crawl($this->model->chat->username, $this->model->original_id);
                if (!$data) return;
                if ($data['is_valid'] && $data['message'] === null) {
                    $model_json = $this->model->toJson(JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    $data_json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    throw new \Exception("Telegram 爬虫返回的数据有误: model: {$model_json} data: {$data_json}");
                }

                Log::info('Update message url: ' . $this->model->url);
                Log::info('Update message data: ', $data);

                if ($data['is_valid']) {
                    $new_data['text'] = $data['message'];
                    $new_data['view_count'] = $data['view_count'];
                }
                $new_data['verified_at'] = now();
                $new_data['is_valid'] = $data['is_valid'];
                $this->model->update($new_data);
            } else {
                throw new \Exception('Unknown model class name: ' . $model_class_name);
            }
        });
    }

    /**
     * 处理失败的任务
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Chat update job failed', [
            'model_class_name' => class_basename($this->model),
            'model_id' => $this->model->id,
            'url' => $this->model->url,
            'error' => $exception->getMessage(),
        ]);
    }
}
