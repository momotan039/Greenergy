/**
 * Generic Swiper Carousel Module
 * 
 * Auto-initializes Swiper instances on elements with .js-swiper-init class.
 * Configuration can be passed via data-swiper-config attribute.
 */
export function initCarousel() {
    const swiperContainers = document.querySelectorAll('.js-swiper-init');

    if (swiperContainers.length === 0) return;

    if (typeof Swiper === 'undefined') {
        console.warn('Swiper library is not loaded. Carousel cannot be initialized.');
        return;
    }

    swiperContainers.forEach(container => {
        // Default Configuration
        const defaultOptions = {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            observer: true,
            observeParents: true,
            pagination: {
                el: container.querySelector('.swiper-pagination') || container.parentElement.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: container.querySelector('.swiper-button-next') || container.parentElement.querySelector('.swiper-button-next'),
                prevEl: container.querySelector('.swiper-button-prev') || container.parentElement.querySelector('.swiper-button-prev'),
            },
        };

        // Custom Configuration from Attribute
        let customOptions = {};
        if (container.dataset.swiperConfig) {
            try {
                customOptions = JSON.parse(container.dataset.swiperConfig);
            } catch (e) {
                console.error('Invalid Swiper Config JSON for:', container, e);
            }
        }

        // Merge Options
        // Note: Simple spread merge might overwrite nested objects like autoplay/navigation if fully provided in custom.
        // We do a smart merge for common nested objects to respect defaults if partial config provided.
        const options = { ...defaultOptions, ...customOptions };

        if (customOptions.navigation) {
            options.navigation = { ...defaultOptions.navigation, ...customOptions.navigation };
        }
        if (customOptions.autoplay) {
            options.autoplay = { ...defaultOptions.autoplay, ...customOptions.autoplay };
        }
        if (customOptions.pagination) {
            options.pagination = { ...defaultOptions.pagination, ...customOptions.pagination };
        }

        // Initialize Swiper
        new Swiper(container, options);
    });
}
