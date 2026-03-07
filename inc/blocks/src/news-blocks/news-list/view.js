/**
 * News List Block Frontend Script
 */
document.addEventListener('DOMContentLoaded', () => {
    // Event delegation for pagination links
    document.body.addEventListener('click', (e) => {
        const link = e.target.closest('.js-ajax-pagination-link');
        
        if (!link) {
            return;
        }

        // Only handle links inside our block's pagination
        const paginationContainer = link.closest('.greenergy-block-pagination');
        if (!paginationContainer) {
            return;
        }

        e.preventDefault();

        const page = link.getAttribute('data-page');
        const queryArgs = paginationContainer.getAttribute('data-query-args');
        const blockContainer = paginationContainer.closest('.wp-block-greenergy-news-list');
        const newsContainer = blockContainer ? blockContainer.querySelector('.flex.flex-col.gap-4') : null;

        if (!page || !queryArgs) {
            return;
        }

        // Add loading state
        if (newsContainer) {
            newsContainer.style.opacity = '0.5';
            newsContainer.style.pointerEvents = 'none';
        }
        
        // Prepare FormData
        const formData = new FormData();
        formData.append('action', 'greenergy_load_posts');
        formData.append('nonce', greenergyData.nonce);
        formData.append('page', page);
        formData.append('query_args', queryArgs);

        // Fetch
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
                    
                    // Re-initialize any JS plugins (like AOS/Swiper) if needed, 
                    // though news list uses standard CSS mainly.
                    if (typeof AOS !== 'undefined') {
                        AOS.refresh();
                    }
                }

                // Update pagination
                paginationContainer.innerHTML = data.data.pagination;

                // Scroll to top of block
                const blockRect = blockContainer.getBoundingClientRect();
                const absoluteElementTop = blockRect.top + window.pageYOffset;
                const headerOffset = 100; // Adjust for sticky header
                window.scrollTo({
                    top: absoluteElementTop - headerOffset,
                    behavior: 'smooth'
                });

            } else {
                console.error('Error loading posts:', data);
                if (newsContainer) {
                    newsContainer.style.opacity = '1';
                    newsContainer.style.pointerEvents = 'auto';
                }
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            if (newsContainer) {
                newsContainer.style.opacity = '1';
                newsContainer.style.pointerEvents = 'auto';
            }
        });
    });
});
