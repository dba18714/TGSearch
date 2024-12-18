<?php

namespace App\ContentAudit\Drivers;

use App\ContentAudit\AuditResult;
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

    protected function checkContent(string $content): array
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

                    return $result;
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

    public function audit(string $content): AuditResult
    {
        if (empty($content)) {
            throw new \InvalidArgumentException('Content cannot be empty');
        }

        $result = $this->checkContent($content);

        $isPassed = $result['Suggestion'] !== 'Block';
        $risks = [];

        $maxRisk = [
            'category' => $result['Label'],
            'score' => $result['Score']/100,
        ];

        foreach ($result['DetailResults'] as $item) {
            if ($item['Suggestion'] === 'Block') {
                $score = $item['Score']/100;
                $risks[] = [
                    'category' => $item['Label'],
                    'score' => $score,
                ];
            }
        }

        if ($maxRisk['score'] > 0.9 && $isPassed){
            $isPassed = false;
        }

        return new AuditResult(
            isPassed: $isPassed,
            risks: $risks,
            maxRisk: $maxRisk,
        );
    }
}
