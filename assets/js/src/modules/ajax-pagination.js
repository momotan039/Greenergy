/**
 * Generic AJAX Pagination Module
 * 
 * Handles pagination for grids via AJAX.
 */

export function initAjaxPagination() {
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.js-ajax-pagination-link');
        if (!link) return;

        e.preventDefault();

        const container = link.closest('.js-ajax-grid');
        if (!container) return;

        const contentContainer = container.querySelector('.js-ajax-grid-content');
        const paginationContainer = container.querySelector('.pagination'); // Or wrapper
        const page = link.dataset.page;
        const queryArgs = container.dataset.queryArgs;
        const templatePart = container.dataset.templatePart || '';

        if (!page || !queryArgs) return;

        // Add Loading State
        container.classList.add('opacity-50', 'pointer-events-none');

        // Prepare Data
        const formData = new FormData();
        formData.append('action', 'greenergy_load_posts');
        formData.append('nonce', greenergyData.nonce);
        formData.append('page', page);
        formData.append('query_args', queryArgs);
        if (templatePart) {
            formData.append('template_part', templatePart);
        }

        // Fetch
        fetch(greenergyData.ajaxUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update Content
                    if (contentContainer) {
                        contentContainer.innerHTML = data.data.content;
                    }

                    // Update Pagination
                    // Use a dedicated wrapper to replace content, avoiding duplication
                    const paginationWrapper = container.querySelector('.js-ajax-pagination-wrapper') || 
                                              container.querySelector('.pagination') || 
                                              container.querySelector('.greenergy-pagination');
                                              
                    if (paginationWrapper) {
                        // If it's a wrapper div, update innerHTML. If it's the nav itself, update outerHTML.
                        if (paginationWrapper.classList.contains('js-ajax-pagination-wrapper')) {
                            paginationWrapper.innerHTML = data.data.pagination;
                        } else {
                            paginationWrapper.outerHTML = data.data.pagination;
                        }
                    } else {
                        // Fallback: append if not found (should be avoided by proper markup)
                        container.insertAdjacentHTML('beforeend', data.data.pagination);
                    }

                    // Re-initialize animations if needed (AOS)
                    if (typeof AOS !== 'undefined') {
                        setTimeout(() => {
                            AOS.refreshHard();
                        }, 100);
                    }

                    // Scroll to top of grid
                    const yOffset = -100; // Header offset
                    const y = container.getBoundingClientRect().top + window.pageYOffset + yOffset;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                } else {
                    console.error('AJAX Error:', data);
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
            })
            .finally(() => {
                container.classList.remove('opacity-50', 'pointer-events-none');
            });
    });
}
