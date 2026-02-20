const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck, URLInput, InnerBlocks, PanelColorSettings, RichText } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, ToggleControl, SelectControl, Button, Dashicon, FormTokenField, RangeControl } = wp.components;
const { useSelect } = wp.data;
const { useState, useEffect } = wp.element;
const ServerSideRender = wp.serverSideRender;
const { Fragment, createElement } = wp.element;

console.log('Greenergy Blocks: Script starting v6 (Unified Header)...');

registerBlockType('greenergy/single-news-content', {
    edit: GreenergyBlockEdit,
    save: () => null,
});

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
const GreenergyPostSelection = ({ selectedPosts, onChange, postType = 'news' }) => {
    // selectedPosts is array of objects {id, title}
    const [suggestions, setSuggestions] = useState([]);
    
    // Fetch posts for suggestions
    const { posts } = useSelect((select) => {
        return {
            posts: select('core').getEntityRecords('postType', postType, { per_page: 50, status: 'publish' })
        };
    }, [postType]);

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

    const tokens = (selectedPosts || []).map(p => {
        if (typeof p === 'object' && p !== null) return p.title;
        // Fallback for ID-only arrays or raw strings
        return String(p);
    }).filter(Boolean);

    return createElement(Fragment, null, 
        createElement('label', { style: {display:'block', marginBottom: '5px'} }, postType === 'jobs' ? __('اختر الوظائف', 'greenergy') : __('اختر الأخبار', 'greenergy')),
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
 * Menu Select Component
 */
const GreenergyMenuSelect = ({ label, value, onChange }) => {
    const menus = useSelect((select) => {
        return select('core').getMenus({ per_page: -1 });
    }, []);

    const options = [
        { label: __('استخدام القائمة الافتراضية (Primary)', 'greenergy'), value: 0 },
        ...(menus || []).map(menu => ({
            label: menu.name,
            value: menu.id
        }))
    ];

    return createElement(SelectControl, {
        label: label,
        value: value,
        options: options,
        onChange: (val) => onChange(parseInt(val))
    });
};

/**
 * Social Links Control
 */
const GreenergySocialLinksControl = ({ label, links, onChange }) => {
    const updateLink = (index, key, val) => {
        const newLinks = [...links];
        newLinks[index] = { ...newLinks[index], [key]: val };
        onChange(newLinks);
    };

    return createElement(PanelBody, { title: label, initialOpen: false },
        (links || []).map((link, index) => (
            createElement(PanelBody, { key: index, title: link.platform || `رابط #${index + 1}`, initialOpen: false },
                createElement(TextControl, {
                    label: __('المنصة', 'greenergy'),
                    value: link.platform,
                    onChange: (val) => updateLink(index, 'platform', val)
                }),
                createElement(TextControl, {
                    label: __('الرابط', 'greenergy'),
                    value: link.url,
                    onChange: (val) => updateLink(index, 'url', val)
                }),
                createElement(SelectControl, {
                    label: __('نوع الأيقونة', 'greenergy'),
                    value: link.iconType || 'system',
                    options: [
                        { label: __('Font Awesome', 'greenergy'), value: 'font-awesome' },
                        { label: __('رفع صورة', 'greenergy'), value: 'image' },
                        { label: __('أيقونة النظام (SVG)', 'greenergy'), value: 'system' },
                    ],
                    onChange: (val) => updateLink(index, 'iconType', val)
                }),
                link.iconType === 'system' && createElement(SelectControl, {
                    label: __('اختر أيقونة النظام', 'greenergy'),
                    value: link.icon,
                    options: [
                        { label: __('LinkedIn', 'greenergy'), value: 'ic16-linkedin.svg' },
                        { label: __('YouTube', 'greenergy'), value: 'youtube.svg' },
                        { label: __('Google', 'greenergy'), value: 'google.svg' },
                        { label: __('Facebook', 'greenergy'), value: 'facebook.svg' },
                    ],
                    onChange: (val) => updateLink(index, 'icon', val)
                }),
                link.iconType === 'font-awesome' && createElement(TextControl, {
                    label: __('كلاس الأيقونة (fas fa-...)', 'greenergy'),
                    value: link.icon,
                    onChange: (val) => updateLink(index, 'icon', val)
                }),
                link.iconType === 'image' && createElement(GreenergyImageControl, {
                    label: __('صورة الأيقونة', 'greenergy'),
                    imageUrl: link.iconImage,
                    imageId: link.iconImageId,
                    onSelect: (media) => {
                        const newLinks = [...links];
                        newLinks[index] = { ...newLinks[index], iconImageId: media.id, iconImage: media.url };
                        onChange(newLinks);
                    }
                }),
                createElement(Button, {
                    isDestructive: true, isLink: true, onClick: () => {
                        const newLinks = [...links];
                        newLinks.splice(index, 1);
                        onChange(newLinks);
                    }
                }, __('حذف الرابط', 'greenergy'))
            )
        )),
        createElement(Button, {
            isPrimary: true, style: { width: '100%', justifyContent: 'center', marginTop: '10px' },
            onClick: () => onChange([...(links || []), { platform: 'جديد', url: '#', icon: 'facebook.svg', iconType: 'system' }])
        }, __('إضافة رابط تواصل', 'greenergy'))
    );
};

/**
 * Navigation Links Control
 */
const GreenergyNavLinksControl = ({ label, links, onChange }) => {
    return createElement(PanelBody, { title: label, initialOpen: false },
        (links || []).map((link, index) => (
            createElement(PanelBody, { key: index, title: link.label || `رابط #${index + 1}`, initialOpen: false },
                createElement(TextControl, {
                    label: __('التسمية', 'greenergy'),
                    value: link.label,
                    onChange: (val) => {
                        const newLinks = [...links];
                        newLinks[index] = { ...newLinks[index], label: val };
                        onChange(newLinks);
                    }
                }),
                createElement('div', { style: { marginBottom: '15px' } },
                    createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('الرابط (ابحث عن صفحة)', 'greenergy')),
                    createElement(URLInput, {
                        value: link.url,
                        autoFocus: false,
                        onChange: (url, post) => {
                            const newLinks = [...links];
                            newLinks[index] = { ...newLinks[index], url: url };
                            if (post && post.title && !link.label) {
                                newLinks[index].label = post.title;
                            }
                            onChange(newLinks);
                        }
                    })
                ),
                createElement(Button, {
                    isDestructive: true, isLink: true, onClick: () => {
                        const newLinks = [...links];
                        newLinks.splice(index, 1);
                        onChange(newLinks);
                    }
                }, __('حذف الرابط', 'greenergy'))
            )
        )),
        createElement(Button, {
            isPrimary: true, style: { width: '100%', justifyContent: 'center', marginTop: '10px' },
            onClick: () => onChange([...(links || []), { label: '', url: '#' }])
        }, __('إضافة رابط جديد', 'greenergy'))
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
                        createElement('div', { style: { marginBottom: '20px' } },
                            createElement('label', { style: { display: 'block', marginBottom: '5px', fontWeight: 'bold' } }, __('رابط الزر (رابط أو ابحث عن صفحة)', 'greenergy')),
                            createElement(URLInput, {
                                value: attributes.ctaUrl,
                                onChange: (val) => updateAttribute('ctaUrl', val),
                                disableSuggestions: false,
                            })
                        ),
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
                                { label: __('ثابت', 'greenergy'), value: 'static' },
                                { label: __('ديناميكي', 'greenergy'), value: 'real' },
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
                                            newStats[index] = { ...newStats[index], value: v };
                                            setAttributes({ stats: newStats });
                                        }
                                    }),
                                    createElement(TextControl, {
                                        label: __('التسمية', 'greenergy'), value: stat.label, onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index] = { ...newStats[index], label: v };
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
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('طريقة الاختيار', 'greenergy') },
                        createElement(SelectControl, {
                            label: __('نمط الاختيار', 'greenergy'),
                            value: attributes.selectionMode,
                            options: [
                                { label: __('تلقائي (الأكثر قراءة)', 'greenergy'), value: 'auto' },
                                { label: __('يدوي (تحديد لكل قسم)', 'greenergy'), value: 'manual' },
                            ],
                            onChange: (val) => updateAttribute('selectionMode', val)
                        })
                    ),
                    attributes.selectionMode === 'manual' && createElement(Fragment, null,
                        createElement(PanelBody, { title: __('العمود الأيمن', 'greenergy'), initialOpen: false },
                            createElement(GreenergyPostSelection, {
                                selectedPosts: attributes.selectedPostsRight,
                                onChange: (val) => updateAttribute('selectedPostsRight', val)
                            })
                        ),
                        createElement(PanelBody, { title: __('المنتصف (السلايدر العلوي)', 'greenergy'), initialOpen: false },
                            createElement(GreenergyPostSelection, {
                                selectedPosts: attributes.selectedPostsCenterTop,
                                onChange: (val) => updateAttribute('selectedPostsCenterTop', val)
                            })
                        ),
                        createElement(PanelBody, { title: __('المنتصف (السلايدر السفلي)', 'greenergy'), initialOpen: false },
                            createElement(GreenergyPostSelection, {
                                selectedPosts: attributes.selectedPostsCenterBottom,
                                onChange: (val) => updateAttribute('selectedPostsCenterBottom', val)
                            })
                        ),
                        createElement(PanelBody, { title: __('العمود الأيسر', 'greenergy'), initialOpen: false },
                            createElement(GreenergyPostSelection, {
                                selectedPosts: attributes.selectedPostsLeft,
                                onChange: (val) => updateAttribute('selectedPostsLeft', val)
                            })
                        )
                    ),
                    createElement(PanelBody, { title: __('أعداد الأخبار', 'greenergy'), initialOpen: attributes.selectionMode === 'auto' },
                        createElement(RangeControl, { label: __('عدد أخبار اليمين', 'greenergy'), value: attributes.rightCount, onChange: (val) => updateAttribute('rightCount', val), min: 0, max: 10 }),
                        createElement(RangeControl, { label: __('عدد السلايدر العلوي', 'greenergy'), value: attributes.centerTopCount, onChange: (val) => updateAttribute('centerTopCount', val), min: 0, max: 10 }),
                        createElement(RangeControl, { label: __('عدد السلايدر السفلي', 'greenergy'), value: attributes.centerBottomCount, onChange: (val) => updateAttribute('centerBottomCount', val), min: 0, max: 10 }),
                        createElement(RangeControl, { label: __('عدد أخبار اليسار', 'greenergy'), value: attributes.leftCount, onChange: (val) => updateAttribute('leftCount', val), min: 0, max: 10 })
                    ),
                    createElement(PanelBody, { title: __('نصوص القسم', 'greenergy'), initialOpen: false },
                        createElement(TextControl, { label: __('نص الشارة', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                        createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                        createElement(TextControl, { label: __('نص الزر', 'greenergy'), value: attributes.buttonText, onChange: (val) => updateAttribute('buttonText', val) })
                    )
                );
            case 'greenergy/latest-news':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('إعدادات القسم', 'greenergy') },
                        createElement(TextControl, { label: __('نص الشارة', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                        createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                        createElement(SelectControl, {
                            label: __('طريقة عرض الفئات', 'greenergy'),
                            value: attributes.selectionMode || 'dynamic',
                            options: [
                                { label: __('تلقائي (عرض كافة الفئات)', 'greenergy'), value: 'dynamic' },
                                { label: __('يدوي (تحديد فئات محددة)', 'greenergy'), value: 'manual' },
                            ],
                            onChange: (val) => updateAttribute('selectionMode', val)
                        }),
                        attributes.selectionMode === 'manual' && createElement(GreenergyTermSelect, {
                            label: __('اختر الفئات', 'greenergy'),
                            taxonomy: 'news_category',
                            selectedTermIds: attributes.selectedCategories,
                            onChange: (val) => updateAttribute('selectedCategories', val)
                        }),
                        createElement(GreenergyImageControl, {
                            label: __('صورة الخلفية (اختياري)', 'greenergy'),
                            imageUrl: attributes.imageUrl,
                            imageId: attributes.imageId,
                            onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                            onSelectURL: (val) => updateAttribute('imageUrl', val)
                        })
                    )
                );
            case 'greenergy/jobs':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('إعدادات القسم', 'greenergy') },
                        createElement(TextControl, { label: __('نص الشارة', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                        createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                        createElement(TextControl, { label: __('نص الزر الرئيسي', 'greenergy'), value: attributes.buttonText, onChange: (val) => updateAttribute('buttonText', val) }),
                        createElement(GreenergyImageControl, {
                            label: __('صورة الخلفية (اختياري)', 'greenergy'),
                            imageUrl: attributes.imageUrl,
                            imageId: attributes.imageId,
                            onSelect: (media) => setAttributes({ imageId: media.id, imageUrl: media.url }),
                            onSelectURL: (val) => updateAttribute('imageUrl', val)
                        })
                    ),
                    createElement(PanelBody, { title: __('طريقة اختيار الوظائف', 'greenergy') },
                        createElement(SelectControl, {
                            label: __('نمط الاختيار', 'greenergy'),
                            value: attributes.selectionMode,
                            options: [
                                { label: __('تلقائي (أحدث الوظائف)', 'greenergy'), value: 'dynamic' },
                                { label: __('يدوي (اختيار مخصص)', 'greenergy'), value: 'manual' },
                            ],
                            onChange: (val) => updateAttribute('selectionMode', val)
                        }),
                        attributes.selectionMode === 'manual' && createElement(GreenergyPostSelection, {
                            postType: 'jobs',
                            selectedPosts: attributes.selectedPosts,
                            onChange: (val) => updateAttribute('selectedPosts', val)
                        })
                    ),
                    createElement(PanelBody, { title: __('روابط الصفحات', 'greenergy'), initialOpen: false },
                        createElement('div', { style: { marginBottom: '15px' } },
                            createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('رابط صفحة "كل الوظائف"', 'greenergy')),
                            createElement(URLInput, {
                                value: attributes.viewAllUrl,
                                onChange: (val) => updateAttribute('viewAllUrl', val),
                                placeholder: __('اتركه فارغاً للافتراضي...', 'greenergy'),
                            })
                        ),
                        createElement('div', { style: { marginBottom: '15px' } },
                            createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('رابط "اكتشف الكل" للفرص الذهبية', 'greenergy')),
                            createElement(URLInput, {
                                value: attributes.goldenAllUrl,
                                onChange: (val) => updateAttribute('goldenAllUrl', val),
                                placeholder: __('اتركه فارغاً للافتراضي...', 'greenergy'),
                            })
                        )
                    )
                );
            case 'greenergy/all-jobs':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('إعدادات العرض', 'greenergy') },
                        createElement(ToggleControl, {
                            label: __('عرض الوظائف الذهبية فقط', 'greenergy'),
                            checked: attributes.showGoldOnly,
                            onChange: (val) => updateAttribute('showGoldOnly', val)
                        }),
                        createElement(RangeControl, {
                            label: __('عدد الوظائف في الصفحة', 'greenergy'),
                            value: attributes.postsPerPage,
                            onChange: (val) => updateAttribute('postsPerPage', val),
                            min: 1, max: 20
                        })
                    ),
                    !attributes.showGoldOnly && createElement(PanelBody, { title: __('رابط "اكتشف الكل"', 'greenergy'), initialOpen: false },
                        createElement('div', { style: { marginBottom: '15px' } },
                            createElement('label', { style: { display: 'block', marginBottom: '5px' } }, __('الرابط المخصص', 'greenergy')),
                            createElement(URLInput, {
                                value: attributes.goldAllUrl,
                                onChange: (val) => updateAttribute('goldAllUrl', val),
                                placeholder: __('اتركه فارغاً للافتراضي...', 'greenergy'),
                            })
                        )
                    )
                );
            case 'greenergy/courses':
                return createElement(PanelBody, { title: __('إعدادات القسم', 'greenergy') },
                    createElement(TextControl, { label: __('نص الشارة', 'greenergy'), value: attributes.badgeText, onChange: (val) => updateAttribute('badgeText', val) }),
                    createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                    createElement(TextControl, { label: __('نص الزر', 'greenergy'), value: attributes.buttonText, onChange: (val) => updateAttribute('buttonText', val) }),
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
                    createElement(TextareaControl, { label: __('الوصف', 'greenergy'), value: attributes.description, onChange: (val) => updateAttribute('description', val) }),
                    createElement(SelectControl, {
                        label: __('وضع العرض', 'greenergy'),
                        value: attributes.viewMode,
                        options: [
                            { label: __('ثابت', 'greenergy'), value: 'static' },
                            { label: __('بث حي (CPT)', 'greenergy'), value: 'real' },
                        ],
                        onChange: (val) => updateAttribute('viewMode', val)
                    }),
                    attributes.viewMode === 'static' && createElement(Fragment, null,
                        createElement('hr', null),
                        createElement('div', { style: { fontWeight: 'bold', marginBottom: '10px' } }, __('قائمة الإحصائيات', 'greenergy')),
                        (attributes.stats || []).map((stat, index) => (
                            createElement(PanelBody, { key: index, title: stat.title || `إحصائية #${index + 1}`, initialOpen: false },
                                createElement(TextControl, {
                                    label: __('العنوان', 'greenergy'), value: stat.title, onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], title: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                createElement(TextControl, {
                                    label: __('القيمة', 'greenergy'), value: stat.value, onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], value: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                createElement(TextControl, {
                                    label: __('الوصف', 'greenergy'), value: stat.desc, onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], desc: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                createElement(SelectControl, {
                                    label: __('نوع الأيقونة', 'greenergy'),
                                    value: stat.iconType || 'font-awesome',
                                    options: [
                                        { label: __('Font Awesome', 'greenergy'), value: 'font-awesome' },
                                        { label: __('رفع صورة', 'greenergy'), value: 'image' },
                                        { label: __('أيقونة النظام (SVG)', 'greenergy'), value: 'system' },
                                    ],
                                    onChange: (val) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], iconType: val };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                (stat.iconType === 'font-awesome' || !stat.iconType) && createElement(TextControl, {
                                    label: __('كلاس الأيقونة (مثال: fas fa-solar-panel)', 'greenergy'), 
                                    value: stat.icon, 
                                    onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], icon: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                stat.iconType === 'system' && createElement(SelectControl, {
                                    label: __('اختر أيقونة النظام', 'greenergy'),
                                    value: stat.icon,
                                    options: [
                                        { label: __('مزرعة رياح', 'greenergy'), value: 'wind-power.png' },
                                        { label: __('طاقة', 'greenergy'), value: 'energy.png' },
                                        { label: __('انبعاثات', 'greenergy'), value: 'cardano-(ada).svg' },
                                        { label: __('مشاريع', 'greenergy'), value: 'note-favorite.svg' },
                                        { label: __('استثمارات', 'greenergy'), value: 'status-up.svg' },
                                        { label: __('عالمي', 'greenergy'), value: 'global.svg' },
                                    ],
                                    onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], icon: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                stat.iconType === 'image' && createElement(GreenergyImageControl, {
                                    label: __('صورة الأيقونة', 'greenergy'),
                                    imageUrl: stat.iconImage,
                                    imageId: stat.iconImageId,
                                    onSelect: (media) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], iconImageId: media.id, iconImage: media.url };
                                        setAttributes({ stats: newStats });
                                    },
                                    onSelectURL: (val) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], iconImage: val };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                createElement(Button, {
                                    isDestructive: true, isLink: true, onClick: () => {
                                        const newStats = [...attributes.stats];
                                        newStats.splice(index, 1);
                                        setAttributes({ stats: newStats });
                                    }
                                }, __('حذف الإحصائية', 'greenergy'))
                            )
                        )),
                        createElement(Button, {
                            isPrimary: true, style: { marginTop: '10px', width: '100%', justifyContent: 'center' }, onClick: () => {
                                setAttributes({
                                    stats: [...(attributes.stats || []), { title: 'عنوان جديد', value: '0', desc: 'وصف جديد', icon: 'fas fa-chart-bar', iconType: 'font-awesome' }]
                                });
                            }
                        }, __('إضافة إحصائية', 'greenergy')),
                        createElement('hr', null),
                        createElement(Button, {
                            isDestructive: true, isOutline: true, style: { width: '100%', justifyContent: 'center' }, onClick: () => {
                                if (window.confirm(__('هل أنت متأكد من إعادة تعيين القسم بالكامل؟ ستفقد جميع التعديلات.', 'greenergy'))) {
                                    setAttributes({
                                        title: 'أرقام تتحدث عن مستقبل الطاقة',
                                        description: 'تعرف على أبرز إنجازات ومؤشرات قطاع الطاقة المتجددة حول العالم، في لمحة سريعة.',
                                        viewMode: 'static',
                                        stats: [
                                            { title: "إجمالي إنتاج الطاقة الشمسية", value: "120+ جيجاوات", desc: "تم إنتاجها عالميًا في آخر 12 شهر", icon: "wind-power.png", iconType: "system", iconImage: "" },
                                            { title: "إجمالي إنتاج طاقة الرياح", value: "95+ جيجاوات", desc: "عبر مزارع الرياح البرية والبحرية", icon: "energy.png", iconType: "system", iconImage: "" },
                                            { title: "انخفاض انبعاثات الكربون", value: "2.5+ مليون طن", desc: "تم تجنبها باستخدام مصادر الطاقة النظيفة", icon: "cardano-(ada).svg", iconType: "system", iconImage: "" },
                                            { title: "عدد المشاريع الجديدة", value: "3,200+ مشروع", desc: "في مجال الطاقة المتجددة خلال العام الحالي", icon: "note-favorite.svg", iconType: "system", iconImage: "" },
                                            { title: "الاستثمارات العالمية", value: "500+ مليار دولار", desc: "تم ضخها في قطاع الطاقة النظيفة", icon: "status-up.svg", iconType: "system", iconImage: "" },
                                            { title: "عدد الدول المشاركة", value: "180+ دولة", desc: "تتبنى سياسات للطاقة المستدامة", icon: "global.svg", iconType: "system", iconImage: "" }
                                        ]
                                    });
                                }
                            }
                        }, __('إعادة تعيين القسم', 'greenergy'))
                    )
                );
            case 'greenergy/main-banner':
                return createElement(PanelBody, { title: __('إعدادات اللافتة', 'greenergy') },
                    createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(TextControl, { label: __('العنوان الفرعي', 'greenergy'), value: attributes.subtitle, onChange: (val) => updateAttribute('subtitle', val) }),
                    createElement(SelectControl, {
                        label: __('نوع الخلفية', 'greenergy'),
                        value: attributes.bannerType || 'image',
                        options: [
                            { label: __('صورة', 'greenergy'), value: 'image' },
                            { label: __('فيديو', 'greenergy'), value: 'video' },
                        ],
                        onChange: (val) => updateAttribute('bannerType', val)
                    }),
                    createElement(RangeControl, {
                        label: __('قوة تظليل الخلفية', 'greenergy'),
                        value: attributes.overlayOpacity || 40,
                        onChange: (val) => updateAttribute('overlayOpacity', val),
                        min: 0,
                        max: 100,
                        step: 10
                    }),
                    attributes.bannerType === 'video' && createElement(TextControl, {
                        label: __('رابط الفيديو (MP4)', 'greenergy'),
                        value: attributes.videoUrl,
                        onChange: (val) => updateAttribute('videoUrl', val)
                    }),
                    createElement(GreenergyImageControl, {
                        label: __('صورة الخلفية', 'greenergy'),
                        imageUrl: attributes.backgroundImage,
                        imageId: attributes.backgroundImageId,
                        onSelect: (media) => setAttributes({ backgroundImageId: media.id, backgroundImage: media.url }),
                        onSelectURL: (val) => updateAttribute('backgroundImage', val)
                    }),
                    createElement(ToggleControl, {
                        label: __('إظهار العنوان', 'greenergy'),
                        checked: attributes.showTitle,
                        onChange: (val) => updateAttribute('showTitle', val)
                    }),
                    createElement(ToggleControl, {
                        label: __('إظهار العنوان الفرعي', 'greenergy'),
                        checked: attributes.showSubtitle,
                        onChange: (val) => updateAttribute('showSubtitle', val)
                    }),
                    createElement(ToggleControl, {
                        label: __('إظهار الوصف', 'greenergy'),
                        checked: attributes.showDesc,
                        onChange: (val) => updateAttribute('showDesc', val)
                    }),
                    attributes.showDesc && createElement(TextareaControl, {
                        label: __('الوصف', 'greenergy'),
                        value: attributes.desc,
                        onChange: (val) => updateAttribute('desc', val)
                    }),
                    createElement(ToggleControl, {
                        label: __('إظهار الإحصائيات (مثل صفحة الوظائف)', 'greenergy'),
                        checked: attributes.showStats || attributes.isJobsPage,
                        onChange: (val) => {
                            updateAttribute('showStats', val);
                            updateAttribute('isJobsPage', val); // Keep in sync for compatibility
                        }
                    }),
                    (attributes.showStats || attributes.isJobsPage) && createElement(Fragment, null,
                        createElement('hr', null),
                        createElement('div', { style: { fontWeight: 'bold', marginBottom: '10px' } }, __('قائمة الإحصائيات', 'greenergy')),
                        (attributes.stats || []).map((stat, index) => (
                            createElement('div', { key: index, style: { padding: '10px', border: '1px solid #eee', marginBottom: '10px', borderRadius: '8px' } },
                                createElement(SelectControl, {
                                    label: __('نوع الإحصائية', 'greenergy'),
                                    value: stat.mode || 'manual',
                                    options: [
                                        { label: __('يدوي', 'greenergy'), value: 'manual' },
                                        { label: __('ديناميكي', 'greenergy'), value: 'dynamic' },
                                    ],
                                    onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], mode: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                (stat.mode === 'manual' || !stat.mode) ?
                                    createElement(TextControl, {
                                        label: __('القيمة', 'greenergy'), value: stat.value, onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index] = { ...newStats[index], value: v };
                                            setAttributes({ stats: newStats });
                                        }
                                    }) :
                                    createElement(SelectControl, {
                                        label: __('مصدر البيانات', 'greenergy'),
                                        value: stat.dataSource || 'jobs_count',
                                        options: [
                                            { label: __('عدد الوظائف', 'greenergy'), value: 'jobs_count' },
                                            { label: __('فرص ذهبية', 'greenergy'), value: 'gold_jobs_count' },
                                            { label: __('عدد الأخبار', 'greenergy'), value: 'news_count' },
                                            { label: __('عدد الصفحات', 'greenergy'), value: 'pages_count' },
                                        ],
                                        onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index] = { ...newStats[index], dataSource: v };
                                            setAttributes({ stats: newStats });
                                        }
                                    }),
                                createElement(TextControl, {
                                    label: __('التسمية', 'greenergy'), value: stat.label, onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], label: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                createElement(SelectControl, {
                                    label: __('نوع الأيقونة', 'greenergy'),
                                    value: stat.iconType || 'platform',
                                    options: [
                                        { label: __('أيقونة المنصة', 'greenergy'), value: 'platform' },
                                        { label: __('Font Awesome', 'greenergy'), value: 'font-awesome' },
                                    ],
                                    onChange: (v) => {
                                        const newStats = [...attributes.stats];
                                        newStats[index] = { ...newStats[index], iconType: v };
                                        setAttributes({ stats: newStats });
                                    }
                                }),
                                (stat.iconType === 'font-awesome') ?
                                    createElement(Fragment, null,
                                        createElement(TextControl, {
                                            label: __('كلاس الأيقونة (مثال: fas fa-user)', 'greenergy'),
                                            value: stat.faIcon || '',
                                            onChange: (v) => {
                                                const newStats = [...attributes.stats];
                                                newStats[index] = { ...newStats[index], faIcon: v };
                                                setAttributes({ stats: newStats });
                                            }
                                        }),
                                        createElement(RangeControl, {
                                            label: __('حجم الأيقونة', 'greenergy'),
                                            value: stat.faIconSize || 12,
                                            onChange: (v) => {
                                                const newStats = [...attributes.stats];
                                                newStats[index] = { ...newStats[index], faIconSize: v };
                                                setAttributes({ stats: newStats });
                                            },
                                            min: 8,
                                            max: 24
                                        })
                                    ) :
                                    createElement(SelectControl, {
                                        label: __('اختر أيقونة المنصة', 'greenergy'),
                                        value: stat.icon,
                                        options: [
                                            { label: __('مفكرة', 'greenergy'), value: 'clipboard-text.svg' },
                                            { label: __('مستخدمون', 'greenergy'), value: 'profile-2user.svg' },
                                            { label: __('وسام', 'greenergy'), value: 'medal.svg' },
                                            { label: __('موقع', 'greenergy'), value: 'location.svg' },
                                            { label: __('وقت', 'greenergy'), value: 'clock.svg' },
                                            { label: __('مبنى', 'greenergy'), value: 'building.svg' },
                                        ],
                                        onChange: (v) => {
                                            const newStats = [...attributes.stats];
                                            newStats[index] = { ...newStats[index], icon: v };
                                            setAttributes({ stats: newStats });
                                        }
                                    }),
                                createElement(Button, {
                                    isDestructive: true, isLink: true, onClick: () => {
                                        const newStats = [...attributes.stats];
                                        newStats.splice(index, 1);
                                        setAttributes({ stats: newStats });
                                    }
                                }, __('حذف الإحصائية', 'greenergy'))
                            )
                        )),
                        createElement(Button, {
                            isPrimary: true, style: { width: '100%', justifyContent: 'center' }, onClick: () => {
                                setAttributes({
                                    stats: [...(attributes.stats || []), { value: '0', label: 'توضيح', icon: 'clipboard-text.svg', mode: 'manual', dataSource: 'jobs_count' }]
                                });
                            }
                        }, __('إضافة إحصائية', 'greenergy'))
                    )
                );
            case 'greenergy/featured-news':
                return createElement(PanelBody, { title: __('إعدادات العرض', 'greenergy') },
                    createElement(SelectControl, {
                        label: __('نمط الاختيار', 'greenergy'),
                        value: attributes.selectionMode,
                        options: [
                            { label: __('الي', 'greenergy'), value: 'dynamic' },
                            { label: __('اختيار يدوي', 'greenergy'), value: 'manual' },
                        ],
                        onChange: (val) => updateAttribute('selectionMode', val)
                    }),
                    createElement(RangeControl, {
                        label: __('عدد الأخبار', 'greenergy'),
                        value: attributes.count,
                        onChange: (val) => updateAttribute('count', val),
                        min: 1
                    }),
                    attributes.selectionMode === 'manual' && createElement(GreenergyPostSelection, {
                        selectedPosts: attributes.selectedPosts,
                        onChange: (val) => updateAttribute('selectedPosts', val)
                    }),
                    attributes.selectionMode === 'dynamic' && createElement(GreenergyTermSelect, {
                        label: __('تصفية حسب التصنيف', 'greenergy'),
                        taxonomy: 'news_category',
                        selectedTermIds: attributes.queryCategories || [],
                        onChange: (val) => updateAttribute('queryCategories', val)
                    }),
                    attributes.selectionMode === 'dynamic' && createElement(SelectControl, {
                        label: __('ترتيب حسب', 'greenergy'),
                        value: attributes.orderBy,
                        options: [
                            { label: __('الاحدث', 'greenergy'), value: 'latest' },
                            { label: __('الاقدم', 'greenergy'), value: 'oldest' },
                            { label: __('الاكثر قراءة', 'greenergy'), value: 'popular' },
                            { label: __('حسب قسم "ترتيب حسب"', 'greenergy'), value: 'url_parameter' },
                        ],
                        onChange: (val) => updateAttribute('orderBy', val)
                    })
                );
            case 'greenergy/news-grid':
                 return createElement(PanelBody, { title: __('إعدادات العرض', 'greenergy') },
                    createElement(TextControl, { label: __('العنوان القسم', 'greenergy'), value: attributes.title, onChange: (val) => updateAttribute('title', val) }),
                    createElement(RangeControl, {
                        label: __('عدد الأخبار', 'greenergy'),
                        value: attributes.count,
                        onChange: (val) => updateAttribute('count', val),
                        min: 1, max: 20
                    }),
                );
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
             case 'greenergy/header':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('إعدادات عامة', 'greenergy') },
                        createElement(GreenergyImageControl, {
                            label: __('الشعار', 'greenergy'),
                            imageUrl: attributes.logoUrl,
                            imageId: attributes.logoId,
                            onSelect: (media) => setAttributes({ logoId: media.id, logoUrl: media.url }),
                            onSelectURL: (val) => updateAttribute('logoUrl', val)
                        }),
                        createElement(TextControl, {
                             label: __('نص تابعنا', 'greenergy'),
                             value: attributes.followUsText,
                             onChange: (val) => updateAttribute('followUsText', val)
                        }),
                        createElement(TextControl, {
                             label: __('تسمية البحث', 'greenergy'),
                             value: attributes.searchLabel,
                             onChange: (val) => updateAttribute('searchLabel', val)
                        }),
                        createElement(TextControl, {
                             label: __('نص عنصر نائب البحث', 'greenergy'),
                             value: attributes.searchPlaceholder,
                             onChange: (val) => updateAttribute('searchPlaceholder', val)
                        }),
                        createElement(TextControl, {
                             label: __('تسمية اللغة', 'greenergy'),
                             value: attributes.langLabel,
                             onChange: (val) => updateAttribute('langLabel', val)
                        })
                    ),
                    createElement(PanelBody, { title: __('الإعلانات', 'greenergy'), initialOpen: false },
                        createElement('div', { style: { fontWeight: 'bold' } }, __('الإعلان الأيمن', 'greenergy')),
                        createElement(SelectControl, {
                            label: __('نوع الإعلان', 'greenergy'),
                            value: attributes.adRight?.adType || 'image',
                            options: [ { label: 'صورة', value: 'image' }, { label: 'كود', value: 'code' } ],
                            onChange: (val) => updateAttribute('adRight', { ...attributes.adRight, adType: val })
                        }),
                        (attributes.adRight?.adType === 'image' || !attributes.adRight?.adType) && createElement(Fragment, null,
                            createElement(GreenergyImageControl, {
                                imageUrl: attributes.adRight?.imageUrl,
                                onSelect: (media) => updateAttribute('adRight', { ...attributes.adRight, imageUrl: media.url }),
                            }),
                            createElement(TextControl, { label: 'رابط الإعلان', value: attributes.adRight?.adLink, onChange: (val) => updateAttribute('adRight', { ...attributes.adRight, adLink: val }) })
                        ),
                        attributes.adRight?.adType === 'code' && createElement(TextareaControl, { label: 'كود الإعلان', value: attributes.adRight?.adCode, onChange: (val) => updateAttribute('adRight', { ...attributes.adRight, adCode: val }) }),
                        
                        createElement('hr', {}),

                        createElement('div', { style: { fontWeight: 'bold' } }, __('الإعلان الأيسر', 'greenergy')),
                        createElement(SelectControl, {
                            label: __('نوع الإعلان', 'greenergy'),
                            value: attributes.adLeft?.adType || 'image',
                            options: [ { label: 'صورة', value: 'image' }, { label: 'كود', value: 'code' } ],
                            onChange: (val) => updateAttribute('adLeft', { ...attributes.adLeft, adType: val })
                        }),
                        (attributes.adLeft?.adType === 'image' || !attributes.adLeft?.adType) && createElement(Fragment, null,
                            createElement(GreenergyImageControl, {
                                imageUrl: attributes.adLeft?.imageUrl,
                                onSelect: (media) => updateAttribute('adLeft', { ...attributes.adLeft, imageUrl: media.url }),
                            }),
                            createElement(TextControl, { label: 'رابط الإعلان', value: attributes.adLeft?.adLink, onChange: (val) => updateAttribute('adLeft', { ...attributes.adLeft, adLink: val }) })
                        ),
                        attributes.adLeft?.adType === 'code' && createElement(TextareaControl, { label: 'كود الإعلان', value: attributes.adLeft?.adCode, onChange: (val) => updateAttribute('adLeft', { ...attributes.adLeft, adCode: val }) })
                    ),
                    createElement(PanelBody, { title: __('القائمة والروابط', 'greenergy'), initialOpen: false },
                        createElement(GreenergyMenuSelect, {
                            label: __('القائمة الرئيسية (موبايل + سطح مكتب)', 'greenergy'),
                            value: attributes.menuId,
                            onChange: (val) => updateAttribute('menuId', val)
                        }),
                        createElement(TextControl, {
                            label: __('تسمية الزر الرئيسي (مثلاً: الرئيسة)', 'greenergy'),
                            value: attributes.homeLabel,
                            onChange: (val) => updateAttribute('homeLabel', val)
                        }),
                        createElement(GreenergyNavLinksControl, {
                            label: __('روابط إضافية', 'greenergy'),
                            links: attributes.navLinks,
                            onChange: (val) => updateAttribute('navLinks', val)
                        })
                    ),
                    createElement(GreenergySocialLinksControl, {
                        label: __('روابط التواصل (للموبايل)', 'greenergy'),
                        links: attributes.socialLinks,
                        onChange: (val) => updateAttribute('socialLinks', val)
                    })
                );

            case 'greenergy/footer-widgets':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('إعدادات عامة', 'greenergy') },
                        createElement(GreenergyImageControl, {
                            label: __('الشعار (فوتر)', 'greenergy'),
                            imageUrl: attributes.logoUrl,
                            onSelect: (media) => updateAttribute('logoUrl', media.url),
                            onSelectURL: (val) => updateAttribute('logoUrl', val)
                        }),
                        createElement(TextareaControl, {
                            label: __('الوصف', 'greenergy'),
                            value: attributes.description,
                            onChange: (val) => updateAttribute('description', val)
                        }),
                    ),
                    createElement(PanelBody, { title: __('العمود الأول', 'greenergy'), initialOpen: false },
                        createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.col1Title, onChange: (val) => updateAttribute('col1Title', val) }),
                        createElement(GreenergyNavLinksControl, { label: __('الروابط', 'greenergy'), links: attributes.col1Links, onChange: (val) => updateAttribute('col1Links', val) })
                    ),
                    createElement(PanelBody, { title: __('العمود الثاني', 'greenergy'), initialOpen: false },
                        createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.col2Title, onChange: (val) => updateAttribute('col2Title', val) }),
                        createElement(GreenergyNavLinksControl, { label: __('الروابط', 'greenergy'), links: attributes.col2Links, onChange: (val) => updateAttribute('col2Links', val) })
                    ),
                    createElement(PanelBody, { title: __('العمود الثالث', 'greenergy'), initialOpen: false },
                        createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.col3Title, onChange: (val) => updateAttribute('col3Title', val) }),
                        createElement(GreenergyNavLinksControl, { label: __('الروابط', 'greenergy'), links: attributes.col3Links, onChange: (val) => updateAttribute('col3Links', val) })
                    ),
                    createElement(PanelBody, { title: __('العمود الرابع', 'greenergy'), initialOpen: false },
                        createElement(TextControl, { label: __('العنوان', 'greenergy'), value: attributes.col4Title, onChange: (val) => updateAttribute('col4Title', val) }),
                        createElement(GreenergyNavLinksControl, { label: __('الروابط', 'greenergy'), links: attributes.col4Links, onChange: (val) => updateAttribute('col4Links', val) })
                    )
                );
            case 'greenergy/footer-copyright':
                return createElement(Fragment, null,
                    createElement(PanelBody, { title: __('نصوص الحقوق', 'greenergy') },
                        createElement(TextControl, {
                            label: __('نص الحقوق (استخدم {year} للسنة)', 'greenergy'),
                            value: attributes.copyrightText,
                            onChange: (val) => updateAttribute('copyrightText', val)
                        }),
                        createElement(TextControl, {
                            label: __('نص تابعنا', 'greenergy'),
                            value: attributes.followUsText,
                            onChange: (val) => updateAttribute('followUsText', val)
                        }),
                        createElement(RangeControl, {
                            label: __('حجم الأيقونات (px)', 'greenergy'),
                            value: attributes.iconSize,
                            onChange: (val) => updateAttribute('iconSize', val),
                            min: 20,
                            max: 100
                        }),
                        createElement(RangeControl, {
                            label: __('المسافة بين الأيقونات (px)', 'greenergy'),
                            value: attributes.iconGap,
                            onChange: (val) => updateAttribute('iconGap', val),
                            min: 0,
                            max: 100
                        }),
                    ),
                    createElement(GreenergySocialLinksControl, {
                        label: __('روابط التواصل الاجتماعي', 'greenergy'),
                        links: attributes.socialLinks,
                        onChange: (val) => updateAttribute('socialLinks', val)
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
    { 
        name: 'most-read-news', 
        title: __('الأكثر قراءة', 'greenergy'), 
        icon: 'megaphone',
        attributes: {
            badgeText: { type: 'string', default: 'الأكثر قراءة' },
            description: { type: 'string', default: 'استكشف أبرز الأخبار التي حظيت باهتمام القراء في عالم الطاقة المتجددة، من المشاريع العملاقة إلى أحدث الابتكارات البيئية.' },
            buttonText: { type: 'string', default: 'عرض جميع الاخبار' },
            selectionMode: { type: 'string', default: 'auto' },
            leftCount: { type: 'number', default: 3 },
            rightCount: { type: 'number', default: 3 },
            centerTopCount: { type: 'number', default: 2 },
            centerBottomCount: { type: 'number', default: 2 },
            selectedPostsLeft: { type: 'array', default: [] },
            selectedPostsRight: { type: 'array', default: [] },
            selectedPostsCenterTop: { type: 'array', default: [] },
            selectedPostsCenterBottom: { type: 'array', default: [] }
        }
    },
    { name: 'stats', title: __('إحصائيات عالمية', 'greenergy'), icon: 'chart-area' },
    // news page blocks
    { name: 'news-filter', title: __('تصفية الأخبار', 'greenergy'), icon: 'filter' },
    { 
        name: 'featured-news', 
        title: __('أخبار مميزة', 'greenergy'), 
        icon: 'cover-image',
        attributes: {
            count: { type: 'number', default: 1 },
            selectionMode: { type: 'string', default: 'dynamic' },
            selectedPosts: { type: 'array', default: [] },
            queryCategories: { type: 'array', default: [] },
            orderBy: { type: 'string', default: 'latest' }
        }
    },
    { 
        name: 'news-list', 
        title: __('قائمة الأخبار', 'greenergy'), 
        icon: 'list-view',
        attributes: {
            count: { type: 'number', default: 5 },
            offset: { type: 'number', default: 0 },
            title: { type: 'string', default: 'آخر الأخبار' },
            queryCategories: { type: 'array', default: [] }
        }
    },
    { 
        name: 'news-grid', 
        title: __('شبكة الأخبار', 'greenergy'), 
        icon: 'grid-view',
        attributes: {
            count: { type: 'number', default: 3 },
            title: { type: 'string', default: 'اخبار اخرى' },
            queryCategories: { type: 'array', default: [] }
        }
    },
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
    // header blocks
    { 
        name: 'header', 
        title: __('هيدر - موحد', 'greenergy'), 
        icon: 'layout',
        attributes: {
            logoUrl: { type: 'string', default: '' },
            logoId: { type: 'number', default: 0 },
            adLeft: { type: 'object', default: { imageUrl: '', adLink: '#', adType: 'image', adCode: '' } },
            adRight: { type: 'object', default: { imageUrl: '', adLink: '#', adType: 'image', adCode: '' } },
            menuId: { type: 'number', default: 0 },
            homeLabel: { type: 'string', default: 'الرئيسية' },
            searchLabel: { type: 'string', default: 'ابحث هنا...' },
            searchPlaceholder: { type: 'string', default: 'بحث عن...' },
            followUsText: { type: 'string', default: 'تابعنا' },
            langLabel: { type: 'string', default: 'العربية - AR' },
            navLinks: { type: 'array', default: [] },
            socialLinks: { 
                type: 'array', 
                default: [
                    { platform: 'Facebook', url: '#', icon: 'facebook.svg', iconType: 'system' },
                    { platform: 'Youtube', url: '#', icon: 'youtube.svg', iconType: 'system' },
                    { platform: 'LinkedIn', url: '#', icon: 'ic16-linkedin.svg', iconType: 'system' },
                    { platform: 'Twitter', url: '#', icon: 'twitter_icon.svg', iconType: 'system' }
                ] 
            }
        }
    },

    // footer blocks
    { 
        name: 'footer-widgets', 
        title: __('فوتر - ويدجت', 'greenergy'), 
        icon: 'layout',
        attributes: {
            logoUrl: { type: 'string', default: '' },
            description: { 
                type: 'string', 
                default: __('منصة Greenergy الرائدة في مجال الطاقة المتجددة والاستدامة، نقدم المحتوى التعليمي والفرص الوظيفية ودليل الخبراء في قطاع الطاقة النظيفة.', 'greenergy') 
            },
            col1Title: { type: 'string', default: __('عن الشركة', 'greenergy') },
            col1Links: { 
                type: 'array', 
                default: [
                    { label: __('من نحن', 'greenergy'), url: '#' },
                    { label: __('فريق العمل', 'greenergy'), url: '#' },
                    { label: __('الشركات', 'greenergy'), url: '#' },
                    { label: __('تواصل معنا', 'greenergy'), url: '#' }
                ] 
            },
            col2Title: { type: 'string', default: __('خدماتنا', 'greenergy') },
            col2Links: { 
                type: 'array', 
                default: [
                    { label: __('الأخبار والمقالات', 'greenergy'), url: '#' },
                    { label: __('الدورات التدريبية', 'greenergy'), url: '#' },
                    { label: __('فرص العمل', 'greenergy'), url: '#' },
                    { label: __('دليل الشركات', 'greenergy'), url: '#' }
                ] 
            },
            col3Title: { type: 'string', default: __('أنواع الطاقة', 'greenergy') },
            col3Links: { 
                type: 'array', 
                default: [
                    { label: __('الطاقة الشمسية', 'greenergy'), url: '#' },
                    { label: __('طاقة الرياح', 'greenergy'), url: '#' },
                    { label: __('الطاقة المائية', 'greenergy'), url: '#' },
                    { label: __('الطاقة الحيوية', 'greenergy'), url: '#' }
                ] 
            },
            col4Title: { type: 'string', default: __('الموارد', 'greenergy') },
            col4Links: { 
                type: 'array', 
                default: [
                    { label: __('مكتبة الوسائط', 'greenergy'), url: '#' },
                    { label: __('المقالات', 'greenergy'), url: '#' },
                    { label: __('الدراسات', 'greenergy'), url: '#' },
                    { label: __('الأسئلة الشائعة', 'greenergy'), url: '#' }
                ] 
            }
        }
    },
    { 
        name: 'footer-copyright', 
        title: __('فوتر - حقوق النشر', 'greenergy'), 
        icon: 'text',
        attributes: {
            copyrightText: { 
                type: 'string', 
                default: __('كل الحقوق محفوظة لدى © Greenergy {year}', 'greenergy') 
            },
            followUsText: { type: 'string', default: __('تابعنا', 'greenergy') },
            socialLinks: { 
                type: 'array', 
                default: [
                    { platform: 'Facebook', url: '#', icon: 'facebook.svg', iconType: 'system' },
                    { platform: 'Youtube', url: '#', icon: 'youtube.svg', iconType: 'system' },
                    { platform: 'LinkedIn', url: '#', icon: 'ic16-linkedin.svg', iconType: 'system' },
                    { platform: 'Twitter', url: '#', icon: 'twitter_icon.svg', iconType: 'system' }
                ] 
            },
            iconSize: { type: 'number', default: 40 },
            iconGap: { type: 'number', default: 20 }
        }
    },
];

blocks.forEach(b => {
    try {
        const registrationArgs = {
            title: b.title,
            icon: b.icon,
            category: 'greenergy-blocks',
            edit: (props) => GreenergyBlockEdit({ ...props, name: `greenergy/${b.name}` }),
            save: () => null,
        };

        if (b.attributes) {
            registrationArgs.attributes = b.attributes;
        }

        registerBlockType(`greenergy/${b.name}`, registrationArgs);
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
                )
            ),
            createElement('div', { ...blockProps },
                createElement(ServerSideRender, { block: "greenergy/ad-block", attributes: attributes })
            )
        );
    },
    save: () => null,
});


