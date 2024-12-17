<?php

namespace App\Telegram\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class RecruitConversation extends Conversation
{
    protected InlineKeyboardMarkup $cancelkeyboard;
    protected ?int $startMessageId = null;

    public function __construct()
    {
        $keyboard = new InlineKeyboardMarkup();
        $buttons = [];

        $buttons[] = new InlineKeyboardButton(
            text: '❌ 退出',
            callback_data: "cancel"
        );

        $keyboard->addRow(...$buttons);

        $this->cancelkeyboard = $keyboard;
    }

    public function getStartText() {
        $text = '';
        $text .= '请向我发送您需要提交的[频道/群组/个人/机器人/消息]链接或用户名\n';
        $text .= "<i>可批量提交（每行一条）</i>\n";
        $text .= "<i>支持多种格式: 完整URL、用户名(@格式)、纯用户名</i>";

        return $text;
    }


    public function start(Nutgram $bot)
    {
        $message = $bot->sendMessage(
            text: $this->getStartText(),
            parse_mode: 'HTML',
            disable_web_page_preview: true,
            reply_markup: $this->cancelkeyboard
        );

        $this->startMessageId = $message->message_id;  // 保存消息ID

        $this->setSkipHandlers(true)->next('secondStep');
    }

    public function secondStep(Nutgram $bot)
    {
        if ($bot->callbackQuery()?->data === 'cancel') {
            $bot->answerCallbackQuery();
            $bot->editMessageText('已取消操作');
            $this->end();
            return;
        }

        // 删除“取消按钮”
        if ($this->startMessageId) {
            $bot->editMessageText(
                text: $this->getStartText(),
                message_id: $this->startMessageId,
                parse_mode: 'HTML'
            );
        }

        // 获取用户发送的文本
        $message = $bot->message()?->text;

        if (empty($message)) {
            $bot->sendMessage(
                text: '请发送有效的链接或用户名',
                reply_markup: $this->cancelkeyboard
            );
            return;
        }

        // 将文本按行分割
        $links = explode("\n", $message);
        $links = array_map('trim', $links);
        $links = array_filter($links);

        if (empty($links)) {
            $bot->sendMessage(
                text: '未检测到有效的链接或用户名',
                reply_markup: $this->cancelkeyboard
            );
            return;
        }

        // 处理每个链接
        $processedLinks = [];
        foreach ($links as $link) {
            $username = $this->processLink($link);
            if ($username) {
                $processedLinks[] = $username;
            }
        }

        if (empty($processedLinks)) {
            $bot->sendMessage(
                text: '未能成功处理任何链接，请检查格式是否正确',
                reply_markup: $this->cancelkeyboard
            );
            return;
        } else {
            $responseText = "成功处理以下链接：\n";
            foreach ($processedLinks as $index => $username) {
                $responseText .= ($index + 1) . ". @" . $username . "\n";
            }
            $bot->sendMessage($responseText);
        }

        $this->end();
    }

    /**
     * 处理单个链接，提取用户名
     * 
     * @param string $link
     * @return string|null
     */
    private function processLink(string $link): ?string
    {
        // 移除首尾空白
        $link = trim($link);

        // 处理完整URL格式 (如 https://t.me/username 或 https://telegram.me/username)
        if (str_contains($link, 't.me/') || str_contains($link, 'telegram.me/')) {
            $parts = explode('/', $link);
            $username = end($parts);
            return $this->cleanUsername($username);
        }

        // 处理@格式
        if (str_starts_with($link, '@')) {
            return $this->cleanUsername(substr($link, 1));
        }

        // 处理纯用户名
        return $this->cleanUsername($link);
    }

    /**
     * 清理用户名
     * 
     * @param string $username
     * @return string|null
     */
    private function cleanUsername(string $username): ?string
    {
        // 移除可能的查询参数
        if (str_contains($username, '?')) {
            $username = explode('?', $username)[0];
        }

        // 基本验证：用户名应该只包含字母、数字和下划线
        if (preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            return $username;
        }

        return null;
    }
}
