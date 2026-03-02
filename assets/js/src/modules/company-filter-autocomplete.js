/**
 * Company filter: smart search with autocomplete suggestions.
 * Fetches company name suggestions as the user types and submits on suggestion select.
 */
export function initCompanyFilterAutocomplete() {
    const wrap = document.querySelector('.js-company-filter-search-wrap');
    if (!wrap) return;

    const input = wrap.querySelector('.js-company-filter-search-input');
    const list = wrap.querySelector('.js-company-filter-suggestions');
    const form = wrap.closest('form');
    if (!input || !list || !form) return;

    let debounceTimer = null;
    const debounceMs = 300;
    let suggestions = [];
    let highlightedIndex = -1;

    function hideSuggestions() {
        list.classList.add('hidden');
        list.innerHTML = '';
        if (input) input.setAttribute('aria-expanded', 'false');
        highlightedIndex = -1;
    }

    function showSuggestions(items) {
        suggestions = items || [];
        highlightedIndex = -1;
        list.innerHTML = '';
        if (suggestions.length === 0) {
            hideSuggestions();
            return;
        }
        suggestions.forEach((item, i) => {
            const li = document.createElement('li');
            li.setAttribute('role', 'option');
            li.setAttribute('data-index', String(i));
            li.className = 'px-4 py-2.5 text-right text-sm text-neutral-800 cursor-pointer hover:bg-green-50 focus:bg-green-50 focus:outline-none';
            li.textContent = item.title;
            li.addEventListener('click', () => selectSuggestion(i));
            list.appendChild(li);
        });
        list.classList.remove('hidden');
        if (input) input.setAttribute('aria-expanded', 'true');
    }

    function selectSuggestion(index) {
        if (index < 0 || index >= suggestions.length) return;
        input.value = suggestions[index].title;
        hideSuggestions();
        form.submit();
    }

    function fetchSuggestions(q) {
        if (typeof greenergyData === 'undefined' || !greenergyData.ajaxUrl) return;
        const params = new URLSearchParams({
            action: 'greenergy_companies_search_suggest',
            term: q,
        });
        fetch(greenergyData.ajaxUrl + '?' + params.toString(), { method: 'GET' })
            .then((res) => res.json())
            .then((json) => {
                if (json.success && Array.isArray(json.data)) {
                    showSuggestions(json.data);
                } else {
                    hideSuggestions();
                }
            })
            .catch(() => hideSuggestions());
    }

    function onInput() {
        const q = (input.value || '').trim();
        if (debounceTimer) clearTimeout(debounceTimer);
        if (q.length < 2) {
            hideSuggestions();
            return;
        }
        debounceTimer = setTimeout(() => fetchSuggestions(q), debounceMs);
    }

    function onKeydown(e) {
        if (!list.classList.contains('hidden') && suggestions.length > 0) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                highlightedIndex = Math.min(highlightedIndex + 1, suggestions.length - 1);
                updateHighlight();
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                highlightedIndex = Math.max(highlightedIndex - 1, -1);
                updateHighlight();
                return;
            }
            if (e.key === 'Enter' && highlightedIndex >= 0) {
                e.preventDefault();
                selectSuggestion(highlightedIndex);
                return;
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                hideSuggestions();
                return;
            }
        }
    }

    function updateHighlight() {
        const options = list.querySelectorAll('[role="option"]');
        options.forEach((el, i) => {
            el.classList.toggle('bg-green-100', i === highlightedIndex);
            el.classList.toggle('text-green-800', i === highlightedIndex);
        });
    }

    input.addEventListener('input', onInput);
    input.addEventListener('focus', () => {
        const q = (input.value || '').trim();
        if (q.length >= 2 && suggestions.length > 0) showSuggestions(suggestions);
    });
    input.addEventListener('keydown', onKeydown);

    document.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) hideSuggestions();
    });

    list.addEventListener('mouseenter', () => { highlightedIndex = -1; updateHighlight(); });
}
