<?php

namespace App\Services;

use App\Contracts\ContentModerationService;
use OpenAI\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenaiModerationService implements ContentModerationService
{
    public function __construct(
        private Client $client
    ) {}

    /**
     * 检查内容
     * 
     * @param string $content 需要检查的内容
     */
    public function checkContent(string $content): array
    {
        // 使用缓存避免重复请求
        $cacheDuration = config('app.debug') ? now()->addSeconds(0) : now()->addDay();
        $cacheKey = 'moderation_' . md5($content);
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

    /**
     * 检查内容是否安全
     */
    public function isSafe(string $content): bool
    {
        $result = $this->checkContent($content);
        return !($result['flagged'] ?? false);
    }

    /**
     * 获取详细的审核结果
     */
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

        // 检查每个分类
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
