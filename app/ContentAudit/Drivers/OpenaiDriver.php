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

    // public function isSafe(string $content): bool
    // {
    //     $result = $this->checkContent($content);
    //     return !($result['flagged'] ?? false);
    // }

    public function audit(string $content): AuditResult
    {
        $result = $this->checkContent($content);

        if (isset($result['error'])) {
            return new AuditResult(
                isPassed: true,
                risk: [],
                overallRiskLevel: 0.0,
            );
        }

        $risk = [];
        $maxScore = 0.0;

        foreach ($result['categories'] ?? [] as $category => $flagged) {
            if ($flagged) {
                $score = $result['category_scores']->{$category} ?? 0.0;
                $risk = [
                    'category' => $category,
                    'score' => $score
                ];
                $maxScore = max($maxScore, $score);
            }
        }

        return new AuditResult(
            isPassed: !($result['flagged'] ?? false),
            risk: $risk,
            overallRiskLevel: $maxScore,
        );
    }
}
