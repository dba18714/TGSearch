<?php

namespace App\Telegram\Handlers;

use App\Models\TgGroup;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class GroupWelcomeHandler
{
    public function __invoke(Nutgram $bot, string $action = null): void
    {
        $messageId = $bot->message()?->message_id;
        $user = $bot->get('user');

        $group = $bot->get('group'); // 当处理按钮回调时没有这项
        // Log::info('$group', $group->toArray());
        if (!$group) {
            $group = TgGroup::firstOrCreate(
                ['source_id' => $bot->chat()->id],
                [
                    'name' => $bot->chat()->title,
                    'user_id' => $user->id,
                ]
            );
        }
        if ($action == 'set:direct_search_enabled') {
            $group->direct_search_enabled = !$group->direct_search_enabled;
            $group->save();
            $status = $group->direct_search_enabled ? '开启' : '关闭';
            $bot->answerCallbackQuery(
                text: "关键词直接搜索已{$status}",
            );
        }
        if ($action == 'set:command_search_enabled') {
            $group->command_search_enabled = !$group->command_search_enabled;
            $group->save();
            $status = $group->command_search_enabled ? '开启' : '关闭';
            $bot->answerCallbackQuery(
                text: "/q+[空格]+[关键词]搜索已{$status}",
            );
        }

        // 创建键盘按钮
        $direct_icon = $group->direct_search_enabled ? '✅' : '☑️';
        $command_icon = $group->command_search_enabled ? '✅' : '☑️';
        $keyboard = InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(
                    "{$direct_icon} 关键词直接搜索",
                    callback_data: 'GroupWelcome:set:direct_search_enabled'
                ),
            )
            ->addRow(
                InlineKeyboardButton::make(
                    "{$command_icon} /q+[空格]+[关键词]搜索",
                    callback_data: 'GroupWelcome:set:command_search_enabled'
                ),
            );

        $name = trim($bot->user()?->first_name . ' ' . $bot->user()?->last_name);
        if (empty($name)) $name = 'unknown';
        $user_link = "<a href='tg://user?id={$bot->user()->id}'>{$name}</a>";

        // TODO 群成员每完成一次搜索，{$user_link} 都将获得 0.0036 USST 的奖励
        $text = <<<HTML
感謝您將我加入到您的群組！
欢迎使用易搜！
HTML;

        if ($messageId) {
            $bot->editMessageText(
                text: $text,
                parse_mode: ParseMode::HTML,
                reply_markup: $keyboard,
            );
        } else {
            $bot->sendMessage(
                text: $text,
                disable_web_page_preview: true,
                parse_mode: ParseMode::HTML,
                reply_markup: $keyboard,
            );
        }
    }
}
