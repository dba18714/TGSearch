<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class StartHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $bot->sendMessage(
            text: "👋 欢迎使用搜索机器人！\n\n".
                  "直接发送关键词即可搜索频道和消息内容。\n".
                  "示例：laravel",
            parse_mode: ParseMode::HTML
        );
    }
}