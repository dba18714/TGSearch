<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class HelpHandler
{
    public function __invoke(Nutgram $bot, string $aff = null): void
    {
        $helpText = <<<HTML
📖 <b>使用帮助</b>

1️⃣ <b>搜索功能</b>
• 直接发送关键词即可搜索
• 支持搜索频道/群组/机器人/消息内容

2️⃣ <b>主要命令</b>
• /menu - 打开主菜单
• /help - 显示此帮助信息

3️⃣ <b>其他说明</b>
• 易搜官方讨论群: @yisouChat

祝您使用愉快！ 🎉
HTML;

        $bot->sendMessage(
            text: $helpText,
            disable_web_page_preview: true,
            parse_mode: ParseMode::HTML
        );
    }
}
