/**
 * Lazy Load Module
 *
 * Enhances native lazy loading with fade-in effects.
 *
 * @package Greenergy
 */

export function initLazyLoad() {
    // Native lazy loading is used via HTML attributes
    // This adds visual enhancement

    const lazyImages = document.querySelectorAll('img[loading="lazy"]');

    if ('loading' in HTMLImageElement.prototype) {
        // Browser supports native lazy loading
        lazyImages.forEach(img => {
            img.classList.add('lazy-fade');

            if (img.complete) {
                img.classList.add('loaded');
            } else {
                img.addEventListener('load', () => {
                    img.classList.add('loaded');
                }, { once: true });
            }
        });
    } else {
        // Fallback for older browsers - use IntersectionObserver
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            });
        });

        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }
}
