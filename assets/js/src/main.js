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

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initLazyLoad();
    initThemeToggle();
});

// Export for debugging
window.Greenergy = {
    version: '1.0.0',
};
