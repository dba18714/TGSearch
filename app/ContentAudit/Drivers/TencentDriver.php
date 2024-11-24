<?php

namespace App\ContentAudit\Drivers;

use App\ContentAudit\Contracts\ContentAuditInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use TencentCloud\Tms\V20201229\TmsClient;
use TencentCloud\Tms\V20201229\Models\TextModerationRequest;

class TencentDriver implements ContentAuditInterface
{
    public function __construct(
        private TmsClient $client
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
                    $req = new TextModerationRequest();
                    $req->Content = base64_encode($content);

                    $resp = $this->client->TextModeration($req);
                    $result = json_decode(json_encode($resp), true);

                    Log::debug('Tencent Cloud Moderation Response', [
                        'response' => $result
                    ]);

                    return [
                        'flagged' => ($result['Suggestion'] ?? 'Pass') === 'Block',
                        'categories' => [
                            'Porn' => ($result['Label'] ?? 'Normal') === 'Porn',
                            'Abuse' => ($result['Label'] ?? 'Normal') === 'Abuse',
                            'Ad' => ($result['Label'] ?? 'Normal') === 'Ad',
                            'Illegal' => ($result['Label'] ?? 'Normal') === 'Illegal',
                            'Spam' => ($result['Label'] ?? 'Normal') === 'Spam',
                        ],
                        'category_scores' => $result['Score'] ?? 0,
                        'suggestion' => $result['Suggestion'] ?? 'Pass',
                        'label' => $result['Label'] ?? 'Normal',
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
            'issues' => []
        ];

        if ($result['suggestion'] === 'Block') {
            $analysis['issues'][] = [
                'category' => $result['label'],
                'score' => $result['category_scores']
            ];
        }

        return $analysis;
    }
}