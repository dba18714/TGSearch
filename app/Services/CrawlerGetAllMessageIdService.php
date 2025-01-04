<?php

namespace App\Services;

use App\Models\Chat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CrawlerGetAllMessageIdService
{
    protected $allMessageIds = [];
    protected $username;
    protected $maxRetries = 3; // 最大重试次数
    protected $sleepSeconds = 1; // 请求间隔时间(秒)

    public function crawl(string $username)
    {
        $this->username = $username;
        $this->allMessageIds = [];

        try {
            // 获取第一页
            $url = "https://t.me/s/{$username}/1";
            $xpath = $this->getXpath(url: $url);
            return $messageIds = $this->extractMessageIds($xpath);
            
            if (empty($messageIds)) {
                Log::warning("No messages found for {$username}");
                return [];
            }

            $this->allMessageIds = array_merge($this->allMessageIds, $messageIds);
            $lastMessageId = end($messageIds);
            
            // 循环获取后续页面
            $previousLastId = 0;
            $retryCount = 0;

            while (true) {
                sleep($this->sleepSeconds); // 添加延迟避免请求过快

                $url = "https://t.me/s/{$username}/{$lastMessageId}";
                Log::debug("Crawling URL: {$url}");
                
                $xpath = $this->getXpath(url: $url);
                $messageIds = $this->extractMessageIds($xpath);
                
                // 如果没有新消息或者消息ID没有变化，说明已经到达末尾
                if (empty($messageIds) || $lastMessageId === $previousLastId) {
                    $retryCount++;
                    if ($retryCount >= $this->maxRetries) {
                        Log::info("Reached the end of messages for {$username}");
                        break;
                    }
                    continue;
                }

                $retryCount = 0; // 重置重试计数
                $this->allMessageIds = array_merge($this->allMessageIds, $messageIds);
                
                $previousLastId = $lastMessageId;
                $lastMessageId = end($messageIds);

                Log::debug("Found " . count($messageIds) . " messages, last ID: {$lastMessageId}");
            }

            // 去重并排序
            $this->allMessageIds = array_unique($this->allMessageIds);
            sort($this->allMessageIds);
            
            Log::info("Total messages found for {$username}: " . count($this->allMessageIds));
            return $this->allMessageIds;

        } catch (\Exception $e) {
            Log::error("Error processing {$username}: " . $e->getMessage());
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
            if (preg_match("/{$this->username}\/(\d+)/", $dataMessage, $matches)) {
                $messageIds[] = (int)$matches[1];
            }
        }
        
        return $messageIds;
    }
}