const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { 
    Button, 
    PanelBody, 
    TextControl, 
    SelectControl, 
    CheckboxControl,
    BaseControl,
    ToggleControl
} = wp.components;


const Edit = ({ attributes, setAttributes }) => {
    const { 
        bannerType, 
        bannerImage, 
        bannerVideo, 
        bannerTitle, 
        showBannerTitle = true,
        bannerSubtitle,
        showBannerSubtitle = true,
        shareProviders 
    } = attributes;

    const onSelectImage = (media) => setAttributes({ bannerImage: media.url });
    const onSelectVideo = (media) => setAttributes({ bannerVideo: media.url });

    const shareOptions = [
        { label: 'واتساب', value: 'whatsapp' },
        { label: 'تيليجرام', value: 'telegram' },
        { label: 'فيسبوك', value: 'facebook' },
        { label: 'إنستغرام', value: 'instagram' },
        { label: 'يوتيوب', value: 'youtube' },
        { label: 'RSS', value: 'rss' },
        { label: 'نسخ الرابط', value: 'copy' },
    ];

    const updateShareProviders = (checked, value) => {
        let newProviders = [...(shareProviders || [])];
        if (checked) {
            if (!newProviders.includes(value)) newProviders.push(value);
        } else {
            newProviders = newProviders.filter(p => p !== value);
        }
        setAttributes({ shareProviders: newProviders });
    };

    return (
        <div className="greenergy-news-settings-block" style={{ padding: '20px', background: '#fff', border: '1px solid #ccc' }}>
            <h2 style={{ borderBottom: '1px solid #eee', paddingBottom: '10px', marginBottom: '20px' }}>{__('إعدادات الأخبار', 'greenergy')}</h2>
            
            <PanelBody title={__('إعدادات اللافتة العامة', 'greenergy')} initialOpen={true}>
                <SelectControl
                    label={__('نوع الخلفية', 'greenergy')}
                    value={bannerType}
                    options={[
                        { label: 'صورة', value: 'image' },
                        { label: 'فيديو', value: 'video' },
                    ]}
                    onChange={(value) => setAttributes({ bannerType: value })}
                />

                {bannerType === 'image' && (
                    <BaseControl label={__('صورة الخلفية', 'greenergy')}>
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={onSelectImage}
                                allowedTypes={['image']}
                                value={bannerImage}
                                render={({ open }) => (
                                    <>
                                        {bannerImage && <img src={bannerImage} style={{ maxWidth: '100%', marginBottom: '10px', display: 'block' }} alt="Banner" />}
                                        <Button variant="secondary" onClick={open} style={{ marginBottom: '10px' }}>
                                            {bannerImage ? __('تغيير الصورة', 'greenergy') : __('رفع صورة', 'greenergy')}
                                        </Button>
                                    </>
                                )}
                            />
                        </MediaUploadCheck>
                        <TextControl
                            label={__('أو أدخل رابط الصورة', 'greenergy')}
                            value={bannerImage}
                            onChange={(value) => setAttributes({ bannerImage: value })}
                            help={__('يمكنك إدخال رابط خارجي للصورة مباشرة', 'greenergy')}
                        />
                    </BaseControl>
                )}

                {bannerType === 'video' && (
                    <BaseControl label={__('فيديو الخلفية', 'greenergy')}>
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={onSelectVideo}
                                allowedTypes={['video']}
                                value={bannerVideo}
                                render={({ open }) => (
                                    <>
                                        {bannerVideo && <video src={bannerVideo} style={{ maxWidth: '100%', marginBottom: '10px', display: 'block' }} controls />}
                                        <Button variant="secondary" onClick={open} style={{ marginBottom: '10px' }}>
                                            {bannerVideo ? __('تغيير الفيديو', 'greenergy') : __('رفع فيديو', 'greenergy')}
                                        </Button>
                                    </>
                                )}
                            />
                        </MediaUploadCheck>
                         <TextControl
                            label={__('أو أدخل رابط الفيديو', 'greenergy')}
                            value={bannerVideo}
                            onChange={(value) => setAttributes({ bannerVideo: value })}
                            help={__('يمكنك إدخال رابط خارجي للفيديو (MP4) مباشرة', 'greenergy')}
                        />
                    </BaseControl>
                )}

                <div style={{ marginTop: '20px' }}>
                    <ToggleControl
                        label={__('عرض عنوان اللافتة', 'greenergy')}
                        checked={showBannerTitle}
                        onChange={(value) => setAttributes({ showBannerTitle: value })}
                        help={showBannerTitle ? __('سيظهر العنوان في اللافتة', 'greenergy') : __('سيتم إخفاء العنوان من اللافتة', 'greenergy')}
                    />

                    {showBannerTitle && (
                        <TextControl
                            label={__('عنوان اللافتة الافتراضي', 'greenergy')}
                            value={bannerTitle}
                            onChange={(value) => setAttributes({ bannerTitle: value })}
                            help={__('يستخدم كافتراضي إذا لم يتم تحديد عنوان للخبر', 'greenergy')}
                        />
                    )}

                    <div style={{ height: '10px' }}></div>

                    <ToggleControl
                        label={__('عرض العنوان الفرعي', 'greenergy')}
                        checked={showBannerSubtitle}
                        onChange={(value) => setAttributes({ showBannerSubtitle: value })}
                         help={showBannerSubtitle ? __('سيظهر العنوان الفرعي في اللافتة', 'greenergy') : __('سيتم إخفاء العنوان الفرعي من اللافتة', 'greenergy')}
                    />

                    {showBannerSubtitle && (
                        <TextControl
                            label={__('العنوان الفرعي الافتراضي', 'greenergy')}
                            value={bannerSubtitle}
                            onChange={(value) => setAttributes({ bannerSubtitle: value })}
                        />
                    )}
                </div>
            </PanelBody>

            <PanelBody title={__('أزرار المشاركة', 'greenergy')} initialOpen={false}>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '10px' }}>
                    {shareOptions.map(option => (
                        <CheckboxControl
                            key={option.value}
                            label={option.label}
                            checked={(shareProviders || []).includes(option.value)}
                            onChange={(checked) => updateShareProviders(checked, option.value)}
                        />
                    ))}
                </div>
            </PanelBody>
        </div>
    );
};

