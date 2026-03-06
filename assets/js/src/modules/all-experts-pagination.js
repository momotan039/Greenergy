/**
 * All-experts block: filters and pagination without full page refresh.
 * Reads location, s_exp from the block's filter form and fetches grid + pagination via greenergy_experts_page.
 */
export function initAllExpertsPagination() {
    function getFormForBlock(block) {
        const blockId = block.id || block.getAttribute('data-block-id');
        if (blockId) {
            const form = document.querySelector('form.js-all-experts-filter-form[data-block-id="' + blockId + '"]');
            if (form) return form;
        }
        return block.closest('main')?.querySelector('form.js-all-experts-filter-form') || document.querySelector('form.js-all-experts-filter-form');
    }

    function loadPage(block, page) {
        const form = getFormForBlock(block);
        const grid = block.querySelector('.js-all-experts-grid');
        const gridContainer = block.querySelector('.js-ajax-grid');
        const paginationWrap = block.querySelector('.js-all-experts-pagination-wrap');

        const perPage = parseInt(block.getAttribute('data-per-page'), 10) || 9;

        let location = '';
        let cat = '';
        let sExp = '';
        if (form) {
            const locationEl = form.querySelector('select[name="location"]');
            const catEl = form.querySelector('select[name="cat"]');
            const sExpEl = form.querySelector('input[name="s_exp"]');
            if (locationEl) location = locationEl.value || '';
            if (catEl) cat = catEl.value || '';
            if (sExpEl) sExp = (sExpEl.value || '').trim();
        }

        if (!grid || typeof greenergyData === 'undefined' || !greenergyData.ajaxUrl || !greenergyData.nonce) return;

        if (gridContainer) {
            gridContainer.classList.add('is-loading', 'pointer-events-none');
        }

        const formData = new FormData();
        formData.append('action', 'greenergy_experts_page');
        formData.append('nonce', greenergyData.nonce);
        formData.append('page', String(page));
        formData.append('per_page', String(perPage));
        formData.append('location', location);
        formData.append('cat', cat);
        formData.append('s_exp', sExp);

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
                    const countEl = block.querySelector('.js-all-experts-count');
                    if (countEl && data.data.count_html !== undefined) {
                        countEl.outerHTML = data.data.count_html;
                    }
                    if (paginationWrap && data.data.pagination !== undefined) {
                        paginationWrap.innerHTML = data.data.pagination;
                    }
                }
            })
            .catch(function () {})
            .finally(function () {
                setTimeout(function () {
                    if (gridContainer) {
                        gridContainer.classList.remove('is-loading', 'pointer-events-none');
                    }
                }, 400);
            });
    }

    // Pagination button click
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-all-experts-page');
        if (!btn) return;
        e.preventDefault();
        const block = btn.closest('.js-all-experts-block');
        if (!block) return;
        const page = parseInt(btn.getAttribute('data-page'), 10);
        if (!page || page < 1) return;
        loadPage(block, page);
    });

    // Filter form submit (e.g. search Enter) and select change: go to page 1
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('form.js-all-experts-filter-form');
        if (!form) return;
        e.preventDefault();
        const blockId = form.getAttribute('data-block-id');
        const block = blockId ? document.getElementById(blockId) || document.querySelector('.js-all-experts-block[data-block-id="' + blockId + '"]') : document.querySelector('.js-all-experts-block');
        if (block) loadPage(block, 1);
    });

    document.addEventListener('change', function (e) {
        if (!e.target.matches('.js-all-experts-filter')) return;
        const select = e.target;
        const group = select.closest('.group');
        const label = group ? group.querySelector('.js-all-experts-filter-label') : null;
        if (label) {
            const option = select.options[select.selectedIndex];
            label.textContent = option ? option.textContent.trim() : '';
        }
        const form = select.closest('form.js-all-experts-filter-form');
        if (!form) return;
        const blockId = form.getAttribute('data-block-id');
        const block = blockId ? document.getElementById(blockId) || document.querySelector('.js-all-experts-block[data-block-id="' + blockId + '"]') : document.querySelector('.js-all-experts-block');
        if (block) loadPage(block, 1);
    });

    document.addEventListener('greenergy:experts-apply-search', function (e) {
        if (e.detail && e.detail.block) loadPage(e.detail.block, 1);
    });
}
