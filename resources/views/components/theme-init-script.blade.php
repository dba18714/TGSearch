<script>
    // 立即执行，在页面加载前设置主题
    (function() {
        function getThemePreference() {
            if (localStorage.theme === 'system') {
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            return localStorage.theme === 'dark' ? 'dark' : 'light';
        }

        // 在页面加载前应用主题
        if (getThemePreference() === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    })();
</script>

<style>
    /* 预设图标尺寸，防止加载时的尺寸跳动 */
    #themeToggle svg {
        width: 1.25rem;
        /* w-5 = 1.25rem */
        height: 1.25rem;
        /* h-5 = 1.25rem */
    }
</style>
