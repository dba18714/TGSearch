<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\Search;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SearchHandler
{
    private const PER_PAGE = 5;
    private const MAX_MESSAGE_LENGTH = 50;

    /**
     * 处理搜索请求
     */
    public function __invoke(Nutgram $bot)
    {
        try {
            $query = $this->extractSearchQuery($bot->message()->text);

            if (empty($query['search_text'])) {
                return $bot->sendMessage('请输入搜索关键词');
            }

            $searchResults = $this->performSearch($query['search_text'], $query['page']);

            if ($searchResults['total_results']->isEmpty()) {
                return $bot->sendMessage('没有找到相关结果 😢');
            }

            $this->sendSearchResults($bot, $searchResults, $query);
        } catch (\Throwable $e) {
            $this->handleError($bot, $e, '搜索过程中出现错误');
        }
    }

    /**
     * 处理分页回调
     */
    public function handlePagination(Nutgram $bot)
    {
        try {
            $callbackData = $this->parseCallbackData($bot->callbackQuery()->data);
            if (!$callbackData) {
                return;
            }

            $searchResults = $this->performSearch($callbackData['query'], $callbackData['page']);

            $this->updateSearchResults($bot, $searchResults, $callbackData);
            $bot->answerCallbackQuery();
        } catch (\Throwable $e) {
            $this->handleError($bot, $e, '处理分页时出错，请重试', true);
        }
    }

    /**
     * 从查询字符串中提取搜索关键词和页码
     */
    private function extractSearchQuery(string $text): array
    {
        $page = 1;
        $searchText = $text;

        if (str_contains($text, 'page:')) {
            preg_match('/page:(\d+)/', $text, $matches);
            $page = (int)$matches[1];
            $searchText = trim(str_replace("page:{$page}", '', $text));
        }

        return [
            'search_text' => $searchText,
            'page' => $page
        ];
    }

    /**
     * 执行搜索操作
     */
    private function performSearch(string $query, int $page): array
    {
        Search::recordSearch($query);

        $messageChatIds = $this->searchMessages($query);
        $chats = $this->searchChats($query);

        $allChatIds = $messageChatIds->keys()->merge($chats->pluck('id'))->unique();
        $paginatedChats = Chat::whereIn('id', $allChatIds)->paginate(self::PER_PAGE, page: $page);

        $this->attachMatchedMessages($paginatedChats, $messageChatIds);

        return [
            'message_chat_ids' => $messageChatIds,
            'chats' => $paginatedChats,
            'total_results' => $allChatIds
        ];
    }

    /**
     * 搜索消息
     */
    private function searchMessages(string $query)
    {
        return Message::search($query)
            ->get(['id', 'chat_id', 'text'])
            ->groupBy('chat_id')
            ->map(fn($messages) => $messages->take(1));
    }

    /**
     * 搜索聊天
     */
    private function searchChats(string $query)
    {
        return Chat::search($query)->get();
    }

    /**
     * 将匹配的消息附加到聊天结果中
     */
    private function attachMatchedMessages($chats, $messageChatIds): void
    {
        foreach ($chats as $chat) {
            $chat->matched_messages = $messageChatIds->get($chat->id);
        }
    }

    /**
     * 发送搜索结果
     */
    private function sendSearchResults(Nutgram $bot, array $searchResults, array $query): void
    {
        $text = $this->buildResultMessage(
            $searchResults['message_chat_ids'],
            $searchResults['chats'],
            $query['page']
        );

        $keyboard = $this->buildPaginationKeyboard(
            $query['search_text'],
            $query['page'],
            $searchResults['message_chat_ids'],
            $searchResults['chats']
        );

        $bot->sendMessage(
            text: $text,
            chat_id: $bot->chatId(),
            parse_mode: 'HTML',
            disable_web_page_preview: true,
            reply_markup: $keyboard
        );
    }

    /**
     * 解析回调数据
     */
    private function parseCallbackData(string $data): ?array
    {
        $parts = explode(':', $data);

        if (count($parts) !== 4) {
            Log::error('Invalid callback data format');
            return null;
        }

        return [
            'query' => urldecode($parts[1]),
            'page' => (int)$parts[3]
        ];
    }

    /**
     * 更新搜索结果消息
     */
    private function updateSearchResults(Nutgram $bot, array $searchResults, array $callbackData): void
    {
        $text = $this->buildResultMessage(
            $searchResults['message_chat_ids'],
            $searchResults['chats'],
            $callbackData['page']
        );

        $keyboard = $this->buildPaginationKeyboard(
            $callbackData['query'],
            $callbackData['page'],
            $searchResults['message_chat_ids'],
            $searchResults['chats']
        );

        $bot->editMessageText(
            text: $text,
            chat_id: $bot->chatId(),
            message_id: $bot->callbackQuery()->message->message_id,
            parse_mode: 'HTML',
            disable_web_page_preview: true,
            reply_markup: $keyboard
        );
    }

    /**
     * 构建结果消息文本
     */
    private function buildResultMessage($messageChatIds, $chats, $page): string
    {
        $totalMessages = $messageChatIds->count();
        $totalChats = $chats->total();
        $totalRecords = $totalMessages + $totalChats;
        $totalPages = $chats->lastPage();

        $text = "🔍 搜索结果 (第 {$page}/{$totalPages} 页，共 {$totalRecords} 条记录)\n\n";

        if ($chats->isNotEmpty()) {
            foreach ($chats as $chat) {
                $text .= $this->formatChatResult($chat);
            }
        }

        return $text;
    }

    /**
     * 格式化单个聊天结果
     */
    private function formatChatResult($chat): string
    {
        $text = "- <a href='{$chat->url}'>{$chat->name}</a>\n";

        if ($chat->matched_messages && $chat->matched_messages->isNotEmpty()) {
            $message = $chat->matched_messages->first();
            $text .= "  💬 {$this->truncate($message->text)}\n";
        }

        $text .= "  {$chat->type_name}" .
            ($chat->member_count ? " | {$chat->member_count} 成员" : "") . "\n\n";

        return $text;
    }

    /**
     * 构建分页键盘
     */
    private function buildPaginationKeyboard($query, $page, $messageChatIds, $chats): InlineKeyboardMarkup
    {
        $keyboard = new InlineKeyboardMarkup();
        $buttons = [];
        $encodedQuery = urlencode($query);

        if ($page > 1) {
            $buttons[] = new InlineKeyboardButton(
                text: '⬅️ 上一页',
                callback_data: "search:{$encodedQuery}:page:" . ($page - 1)
            );
        }

        if ($chats->hasMorePages()) {
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

    /**
     * 截断文本
     */
    private function truncate(string $text): string
    {
        if (mb_strlen($text) <= self::MAX_MESSAGE_LENGTH) {
            return $text;
        }
        return mb_substr($text, 0, self::MAX_MESSAGE_LENGTH) . '...';
    }

    /**
     * 处理错误
     */
    private function handleError(Nutgram $bot, \Throwable $e, string $message, bool $isCallback = false): void
    {
        Log::error('Search handler error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        if ($isCallback) {
            $bot->answerCallbackQuery(
                text: $message,
                show_alert: true
            );
        } else {
            $bot->sendMessage(
                text: $message,
                chat_id: $bot->chatId()
            );
        }
    }
}
