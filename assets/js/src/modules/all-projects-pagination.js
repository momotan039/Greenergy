/**
 * All-projects block: filters and pagination without full page refresh.
 * Reads type, country, sort, s_proj from the block's filter form and fetches grid + pagination via greenergy_projects_page.
 */
export function initAllProjectsPagination() {
    function getFormForBlock(block) {
        const blockId = block.id || block.getAttribute('data-block-id');
        if (blockId) {
            const form = document.querySelector('form.js-all-projects-filter-form[data-block-id="' + blockId + '"]');
            if (form) return form;
        }
        return block.closest('main')?.querySelector('form.js-all-projects-filter-form') || document.querySelector('form.js-all-projects-filter-form');
    }

    function loadPage(block, page) {
        const form = getFormForBlock(block);
        const grid = block.querySelector('.js-all-projects-grid');
        const gridContainer = block.querySelector('.js-ajax-grid');
        const paginationWrap = block.querySelector('.js-all-projects-pagination-wrap');

        const perPage = parseInt(block.getAttribute('data-per-page'), 10) || 15;

        let type = '';
        let country = '';
        let sort = 'latest';
        let sProj = '';
        if (form) {
            const typeEl = form.querySelector('select[name="type"]');
            const countryEl = form.querySelector('select[name="country"]');
            const sortEl = form.querySelector('select[name="sort"]');
            const sProjEl = form.querySelector('input[name="s_proj"]');
            if (typeEl) type = typeEl.value || '';
            if (countryEl) country = countryEl.value || '';
            if (sortEl) sort = sortEl.value || 'latest';
            if (sProjEl) sProj = (sProjEl.value || '').trim();
        }

        if (!grid || typeof greenergyData === 'undefined' || !greenergyData.ajaxUrl || !greenergyData.nonce) return;

        if (gridContainer) {
            gridContainer.classList.add('is-loading', 'pointer-events-none');
        }

        const formData = new FormData();
        formData.append('action', 'greenergy_projects_page');
        formData.append('nonce', greenergyData.nonce);
        formData.append('page', String(page));
        formData.append('per_page', String(perPage));
        formData.append('type', type);
        formData.append('country', country);
        formData.append('sort', sort);
        formData.append('s_proj', sProj);

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
                    const countEl = block.querySelector('.js-all-projects-count');
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

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.js-all-projects-page');
        if (!btn) return;
        e.preventDefault();
        const block = btn.closest('.js-all-projects-block');
        if (!block) return;
        const page = parseInt(btn.getAttribute('data-page'), 10);
        if (!page || page < 1) return;
        loadPage(block, page);
    });

    document.addEventListener('submit', function (e) {
        const form = e.target.closest('form.js-all-projects-filter-form');
        if (!form) return;
        e.preventDefault();
        const blockId = form.getAttribute('data-block-id');
        const block = blockId ? document.getElementById(blockId) || document.querySelector('.js-all-projects-block[data-block-id="' + blockId + '"]') : document.querySelector('.js-all-projects-block');
        if (block) loadPage(block, 1);
    });

    document.addEventListener('change', function (e) {
        if (!e.target.matches('.js-all-projects-filter')) return;
        const select = e.target;
        const group = select.closest('.group');
        const label = group ? group.querySelector('.js-all-projects-filter-label') : null;
        if (label) {
            const option = select.options[select.selectedIndex];
            label.textContent = option ? option.textContent.trim() : '';
        }
        const form = select.closest('form.js-all-projects-filter-form');
        if (!form) return;
        const blockId = form.getAttribute('data-block-id');
        const block = blockId ? document.getElementById(blockId) || document.querySelector('.js-all-projects-block[data-block-id="' + blockId + '"]') : document.querySelector('.js-all-projects-block');
        if (block) loadPage(block, 1);
    });
}
