/**
 * Navigation links editor (label + URL)
 */
import { createElement } from '../deps.js';
import { PanelBody, TextControl, Button } from '../deps.js';
import { URLInput } from '../deps.js';
import { __ } from '../deps.js';

export const NavLinksControl = ({ label, links, onChange }) =>
    createElement(PanelBody, { title: label, initialOpen: false },
        (links || []).map((link, index) =>
            createElement(PanelBody, {
                key: index,
                title: link.label || `رابط #${index + 1}`,
                initialOpen: false,
            },
                createElement(TextControl, {
                    label: __('التسمية', 'greenergy'),
                    value: link.label,
                    onChange: (val) => {
                        const newLinks = [...links];
                        newLinks[index] = { ...newLinks[index], label: val };
                        onChange(newLinks);
                    },
                }),
                createElement('div', { style: { marginBottom: '15px' } },
                    createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('الرابط (ابحث عن صفحة)', 'greenergy')),
                    createElement(URLInput, {
                        value: link.url,
                        autoFocus: false,
                        onChange: (url, post) => {
                            const newLinks = [...links];
                            newLinks[index] = { ...newLinks[index], url };
                            if (post?.title && !link.label) newLinks[index].label = post.title;
                            onChange(newLinks);
                        },
                    }),
                ),
                createElement(Button, {
                    isDestructive: true,
                    isLink: true,
                    onClick: () => {
                        const newLinks = [...links];
                        newLinks.splice(index, 1);
                        onChange(newLinks);
                    },
                }, __('حذف الرابط', 'greenergy')),
            ),
        ),
        createElement(Button, {
            isPrimary: true,
            style: { width: '100%', justifyContent: 'center', marginTop: '10px' },
            onClick: () => onChange([...(links || []), { label: '', url: '#' }]),
        }, __('إضافة رابط جديد', 'greenergy')),
    );
