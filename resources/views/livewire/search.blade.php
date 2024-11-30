<div>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 pt-8 pb-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8 text-center sm:hidden">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Telegram 资源导航</h1>
            </div>

            {{-- <livewire:ad-display position="sidebar" />
            <livewire:ad-display position="header" />
            <livewire:ad-display position="content" />
            <livewire:ad-display position="footer" /> --}}

            <!-- Search and Sort Section -->
            <div class="max-w-4xl mx-auto mb-8" id="paginated-posts">
                <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-md">
                    <div class="relative flex items-center space-x-2">
                        <div x-data="{ focused: false }" class="relative flex-1">
                            <input type="search" wire:model.live.debounce.250ms="searchInput" wire:key="search-input"
                                wire:keydown.enter="doSearch" @focus="focused = true"
                                @blur="setTimeout(() => focused = false, 200)" placeholder="名称/用户名/介绍/消息..."
                                class="w-full pl-12 pr-4 py-3.5 text-base border-0 bg-gray-200 dark:bg-gray-700
                                rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400
                                text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400
                                shadow-sm hover:bg-gray-100 dark:hover:bg-gray-600
                                transition-all duration-200">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <div x-show="focused && $wire.showSuggestions && $wire.suggestions.length" x-cloak
                                class="absolute left-0 right-0 z-50 mt-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700">
                                @foreach ($suggestions as $suggestion)
                                    <div wire:mousedown.prevent="selectSuggestion('{{ $suggestion }}'); $nextTick(() => doSearch())"
                                        class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-gray-900 dark:text-gray-100">
                                        {{ $suggestion }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <button wire:click="doSearch"
                            class="px-6 py-3.5 bg-indigo-600 text-white rounded-xl font-medium
                            hover:bg-indigo-700 active:bg-indigo-800
                            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                            dark:bg-indigo-500 dark:hover:bg-indigo-600 dark:active:bg-indigo-700
                            dark:focus:ring-offset-gray-800 shadow-sm
                            transform transition-all duration-200 hover:scale-105">
                            <div class="flex items-center justify-center space-x-2">
                                <span>搜索</span>
                            </div>
                        </button>
                    </div>

                    <!-- Type and Sort Toggles -->
                    <div
                        class="mt-4 flex flex-col sm:flex-row items-start sm:items-center justify-start sm:justify-center space-y-4 sm:space-y-0 sm:space-x-8">
                        <!-- Type Toggle -->
                        <div class="w-full sm:w-auto">
                            <div class="flex flex-wrap items-center bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                                <span
                                    class="m-1 px-3 py-1 text-sm text-gray-600 dark:text-gray-400 font-medium">类型:</span>
                                <button wire:click="$set('type', '')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $type === '' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    全部
                                </button>
                                <button wire:click="$set('type', 'bot')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $type === 'bot' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    机器人
                                </button>
                                <button wire:click="$set('type', 'channel')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $type === 'channel' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    频道
                                </button>
                                <button wire:click="$set('type', 'group')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $type === 'group' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    群组
                                </button>
                                <button wire:click="$set('type', 'person')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $type === 'person' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    个人
                                </button>
                                <button wire:click="$set('type', 'message')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $type === 'message' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    消息
                                </button>
                            </div>
                        </div>

                        <!-- Sort Toggle -->
                        <div class="w-full sm:w-auto">
                            <div class="flex flex-wrap items-center bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                                <span
                                    class="m-1 px-3 py-1 text-sm text-gray-600 dark:text-gray-400 font-medium">排序:</span>
                                <button wire:click="sortBy('')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $sortField === '' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    默认
                                </button>
                                <button wire:click="sortBy('member_or_view_count')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $sortField === 'member_or_view_count' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    成员数/查看数
                                    {{ $sortField === 'member_or_view_count' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Chats Grid -->
            @if ($unified_searches->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">没有找到相关记录</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if ($q || $type)
                            当前搜索条件：
                            @if ($q)
                                搜索词 "{{ $q }}"
                            @endif
                            @if ($type)
                                @if ($q)
                                    ,
                                @endif
                                类型 "{{ $type }}"
                            @endif
                        @else
                            没有应用任何搜索条件
                        @endif
                    </p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        请尝试使用其他搜索词或清除筛选条件。
                    </p>
                    <button wire:click="resetFilters"
                        class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        清除所有筛选条件
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($unified_searches as $unified_search)
                        <div
                            class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transform hover:-translate-y-1 transition-all duration-200">
                            <!-- 类型标签 - 调整定位到左上角 -->
                            <div class="absolute top-0 left-0">
                                <span
                                    class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-br-xl text-xs font-medium transition-colors duration-200
            {{ $unified_search->isBot() ? 'bg-purple-100 dark:bg-purple-900/70 text-purple-700 dark:text-purple-300' : '' }}
            {{ $unified_search->isChannel() ? 'bg-blue-100 dark:bg-blue-900/70 text-blue-700 dark:text-blue-300' : '' }}
            {{ $unified_search->isGroup() ? 'bg-green-100 dark:bg-green-900/70 text-green-700 dark:text-green-300' : '' }}
            {{ $unified_search->isPerson() ? 'bg-yellow-100 dark:bg-yellow-900/70 text-yellow-700 dark:text-yellow-300' : '' }}
            {{ $unified_search->isMessage() ? 'bg-red-100 dark:bg-red-900/70 text-red-700 dark:text-red-300' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        @if ($unified_search->isBot())
                                            <path
                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        @elseif($unified_search->isChannel())
                                            <path
                                                d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                                            <path
                                                d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                                        @elseif($unified_search->isGroup())
                                            <path
                                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        @elseif($unified_search->isPerson())
                                            <path fill-rule="evenodd"
                                                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                clip-rule="evenodd" />
                                        @elseif($unified_search->isMessage())
                                            <path fill-rule="evenodd"
                                                d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                                clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                    <span>{{ $unified_search->type_name }}</span>
                                </span>
                            </div>

                            <div class="p-6 pt-8">
                                <!-- 标题和描述 -->
                                <a href="{{ $unified_search->unified_searchable->route }}" wire:navigate
                                    class="block group/title space-y-3">
                                    <h3
                                        class="text-gray-900 dark:text-white group-hover/title:text-indigo-600 dark:group-hover/title:text-indigo-400 transition-colors duration-200">
                                        {{ $unified_search->title }}
                                    </h3>
                                </a>

                                <!-- 底部信息和操作按钮 -->
                                <div class="mt-3 flex items-center justify-between">
                                    <!-- 人数标签改为更低调的样式 -->
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ number_format($unified_search->unified_searchable->member_count ?? $unified_search->unified_searchable->view_count) }}
                                    </div>

                                    <!-- 保持原来的 Telegram 按钮文字 -->
                                    <a href="{{ $unified_search->unified_searchable->url }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:text-indigo-400 dark:bg-indigo-900/50 dark:hover:bg-indigo-800/50 transition-colors duration-200">
                                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.47-1.13 7.25-.14.74-.42 1-.68 1.02-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.54-.37-.62-.21-1.11-.32-1.07-.68.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z" />
                                        </svg>
                                        在 Telegram 查看{{ $unified_search->type_name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-12">
                {{ $unified_searches->links(data: ['scrollTo' => '#paginated-posts']) }}
            </div>
        </div>
    </div>
    <x-loading-indicator />
</div>
