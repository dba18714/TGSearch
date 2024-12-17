<?php

if (!function_exists('br2nl')) {
    function saveToEnv($data = [])
    {
        foreach ($data as $key => $value) {
            if (! is_bool(strpos($value, ' '))) {
                $value = '"' . $value . '"';
            }
            $key = strtoupper($key);

            $envPath = app()->environmentFilePath();
            $contents = file_get_contents($envPath);

            preg_match("/^{$key}=[^\r\n]*/m", $contents, $matches);

            $oldValue = count($matches) ? $matches[0] : '';

            if ($oldValue) {
                $contents = str_replace("{$oldValue}", "{$key}={$value}", $contents);
            } else {
                $contents = $contents . "\n{$key}={$value}\n";
            }

            $file = fopen($envPath, 'w');
            fwrite($file, $contents);
            fclose($file);
        }
    }
}

if (!function_exists('br2nl')) {
    function br2nl($string)
    {
        return preg_replace('/<br\s*\/?>/i', "\n", $string);
    }
}

if (!function_exists('extract_telegram_username_by_url')) {
    function extract_telegram_username_by_url($url)
    {
        // 匹配带协议和不带协议的 t.me 或 telegram.me 链接中的用户名
        if (preg_match('#^(?:https?://)?(?:t|telegram)\.me/(?:s/)?([^/?]+)#i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}

/* 
Example usage:
$urls = [
    'https://t.me/username/123456',
    'https://t.me/username?before=123456',
    'https://t.me/username/something?before=123456&other=params'
];
foreach ($urls as $url) {
    $result = extract_telegram_message_id_by_url($url);
    echo "URL: $url\n";
    echo "Result: $result\n\n";
}

Output:
URL: https://t.me/username/123456
Result: 123456

URL: https://t.me/username?before=123456
Result: 123455

URL: https://t.me/username/something?before=123456&other=params
Result: 123455
*/
if (!function_exists('extract_telegram_message_id_by_url')) {
    function extract_telegram_message_id_by_url($url)
    {
        // 匹配 URL 中的消息 ID
        if (preg_match('#^https?://(?:t|telegram)\.me/[^/]+/(\d+)#i', $url, $matches)) {
            return (int)$matches[1];
        }

        // 匹配 URL 中 before 参数的消息 ID，并返回 ID-1
        if (preg_match('#[?&]before=(\d+)#i', $url, $matches)) {
            return (int)$matches[1] - 1;
        }

        return null;
    }
}

if (!function_exists('humanNumberToInteger')) {
    function humanNumberToInteger(string $number): int
    {
        // 移除所有空格
        $number = trim($number);

        // 匹配数字和单位
        preg_match('/^([\d.]+)\s*([KkMmBb])?$/', $number, $matches);

        if (empty($matches)) {
            return 0;
        }

        $num = (float) $matches[1];
        $unit = $matches[2] ?? '';

        return match (strtoupper($unit)) {
            'K' => (int) ($num * 1000),
            'M' => (int) ($num * 1000000),
            'B' => (int) ($num * 1000000000),
            default => (int) $num,
        };
    }
}
