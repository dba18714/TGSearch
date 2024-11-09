<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    <script>
        // 在页面加载前执行
        (function() {
            function getInitialTheme() {
                const savedTheme = localStorage.getItem('theme');
                if (savedTheme) {
                    return savedTheme;
                }
                // 如果没有保存的主题，默认使用系统主题
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }

            const theme = getInitialTheme();

            // 立即应用主题
            if (theme === 'dark' ||
                (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite('resources/css/app.css')

    <wireui:scripts />
    <script src="//unpkg.com/alpinejs" defer></script>

</head>

<body>
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
                    <button class="theme-option flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" data-theme="light">
                        <span>明亮模式</span>
                        <svg class="check-icon hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button class="theme-option flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" data-theme="dark">
                        <span>暗黑模式</span>
                        <svg class="check-icon hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button class="theme-option flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" role="menuitem" data-theme="system">
                        <span>跟随系统</span>
                        <svg class="check-icon hidden w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{ $slot }}

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
                updateActiveTheme(theme);
            }

            // 更新当前选中的主题指示
            function updateActiveTheme(currentTheme) {
            themeOptions.forEach(option => {
                    const checkIcon = option.querySelector('.check-icon');
                    if (option.dataset.theme === currentTheme) {
                        option.classList.add('bg-gray-100', 'dark:bg-gray-700');
                        checkIcon.classList.remove('hidden');
                    } else {
                        option.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                        checkIcon.classList.add('hidden');
                    }
            });
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

            // 初始化时更新图标和当前选中主题
            updateThemeIcon();
            updateActiveTheme(localStorage.getItem('theme') || 'system');

            // 点击其他地方关闭菜单
            document.addEventListener('click', function(event) {
                if (!themeToggle.contains(event.target) && !themeMenu.contains(event.target)) {
                    themeMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>