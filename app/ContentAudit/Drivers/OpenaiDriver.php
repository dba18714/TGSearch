<?php

namespace App\ContentAudit\Drivers;

use App\ContentAudit\Contracts\ContentAuditInterface;
use OpenAI\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenaiDriver implements ContentAuditInterface
{
    public function __construct(
        private Client $client
    ) {}

    public function checkContent(string $content): array
    {
        $cacheDuration = config('app.debug') ? now()->addSeconds(0) : now()->addDay();
        $cacheKey = 'content_audit_' . md5($content);
        return cache()->remember(
            $cacheKey,
            $cacheDuration,
            function () use ($content) {
                try {
                    $response = $this->client->moderations()->create([
                        'input' => $content,
                    ]);

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

    public function isSafe(string $content): bool
    {
        $result = $this->checkContent($content);
        return !($result['flagged'] ?? false);
    }

    public function getDetailedAnalysis(string $content): array
    {
        $result = $this->checkContent($content);

        if (isset($result['error'])) {
            return [
                'safe' => true,
                'error' => $result['error']
            ];
        }

        $categories = $result['categories'] ?? [];
        $scores = $result['category_scores'] ?? [];

        $analysis = [
            'safe' => !($result['flagged'] ?? false),
            'issues' => []
        ];

        foreach ($categories as $category => $flagged) {
            if ($flagged) {
                $analysis['issues'][] = [
                    'category' => $category,
                    'score' => $scores->{$category} ?? null
                ];
            }
        }

        return $analysis;
    }
}