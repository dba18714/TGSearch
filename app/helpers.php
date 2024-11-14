<?php

if (!function_exists('br2nl')) {
    function br2nl($string) {
        return preg_replace('/<br\s*\/?>/i', "\n", $string);
    }
}

if (!function_exists('extract_telegram_username_by_url')) {
    function extract_telegram_username_by_url($url) {
        // 使用正则表达式匹配 t.me 或 telegram.me 链接中的用户名
        if (preg_match('#^https?://(?:t|telegram)\.me/(?:s/)?([^/?]+)#i', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}

if (!function_exists('extract_telegram_message_id_by_url')) {
    function extract_telegram_message_id_by_url($url) {
        // 匹配 URL 中的消息 ID
        if (preg_match('#^https?://(?:t|telegram)\.me/[^/]+/(\d+)#i', $url, $matches)) {
            return (int)$matches[1];
        }
        
        // 匹配 URL 中 before 参数的消息 ID
        if (preg_match('#[?&]before=(\d+)#i', $url, $matches)) {
            return (int)$matches[1];
        }
        
        return null;
    }
}