/**
 * Navigation Module
 *
 * Handles mobile menu, dropdowns, sticky header.
 *
 * @package Greenergy
 */

export function initNavigation() {
    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenuClose = document.querySelector('[data-mobile-menu-close]');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuOverlay = document.querySelector('[data-mobile-menu-overlay]');

    if (!mobileMenu || !mobileMenuToggle) return;

    // Open mobile menu
    mobileMenuToggle.addEventListener('click', () => {
        mobileMenu.classList.add('is-open');
        mobileMenuOverlay?.classList.add('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'true');
        mobileMenu.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    });

    // Close mobile menu
    const closeMobileMenu = () => {
        mobileMenu.classList.remove('is-open');
        mobileMenuOverlay?.classList.remove('is-open');
        mobileMenuToggle.setAttribute('aria-expanded', 'false');
        mobileMenu.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    };

    mobileMenuClose?.addEventListener('click', closeMobileMenu);
    mobileMenuOverlay?.addEventListener('click', closeMobileMenu);

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
            closeMobileMenu();
        }
    });

    // Sticky header behavior
    initStickyHeader();
}

function initStickyHeader() {
    const header = document.querySelector('.site-header.sticky');
    if (!header) return;

    let lastScroll = 0;
    const scrollThreshold = 100;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll <= 0) {
            header.classList.remove('header-hidden');
            return;
        }

        if (currentScroll > lastScroll && currentScroll > scrollThreshold) {
            // Scrolling down - hide header
            header.classList.add('header-hidden');
        } else if (currentScroll < lastScroll) {
            // Scrolling up - show header
            header.classList.remove('header-hidden');
        }

        lastScroll = currentScroll;
    }, { passive: true });
}
