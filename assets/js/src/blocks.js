const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, ToggleControl, SelectControl, Button, Dashicon, FormTokenField, RangeControl } = wp.components;
const { useSelect } = wp.data;
const { useState, useEffect } = wp.element;
const ServerSideRender = wp.serverSideRender;
const { Fragment, createElement } = wp.element;

console.log('Greenergy Blocks: Script starting v4 (With Read Also)...');

/**
 * Image Control Component
 */
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
                        imageId ? __('تغيير الصورة', 'greenergy') : __('اختيار من المكتبة', 'greenergy')
                    )
                })
            ),
            createElement(TextControl, {
                placeholder: __('أو أدخل الرابط...', 'greenergy'),
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
            }, __('حذف', 'greenergy'))
        )
    );
};

/**
 * Post Select Control (Searchable)
 */
const GreenergyPostSelection = ({ selectedPosts, onChange }) => {
    // selectedPosts is array of objects {id, title}
    const [suggestions, setSuggestions] = useState([]);
    
    // Fetch posts for suggestions
    const { posts } = useSelect((select) => {
        return {
            posts: select('core').getEntityRecords('postType', 'news', { per_page: 20, status: 'publish' })
        };
    }, []);

    useEffect(() => {
        if (posts) {
            setSuggestions(posts.map(post => post.title.raw));
        }
    }, [posts]);

    const onTokensChange = (tokens) => {
        // Map tokens back to post objects
        // This is a simplified version. Real world often needs improved matching or async search on type.
        // For now we match against loaded 'posts'.
        const newSelected = tokens.map(token => {
            // Check if it's already a selected post object (if generic object pass through?)
            // FormTokenField acts on strings primarily.
            
            // Try to find in global posts
            const found = posts?.find(p => p.title.raw === token);
            if (found) return { id: found.id, title: found.title.raw };
            
            // Or maybe it was already selected, find in current value
            const existing = selectedPosts.find(p => p.title === token);
            if (existing) return existing;

            return null; 
        }).filter(Boolean);

        onChange(newSelected);
    };

    const tokens = (selectedPosts || []).map(p => p.title);

    return createElement(Fragment, null, 
        createElement('label', { style: {display:'block', marginBottom: '5px'} }, __('اختر الأخبار', 'greenergy')),
        createElement(FormTokenField, {
            value: tokens,
            suggestions: suggestions,
            onChange: onTokensChange,
            __experimentalExpandOnFocus: true
        }),
        createElement('p', {className: 'components-base-control__help'}, __('ابحث عن الخبر...', 'greenergy'))
    );
};


/**
 * Term Select Control
 */
const GreenergyTermSelect = ({ label, taxonomy, selectedTermIds, onChange }) => {
    const [suggestions, setSuggestions] = useState([]);
    const [selectedTokens, setSelectedTokens] = useState([]);

    // Fetch terms
    const { terms } = useSelect((select) => {
        return {
            terms: select('core').getEntityRecords('taxonomy', taxonomy, { per_page: 100 })
        };
    }, [taxonomy]);

    useEffect(() => {
        if (terms) {
            setSuggestions(terms.map(t => t.name));
        }
    }, [terms]);

    useEffect(() => {
        if (terms && selectedTermIds) {
            // Convert IDs to names for the token field
            const names = selectedTermIds.map(id => {
                const term = terms.find(t => t.id === id);
                return term ? term.name : null;
            }).filter(Boolean);
            setSelectedTokens(names);
        } else {
            setSelectedTokens([]);
        }
    }, [selectedTermIds, terms]);

    const onTokensChange = (tokens) => {
        // Map names back to IDs
        const newIds = tokens.map(token => {
            const term = terms?.find(t => t.name === token);
            return term ? term.id : null;
        }).filter(Boolean);
        onChange(newIds);
    };

    return createElement(Fragment, null,
        createElement('label', { style: { display: 'block', marginBottom: '5px' } }, label),
        createElement(FormTokenField, {
            value: selectedTokens,
            suggestions: suggestions,
            onChange: onTokensChange,
            __experimentalExpandOnFocus: true
        })
    );
};

