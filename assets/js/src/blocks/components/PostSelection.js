/**
 * Post selection control (searchable FormTokenField)
 */
import { createElement, Fragment, useState, useEffect } from '../deps.js';
import { FormTokenField } from '../deps.js';
import { useSelect } from '../deps.js';
import { __ } from '../deps.js';

const LABELS = {
    jobs: ['اختر الوظائف', 'ابحث عن الوظيفة...'],
    experts: ['اختر الخبراء', 'ابحث عن الخبير...'],
    project: ['اختر المشروع', 'ابحث عن المشروع...'],
    company_product: ['اختر المنتجات', 'ابحث عن المنتج...'],
    companies: ['اختر الشركات المميزة', 'ابحث عن الشركة...'],
};

const getLabels = (postType) => LABELS[postType] || ['اختر الأخبار', 'ابحث عن الخبر...'];

export const PostSelection = ({ selectedPosts, onChange, postType = 'news', refreshKey = 0 }) => {
    const [suggestions, setSuggestions] = useState([]);
    const { posts } = useSelect(
        (select) => ({
            posts: select('core').getEntityRecords('postType', postType, {
                per_page: 50,
                status: 'publish',
                // Dummy param so each refreshKey forces a fresh REST request
                _greenergyRefresh: refreshKey,
            }),
        }),
        [postType, refreshKey],
    );

    useEffect(() => {
        if (!posts) {
            setSuggestions([]);
            return;
        }

        const selectedIds = (selectedPosts || [])
            .map((p) => (typeof p === 'object' && p !== null ? p.id : null))
            .filter(Boolean);

        const availablePosts = posts.filter((p) => !selectedIds.includes(p.id));
        setSuggestions(availablePosts.map((p) => p.title.raw));
    }, [posts, selectedPosts]);

    const onTokensChange = (tokens) => {
        const newSelected = tokens
            .map((token) => {
                const found = posts?.find((p) => p.title.raw === token);
                if (found) return { id: found.id, title: found.title.raw };
                const existing = selectedPosts.find((p) => p.title === token);
                return existing || null;
            })
            .filter(Boolean);
        onChange(newSelected);
    };

    const tokens = (selectedPosts || []).map((p) => (typeof p === 'object' && p !== null ? p.title : String(p))).filter(Boolean);
    const [selectLabel, searchLabel] = getLabels(postType);

    return createElement(Fragment, null,
        createElement('label', { style: { display: 'block', marginBottom: '5px' } }, selectLabel),
        createElement(FormTokenField, {
            value: tokens,
            suggestions,
            onChange: onTokensChange,
            __experimentalExpandOnFocus: true,
        }),
        createElement('p', { className: 'components-base-control__help' }, searchLabel),
    );
};
