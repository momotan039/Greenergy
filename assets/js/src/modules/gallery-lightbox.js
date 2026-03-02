/**
 * Company Gallery Lightbox
 * Opens a Swiper lightbox when clicking gallery images; initializes lightbox Swiper.
 */
export function initGalleryLightbox() {
    const triggers = document.querySelectorAll('.js-gallery-lightbox-trigger');
    if (triggers.length === 0) return;

    triggers.forEach((trigger) => {
        if (trigger.dataset.galleryLightboxBound) return;
        trigger.dataset.galleryLightboxBound = '1';

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            const galleryId = this.dataset.galleryId;
            const index = parseInt(this.dataset.index, 10) || 0;
            const lightbox = document.querySelector(`.greenergy-gallery-lightbox[data-gallery-id="${galleryId}"]`);
            if (!lightbox) return;

            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            document.body.style.overflow = 'hidden';

            const swiperEl = lightbox.querySelector('.js-gallery-lightbox-swiper');
            const nextBtn = lightbox.querySelector('.greenergy-lightbox-next');
            const prevBtn = lightbox.querySelector('.greenergy-lightbox-prev');
            if (swiperEl && typeof Swiper !== 'undefined') {
                if (!swiperEl.swiper) {
                    new Swiper(swiperEl, {
                        slidesPerView: 1,
                        initialSlide: index,
                        centeredSlides: true,
                        navigation: {
                            nextEl: nextBtn,
                            prevEl: prevBtn,
                        },
                    });
                } else {
                    swiperEl.swiper.slideTo(index);
                }
            }

            const closeBtn = lightbox.querySelector('.greenergy-lightbox-close');
            const close = () => {
                lightbox.classList.add('hidden');
                lightbox.classList.remove('flex');
                document.body.style.overflow = '';
            };

            if (closeBtn) closeBtn.onclick = close;
            lightbox.onclick = (ev) => { if (ev.target === lightbox && !ev.target.closest('.swiper')) close(); };
            document.addEventListener('keydown', function keydown(e) {
                if (e.key === 'Escape') {
                    close();
                    document.removeEventListener('keydown', keydown);
                }
            });
        });
    });
}
