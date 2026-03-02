/**
 * Taxonomy term selection control
 */
import { createElement, Fragment, useState, useEffect } from '../deps.js';
import { FormTokenField } from '../deps.js';
import { useSelect } from '../deps.js';

export const TermSelect = ({ label, taxonomy, selectedTermIds, onChange, parent }) => {
    const [suggestions, setSuggestions] = useState([]);
    const [selectedTokens, setSelectedTokens] = useState([]);

    const query = { per_page: 100 };
    if (parent !== undefined && parent !== null) {
        query.parent = Number(parent);
    }

    const { terms } = useSelect(
        (select) => ({ terms: select('core').getEntityRecords('taxonomy', taxonomy, query) }),
        [taxonomy, parent],
    );

    useEffect(() => {
        if (terms) setSuggestions(terms.map((t) => t.name));
    }, [terms]);

    useEffect(() => {
        if (terms && selectedTermIds && selectedTermIds.length) {
            const names = selectedTermIds
                .map((id) => terms.find((t) => Number(t.id) === Number(id))?.name)
                .filter(Boolean);
            setSelectedTokens(names);
        } else {
            setSelectedTokens([]);
        }
    }, [selectedTermIds, terms]);

    const onTokensChange = (tokens) => {
        const newIds = tokens
            .map((token) => terms.find((t) => t.name === token)?.id)
            .filter((id) => id != null)
            .map((id) => Number(id));
        onChange(newIds);
    };

    return createElement(Fragment, null,
        createElement('label', { style: { display: 'block', marginBottom: '5px' } }, label),
        createElement(FormTokenField, {
            value: selectedTokens,
            suggestions,
            onChange: onTokensChange,
            __experimentalExpandOnFocus: true,
        }),
    );
};
