<?php

namespace App\Telegram\Handlers;

use App\Models\SearchRecord;
use App\Services\UnifiedSearchService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SearchHandler
{
    private const PER_PAGE = 10;
    private const MAX_MESSAGE_LENGTH = 60;

    private const SORT_OPTIONS = [
        'member_or_view_count:desc' => '按人数降序',
        'member_or_view_count:asc' => '升序'
    ];

    private const SUPPORTED_TYPES = [
        'channel' => '📢',
        'group' => '👥',
        'message' => '💬',
        'bot' => '🤖',
        'person' => '👤',
    ];

    protected UnifiedSearchService $searchService;

    public function __construct(UnifiedSearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * 处理搜索请求
     */
    public function __invoke(Nutgram $bot)
    {
        try {
            $query = $bot->message()?->text;
            if (empty($query)) {
                return;
            }
            $query = trim($query);

            // 如果是提交收录的格式，则转给 RecruitHandler 处理
            if (
                str_starts_with($query, 'https://t.me/') ||
                str_starts_with($query, 't.me/') ||
                str_starts_with($query, '@')
            ) {
                app(RecruitHandler::class)->handleSubmit($bot);
                return;
            }

            // 移除多余的空白字符(包括换行、制表符等)
            $query = preg_replace('/\s+/', ' ', $query);
            $searchResults = $this->performSearch($query);

            if ($searchResults->isEmpty()) {
                return $bot->sendMessage('没有找到相关结果 😢');
            }

            $text = $this->buildResultMessage(
                searchResults: $searchResults,
                page: 1,
                query: $query,
            );

            // 构建键盘 - 使用默认状态
            $keyboard = $this->buildPaginationKeyboard(
                $searchResults
            );

            // 发送消息时直接包含键盘
            $message = $bot->sendMessage(
                text: $text,
                parse_mode: 'HTML',
                disable_web_page_preview: true,
                reply_markup: $keyboard
            );

            // 缓存搜索状态供后续分页使用
            $this->cacheSearchState($message->message_id, [
                'query' => $query,
                'page' => null,
                'type' => null,
                'sort' => null,
                'direction' => null
            ]);
        } catch (\Throwable $e) {
            $this->handleError($bot, $e, '搜索过程中出现错误');
        }
    }

    /**
     * 执行搜索操作
     */
    private function performSearch(string $query, ?int $page = null, ?string $type = null, ?string $sort = null, ?string $direction = null)
    {
        SearchRecord::recordSearch($query);

        $filters = [];
        if ($type !== null) {
            $filters['type'] = $type;
        }

        $options = [
            'per_page' => self::PER_PAGE,
            'page' => $page
        ];

        if ($sort && $direction) {
            $options['sort'] = $sort;
            $options['direction'] = $direction;
        }

        return $this->searchService->search($query, $filters, $options);
    }

    /**
     * 构建结果消息文本
     */
    private function buildResultMessage($searchResults, $page, string $query): string
    {
        $totalRecords = $searchResults->total();
        $totalPages = $searchResults->lastPage();

        $text = "🔍 搜索 \"{$query}\" 的结果:\n\n";

        foreach ($searchResults as $result) {
            $searchable = $result->unified_searchable;

            if (!$searchable) continue;

            $title = $result->getTitle(self::MAX_MESSAGE_LENGTH);
            $text .= "$result->type_emoji <a href='{$result->url}'>{$title}</a>\n";

            if ($result->member_or_view_count > 0) {
                $member_or_view_count = number_format($result->member_or_view_count);
                $text .= "{$member_or_view_count} " .
                    ($result->type === 'message' ? '阅读' : '成员');
            }
            $text .= "\n\n";
        }

        $totalPages = $totalPages > 9 ? '9+' : $totalPages;
        $totalRecords = $totalRecords > 99 ? '99+' : $totalRecords;
        $text .= "第 {$page}/{$totalPages} 页，共 {$totalRecords} 条记录";

        return $text;
    }

    /**
     * 缓存搜索状态
     */
    private function cacheSearchState(int $messageId, array $state): void
    {
        Cache::put("search:{$messageId}", $state, now()->addMonth());
    }

    /**
     * 获取搜索状态
     */
    private function getSearchState(?int $messageId = null): array
    {
        if ($messageId) {
            return Cache::get("search:{$messageId}") ?? $this->getDefaultSearchState();
        }

        return $this->getDefaultSearchState();
    }

    /**
     * 获取默认搜索状态
     */
    private function getDefaultSearchState(): array
    {
        return [
            'query' => null,
            'page' => null,
            'type' => null,
            'sort' => null,
            'direction' => null
        ];
    }

    /**
     * 构建分页和类型筛选键盘
     */
    private function buildPaginationKeyboard($searchResults, string $currentType = null, ?int $messageId = null): InlineKeyboardMarkup
    {
        $keyboard = new InlineKeyboardMarkup();

        // 添加类型筛选按钮
        $typeButtons = [];
        $typesPerRow = 6;
        $currentTypeButtons = [];

        $currentTypeButtons[] = new InlineKeyboardButton(
            text: ($currentType === null ? '✓' : '') . 'All',
            callback_data: "search:type:"
        );

        foreach (self::SUPPORTED_TYPES as $type => $label) {
            $button = new InlineKeyboardButton(
                text: ($currentType === $type ? '✓' : '') . $label,
                callback_data: "search:type:{$type}"
            );

            $currentTypeButtons[] = $button;

            if (count($currentTypeButtons) === $typesPerRow) {
                $keyboard->addRow(...$currentTypeButtons);
                $currentTypeButtons = [];
            }
        }

        if (!empty($currentTypeButtons)) {
            $keyboard->addRow(...$currentTypeButtons);
        }

        // 获取当前状态
        $state = $this->getSearchState($messageId);
        $currentSort = $state['sort'] ?? null;
        $currentDirection = $state['direction'] ?? null;
        $page = $state['page'] ?? 1;

        // 添加排序按钮 - 在同一行显示
        $sortButtons = [];

        // 添加默认排序按钮
        $sortButtons[] = new InlineKeyboardButton(
            text: ($currentSort === null ? '✓' : '') . '默认排序',
            callback_data: "search:sort:default"
        );

        // 添加其他排序选项
        foreach (self::SORT_OPTIONS as $sortOption => $label) {
            [$sort, $direction] = explode(':', $sortOption);
            $isCurrentSort = $currentSort === $sort && $currentDirection === $direction;

            $sortButtons[] = new InlineKeyboardButton(
                text: ($isCurrentSort ? '✓' : '') . $label,
                callback_data: "search:sort:{$sort}:{$direction}"
            );
        }

        // 将所有排序按钮添加到同一行
        if (!empty($sortButtons)) {
            $keyboard->addRow(...$sortButtons);
        }

        // 添加分页按钮
        $paginationButtons = [];

        if ($page > 1) {
            $paginationButtons[] = new InlineKeyboardButton(
                text: '⬅️ 上一页',
                callback_data: "search:page:" . ($page - 1)
            );
        }

        if ($searchResults->hasMorePages()) {
            $paginationButtons[] = new InlineKeyboardButton(
                text: '下一页 ➡️',
                callback_data: "search:page:" . ($page + 1)
            );
        }

        if (!empty($paginationButtons)) {
            $keyboard->addRow(...$paginationButtons);
        }

        return $keyboard;
    }

    /**
     * 处理分页和类型筛选回调
     */
    public function handleSearchCallback(Nutgram $bot)
    {
        try {
            $callbackData = $bot->callbackQuery()->data;
            $messageId = $bot->callbackQuery()->message->message_id;
            $parts = explode(':', $callbackData);

            if (count($parts) < 3) {
                return;
            }

            [, $action, $value] = $parts;

            // 获取缓存的搜索状态
            $state = $this->getSearchState($messageId);
            if (empty($state['query'])) {
                $bot->answerCallbackQuery(
                    text: '搜索已过期，请重新搜索',
                    show_alert: true
                );
                return;
            }

            // 保存旧状态用于比较
            $oldState = $state;

            // 根据动作更新状态
            switch ($action) {
                case 'type':
                    $state['type'] = $value ? $value : null;
                    $state['page'] = 1;
                    break;

                case 'sort':
                    if ($value === 'default') {
                        $state['sort'] = null;
                        $state['direction'] = null;
                    } else {
                        $state['sort'] = $parts[2];
                        $state['direction'] = $parts[3];
                    }
                    $state['page'] = 1;
                    break;

                case 'page':
                    $state['page'] = (int)$value;
                    break;
            }


            // 如果状态没有变化，直接返回
            if ($this->statesAreEqual($oldState, $state)) {
                $bot->answerCallbackQuery();
                return;
            }

            // 执行搜索
            $searchResults = $this->performSearch(
                $state['query'],
                $state['page'],
                $state['type'],
                $state['sort'],
                $state['direction']
            );

            // 更新缓存状态
            $this->cacheSearchState($messageId, $state);

            // 更新消息
            $text = $this->buildResultMessage(
                $searchResults,
                $state['page'],
                $state['query'],
                $state['type']
            );

            $keyboard = $this->buildPaginationKeyboard(
                $searchResults,
                $state['type'],
                $messageId
            );

            $bot->editMessageText(
                text: $text,
                message_id: $messageId,
                parse_mode: 'HTML',
                disable_web_page_preview: true,
                reply_markup: $keyboard
            );

            $bot->answerCallbackQuery();
        } catch (\Throwable $e) {
            $this->handleError($bot, $e, '处理分页时出错，请重试', true);
        }
    }

    /**
     * 比较两个状态是否相等
     */
    private function statesAreEqual(array $state1, array $state2): bool
    {
        return $state1['query'] == $state2['query'] &&
            $state1['page'] == $state2['page'] &&
            $state1['type'] == $state2['type'] &&
            $state1['sort'] == $state2['sort'] &&
            $state1['direction'] == $state2['direction'];
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
