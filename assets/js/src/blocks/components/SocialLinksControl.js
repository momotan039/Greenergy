/**
 * Social links editor (platform, url, icon type)
 */
import { createElement } from '../deps.js';
import { PanelBody, TextControl, SelectControl, Button } from '../deps.js';
import { __ } from '../deps.js';
import { ImageControl } from './ImageControl.js';

export const SocialLinksControl = ({ label, links, onChange }) => {
    const updateLink = (index, key, val) => {
        const newLinks = [...links];
        newLinks[index] = { ...newLinks[index], [key]: val };
        onChange(newLinks);
    };

    return createElement(PanelBody, { title: label, initialOpen: false },
        (links || []).map((link, index) =>
            createElement(PanelBody, {
                key: index,
                title: link.platform || `رابط #${index + 1}`,
                initialOpen: false,
            },
                createElement(TextControl, {
                    label: __('المنصة', 'greenergy'),
                    value: link.platform,
                    onChange: (val) => updateLink(index, 'platform', val),
                }),
                createElement(TextControl, {
                    label: __('الرابط', 'greenergy'),
                    value: link.url,
                    onChange: (val) => updateLink(index, 'url', val),
                }),
                createElement(SelectControl, {
                    label: __('نوع الأيقونة', 'greenergy'),
                    value: link.iconType || 'system',
                    options: [
                        { label: __('Font Awesome', 'greenergy'), value: 'font-awesome' },
                        { label: __('رفع صورة', 'greenergy'), value: 'image' },
                        { label: __('أيقونة النظام (SVG)', 'greenergy'), value: 'system' },
                    ],
                    onChange: (val) => updateLink(index, 'iconType', val),
                }),
                link.iconType === 'system' &&
                    createElement(SelectControl, {
                        label: __('اختر أيقونة النظام', 'greenergy'),
                        value: link.icon,
                        options: [
                            { label: __('LinkedIn', 'greenergy'), value: 'ic16-linkedin.svg' },
                            { label: __('YouTube', 'greenergy'), value: 'youtube.svg' },
                            { label: __('Google', 'greenergy'), value: 'google.svg' },
                            { label: __('Facebook', 'greenergy'), value: 'facebook.svg' },
                        ],
                        onChange: (val) => updateLink(index, 'icon', val),
                    }),
                link.iconType === 'font-awesome' &&
                    createElement(TextControl, {
                        label: __('كلاس الأيقونة (fas fa-...)', 'greenergy'),
                        value: link.icon,
                        onChange: (val) => updateLink(index, 'icon', val),
                    }),
                link.iconType === 'image' &&
                    createElement(ImageControl, {
                        label: __('صورة الأيقونة', 'greenergy'),
                        imageUrl: link.iconImage,
                        imageId: link.iconImageId,
                        onSelect: (media) => {
                            const newLinks = [...links];
                            newLinks[index] = { ...newLinks[index], iconImageId: media.id, iconImage: media.url };
                            onChange(newLinks);
                        },
                        onSelectURL: (val) => {
                            const newLinks = [...links];
                            newLinks[index] = { ...newLinks[index], iconImage: val };
                            onChange(newLinks);
                        },
                    }),
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
            onClick: () =>
                onChange([...(links || []), { platform: 'جديد', url: '#', icon: 'facebook.svg', iconType: 'system' }]),
        }, __('إضافة رابط تواصل', 'greenergy')),
    );
};
