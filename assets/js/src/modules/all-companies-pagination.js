/**
 * All-companies block: AJAX pagination without full page reload.
 * Binds to .js-all-companies-page and fetches grid + pagination via greenergy_companies_page.
 */
export function initAllCompaniesPagination() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-all-companies-page');
        if (!btn) return;
        e.preventDefault();
        const block = btn.closest('.js-all-companies-block');
        if (!block) return;
        const page = parseInt(btn.getAttribute('data-page'), 10);
        if (!page || page < 1) return;

        const perPage = parseInt(block.getAttribute('data-per-page'), 10) || 9;
        const cat = block.getAttribute('data-cat') || '';
        const country = block.getAttribute('data-country') || '';
        const sort = block.getAttribute('data-sort') || 'latest';
        const sCompany = block.getAttribute('data-s-company') || '';
        const featuredIds = (block.getAttribute('data-featured-ids') || '').trim();

        const grid = block.querySelector('.js-all-companies-grid');
        const gridContainer = block.querySelector('.js-ajax-grid');
        const paginationWrap = block.querySelector('.greenergy-all-companies-pagination');

        if (!grid || typeof greenergyData === 'undefined' || !greenergyData.ajaxUrl || !greenergyData.nonce) return;

        if (gridContainer) {
            gridContainer.classList.add('is-loading', 'pointer-events-none');
        }

        const formData = new FormData();
        formData.append('action', 'greenergy_companies_page');
        formData.append('nonce', greenergyData.nonce);
        formData.append('page', String(page));
        formData.append('per_page', String(perPage));
        formData.append('cat', cat);
        formData.append('country', country);
        formData.append('sort', sort);
        formData.append('s_company', sCompany);
        formData.append('featured_ids', featuredIds);

        fetch(greenergyData.ajaxUrl, {
            method: 'POST',
            body: formData,
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success && data.data) {
                    if (data.data.content !== undefined) {
                        grid.innerHTML = data.data.content;
                    }
                    if (paginationWrap && data.data.pagination !== undefined) {
                        paginationWrap.outerHTML = data.data.pagination;
                    }
                }
            })
            .catch(function () {
                if (gridContainer) {
                    gridContainer.classList.remove('is-loading', 'pointer-events-none');
                }
            })
            .finally(function () {
                setTimeout(function () {
                    if (gridContainer) {
                        gridContainer.classList.remove('is-loading', 'pointer-events-none');
                    }
                }, 400);
            });
    });
}
