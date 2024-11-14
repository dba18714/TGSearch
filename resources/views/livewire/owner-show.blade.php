<div>
    <div class="bg-gray-100 dark:bg-gray-900 min-h-screen py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 左右布局容器 -->
            <div class="{{ $owner->messages ? 'flex flex-col lg:flex-row gap-6' : 'flex justify-center' }}">
                <!-- 左侧名片信息 -->
                <div class="lg:w-1/3 lg:min-w-[320px]">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden {{ $owner->messages ? 'lg:sticky lg:top-6' : '' }}">
                        <!-- 频道/群组头部信息 -->
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-24 h-24 bg-indigo-500 rounded-full flex items-center justify-center text-white text-3xl font-bold mb-4">
                                    {{ strtoupper(substr($owner->name, 0, 1)) }}
                                </div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $owner->name }}</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $owner->username }}</p>
                                <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
    {{ $owner->isBot() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
    {{ $owner->isChannel() ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
    {{ $owner->isGroup() ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
    {{ $owner->isPerson() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
    {{ $owner->isMessage() ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}">
                                    {{ $owner->type_name }}
                                </span>
                            </div>

                            @if($owner->introduction && $owner->introduction !== 'None')
                            <div class="mt-4 text-gray-600 dark:text-gray-300 text-sm">
                                {!! nl2br(e($owner->introduction)) !!}
                            </div>
                            @endif

                            <div class="mt-4 flex justify-center space-x-6">
                                <div class="text-center">
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($owner->member_count) }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">订阅者</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-semibold text-gray-900 dark:text-white">{{ number_format($owner->view_count) }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">次浏览</div>
                                </div>
                            </div>
                        </div>

                        <!-- 访问按钮 -->
                        <div class="p-6 bg-gray-50 dark:bg-gray-700">
                            <a href="{{ $owner->url }}"
                                target="_blank"
                                class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 
                                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                 dark:bg-indigo-500 dark:hover:bg-indigo-600
                                 transition-all duration-200">
                                <span>访问{{ $owner->type_name }}</span>
                                <svg class="ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                    <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 右侧消息内容 -->
                <div class="lg:w-2/3">
                    @if($owner->messages)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white text-lg font-bold">
                                        {{ strtoupper(substr($owner->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center mb-2">
                                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $owner->name }}</h2>
                                        <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $owner->created_at->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                    @foreach ($owner->messages as $message)
                                    <div class="prose dark:prose-invert max-w-none">
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{!! nl2br(e($message->text)) !!}</p>
                                    </div>
                                     <hr class="my-4 border-gray-200 dark:border-gray-700" />
                                    @endforeach
                                    <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="h-5 w-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ number_format($owner->view_count) }} 次浏览
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 相关推荐 -->
                    @if($relatedOwners->isNotEmpty())
                    <div class="mt-6 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">相关推荐</h2>
                        <div class="grid grid-cols-1 gap-6">
                            @foreach($relatedOwners as $relatedOwner)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                                <div class="p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white text-lg font-bold">
                                                    {{ strtoupper(substr($relatedOwner->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $relatedOwner->name }}</h3>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $relatedOwner->username }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
    {{ $relatedOwner->isBot() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
    {{ $relatedOwner->isChannel() ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
    {{ $relatedOwner->isGroup() ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
    {{ $relatedOwner->isPerson() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
    {{ $relatedOwner->isMessage() ? 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100' : '' }}">
                                            {{ $relatedOwner->type_name }}
                                        </span>
                                    </div>

                                    @if($relatedOwner->introduction && $relatedOwner->introduction !== 'None')
                                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $relatedOwner->introduction }}</p>
                                    @endif

                                    <div class="mt-6 flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                            <svg class="h-5 w-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ number_format($relatedOwner->member_count) }}
                                        </div>
                                        <a href="{{ route('link.show', $relatedOwner) }}"
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
        </div>
    </div>
    <x-loading-indicator />
</div>