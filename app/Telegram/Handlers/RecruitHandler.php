<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use App\Models\Message;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class RecruitHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $text = "📝 <b>提交收录</b>\n\n";
        $text .= "请向我发送您需要提交的[频道/群组/个人/机器人/消息]链接或用户名\n\n";
        $text .= "<i>可批量提交（每行一条）</i>\n";
        $text .= "<i>支持多种格式: 完整URL、用户名(@格式)</i>\n\n";
        $text .= "示例格式：\n";
        $text .= "• https://t.me/example\n";
        $text .= "• t.me/example\n";
        $text .= "• @example\n";

        $bot->sendMessage(
            text: $text,
            disable_web_page_preview: true,
            parse_mode: ParseMode::HTML
        );
    }

    /**
     * 处理提交的内容
     */
    public function handleSubmit(Nutgram $bot): void
    {
        $user = $bot->get('user');

        $message = $bot->message();
        if (empty($message?->text)) {
            return;
        }

        // 处理提交的内容
        $links = array_filter(explode("\n", $message->text));
        $validLinks = [];
        $invalidLinks = [];

        foreach ($links as $link) {
            $link = trim($link);
            // 验证链接格式
            if (
                str_starts_with($link, 'https://t.me/') ||
                str_starts_with($link, 't.me/') ||
                str_starts_with($link, '@')
            ) {
                $validLinks[] = $link;
            } else {
                $invalidLinks[] = $link;
            }
        }

        // 如果没有有效链接，不处理
        if (empty($validLinks)) {
            return;
        }

        // 发送处理结果
        $response = "<b>提交处理结果</b>\n\n";
        $keyboard = new InlineKeyboardMarkup();

        if (!empty($validLinks)) {
            $response .= "✅ 成功接收 " . count($validLinks) . " 条提交：\n";
            foreach ($validLinks as $link) {
                $response .= "• {$link}\n";

                if (str_starts_with($link, '@') || !str_contains($link, '/')) {
                    $username = ltrim($link, '@');
                } else {
                    $username = extract_telegram_username_by_url($link);
                    $message_id = extract_telegram_message_id_by_url($link);
                }
                $chat = Chat::firstOrCreate(
                    ['username' => $username],
                    [
                        'source' => 'manual',
                        'user_id' => $user->id,
                        'source_str' => $link,
                    ]
                );
                if (isset($message_id) && !empty($message_id)) {
                    Message::firstOrCreate(
                        ['chat_id' => $chat->id, 'source_id' => $message_id],
                        [
                            'source' => 'manual',
                            'user_id' => $user->id,
                            'source_str' => $link,
                        ]
                    );
                }

                // 为每个有效链接添加一个按钮
                $keyboard->addRow(
                    new InlineKeyboardButton(
                        text: "📊 查看 @{$username} 的收录情况",
                        callback_data: "recruit:detail:{$chat->id}"
                    )
                );
            }
        }

        if (!empty($invalidLinks)) {
            $response .= "\n❌ 以下 " . count($invalidLinks) . " 条格式无效：\n";
            foreach ($invalidLinks as $link) {
                $response .= "• {$link}\n";
            }
        }

        $response .= "\n提交完成！";

        $bot->sendMessage(
            text: $response,
            disable_web_page_preview: true,
            parse_mode: ParseMode::HTML,
            reply_markup: $keyboard,
        );
    }

    public function handleDetailCallback(Nutgram $bot): void
    {
        try {
            $callbackQuery = $bot->callbackQuery();
            $parts = explode(':', $callbackQuery->data);
            $chatId = $parts[2] ?? null;

            if (!$chatId) {
                $bot->answerCallbackQuery(
                    text: '无效的请求',
                    show_alert: true
                );
                return;
            }

            $chat = Chat::find($chatId);
            if (!$chat) {
                $bot->answerCallbackQuery(
                    text: '未找到相关记录',
                    show_alert: true
                );
                return;
            }

            // 构建详情消息
            $text = "<b>📊 详细信息</b>\n\n";
            $text .= "TG用户名: @{$chat->username}\n";
            $text .= "首次提交时间: " . $chat->created_at?->format('Y-m-d H:i:s') . "\n"; // TODO 处理时区
            $text .= "首次提交者: {$chat->user->name}\n";
            $text .= "收录时间: " . ($chat->verified_at ? $chat->verified_at?->format('Y-m-d H:i:s') : '正在排队收录中') . "\n"; // TODO 处理时区
            if ($chat->verified_at) $text .= "是否有效: " . ($chat->is_valid ? 'Yes' : '无效') . "\n";

            $bot->answerCallbackQuery();
            $bot->sendMessage(
                text: $text,
                parse_mode: ParseMode::HTML,
                disable_web_page_preview: true
            );
        } catch (\Throwable $e) {
            \Log::error('处理详情回调时出错', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $bot->answerCallbackQuery(
                text: '处理请求时出错，请稍后重试',
                show_alert: true
            );
        }
    }
}