/**
 * Universal Block Edit Component
 */
const GreenergyBlockEdit = (props) => {
    const { attributes, setAttributes, name } = props;
    const blockProps = useBlockProps();

    const updateAttribute = (key, val) => {
        setAttributes({ [key]: val });
    };

    // Determine which controls to show based on block name
    const renderInspector = () => {
        switch (name) {
            case 'greenergy/read-also':
                return createElement(PanelBody, { title: __('إعدادات', 'greenergy') },
                        createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                        createElement(SelectControl, {
                            label: __('طريقة الاختيار', 'greenergy'),
                            value: attributes.selectionMode,
                            options: [
                                { label: __('تلقائي (ذو صلة)', 'greenergy'), value: 'auto' },
                                { label: __('اختيار يدوي', 'greenergy'), value: 'manual' },
                            ],
                            onChange: (val) => updateAttribute('selectionMode', val)
                        }),
                        createElement(RangeControl, {
                            label: __('عدد الأخبار', 'greenergy'),
                            value: attributes.postsToShow,
                            onChange: (val) => updateAttribute('postsToShow', val),
                            min: 1, max: 10
                        }),
                        attributes.selectionMode === 'manual' && createElement(GreenergyPostSelection, {
                            selectedPosts: attributes.selectedPosts,
                            onChange: (val) => updateAttribute('selectedPosts', val)
                        }),
                        attributes.selectionMode === 'auto' && createElement(Fragment, null, 
                            createElement('p', { className: 'description', style: {marginBottom: '10px', fontStyle: 'italic'} }, 
                                __('اتركه فارغاً للكشف التلقائي حسب الخبر الحالي.', 'greenergy')
                            ),
                            createElement(GreenergyTermSelect, {
                                label: __('تصفية حسب التصنيف (اختياري)', 'greenergy'),
                                taxonomy: 'news_category',
                                selectedTermIds: attributes.queryCategories || [],
                                onChange: (val) => updateAttribute('queryCategories', val)
                            }),
                            createElement(GreenergyTermSelect, {
                                label: __('تصفية حسب الوسم (اختياري)', 'greenergy'),
                                taxonomy: 'post_tag',
                                selectedTermIds: attributes.queryTags || [],
                                onChange: (val) => updateAttribute('queryTags', val)
                            })
                        )
                    );
            case 'greenergy/hero-block':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('إعدادات عامة', 'greenergy') },
                        createElement(TextControl, { label: __('نص الشارة', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                        createElement(TextControl, { label: __('العنوان البارز', 'greenergy'), value: attributes.headlineHighlight, onChange: (val) => updateAttribute('headlineHighlight', val) }),
                        createElement(TextControl, { label: __('العنوان الرئيسي', 'greenergy'), value: attributes.headlineMain, onChange: (val) => updateAttribute('headlineMain', val) }),
                        createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                        createElement(TextControl, { label: __('نص الزر', 'greenergy'), value: attributes.ctaText, onChange: (val) => updateAttribute('ctaText', val) }),
                        createElement(TextControl, { label: __('رابط الزر', 'greenergy'), value: attributes.ctaUrl, onChange: (val) => updateAttribute('ctaUrl', val) }),
                        createElement(GreenergyImageControl, {
                            label: __('صورة الهيرو', 'greenergy'),
                            imageUrl: attributes.imageUrl,
                            imageId: attributes.imageId,
                            onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                            onSelectURL: (val) => updateAttribute('imageUrl', val)
                        })
                    ),
                    createElement(PanelBody, { title: __('إعدادات الإحصائيات', 'greenergy'), initialOpen: false },
                        createElement(SelectControl, {
                            label: __('طريقة عرض الإحصائيات', 'greenergy'),
                            value: attributes.viewMode,
                            options: [
                                { label: __('ثابت (يدوي)', 'greenergy'), value: 'static' },
                                { label: __('وقت حقيقي (قاعدة بيانات)', 'greenergy'), value: 'real' },
                            ],
                            onChange: (val) => updateAttribute('viewMode', val)
                        }),
                        attributes.viewMode === 'static' && createElement(Fragment, null,
                            createElement('div', { style: { fontWeight: 'bold', marginBottom: '10px' } }, __('إحصائيات يدوية', 'greenergy')),
                            attributes.stats.map((stat, index) => (
                                createElement('div', { key: index, style: { padding: '10px', border: '1px solid #eee', marginBottom: '10px' } },
                                    createElement(TextControl, {
                                        label: __('القيمة', 'greenergy'), value: stat.value, onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index].value = v;
                                            setAttributes({ stats: newStats });
                                        }
                                    }),
                                    createElement(TextControl, {
                                        label: __('التسمية', 'greenergy'), value: stat.label, onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index].label = v;
                                            setAttributes({ stats: newStats });
                                        }
                                    })
                                )
                            )),
                            createElement('div', { style: { fontWeight: 'bold', margin: '15px 0 10px' } }, __('المربع الأزرق المميز', 'greenergy')),
                            createElement(TextControl, { label: __('القيمة', 'greenergy'), value: attributes.featuredStat.value, onChange: (v) => setAttributes({ featuredStat: { ...attributes.featuredStat, value: v } }) }),
                            createElement(TextControl, { label: __('التسمية', 'greenergy'), value: attributes.featuredStat.label, onChange: (v) => setAttributes({ featuredStat: { ...attributes.featuredStat, label: v } }) })
                        )
                    )
                );
            case 'greenergy/stories':
                const stories = attributes.stories || [];
                return createElement(PanelBody, { title: __('إعدادات القصص', 'greenergy') },
                    stories.map((story, index) => (
                        createElement('div', { key: index, style: { marginBottom: '20px', padding: '10px', border: '1px solid #ddd', borderRadius: '4px' } },
                            createElement('div', { style: { display: 'flex', justifyContent: 'space-between', marginBottom: '10px' } },
                                createElement('strong', null, `${__('قصة #', 'greenergy')}${index + 1}`),
                                createElement(Button, {
                                    isDestructive: true, isSmall: true, onClick: () => {
                                        const newStories = [...stories];
                                        newStories.splice(index, 1);
                                        setAttributes({ stories: newStories });
                                    }
                                }, __('حذف', 'greenergy'))
                            ),
                            createElement(TextControl, {
                                label: __('التسمية', 'greenergy'), value: story.label, onChange: (val) => {
                                    const newStories = [...stories];
                                    newStories[index].label = val;
                                    setAttributes({ stories: newStories });
                                }
                            }),
                            createElement(GreenergyImageControl, {
                                label: __('صورة القصة', 'greenergy'),
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
                            setAttributes({ stories: [...stories, { label: 'قصة جديدة', image: '', imageId: 0, link: '#' }] });
                        }
                    }, __('أضف قصة جديدة', 'greenergy'))
                );
            case 'greenergy/most-read-news':
                return createElement(PanelBody, { title: __('طريقة الاختيار', 'greenergy') },
                    createElement(SelectControl, {
                        label: __('نمط الأخبار', 'greenergy'),
                        value: attributes.selectionMode,
                        options: [
                            { label: __('تلقائي (الأكثر قراءة)', 'greenergy'), value: 'auto' },
                            { label: __('يدوي (تحديد معرفات)', 'greenergy'), value: 'manual' },
                        ],
                        onChange: (val) => updateAttribute('selectionMode', val)
                    }),
                    attributes.selectionMode === 'manual' && createElement(TextControl, {
                        label: __('معرفات الأخبار (مفصولة بفاصلة)', 'greenergy'),
                        value: attributes.selectedPosts.join(','),
                        onChange: (val) => updateAttribute('selectedPosts', val.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id)))
                    }),
                    createElement(TextControl, { label: __('نص الشارة', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                    createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                    createElement(TextControl, { label: __('نص الزر', 'greenergy'), value: attributes.buttonText, onChange: (val) => updateAttribute('buttonText', val) })
                );
            case 'greenergy/latest-news':
            case 'greenergy/courses':
            case 'greenergy/jobs':
                return createElement(PanelBody, { title: __('إعدادات القسم', 'greenergy') },
                    createElement(TextControl, { label: __('نص الشارة', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                    name === 'greenergy/courses' && createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                    (name !== 'greenergy/latest-news') && createElement(TextControl, { label: __('نص الزر', 'greenergy'), value: attributes.buttonText, onChange: (val) => updateAttribute('buttonText', val) }),
                    createElement(GreenergyImageControl, {
                        label: __('صورة الخلفية (اختياري)', 'greenergy'),
                        imageUrl: attributes.imageUrl,
                        imageId: attributes.imageId,
                        onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                        onSelectURL: (val) => updateAttribute('imageUrl', val)
                    })
                );
            case 'greenergy/stats':
                return createElement(PanelBody, { title: __('إعدادات الإحصائيات', 'greenergy') },
                    createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) })
                );
            case 'greenergy/news-grid':
            case 'greenergy/news-list':
                return createElement(PanelBody, { title: __('إعدادات العرض', 'greenergy') },
                    createElement(TextControl, { label: __('العنوان القسم', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(RangeControl, {
                        label: __('عدد الأخبار', 'greenergy'),
                        value: attributes.count,
                        onChange: (val) => updateAttribute('count', val),
                        min: 1, max: 20
                    }),
                    createElement(RangeControl, {
                        label: __('الإزاحة (Offset)', 'greenergy'),
                        value: attributes.offset,
                        onChange: (val) => updateAttribute('offset', val),
                        min: 0, max: 20
                    }),
                    createElement(GreenergyTermSelect, {
                        label: __('تصفية حسب التصنيف', 'greenergy'),
                        taxonomy: 'news_category',
                        selectedTermIds: attributes.queryCategories || [],
                        onChange: (val) => updateAttribute('queryCategories', val)
                    })
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
    { name: 'hero-block', title: __('قسم الهيرو', 'greenergy'), icon: 'cover-image' },
    { name: 'stories', title: __('قصص جرينرجي', 'greenergy'), icon: 'groups' },
    { name: 'courses', title: __('دورات جرينرجي', 'greenergy'), icon: 'welcome-learn-more' },
    { name: 'jobs', title: __('وظائف جرينرجي', 'greenergy'), icon: 'businessman' },
    { name: 'latest-news', title: __('آخر أخبار جرينرجي', 'greenergy'), icon: 'admin-post' },
    { name: 'most-read-news', title: __('الأكثر قراءة', 'greenergy'), icon: 'megaphone' },
    { name: 'stats', title: __('إحصائيات عالمية', 'greenergy'), icon: 'chart-area' },
    // news page blocks
    { name: 'news-filter', title: __('تصفية الأخبار', 'greenergy'), icon: 'filter' },
    { name: 'featured-news', title: __('أخبار مميزة', 'greenergy'), icon: 'cover-image' },
    { name: 'news-list', title: __('قائمة الأخبار', 'greenergy'), icon: 'list-view' },
    { name: 'news-grid', title: __('شبكة الأخبار', 'greenergy'), icon: 'grid-view' },
    { name: 'sidebar', title: __('حاوية الشريط الجانبي', 'greenergy'), icon: 'sidebar' },
    { name: 'directory-widget', title: __('ودجت الدليل', 'greenergy'), icon: 'building' },
    { name: 'courses-widget', title: __('ودجت الدورات', 'greenergy'), icon: 'book' },
    { name: 'featured-jobs-widget', title: __('ودجت الوظائف المميزة', 'greenergy'), icon: 'businessman' },
    { name: 'follow-us-widget', title: __('ودجت تابعنا', 'greenergy'), icon: 'share' },
    { name: 'breadcrumb', title: __('فتات الخبز', 'greenergy'), icon: 'admin-home' },
    { name: 'main-banner', title: __('اللافتة الرئيسية', 'greenergy'), icon: 'cover-image' },
    { 
        name: 'read-also', 
        title: __('اقرأ أيضاً', 'greenergy'), 
        icon: 'book',
        attributes: {
            title: { type: 'string', default: 'اقرأ أيضا' },
            selectionMode: { type: 'string', default: 'auto' },
            postsToShow: { type: 'number', default: 3 },
            selectedPosts: { type: 'array', default: [] },
            queryCategories: { type: 'array', default: [], items: { type: 'number' } },
            queryTags: { type: 'array', default: [], items: { type: 'number' } }
        }
    },
    // theme blocks
    { name: 'scroll-progress', title: __('مؤشر التمرير', 'greenergy'), icon: 'upload' },
];

blocks.forEach(b => {
    try {
        registerBlockType(`greenergy/${b.name}`, {
            title: b.title,
            icon: b.icon,
            category: 'greenergy-blocks',
            attributes: b.attributes || {},
            edit: (props) => GreenergyBlockEdit({ ...props, name: `greenergy/${b.name}` }),
            save: () => null,
        });
        console.log(`Registered block: greenergy/${b.name}`);
    } catch (e) {
        console.error(`Failed to register block: greenergy/${b.name}`, e);
    }
});


/**
 * Ad Block
 */
registerBlockType('greenergy/ad-block', {
    title: __('كتلة إعلان', 'greenergy'),
    icon: 'megaphone',
    category: 'greenergy-blocks',
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();
        const { adType, imageUrl, imageId, adLink, adCode, fullWidth } = attributes;

        return createElement(Fragment, null,
            createElement(InspectorControls, null,
                createElement(PanelBody, { title: __('إعدادات الإعلان', 'greenergy') },
                    createElement(SelectControl, {
                        label: __('نوع الإعلان', 'greenergy'),
                        value: adType,
                        options: [
                            { label: __('صورة لافتة', 'greenergy'), value: 'image' },
                            { label: __('كود مخصص (HTML/JS)', 'greenergy'), value: 'code' },
                        ],
                        onChange: (val) => setAttributes({ adType: val }),
                    }),
                    adType === 'image' && createElement(Fragment, null,
                        createElement(GreenergyImageControl, {
                            label: __('صورة اللافتة', 'greenergy'),
                            imageUrl: imageUrl,
                            imageId: imageId,
                            onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                            onSelectURL: (val) => setAttributes({ imageUrl: val })
                        }),
                        createElement(TextControl, { label: __('رابط الوجهة', 'greenergy'), value: adLink, onChange: (val) => setAttributes({ adLink: val }) })
                    ),
                    adType === 'code' && createElement(TextareaControl, { label: __('كود الإعلان', 'greenergy'), value: adCode, onChange: (val) => setAttributes({ adCode: val }) }),
                    createElement(TextControl, {
                        label: __('الارتفاع (مثلاً: 10rem, 200px)', 'greenergy'),
                        value: attributes.height,
                        onChange: (val) => setAttributes({ height: val }),
                        help: __('حدد ارتفاع حاوية الإعلان.', 'greenergy')
                    }),
                    createElement(ToggleControl, { label: __('عرض كامل', 'greenergy'), checked: fullWidth, onChange: (val) => setAttributes({ fullWidth: val }) })
                )
            ),
            createElement('div', { ...blockProps },
                createElement(ServerSideRender, { block: "greenergy/ad-block", attributes: attributes })
            )
        );
    },
    save: () => null,
});

