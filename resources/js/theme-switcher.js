document.addEventListener('livewire:navigated', function() {
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

    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addListener(function(e) {
            if (localStorage.getItem('theme') === 'system') {
                setTheme('system');
                updateThemeIcon();
            }
        });
    }

    updateThemeIcon();
    updateActiveTheme(localStorage.getItem('theme') || 'system');

    document.addEventListener('click', function(event) {
        if (!themeToggle.contains(event.target) && !themeMenu.contains(event.target)) {
            themeMenu.classList.add('hidden');
        }
    });
});