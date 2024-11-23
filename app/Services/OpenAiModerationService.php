<?php

namespace App\Services;

use OpenAI\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenAiModerationService
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * 检查内容
     * 
     * @param string $content 需要检查的内容
     */
    public function checkContent(string $content)
    {
        // 使用缓存避免重复请求
        $cacheDuration = config('app.debug') ? now()->addSeconds(0) : now()->addDay();
        return Cache::remember(
            'moderation_' . md5($content),
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
                        'category_scores' => $moderationResult->category_scores ?? $moderationResult->scores ?? [],
                    ];
                } catch (\Exception $e) {
                    Log::error('OpenAI Moderation API error', [
                        'error' => $e->getMessage(),
                        'content' => $content
                    ]);

                    throw $e; // 直接抛出异常，而不是返回安全结果
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
