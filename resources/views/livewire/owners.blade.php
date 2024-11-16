<div>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 pt-8 pb-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header Section -->
            <div class="mb-8 text-center sm:hidden">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Telegram 资源导航</h1>
            </div>

            <!-- Search and Sort Section -->
            <div class="max-w-3xl mx-auto mb-8" id="paginated-posts">
                <div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-2xl shadow-md">
                    <!-- Search Input -->
                    <div class="relative flex items-center space-x-2">
                        <div class="relative flex-1">
                            <input wire:model="search" wire:key="search-input-{{ $search }}"
                                wire:keydown.enter="doSearch" type="search" placeholder="名称/用户名/介绍/消息..."
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
                                {{-- <button
                                    wire:click="$set('type', 'message')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $type === 'message' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    消息
                                </button> --}}
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
                                <button wire:click="sortBy('id')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $sortField === 'id' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    收录时间 {{ $sortField === 'id' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                                </button>
                                <button wire:click="sortBy('member_count')"
                                    class="m-1 px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $sortField === 'member_count' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                                    成员数
                                    {{ $sortField === 'member_count' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Owners Grid -->
            @if ($owners->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">没有找到相关记录</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if ($search || $type)
                            当前搜索条件：
                            @if ($search)
                                搜索词 "{{ $search }}"
                            @endif
                            @if ($type)
                                @if ($search)
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
                    @foreach ($owners as $owner)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            @if (isset($owner->_formatted['name']))
                                                {!! $owner->_formatted['name'] !!}
                                            @else
                                                {{ $owner->name }}
                                            @endif
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $owner->username }}
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium flex-shrink-0 whitespace-nowrap
{{ $owner->isBot() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
{{ $owner->isChannel() ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
{{ $owner->isGroup() ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
{{ $owner->isPerson() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
{{ $owner->isUnknown() ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}
">
                                        {{ $owner->type_name }}
                                    </span>
                                </div>

                                <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                    {{ $owner->introduction }}</p>

                                <!-- 添加匹配的消息显示 -->
                                @if (!empty($search) && isset($owner->matched_messages))
                                    <div class="mt-4 space-y-2">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">匹配的消息:</h4>
                                        @foreach ($owner->matched_messages as $message)
                                            <div
                                                class="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 p-2 rounded">
                                                <a href="{{ $message->route }}"
                                                    class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                    {{ Str::limit($message->text, 100) }}
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mt-6 flex items-center justify-between">
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="h-5 w-5 mr-1.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        {{ number_format($owner->member_count) }}
                                    </div>
                                    <a href="{{ $owner->url }}" target="_blank"
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:text-indigo-400 dark:bg-indigo-900 dark:hover:bg-indigo-800 transition-colors duration-200">
                                        访问链接
                                        <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path
                                                d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                            <path
                                                d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                        </svg>
                                    </a>
                                    <a href="{{ $owner->route }}" wire:navigate
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:text-indigo-400 dark:bg-indigo-900 dark:hover:bg-indigo-800 transition-colors duration-200">
                                        详情 >>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-12">
                {{ $owners->links(data: ['scrollTo' => '#paginated-posts']) }}
            </div>
        </div>
    </div>
    <x-loading-indicator />
</div>
