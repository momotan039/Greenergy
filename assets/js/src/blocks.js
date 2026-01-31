const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, ToggleControl, SelectControl, Button, Dashicon } = wp.components;
const ServerSideRender = wp.serverSideRender;
const { Fragment, createElement } = wp.element;

console.log('Greenergy Blocks: Script starting v3 (Refactored)...');

/**
 * Image Control Component
 */
const GreenergyImageControl = ({ label, imageUrl, imageId, onSelect, onSelectURL }) => {
    return createElement('div', { style: { marginBottom: '15px' } },
        createElement('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } }, label),
        createElement('div', { style: { display: 'flex', gap: '10px', alignItems: 'center' } },
            createElement(MediaUploadCheck, null,
                createElement(MediaUpload, {
                    onSelect: (media) => onSelect(media),
                    allowedTypes: ['image'],
                    value: imageId,
                    render: ({ open }) => createElement(Button, { isSecondary: true, onClick: open },
                        imageId ? __('Change Image', 'greenergy') : __('Select from Library', 'greenergy')
                    )
                })
            ),
            createElement(TextControl, {
                placeholder: __('Or enter URL...', 'greenergy'),
                value: imageUrl,
                onChange: onSelectURL,
                style: { marginBottom: 0, flex: 1 }
            })
        ),
        (imageUrl || imageId) && createElement('div', { style: { marginTop: '10px', textAlign: 'center' } },
            createElement('img', {
                src: imageUrl || (imageId ? 'placeholder' : ''),
                style: { maxWidth: '100%', maxHeight: '100px', borderRadius: '4px', border: '1px solid #ddd' }
            }),
            createElement(Button, {
                isDestructive: true, isLink: true,
                onClick: () => { onSelect({ id: 0, url: '' }); onSelectURL(''); }
            }, __('Remove', 'greenergy'))
        )
    );
};

/**
 * Universal Block Edit Component
 */
const GreenergyBlockEdit = (props) => {
    const { attributes, setAttributes, name } = props;
    const blockProps = useBlockProps();

    console.log(`[Greenergy DEBUG] Editing block: ${name}`, attributes);

    const updateAttribute = (key, val) => {
        setAttributes({ [key]: val });
    };

    // Determine which controls to show based on block name
    const renderInspector = () => {
        switch (name) {
            case 'greenergy/hero-block':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('General Settings', 'greenergy') },
                        createElement(TextControl, { label: __('Badge Text', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                        createElement(TextControl, { label: __('Headline Highlight', 'greenergy'), value: attributes.headlineHighlight, onChange: (val) => updateAttribute('headlineHighlight', val) }),
                        createElement(TextControl, { label: __('Headline Main', 'greenergy'), value: attributes.headlineMain, onChange: (val) => updateAttribute('headlineMain', val) }),
                        createElement(TextareaControl, { label: __('Description', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                        createElement(TextControl, { label: __('CTA Text', 'greenergy'), value: attributes.ctaText, onChange: (val) => updateAttribute('ctaText', val) }),
                        createElement(TextControl, { label: __('CTA URL (Page)', 'greenergy'), value: attributes.ctaUrl, onChange: (val) => updateAttribute('ctaUrl', val) }),
                        createElement(GreenergyImageControl, {
                            label: __('Hero Image', 'greenergy'),
                            imageUrl: attributes.imageUrl,
                            imageId: attributes.imageId,
                            onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                            onSelectURL: (val) => updateAttribute('imageUrl', val)
                        })
                    ),
                    createElement(PanelBody, { title: __('Stats Settings', 'greenergy'), initialOpen: false },
                        createElement(SelectControl, {
                            label: __('Stats Display Mode', 'greenergy'),
                            value: attributes.viewMode,
                            options: [
                                { label: __('Static (Manual)', 'greenergy'), value: 'static' },
                                { label: __('Real Time (Database)', 'greenergy'), value: 'real' },
                            ],
                            onChange: (val) => updateAttribute('viewMode', val)
                        }),
                        attributes.viewMode === 'static' && createElement(Fragment, null,
                            createElement('div', { style: { fontWeight: 'bold', marginBottom: '10px' } }, __('Manual Stats', 'greenergy')),
                            attributes.stats.map((stat, index) => (
                                createElement('div', { key: index, style: { padding: '10px', border: '1px solid #eee', marginBottom: '10px' } },
                                    createElement(TextControl, {
                                        label: __('Value', 'greenergy'), value: stat.value, onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index].value = v;
                                            setAttributes({ stats: newStats });
                                        }
                                    }),
                                    createElement(TextControl, {
                                        label: __('Label', 'greenergy'), value: stat.label, onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index].label = v;
                                            setAttributes({ stats: newStats });
                                        }
                                    })
                                )
                            )),
                            createElement('div', { style: { fontWeight: 'bold', margin: '15px 0 10px' } }, __('Featured Stat (Blue Box)', 'greenergy')),
                            createElement(TextControl, { label: __('Value', 'greenergy'), value: attributes.featuredStat.value, onChange: (v) => setAttributes({ featuredStat: { ...attributes.featuredStat, value: v } }) }),
                            createElement(TextControl, { label: __('Label', 'greenergy'), value: attributes.featuredStat.label, onChange: (v) => setAttributes({ featuredStat: { ...attributes.featuredStat, label: v } }) })
                        )
                    )
                );
            case 'greenergy/stories':
                const stories = attributes.stories || [];
                return createElement(PanelBody, { title: __('Stories Settings', 'greenergy') },
                    stories.map((story, index) => (
                        createElement('div', { key: index, style: { marginBottom: '20px', padding: '10px', border: '1px solid #ddd', borderRadius: '4px' } },
                            createElement('div', { style: { display: 'flex', justifyContent: 'space-between', marginBottom: '10px' } },
                                createElement('strong', null, `${__('Story #', 'greenergy')}${index + 1}`),
                                createElement(Button, {
                                    isDestructive: true, isSmall: true, onClick: () => {
                                        const newStories = [...stories];
                                        newStories.splice(index, 1);
                                        setAttributes({ stories: newStories });
                                    }
                                }, __('Remove', 'greenergy'))
                            ),
                            createElement(TextControl, {
                                label: __('Label', 'greenergy'), value: story.label, onChange: (val) => {
                                    const newStories = [...stories];
                                    newStories[index].label = val;
                                    setAttributes({ stories: newStories });
                                }
                            }),
                            createElement(GreenergyImageControl, {
                                label: __('Story Photo', 'greenergy'),
                                imageUrl: story.image,
                                imageId: story.imageId,
                                onSelect: (media) => {
                                    const newStories = [...stories];
                                    newStories[index].imageId = media.id;
                                    newStories[index].image = media.url;
                                    setAttributes({ stories: newStories });
                                },
                                onSelectURL: (val) => {
                                    const newStories = [...stories];
                                    newStories[index].image = val;
                                    setAttributes({ stories: newStories });
                                }
                            })
                        )
                    )),
                    createElement(Button, {
                        isPrimary: true, onClick: () => {
                            setAttributes({ stories: [...stories, { label: 'New Story', image: '', imageId: 0, link: '#' }] });
                        }
                    }, __('Add New Story', 'greenergy'))
                );
            case 'greenergy/most-read-news':
                return createElement(PanelBody, { title: __('Selection Mode', 'greenergy') },
                    createElement(SelectControl, {
                        label: __('News Mode', 'greenergy'),
                        value: attributes.selectionMode,
                        options: [
                            { label: __('Automatic (Top Read)', 'greenergy'), value: 'auto' },
                            { label: __('Manual (Select IDs)', 'greenergy'), value: 'manual' },
                        ],
                        onChange: (val) => updateAttribute('selectionMode', val)
                    }),
                    attributes.selectionMode === 'manual' && createElement(TextControl, {
                        label: __('News IDs (Comma separated)', 'greenergy'),
                        value: attributes.selectedPosts.join(','),
                        onChange: (val) => updateAttribute('selectedPosts', val.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id)))
                    }),
                    createElement(TextControl, { label: __('Badge Text', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                    createElement(TextareaControl, { label: __('Description', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                    createElement(TextControl, { label: __('Button Text', 'greenergy'), value: attributes.buttonText, onChange: (val) => updateAttribute('buttonText', val) })
                );
            case 'greenergy/latest-news':
            case 'greenergy/courses':
            case 'greenergy/jobs':
                return createElement(PanelBody, { title: __('Section Settings', 'greenergy') },
                    createElement(TextControl, { label: __('Badge Text', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                    name === 'greenergy/courses' && createElement(TextControl, { label: __('Title', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(TextareaControl, { label: __('Description', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                    (name !== 'greenergy/latest-news') && createElement(TextControl, { label: __('Button Text', 'greenergy'), value: attributes.buttonText, onChange: (val) => updateAttribute('buttonText', val) }),
                    createElement(GreenergyImageControl, {
                        label: __('Background Image (Optional)', 'greenergy'),
                        imageUrl: attributes.imageUrl,
                        imageId: attributes.imageId,
                        onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                        onSelectURL: (val) => updateAttribute('imageUrl', val)
                    })
                );
            case 'greenergy/stats':
                return createElement(PanelBody, { title: __('Stats Settings', 'greenergy') },
                    createElement(TextControl, { label: __('Title', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(TextareaControl, { label: __('Description', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) })
                );
            default:
                return null;
        }
    };

    // Special layout for stories in editor
    const renderPreview = () => {
        if (name === 'greenergy/stories') {
            const stories = attributes.stories || [];
            return createElement('div', { className: 'flex gap-4 overflow-x-auto p-4 bg-gray-50 rounded-lg' },
                stories.map((story, i) => createElement('div', { key: i, className: 'flex-none text-center' },
                    createElement('div', { className: 'w-16 h-16 rounded-full border-2 border-brand-green p-0.5 overflow-hidden mx-auto' },
                        createElement('img', { src: story.image || 'placeholder', className: 'w-full h-full object-cover rounded-full' })
                    ),
                    createElement('span', { className: 'text-xs mt-1 block font-bold' }, story.label)
                ))
            );
        }
        return createElement(ServerSideRender, { block: name, attributes: attributes });
    };

    return createElement(Fragment, null,
        createElement(InspectorControls, null, renderInspector()),
        createElement('div', { ...blockProps }, renderPreview())
    );
};

/**
 * Registry
 */
const blocks = [
    // homepage blocks
    { name: 'hero-block', title: __('Hero Section', 'greenergy'), icon: 'cover-image' },
    { name: 'stories', title: __('Greenergy Stories', 'greenergy'), icon: 'groups' },
    { name: 'courses', title: __('Greenergy Courses', 'greenergy'), icon: 'welcome-learn-more' },
    { name: 'jobs', title: __('Greenergy Jobs', 'greenergy'), icon: 'businessman' },
    { name: 'latest-news', title: __('Greenergy Latest News', 'greenergy'), icon: 'admin-post' },
    { name: 'most-read-news', title: __('Greenergy Most Read News', 'greenergy'), icon: 'megaphone' },
    { name: 'stats', title: __('Greenergy Global Stats', 'greenergy'), icon: 'chart-area' },
    // news page blocks
    { name: 'news-filter', title: __('News Filter', 'greenergy'), icon: 'filter' },
    { name: 'featured-news', title: __('Featured News', 'greenergy'), icon: 'cover-image' },
    { name: 'news-list', title: __('News List', 'greenergy'), icon: 'list-view' },
    { name: 'news-grid', title: __('News Grid', 'greenergy'), icon: 'grid-view' },
    { name: 'sidebar', title: __('Sidebar Container', 'greenergy'), icon: 'sidebar' },
    { name: 'directory-widget', title: __('Directory Widget', 'greenergy'), icon: 'building' },
    { name: 'courses-widget', title: __('Courses Widget', 'greenergy'), icon: 'book' },
    { name: 'featured-jobs-widget', title: __('Featured Jobs Widget', 'greenergy'), icon: 'businessman' },
    { name: 'follow-us-widget', title: __('Follow Us Widget', 'greenergy'), icon: 'share' },
    { name: 'breadcrumb', title: __('Breadcrumb', 'greenergy'), icon: 'admin-home' },
    { name: 'main-banner', title: __('Main Banner', 'greenergy'), icon: 'cover-image' },
    // theme blocks
    { name: 'scroll-progress', title: __('Scroll Progress', 'greenergy'), icon: 'upload' },
];

blocks.forEach(b => {
    registerBlockType(`greenergy/${b.name}`, {
        title: b.title,
        icon: b.icon,
        category: 'greenergy-blocks',
        edit: (props) => GreenergyBlockEdit({ ...props, name: `greenergy/${b.name}` }),
        save: () => null,
    });
});

/**
 * Ad Block
 */
registerBlockType('greenergy/ad-block', {
    title: __('Greenergy Ad Block', 'greenergy'),
    icon: 'megaphone',
    category: 'greenergy-blocks',
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();
        const { adType, imageUrl, imageId, adLink, adCode, fullWidth } = attributes;

        return createElement(Fragment, null,
            createElement(InspectorControls, null,
                createElement(PanelBody, { title: __('Ad Settings', 'greenergy') },
                    createElement(SelectControl, {
                        label: __('Ad Type', 'greenergy'),
                        value: adType,
                        options: [
                            { label: __('Image banner', 'greenergy'), value: 'image' },
                            { label: __('Custom Code (HTML/JS)', 'greenergy'), value: 'code' },
                        ],
                        onChange: (val) => setAttributes({ adType: val }),
                    }),
                    adType === 'image' && createElement(Fragment, null,
                        createElement(GreenergyImageControl, {
                            label: __('Banner Image', 'greenergy'),
                            imageUrl: imageUrl,
                            imageId: imageId,
                            onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                            onSelectURL: (val) => setAttributes({ imageUrl: val })
                        }),
                        createElement(TextControl, { label: __('Destination Link', 'greenergy'), value: adLink, onChange: (val) => setAttributes({ adLink: val }) })
                    ),
                    adType === 'code' && createElement(TextareaControl, { label: __('Ad Code', 'greenergy'), value: adCode, onChange: (val) => setAttributes({ adCode: val }) }),
                    createElement(TextControl, {
                        label: __('Height (e.g., 10rem, 200px)', 'greenergy'),
                        value: attributes.height,
                        onChange: (val) => setAttributes({ height: val }),
                        help: __('Set the height of the ad container.', 'greenergy')
                    }),
                    createElement(ToggleControl, { label: __('Full Width', 'greenergy'), checked: fullWidth, onChange: (val) => setAttributes({ fullWidth: val }) })
                )
            ),
            createElement('div', { ...blockProps },
                createElement(ServerSideRender, { block: "greenergy/ad-block", attributes: attributes })
            )
        );
    },
    save: () => null,
});

