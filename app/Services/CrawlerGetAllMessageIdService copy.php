<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CrawlerGetAllMessageIdService1
{
    public function crawl(string $username /* channel or group */)
    {

        try {
            $url = "https://t.me/s/{$username}/1";
            $xpath = $this->getXpath(url: $url);
            $messageIds = $this->extractMessageIds($xpath);

            $lastMessageId = end($messageIds);
            $url = "https://t.me/s/{$username}/{$lastMessageId}";
            $xpath = $this->getXpath(url: $url);
            $messageIds = $this->extractMessageIds($xpath);

            // 我想要循环获取全部的，怎么做

            return $messageIds;
        } catch (\Exception $e) {
            Log::error("处理 URL:{$url} 时发生错误: " . $e->getMessage());
            return null;
        }
    }

    private function getXpath($url)
    {
        $response = Http::withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36')
            ->get($url);

        $response->throw();

        $html = $response->body();
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        return $xpath;
    }

    private function extractMessageIds($xpath) {
        $nodes = $xpath->query('//div[contains(@class, "tgme_widget_message")]');
        $messageIds = [];
        
        foreach ($nodes as $node) {
            $dataMessage = $node->getAttribute('data-post');
            if (preg_match('/yunpanshare\/(\d+)/', $dataMessage, $matches)) {
                $messageIds[] = (int)$matches[1];
            }
        }
        
        return $messageIds;
    }
}
