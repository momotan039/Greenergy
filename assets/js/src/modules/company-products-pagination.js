/**
 * Company products block: AJAX pagination without full page reload.
 * Binds to .js-company-products-page and fetches grid + pagination via greenergy_company_products_page.
 */
export function initCompanyProductsPagination() {
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-company-products-page');
        if (!btn) return;
        e.preventDefault();
        const block = btn.closest('.js-company-products-block');
        if (!block) return;
        const page = parseInt(btn.getAttribute('data-page'), 10);
        if (!page || page < 1) return;

        const productIds = (block.getAttribute('data-product-ids') || '').trim();
        const blockId = block.getAttribute('data-block-id') || '';
        const perPage = parseInt(block.getAttribute('data-per-page'), 10) || 8;

        const grid = block.querySelector('.js-company-products-grid');
        const loader = block.querySelector('.js-company-products-loader');
        const paginationWrap = block.querySelector('.greenergy-company-products-pagination');

        if (!grid || typeof greenergyData === 'undefined' || !greenergyData.ajaxUrl || !greenergyData.nonce) return;

        if (loader) {
            loader.classList.remove('opacity-0', 'pointer-events-none');
            loader.classList.add('opacity-100', 'pointer-events-auto');
        }

        const formData = new FormData();
        formData.append('action', 'greenergy_company_products_page');
        formData.append('nonce', greenergyData.nonce);
        formData.append('product_ids', productIds);
        formData.append('page', String(page));
        formData.append('per_page', String(perPage));
        formData.append('block_id', blockId);

        fetch(greenergyData.ajaxUrl, {
            method: 'POST',
            body: formData,
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (loader) {
                    loader.classList.add('opacity-0', 'pointer-events-none');
                    loader.classList.remove('opacity-100', 'pointer-events-auto');
                }
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
                if (loader) {
                    loader.classList.add('opacity-0', 'pointer-events-none');
                    loader.classList.remove('opacity-100', 'pointer-events-auto');
                }
            });
    });
}
