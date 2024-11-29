<?php

namespace App\Telegram\Handlers;

use App\Models\Search;
use App\Services\UnifiedSearchService;
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
    // private const SUPPORTED_TYPES = [
    //     'all' => '🔍 全部',
    //     'channel' => '📢 频道',
    //     'group' => '👥 群组',
    //     'message' => '💬 消息',
    //     'bot' => '🤖 机器人',
    //     'person' => '👤 个人',
    // ];

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
        // TODO 用缓存方式储存当前筛选状态和搜索关键词等，解决 BUTTON_DATA_INVALID 长度限制问题
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
     * 处理分页和类型筛选回调
     */
    public function handlePagination(Nutgram $bot)
    {
        try {
            $callbackData = $this->parseCallbackData($bot->callbackQuery()->data);
            if (!$callbackData) {
                return;
            }

            $searchResults = $this->performSearch(
                $callbackData['query'],
                $callbackData['page'],
                $callbackData['type'],
                $callbackData['sort'],
                $callbackData['direction']
            );

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

        $keyboard = $this->buildPaginationKeyboard(
            $query['search_text'],
            $query['page'],
            $searchResults,
            $query['type']
        );

        $bot->sendMessage(
            text: $text,
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
        $parts = explode('|', $data);

        if (count($parts) < 3) {
            Log::error('Invalid callback data format', ['data' => $data]);
            return null;
        }

        // 使用更紧凑的格式解析
        $params = [];
        foreach ($parts as $part) {
            [$key, $value] = explode(':', $part);
            $params[$key] = $value;
        }

        return [
            'query' => urldecode($params['q'] ?? ''),
            'type' => $params['t'] ?? 'all',
            'page' => (int)($params['p'] ?? 1),
            'sort' => $params['s'] ?? null,
            'direction' => $params['d'] ?? null
        ];
    }

    /**
     * 更新搜索结果消息
     */
    private function updateSearchResults(Nutgram $bot, $searchResults, array $callbackData): void
    {
        $text = $this->buildResultMessage(
            $searchResults,
            $callbackData['page'],
            $callbackData['query'],
            $callbackData['type']
        );

        $keyboard = $this->buildPaginationKeyboard(
            $callbackData['query'],
            $callbackData['page'],
            $searchResults,
            $callbackData['type'],
            $callbackData['sort'],
            $callbackData['direction']
        );

        $bot->editMessageText(
            text: $text,
            parse_mode: 'HTML',
            disable_web_page_preview: true,
            reply_markup: $keyboard
        );
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
     * 构建分页和类型筛选键盘
     */
    private function buildPaginationKeyboard($query, $page, $searchResults, $currentType = 'all', $currentSort = null, $currentDirection = null): InlineKeyboardMarkup
    {
        $keyboard = new InlineKeyboardMarkup();

        // 限制查询长度并编码
        $q = substr(urlencode($query), 0, 15);
        $t = $currentType;

        // 基础回调数据
        $baseCallback = "q:{$q}|t:{$t}|p:";

        // 添加排序参数（如果存在）
        $sortPart = '';
        if ($currentSort && $currentDirection) {
            $sortPart = "|s:{$currentSort}|d:{$currentDirection}";
        }

        // 添加类型筛选按钮
        $typeButtons = [];
        $typesPerRow = 6;
        $currentTypeButtons = [];

        foreach (self::SUPPORTED_TYPES as $type => $label) {
            $callbackData = "q:{$q}|t:{$type}|p:1" . $sortPart;

            $button = new InlineKeyboardButton(
                text: ($currentType === $type ? '✓' : '') . $label,
                callback_data: $callbackData
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

        // 添加排序按钮 - 在同一行显示
        $sortButtons = [];

        // 添加默认排序按钮 - 总是显示,并在未使用其他排序时显示勾选标记
        $sortButtons[] = new InlineKeyboardButton(
            text: ($currentSort === null ? '✓' : '') . '默认排序',
            callback_data: "q:{$q}|t:{$t}|p:1"
        );

        // 添加其他排序选项
        foreach (self::SORT_OPTIONS as $sortOption => $label) {
            [$sort, $direction] = explode(':', $sortOption);
            $isCurrentSort = $currentSort === $sort && $currentDirection === $direction;

            $sortButtons[] = new InlineKeyboardButton(
                text: ($isCurrentSort ? '✓' : '') . $label,
                callback_data: "q:{$q}|t:{$t}|p:1|s:{$sort}|d:{$direction}"
            );
        }

        // 将所有排序按钮添加到同一行
        if (!empty($sortButtons)) {
            $keyboard->addRow(...$sortButtons);
        }

        // 添加分页按钮
        $paginationButtons = [];

        if ($page > 1) {
            $prevCallback = $baseCallback . ($page - 1) . $sortPart;
            $paginationButtons[] = new InlineKeyboardButton(
                text: '⬅️ 上一页',
                callback_data: $prevCallback
            );
        }

        if ($searchResults->hasMorePages()) {
            $nextCallback = $baseCallback . ($page + 1) . $sortPart;
            $paginationButtons[] = new InlineKeyboardButton(
                text: '下一页 ➡️',
                callback_data: $nextCallback
            );
        }

        if (!empty($paginationButtons)) {
            $keyboard->addRow(...$paginationButtons);
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
