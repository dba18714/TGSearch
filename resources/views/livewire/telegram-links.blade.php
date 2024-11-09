<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-12 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Dark Mode Toggle -->
        <div class="absolute top-4 right-4">
            <div class="relative">
                <button
                    id="themeToggle"
                    class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>
                <div id="themeMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="themeToggle">
                        <button class="theme-option block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left" role="menuitem" data-theme="light">明亮模式</button>
                        <button class="theme-option block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left" role="menuitem" data-theme="dark">暗黑模式</button>
                        <button class="theme-option block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left" role="menuitem" data-theme="system">跟随系统</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Section -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white">Telegram 资源导航</h1>
            <p class="mt-3 text-xl text-gray-600 dark:text-gray-300">发现优质的 Telegram 频道、群组、机器人和个人</p>
        </div>

        <!-- Search Section -->
        <div class="max-w-3xl mx-auto mb-12">
            <div class="bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-md">
                <div class="flex items-center">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-0 px-3">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            <input
                                wire:model.live.debounce.300ms="search"
                                type="text"
                                placeholder="搜索名称或用户名..."
                                class="block w-full border-0 focus:ring-0 px-3 py-3 text-base placeholder-gray-500 text-gray-900 dark:text-white dark:placeholder-gray-400 bg-transparent">
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="h-8 w-px bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Type Select -->
                    <div class="px-3">
                        <select
                            wire:model.live="type"
                            class="block w-full border-0 bg-transparent pr-1 py-3 text-base text-gray-900 dark:text-white focus:ring-0">
                            <option value="">所有类型</option>
                            <option value="bot">机器人</option>
                            <option value="channel">频道</option>
                            <option value="group">群组</option>
                            <option value="person">个人</option>
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="pl-2">
                        <button
                            wire:click="search"
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                            搜索
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sort Options -->
            <div class="flex items-center justify-center space-x-2 mt-6">
                <button
                    wire:click="sortBy('name')"
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200
                        {{ $sortField === 'name' 
                            ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-100' 
                            : 'bg-white text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    <span>按名称</span>
                    @if($sortField === 'name')
                    <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                    </svg>
                    @endif
                </button>
                <button
                    wire:click="sortBy('member_count')"
                    class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium transition-colors duration-200
                        {{ $sortField === 'member_count' 
                            ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-700 dark:text-indigo-100' 
                            : 'bg-white text-gray-500 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    <span>按成员数</span>
                    @if($sortField === 'member_count')
                    <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}" />
                    </svg>
                    @endif
                </button>
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
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">@{{ $link->telegram_username }}</p>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const themeMenu = document.getElementById('themeMenu');
        const html = document.documentElement;
        const themeOptions = document.querySelectorAll('.theme-option');

        function setTheme(theme) {
            if (theme === 'dark') {
                html.classList.add('dark');
            } else if (theme === 'light') {
                html.classList.remove('dark');
            } else if (theme === 'system') {
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    html.classList.add('dark');
                } else {
                    html.classList.remove('dark');
                }
            }
            localStorage.setItem('theme', theme);
        }

        // 更新图标函数
        function updateThemeIcon() {
            const isDark = html.classList.contains('dark');
            themeToggle.innerHTML = isDark ?
                `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                   </svg>` :
                `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                   </svg>`;
        }

        themeToggle.addEventListener('click', function() {
            themeMenu.classList.toggle('hidden');
        });

        themeOptions.forEach(option => {
            option.addEventListener('click', function() {
                const theme = this.dataset.theme;
                setTheme(theme);
                updateThemeIcon();
                themeMenu.classList.add('hidden');
            });
        });

        // 监听系统主题变化
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addListener(function(e) {
                if (localStorage.getItem('theme') === 'system') {
                    setTheme('system');
                    updateThemeIcon();
                }
            });
        }

        // 初始化时更新图标
        updateThemeIcon();

        // 点击其他地方关闭菜单
        document.addEventListener('click', function(event) {
            if (!themeToggle.contains(event.target) && !themeMenu.contains(event.target)) {
                themeMenu.classList.add('hidden');
            }
        });
    });
</script>