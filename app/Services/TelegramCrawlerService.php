<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramCrawlerService
{
    public function crawl($url)
    {
        try {
            $response = Http::withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36')
                ->get($url);

            $response->throw();

            $html = $response->body();
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            $name = $this->extractName($xpath);
            $introduction = $this->extractDescription($xpath);
            $message = $this->extractMessageText($xpath);
            $memberCount = $this->extractMemberCount($xpath);
            $type = $this->determineType($xpath);
            $isValid = $this->checkValidity($xpath);

            return [
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



    private function extractMessageText($xpath)
    {
        $messageNode = $xpath->query('//div[contains(@class, "tgme_widget_message_text js-message_text")]')->item(0);
        if ($messageNode) {
            // 先替换 <br/> 为换行符，然后再去除其他HTML标签
            $html = $messageNode->ownerDocument->saveHTML($messageNode);
            $text = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
            $text = strip_tags($text);

            // 处理可能出现的连续换行和空格
            $text = preg_replace('/\n\s+/', "\n", $text);
            $text = trim($text);

            return $text;
        }
        return null;
    }


    private function extractName($xpath)
    {
        $ogTitleNode = $xpath->query('//meta[@property="og:title"]')->item(0);
        return $ogTitleNode ? $ogTitleNode->getAttribute('content') : 'None';

        // $nameNode = $xpath->query('//div[contains(@class, "tgme_page_title")]/span')->item(0);
        // return $nameNode ? $nameNode->textContent : 'None';
    }

    private function extractDescription($xpath)
    {
        $ogDescNode = $xpath->query('//meta[@property="og:description"]')->item(0);
        return $ogDescNode ? str_replace("\t", "\n", $ogDescNode->getAttribute('content')) : 'None';

        // $descNode = $xpath->query('//div[contains(@class, "tgme_page_description")]')->item(0);
        // return $descNode ? $descNode->textContent : 'None';
    }

    private function extractMemberCount($xpath)
    {
        $memberNode = $xpath->query('//div[contains(@class, "tgme_page_extra")]')->item(0);
        if ($memberNode) {
            $text = $memberNode->textContent;
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
        $extraNode = $xpath->query('//div[contains(@class, "tgme_page_extra")]')->item(0);
        if ($extraNode) {
            $text = $extraNode->textContent;
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

        $messageNode = $xpath->query('//div[contains(@class, "tgme_widget_message_text js-message_text")]')->item(0);
        if ($messageNode) {
            return 'message';
        }
        return 'unknown';
    }

    private function checkValidity($xpath)
    {
        $robotsMeta = $xpath->query('//meta[@name="robots"]')->item(0);
        return !($robotsMeta && $robotsMeta->getAttribute('content') !== 'none');
    }
}
