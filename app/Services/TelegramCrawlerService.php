<?php

namespace App\Services;

use App\Models\Owner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TelegramCrawlerService
{
    protected $url;
    protected $username;
    protected $message_id;
    protected $type;

    public function crawl(string $username, ?int $message_id = null)
    {
        $this->username = $username;
        $this->message_id = $message_id;

        $url = "https://t.me/s/{$username}";
        if ($message_id) $url = "https://t.me/s/{$username}/{$message_id}";
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
            $this->type = $type = $this->determineType($xpath);
            $message = $this->extractMessageText($xpath);
            $view_count = $this->extractViewCount($xpath);

            $member_count = $this->extractMemberCount($xpath);
            $counters = $this->extractCounters($xpath);
            if($member_count === null) $member_count = $counters['subscribers'];

            $isValid = $this->checkValidity($xpath);

            $data = [
                'username' => $username,
                'name' => $name,
                'introduction' => $introduction,
                'message' => $message,
                'view_count' => $view_count,
                'member_count' => $member_count,
                'photo_count' => $counters['photos'],
                'video_count' => $counters['videos'],
                'file_count' => $counters['files'],
                'link_count' => $counters['links'],
                'type' => $type,
                'is_valid' => $isValid,
            ];
            Log::debug("处理 URL:{$url} 成功 data:", $data);
            return $data;
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

    private function extractViewCount($xpath)
    {
        $str = "{$this->username}/{$this->message_id}";
        $node = $xpath->query('//div[@data-post="' . $str . '"]//span[contains(@class, "tgme_widget_message_views")]')->item(0);
        if ($node) {
            $text = $node->textContent;
            return humanNumberToInteger($text);
        }
        return null;
    }

    private function extractMessageText($xpath)
    {
        $str = "{$this->username}/{$this->message_id}";
        $node = $xpath->query('//div[@data-post="' . $str . '"]//div[contains(@class, "tgme_widget_message_text js-message_text")][@dir="auto"]')->item(0);
        if ($node) {
            $html = $node->ownerDocument->saveHTML($node);
            $text = br2nl($html);
            $text = strip_tags($text); // 去除HTML标签
            return trim($text);
        }

        if ($this->message_id && $this->type == 'group') {
            $node = $xpath->query('//meta[@property="og:description"]')->item(0);
            if ($node) return $node->getAttribute('content');
        }

        return null;
    }

    private function extractName($xpath)
    {
        $node = $xpath->query('//meta[@property="og:title"]')->item(0);
        return $node ? $node->getAttribute('content') : 'None';
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

    private function extractCounters($xpath)
    {
        $counters = [
            'subscribers' => null,
            'photos' => null,
            'videos' => null,
            'files' => null,
            'links' => null
        ];

        $nodes = $xpath->query('//div[contains(@class, "tgme_channel_info_counter")]');

        foreach ($nodes as $node) {
            $valueNode = $xpath->query('.//span[@class="counter_value"]', $node)->item(0);
            $typeNode = $xpath->query('.//span[@class="counter_type"]', $node)->item(0);

            if ($valueNode && $typeNode) {
                $value = humanNumberToInteger($valueNode->textContent);
                $type = trim($typeNode->textContent);

                switch ($type) {
                    case 'subscribers':
                        $counters['subscribers'] = $value;
                        break;
                    case 'photos':
                        $counters['photos'] = $value;
                        break;
                    case 'videos':
                        $counters['videos'] = $value;
                        break;
                    case 'files':
                        $counters['files'] = $value;
                        break;
                    case 'links':
                        $counters['links'] = $value;
                        break;
                }
            }
        }

        return $counters;
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
        return null;
    }

    private function determineType($xpath)
    {
        Log::debug('determineType start');
        $node = $xpath->query('//div[contains(@class, "tgme_page_widget_action")]//a[contains(@class, "tgme_action_button_new shine")]')->item(0);
        if ($node) {
            $text = $node->textContent;
            if (strpos($text, 'Group') !== false) {
                return 'group';
            }
        }

        $node = $xpath->query('//div[contains(@class, "tgme_page_extra")]')->item(0);
        if ($node) {
            $text = $node->textContent;
            Log::debug("text: -{$text}-");
            if (strpos($text, 'members') !== false) {
                return 'group';
            }
            if (strpos($text, 'subscribers') !== false) {
                return 'channel';
            }
            if (preg_match('/^@\w+$/', trim($text))) {
                return 'person';
            }
        }

        $node = $xpath->query('//div[contains(@class, "tgme_channel_info_counters")]')->item(0);
        if ($node) {
            $text = $node->textContent;
            if (strpos($text, 'subscribers') !== false) {
                return 'channel';
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
