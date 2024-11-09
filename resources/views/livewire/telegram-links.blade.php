<div x-data="{ open: false }" class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Telegram 资源导航</h1>
            <p class="mt-3 text-xl text-gray-600 dark:text-gray-300">发现优质的 Telegram 频道、群组、机器人和个人</p>
        </div>

        <!-- Search and Sort Section -->
        <div class="max-w-3xl mx-auto mb-12">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-md">
                <!-- Search Input with Type Select and Search Button -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            placeholder="搜索名称或用户名..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>

                    <!-- Type Select and Search Button -->
                    <div class="flex gap-2">
                        <select
                            wire:model.live="type"
                            class="pl-2 pr-8 py-2 border border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 text-gray-500 sm:text-sm rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">全部类型</option>
                            <option value="bot">机器人</option>
                            <option value="channel">频道</option>
                            <option value="group">群组</option>
                            <option value="person">个人</option>
                        </select>
                        <button
                            wire:click="search"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            搜索
                        </button>
                    </div>
                </div>

                <!-- Sort Toggle -->
                <div class="mt-4 flex items-center justify-center space-x-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">排序:</span>
                    <div class="flex items-center bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                        <button
                            wire:click="sortBy('name')"
                            class="px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $sortField === 'name' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                            名称 {{ $sortField === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                        <button
                            wire:click="sortBy('member_count')"
                            class="px-3 py-1 text-sm rounded-md transition-colors duration-200 focus:outline-none {{ $sortField === 'member_count' ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">
                            成员数 {{ $sortField === 'member_count' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Links Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($links as $link)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-all duration-200">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $link->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $link->telegram_username }}</p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $link->isBot() ? 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100' : '' }}
                            {{ $link->isChannel() ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100' : '' }}
                            {{ $link->isGroup() ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : '' }}
                            {{ $link->isPerson() ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100' : '' }}
                        ">
                            {{ ucfirst($link->type) }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ $link->introduction }}</p>

                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                            <svg class="h-5 w-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            {{ number_format($link->member_count) }}
                        </div>
                        <a
                            href="{{ $link->url }}"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:text-indigo-400 dark:bg-indigo-900 dark:hover:bg-indigo-800 transition-colors duration-200">
                            访问链接
                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $links->links() }}
        </div>
    </div>
</div>