const registerNewsBlock = () => {
    // Hardcoded to match social-media-settings pattern
    const blockName = 'greenergy/news-settings'; 
    console.log('Greenergy Admin: Attempting to register block:', blockName);

    try {
        if (wp.blocks.getBlockType(blockName)) {
            console.log('Greenergy Admin: Block already registered, unregistering first.');
            wp.blocks.unregisterBlockType(blockName);
        }

        const result = registerBlockType(blockName, {
            title: __('إعدادات الأخبار', 'greenergy'),
            description: __('إعدادات اللافتة والمشاركة الخاصة بصفحة الأخبار.', 'greenergy'),
            category: 'theme',
            icon: 'format-aside',
            attributes: {
                bannerType: { type: 'string', default: 'image' },
                bannerImage: { type: 'string', default: '' },
                bannerVideo: { type: 'string', default: '' },
                bannerTitle: { type: 'string', default: '' },
                bannerSubtitle: { type: 'string', default: 'الأخبار' },
                shareProviders: { 
                    type: 'array', 
                    default: ["whatsapp", "telegram", "facebook", "instagram", "youtube", "rss", "copy"] 
                }
            },
            edit: Edit,
            save: () => null,
        });
        
        console.log('Greenergy Admin: News Settings Block registered result:', result ? 'SUCCESS' : 'FAILED');
    } catch (e) {
        console.error('Greenergy Admin: News Settings Block registration EXCEPTION:', e);
    }
};

// Register immediately if Gutenberg is ready, otherwise wait
if (wp && wp.blocks && wp.blocks.registerBlockType) {
    registerNewsBlock();
} else if (wp.domReady) {
    wp.domReady(registerNewsBlock);
} else {
    document.addEventListener('DOMContentLoaded', registerNewsBlock);
}
