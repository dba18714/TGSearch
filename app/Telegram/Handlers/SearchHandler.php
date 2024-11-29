<?php

namespace App\Telegram\Handlers;

use App\Models\Search;
use App\Services\UnifiedSearchService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class SearchHandler
{
    private const PER_PAGE = 5;
    private const MAX_MESSAGE_LENGTH = 50;

    private const SORT_OPTIONS = [
        'member_or_view_count:desc' => '按人数降序',
        'member_or_view_count:asc' => '升序'
    ];

    private const SUPPORTED_TYPES = [
        'all' => 'All',
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
            $query = $this->extractSearchQuery($bot->message()->text);

            if (empty($query['search_text'])) {
                return $bot->sendMessage('请输入搜索关键词');
            }

            $searchResults = $this->performSearch(
                $query['search_text'],
                $query['page'],
                $query['type'] ?? 'all'
            );

            if ($searchResults->isEmpty()) {
                return $bot->sendMessage('没有找到相关结果 😢');
            }

            $this->sendSearchResults($bot, $searchResults, $query);
        } catch (\Throwable $e) {
            $this->handleError($bot, $e, '搜索过程中出现错误');
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
            'page' => $page,
            'type' => 'all'
        ];
    }

    /**
     * 执行搜索操作
     */
    private function performSearch(string $query, int $page, string $type = 'all', ?string $sort = null, ?string $direction = null)
    {
        Search::recordSearch($query);

        $filters = [];
        if ($type !== 'all') {
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
     * 发送搜索结果
     */
    private function sendSearchResults(Nutgram $bot, $searchResults, array $query): void
    {
        $text = $this->buildResultMessage(
            $searchResults,
            $query['page'],
            $query['search_text'], 
            $query['type']
        );

        // 构建键盘 - 使用默认状态
        $keyboard = $this->buildPaginationKeyboard(
            $searchResults,
            $query['type']
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
            'query' => $query['search_text'],
            'page' => $query['page'],
            'type' => $query['type'],
            'sort' => null,
            'direction' => null
        ]);
    }

    /**
     * 构建结果消息文本
     */
    private function buildResultMessage($searchResults, $page, string $query, string $type = 'all'): string
    {
        $totalRecords = $searchResults->total();
        $totalPages = $searchResults->lastPage();
        $currentType = self::SUPPORTED_TYPES[$type] ?? '全部';

        $text = "🔍 搜索 \"{$query}\" 的结果:\n\n";

        foreach ($searchResults as $result) {
            $searchable = $result->unified_searchable;

            if (!$searchable) continue;

            $title = $searchable->name ?? $searchable->text;
            $text .= "$result->type_emoji <a href='{$result->url}'>{$title}</a>\n";

            if ($result->member_or_view_count > 0) {
                $member_or_view_count = number_format($result->member_or_view_count);
                $text .= "{$member_or_view_count} " .
                    ($result->type === 'message' ? '阅读' : '成员');
            }
            $text .= "\n\n";
        }

        $text .= "第 {$page}/{$totalPages} 页，共 {$totalRecords} 条记录";

        return $text;
    }

    /**
     * 缓存搜索状态
     */
    private function cacheSearchState(int $messageId, array $state): void
    {
        Cache::put("search:{$messageId}", $state, now()->addDay());
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
            'query' => '',
            'page' => 1,
            'type' => 'all',
            'sort' => null,
            'direction' => null
        ];
    }

    /**
     * 构建分页和类型筛选键盘
     */
    private function buildPaginationKeyboard($searchResults, string $currentType = 'all', ?int $messageId = null): InlineKeyboardMarkup
    {
        $keyboard = new InlineKeyboardMarkup();

        // 添加类型筛选按钮
        $typeButtons = [];
        $typesPerRow = 6;
        $currentTypeButtons = [];

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
                throw new \Exception('搜索已过期，请重新搜索');
            }

            // 根据动作更新状态
            switch ($action) {
                case 'type':
                    $state['type'] = $value;
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