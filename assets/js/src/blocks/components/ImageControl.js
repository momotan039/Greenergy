/**
 * Image upload control with URL fallback
 */
import { createElement } from '../deps.js';
import { MediaUpload, MediaUploadCheck } from '../deps.js';
import { TextControl, Button } from '../deps.js';
import { __ } from '../deps.js';

export const ImageControl = ({ label, imageUrl, imageId, onSelect, onSelectURL }) =>
    createElement('div', { style: { marginBottom: '15px' } },
        createElement('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } }, label),
        createElement('div', { style: { display: 'flex', gap: '10px', alignItems: 'center' } },
            createElement(MediaUploadCheck, null,
                createElement(MediaUpload, {
                    onSelect: (media) => onSelect(media),
                    allowedTypes: ['image'],
                    value: imageId,
                    render: ({ open }) =>
                        createElement(Button, { isSecondary: true, onClick: open },
                            imageId ? __('تغيير الصورة', 'greenergy') : __('اختيار من المكتبة', 'greenergy')),
                }),
            ),
            createElement(TextControl, {
                placeholder: __('أو أدخل الرابط...', 'greenergy'),
                value: imageUrl,
                onChange: onSelectURL,
                style: { marginBottom: 0, flex: 1 },
            }),
        ),
        (imageUrl || imageId) &&
            createElement('div', { style: { marginTop: '10px', textAlign: 'center' } },
                createElement('img', {
                    src: imageUrl || (imageId ? 'placeholder' : ''),
                    style: { maxWidth: '100%', maxHeight: '100px', borderRadius: '4px', border: '1px solid #ddd' },
                }),
                createElement(Button, {
                    isDestructive: true,
                    isLink: true,
                    onClick: () => {
                        onSelect({ id: 0, url: '' });
                        onSelectURL('');
                    },
                }, __('حذف', 'greenergy')),
            ),
    );
