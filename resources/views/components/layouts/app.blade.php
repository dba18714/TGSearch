<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Page Title' }}</title>

    <x-theme-script />

    @vite('resources/js/theme-init.js')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <wireui:scripts />
    <!-- TODO 用laravel推荐的方式安装这个包 -->
    <script src="//unpkg.com/alpinejs" defer></script>

</head>

<body>

    <x-header />

    {{ $slot }}

    <x-footer />

    <!-- <script>
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
    </script> -->
</body>

</html>