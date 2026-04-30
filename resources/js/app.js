import './bootstrap';

// ===== Theme Manager =====
document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const htmlEl = document.documentElement;
    const bodyEl = document.body;
    const isDark = true; // Por padrao o Stitch gerou pro dark

    const applyTheme = (dark) => {
        if (dark) {
            htmlEl.classList.add('dark');
            if (themeToggleBtn) {
                themeToggleBtn.textContent = 'light_mode';
            }
        } else {
            htmlEl.classList.remove('dark');
            if (themeToggleBtn) {
                themeToggleBtn.textContent = 'dark_mode';
            }
        }
    };

    // Initialize based on saved preference or system mode
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        applyTheme(true);
    } else if (savedTheme === 'light') {
        applyTheme(false);
    } else {
        applyTheme(isDark); // fall back to generated default
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const isDarkMode = htmlEl.classList.contains('dark');
            applyTheme(!isDarkMode);
            localStorage.setItem('theme', !isDarkMode ? 'dark' : 'light');
        });
    }
});
