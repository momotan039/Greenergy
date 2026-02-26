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
import { initCarousel } from './modules/carousel.js';
import { initAjaxPagination } from './modules/ajax-pagination.js';
import { initHubNav } from './modules/hub-nav.js';

// Initialize all modules
function initAllModules() {
    initNavigation();
    initLazyLoad();
    initThemeToggle();
    initCounter();
    initCarousel();
    initAjaxPagination();
    initHubNav();

    // Initialize/Refresh AOS
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 50,
        });
        AOS.refresh();
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initAllModules);

// Re-initialize on AJAX content updates
document.addEventListener('greenergy:content-updated', initAllModules);

// Export for debugging
window.Greenergy = {
    version: '1.0.0',
};