/**
 * Job Section Block
 */
/**
 * Job List Item (Helper Block for Individual Icons)
 */
registerBlockType('greenergy/job-list-item', {
    title: __('عنصر قائمة وظيفة', 'greenergy'),
    icon: 'list-view',
    category: 'greenergy-blocks',
    attributes: {
        content: { type: 'string', source: 'html', selector: '.item-text' },
        icon: { type: 'string', default: 'certificate' }
    },
    edit: (props) => {
        const { attributes, setAttributes } = props;
        const blockProps = useBlockProps({
            className: `job-list-item-edit icon-type-${attributes.icon} flex items-center gap-3 mb-2 py-1`
        });

        return createElement(Fragment, null,
            createElement(InspectorControls, null,
                createElement(PanelBody, { title: __('إعدادات العنصر', 'greenergy') },
                    createElement(SelectControl, {
                        label: __('الأيقونة', 'greenergy'),
                        value: attributes.icon,
                        options: [
                            { label: __('شهادة', 'greenergy'), value: 'certificate' },
                            { label: __('تصميم', 'greenergy'), value: 'design' },
                            { label: __('تطوير', 'greenergy'), value: 'development' },
                            { label: __('سيارة', 'greenergy'), value: 'car' },
                            { label: __('تأمين', 'greenergy'), value: 'insurance' },
                            { label: __('هدية', 'greenergy'), value: 'gift' },
                            { label: __('صح', 'greenergy'), value: 'check' },
                            { label: __('نجمة', 'greenergy'), value: 'star' },
                        ],
                        onChange: (val) => setAttributes({ icon: val })
                    })
                )
            ),
            createElement('div', { ...blockProps },
                createElement('div', { 
                    className: 'item-icon-preview w-8 h-8 flex items-center justify-center bg-stone-100 rounded text-primary-600',
                    style: { flexShrink: 0 }
                }),
                createElement(RichText, {
                    tagName: 'span',
                    className: 'item-text flex-1 border-b border-transparent focus:border-stone-200 transition-all',
                    value: attributes.content,
                    onChange: (val) => setAttributes({ content: val }),
                    placeholder: __('اكتب هنا...', 'greenergy')
                })
            )
        );
    },
    save: (props) => {
        return createElement('li', { className: `icon-type-${props.attributes.icon}` },
            createElement(RichText.Content, { tagName: 'span', className: 'item-text', value: props.attributes.content })
        );
    }
});

