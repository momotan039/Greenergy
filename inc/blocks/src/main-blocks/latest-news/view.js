/**
 * Latest News Block Frontend Script
 */
document.addEventListener('DOMContentLoaded', () => {
    const filterContainers = document.querySelectorAll('.js-latest-news-filters');
    
    filterContainers.forEach(container => {
        const buttons = container.querySelectorAll('button');
        const blockSection = container.closest('section');
        const newsContainer = blockSection.querySelector('.js-latest-news-container');
        const viewAllBtn = blockSection.querySelector('.js-latest-news-view-all');
        let sliderInstance = null;

        // Find existing swiper instance
        const sliderElement = blockSection.querySelector('.js-latest-news-slider');
        if (sliderElement && sliderElement.swiper) {
            sliderInstance = sliderElement.swiper;
        }

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const category = button.getAttribute('data-category');
                const termId = button.getAttribute('data-term-id');

                // Update active state
                buttons.forEach(btn => {
                    btn.classList.remove('bg-[#229924]', 'text-white', 'shadow-md', 'active');
                    btn.classList.add('bg-[#EFF2F5]', 'text-gray-600');
                });
                button.classList.add('bg-[#229924]', 'text-white', 'shadow-md', 'active');
                button.classList.remove('bg-[#EFF2F5]', 'text-gray-600');

                // Loading state
                if (newsContainer) {
                    newsContainer.style.opacity = '0.5';
                    newsContainer.style.pointerEvents = 'none';
                }

                // AJAX Fetch
                const formData = new FormData();
                formData.append('action', 'greenergy_filter_latest_news');
                formData.append('nonce', greenergyData.nonce);
                formData.append('category', category);
                formData.append('term_id', termId || 0);

                fetch(greenergyData.ajaxUrl, {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update content
                        if (newsContainer) {
                            newsContainer.innerHTML = data.data.content;
                            newsContainer.style.opacity = '1';
                            newsContainer.style.pointerEvents = 'auto';

                            // Re-init swiper if destroyed or just update
                            if (sliderInstance) {
                                sliderInstance.update();
                                sliderInstance.slideTo(0);
                            }

                            // Re-trigger AOS for new elements
                            if (typeof AOS !== 'undefined') {
                                AOS.refresh();
                            }
                        }

                        // Update View All link
                        if (viewAllBtn && data.data.view_all_url) {
                            viewAllBtn.setAttribute('href', data.data.view_all_url);
                        }
                    }
                })
                .catch(error => {
                    console.error('Filter error:', error);
                    if (newsContainer) {
                        newsContainer.style.opacity = '1';
                        newsContainer.style.pointerEvents = 'auto';
                    }
                });
            });
        });
    });
});
