<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Tms\V20201229\TmsClient;
use TencentCloud\Tms\V20201229\Models\TextModerationRequest;

class TencentCloudModerationService
{
    protected TmsClient $client;

    public function __construct(TmsClient $client)
    {
        $this->client = $client;
    }

    public function checkContent(string $content)
    {
        $cacheDuration = config('app.debug') ? now()->addSeconds(0) : now()->addDay();
        return Cache::remember(
            'tencent_moderation_' . md5($content),
            $cacheDuration,
            function () use ($content) {
                try {
                    $req = new TextModerationRequest();
                    $req->Content = base64_encode($content);
                    
                    $resp = $this->client->TextModeration($req);
                    
                    // 将响应对象转换为数组
                    $result = json_decode(json_encode($resp), true);
                    
                    // 记录原始响应用于调试
                    Log::debug('Tencent Cloud Moderation Response', [
                        'response' => $result
                    ]);

                    // 获取建议操作和标签
                    $suggestion = $result['Suggestion'] ?? 'Pass';
                    $label = $result['Label'] ?? 'Normal';

                    // 初始化分类结果
                    $categories = [
                        'Porn' => false,
                        'Abuse' => false,
                        'Ad' => false,
                        'Illegal' => false,
                        'Spam' => false,
                        'Polity' => false,  // 添加政治类别
                        'Terror' => false,   // 添加恐怖类别
                    ];

                    // 初始化分数
                    $scores = [
                        'Porn' => 0,
                        'Abuse' => 0,
                        'Ad' => 0,
                        'Illegal' => 0,
                        'Spam' => 0,
                        'Polity' => 0,  // 添加政治类别
                        'Terror' => 0,   // 添加恐怖类别
                    ];

                    // 处理 DetailResults
                    if (!empty($result['DetailResults'])) {
                        foreach ($result['DetailResults'] as $detail) {
                            $categoryLabel = $detail['Label'];
                            if (isset($categories[$categoryLabel])) {
                                $categories[$categoryLabel] = $detail['Suggestion'] === 'Block';
                                $scores[$categoryLabel] = $detail['Score'] ?? 0;
                            }
                        }
                    }

                    return [
                        'flagged' => $suggestion === 'Block',
                        'categories' => $categories,
                        'category_scores' => $scores,
                        'suggestion' => $suggestion,
                        'label' => $label,
                        'sub_label' => $result['SubLabel'] ?? '',
                        'raw_response' => $result,
                    ];
                } catch (\Exception $e) {
                    Log::error('Tencent Cloud Moderation API error', [
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
        return ($result['suggestion'] ?? 'Pass') !== 'Block';
    }

    public function getDetailedAnalysis(string $content): array
    {
        $result = $this->checkContent($content);

        $analysis = [
            'safe' => ($result['suggestion'] ?? 'Pass') !== 'Block',
            'issues' => [],
            'suggestion' => $result['suggestion'] ?? 'Pass',
            'label' => $result['label'] ?? 'Normal',
        ];

        // 只有当建议为 Block 时才添加问题
        if ($result['suggestion'] === 'Block') {
            $label = $result['label'];
            $score = $result['category_scores'][$label] ?? 0;
            
            $analysis['issues'][] = [
                'category' => $label,
                'score' => $score
            ];
        }

        return $analysis;
    }
}