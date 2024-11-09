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
    {{ $slot }}
</body>

</html>