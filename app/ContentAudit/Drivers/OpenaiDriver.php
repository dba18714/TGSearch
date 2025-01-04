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
                        'input' => $content,
                    ]);

                    Log::debug('OpenAI Moderation API response', ['result' => $response]);

                    $result = $response->results[0] ?? null;

                    if (!$result) {
                        throw new \Exception('No moderation result returned');
                    }

                    // 转换响应结构
                    $categories = [];
                    $categoryScores = [];
                    
                    foreach ($result->categories as $category => $data) {
                        if (is_object($data)) {
                            // 新的响应结构
                            $categories[$category] = $data->violated;
                            $categoryScores[$category] = $data->score;
                        } else {
                            // 保持对旧响应结构的兼容
                            $categories[$category] = $data;
                            $categoryScores[$category] = $result->category_scores->{$category} ?? 0.0;
                        }
                    }

                    return [
                        'flagged' => $result->flagged ?? false,
                        'categories' => $categories,
                        'category_scores' => $categoryScores,
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
        if (empty($content)) {
            throw new \InvalidArgumentException('Content cannot be empty');
        }

        $result = $this->checkContent($content);

        $risks = [];
        $maxRisk = [
            'category' => null,
            'score' => 0.0,
        ];

        foreach ($result['categories'] as $category => $flagged) {
            if ($flagged) {
                $score = $result['category_scores'][$category] ?? 0.0;
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