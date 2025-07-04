<div>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- // TODO 完善拦截不宜展示的内容 --}}
            @if (!$chat->audit_passed)
                <h3
                    class="text-gray-900 dark:text-white group-hover/title:text-indigo-600 dark:group-hover/title:text-indigo-400 transition-colors duration-200">
                    <div class="text-red-500 dark:text-red-400">
                        该内容不宜在网页版展示
                    </div>
                </h3>
                <br>
                <div
                    class="text-gray-900 dark:text-white group-hover/title:text-indigo-600 dark:group-hover/title:text-indigo-400 transition-colors duration-200">
                    请使用[
                    <a href="https://t.me/yisou123bot" class="text-blue-600 dark:text-blue-400 underline">易搜机器人
                        @yisou123bot</a>
                    ]搜索展示完整结果
                </div>
            @else
                <!-- 左右布局容器 -->
                <div class="{{ $chat->messages ? 'flex flex-col lg:flex-row gap-6' : 'flex justify-center' }}">
                    <!-- 左侧名片信息 -->
                    <div class="lg:w-1/3 lg:min-w-[320px]">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden {{ $chat->messages ? 'lg:sticky lg:top-6' : '' }}">
                            <!-- 频道/群组头部信息 -->
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col items-center text-center">
                                    <div
                                        class="w-24 h-24 bg-indigo-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mb-4">
                                        {{ strtoupper(substr($chat->name, 0, 1)) }}
                                    </div>
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $chat->name }}
                                    </h1>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $chat->username }}</p>
                                    <span
                                        class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
    {{ $chat->isBot() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
    {{ $chat->isChannel() ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
    {{ $chat->isGroup() ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
    {{ $chat->isPerson() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
     ">
                                        {{ $chat->type_name }}
                                    </span>
                                </div>

                                @if ($chat->introduction && $chat->introduction !== 'None')
                                    <div class="mt-4 text-gray-600 dark:text-gray-300 text-sm">
                                        {!! nl2br(e($chat->introduction)) !!}
                                    </div>
                                @endif

                                @if (!$chat->isPerson())
                                    <div class="mt-4 flex justify-center space-x-6">
                                        <div class="text-center">
                                            <div class="text-xl font-semibold text-gray-900 dark:text-white">
                                                {{ number_format($chat->member_count) }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $chat->isBot() ? '使用者' : '' }}
                                                {{ $chat->isChannel() ? '订阅者' : '' }}
                                                {{ $chat->isGroup() ? '成员' : '' }}
                                            </div>
                                        </div>
                                        {{-- <div class="text-center">
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($chat->view_count) }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">次浏览</div>
                                </div> --}}
                                    </div>
                                @endif
                            </div>

                            <!-- 访问按钮 -->
                            <div class="p-6 bg-gray-50 dark:bg-gray-700">
                                <a href="{{ $chat->url }}" target="_blank"
                                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 
                                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                 dark:bg-indigo-500 dark:hover:bg-indigo-600
                                 transition-all duration-200">
                                    <span>访问{{ $chat->type_name }}</span>
                                    <svg class="ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path
                                            d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                        <path
                                            d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                    </svg>
                                </a>
                            </div>
                            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center space-x-2">
                                        <span>今日展示: {{ $todayImpressions }}次</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span>最近7日: {{ $weekImpressions }}次</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 右侧消息列表 -->
                    <div class="lg:w-2/3">
                        @foreach ($messages as $item)
                            <div class="mb-3 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                                <div class="p-6">
                                    <div class="flex">
                                        <div class="flex-shrink-0 mr-4">
                                            <div
                                                class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white text-lg font-bold">
                                                {{ strtoupper(substr($chat->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex items-center mb-2">
                                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ $chat->name }}</h2>
                                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $chat->created_at->format('Y-m-d H:i') }}
                                                </span>
                                            </div>

                                            <div class="prose dark:prose-invert max-w-none">
                                                <p class="text-gray-700 dark:text-gray-300">
                                                    {!! nl2br(e($item->text)) !!}
                                                </p>
                                            </div>

                                            <div
                                                class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                <svg class="h-5 w-5 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ number_format($item->view_count) }} 次浏览
                                                <a class="ml-2" href="{{ $item->url }}" target="_blank">查看原文</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{ $messages->links() }}
                        @if ($message->exists && $chat->messages->count() > 1)
                            <div class="text-center mt-6">
                                <a href="{{ route('chat.show', $chat) }}"
                                    class="text-indigo-600 dark:text-indigo-500">查看所有消息</a>
                            </div>
                        @endif

                        <!-- 相关推荐 -->
                        @if ($relatedSearches->isNotEmpty())
                            <div class="mt-6 p-6">
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">相关推荐</h2>
                                <div class="grid grid-cols-1 gap-6">
                                    @foreach ($relatedSearches as $relatedSearch)
                                        <div
                                            class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                                            <div class="p-6">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0">
                                                            <div
                                                                class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white text-lg font-bold">
                                                                {{ strtoupper(substr($relatedSearch->title, 0, 1)) }}
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <h3
                                                                class="text-lg font-semibold text-gray-900 dark:text-white">
                                                                {{ $relatedSearch->getTitle(200) }}
                                                            </h3>
                                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $relatedSearch->username }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
    {{ $relatedSearch->isBot() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
    {{ $relatedSearch->isChannel() ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
    {{ $relatedSearch->isGroup() ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
    {{ $relatedSearch->isPerson() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
    {{ $relatedSearch->isMessage() ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}
    ">
                                                        {{ $relatedSearch->type_name }}
                                                    </span>
                                                </div>

                                                @if ($relatedSearch->introduction && $relatedSearch->introduction !== 'None')
                                                    <p
                                                        class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                                        {{ $relatedSearch->introduction }}</p>
                                                @endif

                                                <div class="mt-6 flex items-center justify-between">
                                                    <div
                                                        class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                        <svg class="h-5 w-5 mr-1.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        {{ number_format($relatedSearch->member_count) }}
                                                    </div>
                                                    <a href="{{ $relatedSearch->unified_searchable->route }}"
                                                        wire:navigate
                                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 
                                                  dark:text-indigo-400 dark:bg-indigo-900 dark:hover:bg-indigo-800 
                                                  transition-colors duration-200">
                                                        查看详情
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
    <x-loading-indicator />
</div>
