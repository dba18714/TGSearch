<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SearchHandler
{
    protected $perPage = 5; // 每页显示数量

    public function __invoke(Nutgram $bot)
    {
        Log::error('222222222222');

        $query = $bot->message()->text;

        // 从 query string 中获取页码，默认第1页
        $page = 1;
        if (str_contains($query, 'page:')) {
            preg_match('/page:(\d+)/', $query, $matches);
            $page = (int)$matches[1];
            // 移除页码信息以获取纯搜索词
            $query = trim(str_replace("page:{$page}", '', $query));
        }

        // 搜索消息
        $messages = Message::search($query)
            ->query(function ($builder) {
                return $builder->with('chat');
            })
            ->paginate($this->perPage, 'page', $page);

        // 搜索频道/群组
        $chats = Chat::search($query)
            ->paginate($this->perPage, 'page', $page);

        if ($messages->isEmpty() && $chats->isEmpty()) {
            return $bot->sendMessage('没有找到相关结果 😢');
        }

        // 构建结果消息
        $text = $this->buildResultMessage($messages, $chats, $page);

        // 构建分页键盘
        $keyboard = $this->buildPaginationKeyboard($query, $page, $messages, $chats);

        try {
            $bot->sendMessage(
                text: $text,
                chat_id: $bot->chatId(),  // 添加 chat_id 参数
                parse_mode: 'HTML',
                disable_web_page_preview: true,
                reply_markup: $keyboard
            );
        } catch (\Throwable $e) {
            Log::error('Error sending search results: ' . $e->getMessage());
            $bot->sendMessage(
                text: '抱歉，发送结果时出现错误 😅',
                chat_id: $bot->chatId()
            );
        }
    }

    protected function buildResultMessage($messages, $chats, $page)
    {
        // 计算总记录数
        $totalMessages = $messages->total();
        $totalChats = $chats->total();
        $totalRecords = $totalMessages + $totalChats;

        // 计算总页数（取两个分页的最大值）
        $totalPages = max(
            $messages->lastPage(),
            $chats->lastPage()
        );

        $text = "🔍 搜索结果 (第 {$page}/{$totalPages} 页，共 {$totalRecords} 条记录)\n\n";

        if ($messages->isNotEmpty()) {
            $text .= "📝 <b>消息 ({$totalMessages} 条):</b>\n";
            foreach ($messages as $message) {
                $chatName = $message->chat->name ?? $message->chat->username ?? '未知';
                $text .= "- <a href='{$message->url}'>{$this->truncate($message->text)}</a>\n";
                $text .= "  来自: {$chatName}\n\n";
            }
        }

        if ($chats->isNotEmpty()) {
            $text .= "\n📢 <b>频道/群组 ({$totalChats} 个):</b>\n";
            foreach ($chats as $chat) {
                $text .= "- <a href='{$chat->url}'>{$chat->name}</a>\n";
                $text .= "  {$chat->type} | {$chat->members_count} 成员\n\n";
            }
        }

        return $text;
    }

    public function handlePagination(Nutgram $bot, $param)
    {
        try {
            $data = $bot->callbackQuery()->data;
            Log::debug('Callback data:', ['data' => $data]);

            // 解析 callback_data
            $parts = explode(':', $data);
            Log::debug('Parsed parts:', ['parts' => $parts]);

            if (count($parts) !== 4) {
                Log::error('Invalid callback data format');
                return;
            }

            list(, $query,, $pageStr) = $parts;
            // 确保 page 是整数
            $page = (int)$pageStr;

            Log::debug('Extracted values:', [
                'query' => $query,
                'page' => $page
            ]);

            // 执行搜索
            $messages = Message::search($query)
                ->query(function ($builder) {
                    return $builder->with('chat');
                })
                ->paginate($this->perPage, page: $page); // 使用命名参数确保类型正确

            $chats = Chat::search($query)
                ->paginate($this->perPage, page: $page); // 使用命名参数确保类型正确

            // 构建新的消息文本和键盘
            $text = $this->buildResultMessage($messages, $chats, $page);
            $keyboard = $this->buildPaginationKeyboard($query, $page, $messages, $chats);

            Log::debug('Attempting to edit message', [
                'chat_id' => $bot->chatId(),
                'message_id' => $bot->callbackQuery()->message->message_id,
                'text_length' => strlen($text)
            ]);

            // 修改原消息而不是发送新消息
            $bot->editMessageText(
                text: $text,
                chat_id: $bot->chatId(),
                message_id: $bot->callbackQuery()->message->message_id,
                parse_mode: 'HTML',
                disable_web_page_preview: true,
                reply_markup: $keyboard
            );

            // 删除回调查询通知
            $bot->answerCallbackQuery();
        } catch (\Throwable $e) {
            Log::error('Error in handlePagination:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // 通知用户出错
            $bot->answerCallbackQuery(
                text: '处理分页时出错，请重试',
                show_alert: true
            );
        }
    }

    protected function buildPaginationKeyboard($query, $page, $messages, $chats)
    {
        $keyboard = new InlineKeyboardMarkup();
        $buttons = [];

        // URL 编码查询字符串以处理特殊字符
        $encodedQuery = urlencode($query);

        $hasMore = $messages->hasMorePages() || $chats->hasMorePages();

        if ($page > 1) {
            $buttons[] = new InlineKeyboardButton(
                text: '⬅️ 上一页',
                callback_data: "search:{$encodedQuery}:page:" . ($page - 1)
            );
        }

        if ($hasMore) {
            $buttons[] = new InlineKeyboardButton(
                text: '下一页 ➡️',
                callback_data: "search:{$encodedQuery}:page:" . ($page + 1)
            );
        }

        if (!empty($buttons)) {
            $keyboard->addRow(...$buttons);
        }

        return $keyboard;
    }

    protected function truncate($text, $length = 50)
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        return mb_substr($text, 0, $length) . '...';
    }
}
