<?php

namespace App\ContentAudit\Drivers;

use App\ContentAudit\AuditResult;
use App\ContentAudit\Contracts\ContentAuditInterface;
use OpenAI\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenaiDriver implements ContentAuditInterface
{
    public function __construct(
        private Client $client
    ) {}

    protected function checkContent(string $content): array
    {
        $cacheDuration = config('app.debug') ? now()->addSeconds(0) : now()->addDay();
        $cacheKey = 'content_audit_' . md5($content);
        return cache()->remember(
            $cacheKey,
            $cacheDuration,
            function () use ($content) {
                try {
                    $response = $this->client->moderations()->create([
                        'model' => 'text-moderation-latest',

                        // omni-moderation-latest 当前不可用，
                        // 会出错：{"status":500,"body":{"error":{"message":"Unexpected error","type":"server_error","param":null,"code":null}}} 
                        // 社区有人也遇到同样的问题： https://community.openai.com/t/content-moderation-api-throwing-internal-server-errors/959809
                        // 'model' => 'omni-moderation-latest', 

                        'input' => $content,
                    ]);

                    Log::debug('OpenAI Moderation API error', $response);

                    $result = $response->results[0] ?? null;

                    if (!$result) {
                        throw new \Exception('No moderation result returned');
                    }

                    return [
                        'flagged' => $result->flagged ?? false,
                        'categories' => $result->categories ?? [],
                        'category_scores' => $result->category_scores ?? $result->scores ?? [],
                    ];
                } catch (\Exception $e) {
                    Log::error('OpenAI Moderation API error', [
                        'error' => $e->getMessage(),
                        'content' => $content
                    ]);

                    throw $e;
                }
            }
        );
    }

    public function audit(string $content): AuditResult
    {
        $result = $this->checkContent($content);

        $risks = [];
        $maxRisk = [];

        foreach ($result['categories'] as $category => $flagged) {
            if ($flagged) {
                $score = $result['category_scores']->{$category} ?? 0.0;
                $risks[] = [
                    'category' => $category,
                    'score' => $score
                ];
                if (empty($maxRisk) || $score > $maxRisk['score']) {
                    $maxRisk = [
                        'category' => $category,
                        'score' => $score
                    ];
                }
            }
        }

        return new AuditResult(
            isPassed: !($result['flagged'] ?? false),
            risks: $risks,
            maxRisk: $maxRisk,
        );
    }
}
