/**
 * Theme Toggle Module
 *
 * Dark/Light mode toggle with localStorage persistence.
 *
 * @package Greenergy
 */

export function initThemeToggle() {
    const toggleButtons = document.querySelectorAll('[data-theme-toggle]');
    const html = document.documentElement;

    // Check for saved preference or system preference
    const savedTheme = localStorage.getItem('greenergy-theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    // Set initial theme
    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        html.classList.add('dark');
    }

    // Toggle theme
    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            html.classList.toggle('dark');

            const isDark = html.classList.contains('dark');
            localStorage.setItem('greenergy-theme', isDark ? 'dark' : 'light');
        });
    });

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('greenergy-theme')) {
            html.classList.toggle('dark', e.matches);
        }
    });
}