/**
 * Job Section Edit Component
 */
const JobSectionEdit = (props) => {
    const { attributes, setAttributes, clientId } = props;
    const { sectionType, listStyle, iconStrategy, iconType } = attributes;
    const { replaceInnerBlocks } = wp.data.dispatch('core/block-editor');
    const { createBlock } = wp.blocks;
    const [isMounted, setIsMounted] = useState(false);

    useEffect(() => {
        setIsMounted(true);
    }, []);

    // Monitor changes to switch InnerBlocks template while preserving content
    useEffect(() => {
        if (!isMounted) return;

        const timer = setTimeout(() => {
            const currentBlocks = wp.data.select('core/block-editor').getBlocks(clientId);
            const headingBlock = currentBlocks.find(b => b.name === 'core/heading');
            const hasList = currentBlocks.some(b => b.name === 'core/list');
            const hasParagraph = currentBlocks.some(b => b.name === 'core/paragraph');
            
            // Extract existing heading content
            const headingContent = headingBlock ? headingBlock.attributes.content : '';
            
            const blocks = [];
            
            // 1. Always keep/create heading with preserved content
            blocks.push(createBlock('core/heading', { 
                level: 3, 
                content: headingContent,
                placeholder: sectionType === 'list' ? __('عنوان القائمة', 'greenergy') : __('عنوان القسم', 'greenergy'),
                className: 'text-2xl font-bold mb-6'
            }));

            // 2. Decide what the secondary block should be
            if (sectionType === 'list') {
                if (iconStrategy === 'individual') {
                    blocks.push(createBlock('core/list', {}, [
                        createBlock('greenergy/job-list-item', { content: __('عنصر جديد', 'greenergy') })
                    ]));
                } else {
                    const existingList = currentBlocks.find(b => b.name === 'core/list');
                    const isOrdered = listStyle === 'numbers';
                    
                    if (existingList) {
                        blocks.push(createBlock('core/list', {
                            ...existingList.attributes,
                            ordered: isOrdered
                        }, existingList.innerBlocks));
                    } else {
                        blocks.push(createBlock('core/list', { 
                            placeholder: __('أضف قائمة...', 'greenergy'),
                            ordered: isOrdered
                        }));
                    }
                }
            } else {
                const existingPara = currentBlocks.find(b => b.name === 'core/paragraph');
                blocks.push(createBlock('core/paragraph', { 
                    content: existingPara ? existingPara.attributes.content : '',
                    placeholder: __('اكتب المحتوى هنا...', 'greenergy'),
                    className: 'text-stone-500'
                }));
            }

            // Only replace if structure or critical attributes changed
            const currentNames = currentBlocks.map(b => b.name).join(',');
            const newNames = blocks.map(b => b.name).join(',');
            
            const existingList = currentBlocks.find(b => b.name === 'core/list');
            const listTypeChanged = existingList && existingList.attributes.ordered !== (listStyle === 'numbers');
            
            if (currentNames !== newNames || listTypeChanged || (sectionType === 'list' && iconStrategy === 'individual')) {
                replaceInnerBlocks(clientId, blocks);
            }
        }, 50);
        return () => clearTimeout(timer);
    }, [sectionType, iconStrategy, listStyle]); // Depend on type to trigger replacement

    const blockProps = useBlockProps({
        className: `job-section-edit ${sectionType} list-style-${listStyle} icon-strategy-${iconStrategy} icon-type-${iconType}`
    });

    const TEMPLATES = {
        'paragraph': [
            ['core/heading', { level: 3, placeholder: __('عنوان القسم', 'greenergy'), className: 'text-2xl font-bold mb-6' }],
            ['core/paragraph', { placeholder: __('اكتب المحتوى هنا...', 'greenergy'), className: 'text-stone-500' }]
        ],
        'list': [
            ['core/heading', { level: 3, placeholder: __('عنوان القائمة', 'greenergy'), className: 'text-2xl font-bold mb-6' }],
            (iconStrategy === 'individual') 
                ? ['core/list', { placeholder: __('أضف قائمة...', 'greenergy') }, [
                    ['greenergy/job-list-item', { content: __('عنصر جديد', 'greenergy') }]
                ]]
                : ['core/list', { placeholder: __('أضف قائمة نقاط...', 'greenergy') }]
        ]
    };

    const ALLOWED_BLOCKS = ['core/heading', 'core/paragraph', 'core/list', 'greenergy/job-list-item'];

    return createElement(Fragment, null,
        createElement(InspectorControls, null,
            createElement(PanelBody, { title: __('إعدادات القسم', 'greenergy') },
                createElement(SelectControl, {
                    label: __('نوع القسم', 'greenergy'),
                    value: sectionType,
                    options: [
                        { label: __('فقرة', 'greenergy'), value: 'paragraph' },
                        { label: __('نقاط / قائمة', 'greenergy'), value: 'list' },
                    ],
                    onChange: (val) => setAttributes({ sectionType: val })
                }),
                (sectionType === 'list') && createElement(SelectControl, {
                    label: __('نوع النقاط', 'greenergy'),
                    value: listStyle,
                    options: [
                        { label: __('نقاط', 'greenergy'), value: 'bullets' },
                        { label: __('أرقام', 'greenergy'), value: 'numbers' },
                        { label: __('أيقونات', 'greenergy'), value: 'icons' },
                    ],
                    onChange: (val) => setAttributes({ listStyle: val })
                }),
                (sectionType === 'list' && listStyle === 'icons') && createElement(SelectControl, {
                    label: __('آلية الأيقونات', 'greenergy'),
                    value: iconStrategy,
                    options: [
                        { label: __('أيقونة موحدة للجميع', 'greenergy'), value: 'uniform' },
                        { label: __('أيقونة مخصصة لكل عنصر', 'greenergy'), value: 'individual' },
                    ],
                    onChange: (val) => setAttributes({ iconStrategy: val })
                }),
                (sectionType === 'list' && listStyle === 'icons' && iconStrategy === 'uniform') && createElement(SelectControl, {
                    label: __('الأيقونة الموحدة', 'greenergy'),
                    value: iconType,
                    options: [
                        { label: __('شهادة', 'greenergy'), value: 'certificate' },
                        { label: __('تصميم', 'greenergy'), value: 'design' },
                        { label: __('تطوير', 'greenergy'), value: 'development' },
                        { label: __('سيارة', 'greenergy'), value: 'car' },
                        { label: __('تأمين', 'greenergy'), value: 'insurance' },
                        { label: __('هدية', 'greenergy'), value: 'gift' },
                        { label: __('صح', 'greenergy'), value: 'check' },
                        { label: __('نجمة', 'greenergy'), value: 'star' },
                    ],
                    onChange: (val) => setAttributes({ iconType: val })
                })
            ),

            // Manage List Items Panel
            (sectionType === 'list') && createElement(PanelBody, { title: __('إدارة عناصر القائمة', 'greenergy'), initialOpen: true },
                createElement(Button, {
                    isPrimary: true,
                    isLarge: true,
                    className: 'w-full justify-center mb-2',
                    icon: 'plus',
                    onClick: () => {
                        const currentBlocks = wp.data.select('core/block-editor').getBlocks(clientId);
                        const listBlock = currentBlocks.find(b => b.name === 'core/list');
                        if (listBlock) {
                            const newItemType = iconStrategy === 'individual' ? 'greenergy/job-list-item' : 'core/list-item';
                            const newItem = createBlock(newItemType, { content: '' });
                            wp.data.dispatch('core/block-editor').insertBlock(newItem, undefined, listBlock.clientId);
                        }
                    }
                }, __('إضافة عنصر جديد', 'greenergy')),
                createElement('p', { className: 'text-stone-500 text-xs italic' }, __('يمكنك إضافة عناصر جديدة للقائمة من هنا بدلاً من استخدام المحرر.', 'greenergy'))
            ),

            // Quick Add Sections Panel
            createElement(PanelBody, { title: __('إضافة أقسام سريعة', 'greenergy'), initialOpen: false },
                createElement('div', { className: 'grid grid-cols-1 gap-2' },
                    [
                        { label: __('إضافة قسم مسؤوليات', 'greenergy'), type: 'list', title: 'المسؤوليات الرئيسية', style: 'bullets' },
                        { label: __('إضافة قسم مؤهلات', 'greenergy'), type: 'list', title: 'المؤهلات والمتطلبات', style: 'icons', strategy: 'uniform', icon: 'check' },
                        { label: __('إضافة قسم مزايا', 'greenergy'), type: 'list', title: 'المزايا والامتيازات', style: 'icons', strategy: 'uniform', icon: 'gift' }
                    ].map((btn, idx) => (
                        createElement(Button, {
                            key: idx,
                            isSecondary: true,
                            className: 'justify-start',
                            icon: 'insert-after',
                            onClick: () => {
                                const newSection = createBlock('greenergy/job-section', {
                                    sectionType: btn.type,
                                    listStyle: btn.style || 'bullets',
                                    iconStrategy: btn.strategy || 'uniform',
                                    iconType: btn.icon || 'certificate'
                                }, [
                                    createBlock('core/heading', { level: 3, content: btn.title, className: 'text-2xl font-bold mb-6' }),
                                    btn.type === 'list' 
                                        ? createBlock('core/list', {}, [ 
                                            createBlock(btn.strategy === 'individual' ? 'greenergy/job-list-item' : 'core/list-item', { content: '' }) 
                                        ])
                                        : createBlock('core/paragraph', { placeholder: __('اكتب هنا...', 'greenergy') })
                                ]);
                                const currentIndex = wp.data.select('core/block-editor').getBlockIndex(clientId);
                                wp.data.dispatch('core/block-editor').insertBlock(newSection, currentIndex + 1);
                            }
                        }, btn.label)
                    ))
                )
            )
        ),
        createElement('div', { ...blockProps },
            createElement(InnerBlocks, {
                template: TEMPLATES[sectionType] || TEMPLATES['paragraph'],
                allowedBlocks: ALLOWED_BLOCKS,
                templateLock: false,
                renderAppender: false // Hide the default block appender inside the section
            })
        )
    );
};

registerBlockType('greenergy/job-section', {
    title: __('قسم الوظيفة', 'greenergy'),
    icon: 'layout',
    category: 'greenergy-blocks',
    attributes: {
        sectionType: { type: 'string', default: 'paragraph' },
        listStyle: { type: 'string', default: 'bullets' },
        iconStrategy: { type: 'string', default: 'uniform' },
        iconType: { type: 'string', default: 'certificate' }
    },
    edit: JobSectionEdit,
    save: () => createElement(InnerBlocks.Content),
});

registerBlockType('greenergy/all-jobs', {
    edit: GreenergyBlockEdit,
    save: () => null,
});
