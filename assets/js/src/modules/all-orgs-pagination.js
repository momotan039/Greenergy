/**
 * All-organizations block: filters and pagination without full page refresh.
 * Reads cat, country, s_org from the block's filter form and fetches grid + pagination via greenergy_organizations_page.
 */
export function initAllOrgsPagination() {
    function getFormForBlock(block) {
        const blockId = block.id || block.getAttribute('data-block-id');
        if (blockId) {
            const form = document.querySelector('form.js-all-orgs-filter-form[data-block-id="' + blockId + '"]');
            if (form) return form;
        }
        return block.closest('main')?.querySelector('form.js-all-orgs-filter-form') || document.querySelector('form.js-all-orgs-filter-form');
    }

    function loadPage(block, page) {
        const form = getFormForBlock(block);
        const grid = block.querySelector('.js-all-orgs-grid');
        const gridContainer = block.querySelector('.js-ajax-grid');
        const paginationWrap = block.querySelector('.js-all-orgs-pagination-wrap');

        const perPage = parseInt(block.getAttribute('data-per-page'), 10) || 9;
        const featuredIds = (block.getAttribute('data-featured-ids') || '').trim();

        let cat = '';
        let country = '';
        let sOrg = '';
        if (form) {
            const catEl = form.querySelector('select[name="cat"]');
            const countryEl = form.querySelector('select[name="country"]');
            const sOrgEl = form.querySelector('input[name="s_org"]');
            if (catEl) cat = catEl.value || '';
            if (countryEl) country = countryEl.value || '';
            if (sOrgEl) sOrg = (sOrgEl.value || '').trim();
        }

        if (!grid || typeof greenergyData === 'undefined' || !greenergyData.ajaxUrl || !greenergyData.nonce) return;

        if (gridContainer) {
            gridContainer.classList.add('is-loading', 'pointer-events-none');
        }

        const formData = new FormData();
        formData.append('action', 'greenergy_organizations_page');
        formData.append('nonce', greenergyData.nonce);
        formData.append('page', String(page));
        formData.append('per_page', String(perPage));
        formData.append('cat', cat);
        formData.append('country', country);
        formData.append('sort', 'latest');
        formData.append('s_org', sOrg);
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
                    const countEl = block.querySelector('.js-all-orgs-count');
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
        const btn = e.target.closest('.js-all-orgs-page');
        if (!btn) return;
        e.preventDefault();
        const block = btn.closest('.js-all-orgs-block');
        if (!block) return;
        const page = parseInt(btn.getAttribute('data-page'), 10);
        if (!page || page < 1) return;
        loadPage(block, page);
    });

    // Filter form submit (e.g. search Enter) and select change: go to page 1
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('form.js-all-orgs-filter-form');
        if (!form) return;
        e.preventDefault();
        const blockId = form.getAttribute('data-block-id');
        const block = blockId ? document.getElementById(blockId) || document.querySelector('.js-all-orgs-block[data-block-id="' + blockId + '"]') : document.querySelector('.js-all-orgs-block');
        if (block) loadPage(block, 1);
    });

    document.addEventListener('change', function (e) {
        if (!e.target.matches('.js-all-orgs-filter')) return;
        const select = e.target;
        const group = select.closest('.group');
        const label = group ? group.querySelector('.js-all-orgs-filter-label') : null;
        if (label) {
            const option = select.options[select.selectedIndex];
            label.textContent = option ? option.textContent.trim() : '';
        }
        const form = select.closest('form.js-all-orgs-filter-form');
        if (!form) return;
        const blockId = form.getAttribute('data-block-id');
        const block = blockId ? document.getElementById(blockId) || document.querySelector('.js-all-orgs-block[data-block-id="' + blockId + '"]') : document.querySelector('.js-all-orgs-block');
        if (block) loadPage(block, 1);
    });
}
