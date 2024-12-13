<?php

namespace App\Jobs;

use App\ContentAudit\Facades\ContentAudit;
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

class ProcessAuditModelJob implements ShouldQueue
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
    public function handle(): void
    {
        Cache::lock('telegram-content-audit', 1)->block(20, function () {
            $model_class_name = class_basename($this->model);
            if ($model_class_name == 'Chat') {
                $chat = $this->model;
                $content = $chat->name . ' ' . $chat->introduction;
            } elseif ($model_class_name == 'Message') {
                $message = $this->model;
                $content = $message->text;
            } else {
                throw new \Exception('Unknown model class name: ' . $model_class_name);
            }

            $result = ContentAudit::audit($content);
            $isPassed = $result->isPassed();
            $risks = $result->getRisks();
            $maxRisk = $result->getMaxRisk();

            $data = [];
            $data['audited_at'] = now();
            $data['audit_passed'] = $isPassed;
            $data['audit_score'] = $maxRisk['score'];
            $this->model->update($data);
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
