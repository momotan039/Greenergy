/**
 * Greenergy Theme - Main JavaScript Entry
 *
 * ES6 modules bundled with esbuild.
 * Deferred loading for performance.
 *
 * @package Greenergy
 */

// Import modules
import { initNavigation } from './modules/navigation.js';
import { initLazyLoad } from './modules/lazyload.js';
import { initThemeToggle } from './modules/theme-toggle.js';
import { initCounter } from './modules/counter.js';

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initLazyLoad();
    initThemeToggle();
    initCounter();

    // Initialize AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 50,
        });
    }
});

// Export for debugging
window.Greenergy = {
    version: '1.0.0',
};
