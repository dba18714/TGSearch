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
                  "直接发送关键词即可搜索频道/群组/机器人和消息内容。\n",
            parse_mode: ParseMode::HTML
        );
    }
}