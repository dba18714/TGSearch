<?php

namespace App\Services;

use App\Models\Owner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramCrawlerService
{
    protected $url;

    public function crawl($url)
    {
        $this->url = $url;
        try {
            $response = Http::withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36')
                ->get($url);

            $response->throw();

            $html = $response->body();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            $username = $this->extractUsername();
            $name = $this->extractName($xpath);
            $introduction = $this->extractDescription($xpath);
            $message = $this->extractMessageText($xpath);
            $memberCount = $this->extractMemberCount($xpath);
            $type = $this->determineType($xpath);
            $isValid = $this->checkValidity($xpath);

            return [
                'username' => $username,
                'name' => $name,
                'introduction' => $introduction,
                'message' => $message,
                'member_count' => $memberCount,
                'type' => $type,
                'is_valid' => $isValid,
            ];
        } catch (\Exception $e) {
            Log::error("处理 URL:{$url} 时发生错误: " . $e->getMessage());
            return null;
        }
    }

    /* 
    提取用户名
    https://t.me/dillfrash
    https://t.me/dillfrash/17394
    https://t.me/s/dillfrash/17394
    url 的 dillfrash 就是用户名
    */
    private function extractUsername()
    {
        $pattern = '/\/(?:s\/)?([^\/]+)/';
        preg_match($pattern, $this->url, $matches);
        return $matches[1] ?? null;
    }

    private function extractMessageText($xpath)
    {
        $node = $xpath->query('//div[contains(@class, "tgme_widget_message_text js-message_text")]')->item(0);
        if ($node) {
            $html = $node->ownerDocument->saveHTML($node);
            $text = br2nl($html);
            $text = strip_tags($text); // 去除HTML标签
            return trim($text);
        }
        return null;
    }

    private function extractName($xpath)
    {
        $ogTitleNode = $xpath->query('//meta[@property="og:title"]')->item(0);
        return $ogTitleNode ? $ogTitleNode->getAttribute('content') : 'None';
    }

    private function extractDescription($xpath)
    {
        $node = $xpath->query('//div[contains(@class, "tgme_page_description")]')->item(0);
        if ($node) {
            $html = $node->ownerDocument->saveHTML($node);
            $text = br2nl($html);
            $text = strip_tags($text); // 去除HTML标签
            return trim($text);
        }

        $node = $xpath->query('//div[contains(@class, "tgme_channel_info_description")]')->item(0);
        if ($node) {
            $html = $node->ownerDocument->saveHTML($node);
            $text = br2nl($html);
            $text = strip_tags($text); // 去除HTML标签
            return trim($text);
        }

        // TODO 当 url 类似于 https://t.me/dillfrash/17394 时，需要通过 https://t.me/dillfrash 来获取 description。目前可以在任务调度里通过用户名来获取 description，但这样会导致一个问题：如果频道的介绍本来就是空的会到导致重复获取 description。

        return 'None';
    }

    private function extractMemberCount($xpath)
    {
        $node = $xpath->query('//div[contains(@class, "tgme_page_extra")]')->item(0);
        if ($node) {
            $text = $node->textContent;
            if (preg_match('/(\d[\d\s,]*)members/', $text, $matches)) {
                return (int) preg_replace('/\D/', '', $matches[1]);
            }
            if (preg_match('/(\d[\d\s,]*)subscribers/', $text, $matches)) {
                return (int) preg_replace('/\D/', '', $matches[1]);
            }
        }
        return 0;
    }

    private function determineType($xpath)
    {
        $node = $xpath->query('//div[contains(@class, "tgme_widget_message_text js-message_text")]')->item(0);
        if ($node) {
            return 'message';
        }

        $node = $xpath->query('//div[contains(@class, "tgme_page_extra")]')->item(0);
        if ($node) {
            $text = $node->textContent;
            if (strpos($text, 'members') !== false) {
                return 'group';
            }
            if (strpos($text, 'subscribers') !== false) {
                return 'channel';
            }
            if (preg_match('/^@\w+$/', $text)) {
                return 'person';
            }
        }

        return 'unknown';
    }

    private function checkValidity($xpath)
    {
        $robotsMeta = $xpath->query('//meta[@name="robots"]')->item(0);
        return !($robotsMeta && $robotsMeta->getAttribute('content') !== 'none');
    }
}
