<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class StartHandler
{
    public function __invoke(Nutgram $bot, string $aff = null): void
    {
        $aff = trim($aff);
        if (empty($aff)) {
            // $aff = '111';
        }
        // \Log::debug('StartHandler invoked');

        $bot->sendMessage(
            text: "👋 欢迎使用搜索机器人！\n\n" .
                "直接发送关键词即可搜索频道/群组/机器人和消息内容。\n\n" .
                "主菜单: /menu",
            disable_web_page_preview: true,
            parse_mode: ParseMode::HTML
        );
    }
}
