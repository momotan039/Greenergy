const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { Button, TextControl } = wp.components;

// Styles are bundled in admin.css
import metadata from './block.json';

console.log('Greenergy Admin: Social Media Block JS executed.');

const registerSocialBlock = () => {
    const blockName = 'greenergy/social-media-settings';

    // Helper to get FontAwesome icon class based on platform name
    const getPlatformIcon = (platform) => {
        const p = platform.toLowerCase().trim();
        if (p.includes('twitter') || p.includes(' x')) return 'fab fa-twitter';
        if (p.includes('facebook')) return 'fab fa-facebook-f';
        if (p.includes('instagram')) return 'fab fa-instagram';
        if (p.includes('linkedin')) return 'fab fa-linkedin-in';
        if (p.includes('youtube')) return 'fab fa-youtube';
        if (p.includes('whatsapp')) return 'fab fa-whatsapp';
        if (p.includes('tiktok')) return 'fab fa-tiktok';
        if (p.includes('snapchat')) return 'fab fa-snapchat-ghost';
        if (p.includes('telegram')) return 'fab fa-telegram-plane';
        return 'fas fa-link';
    };

    try {
        console.log('Greenergy Admin: Attempting to register block:', blockName);

        // Unregister first if already exists to avoid conflict during hot-reloads/debug
        if (wp.blocks.getBlockType(blockName)) {
            console.log('Greenergy Admin: Block already registered, unregistering first.');
            wp.blocks.unregisterBlockType(blockName);
        }

        const result = registerBlockType(blockName, {
            title: __('Social Media Settings', 'greenergy'),
            icon: 'share',
            category: 'theme',
            attributes: {
                items: {
                    type: 'array',
                    default: []
                }
            },
            edit: ({ attributes, setAttributes }) => {
                const { items } = attributes;

                const updateItem = (index, key, value) => {
                    const newItems = [...items];
                    newItems[index][key] = value;
                    setAttributes({ items: newItems });
                };

                const addItem = () => {
                    setAttributes({
                        items: [
                            ...items,
                            { platform: '', url: '', icon: '', iconId: 0 }
                        ]
                    });
                };

                const removeItem = (index) => {
                    const newItems = items.filter((_, i) => i !== index);
                    setAttributes({ items: newItems });
                };

                return (
                    <div {...useBlockProps({ className: 'greenergy-card-block' })}>
                        <div className="greenergy-block-header">
                            <h2>{__('Social Media Links', 'greenergy')}</h2>
                            <p>{__('Add links to your social media profiles. These will appear in the header and footer.', 'greenergy')}</p>
                        </div>

                        <div className="greenergy-social-list">
                            {items.map((item, index) => (
                                <div key={index} className="greenergy-social-item" style={{ border: '1px solid #e2e8f0', padding: '15px', borderRadius: '8px', marginBottom: '15px', background: '#f8fafc' }}>
                                    <div className="social-item-header" style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '10px' }}>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                                            <span className="item-count" style={{ fontWeight: 'bold', color: '#64748b' }}>#{index + 1}</span>
                                            <div style={{ width: '32px', height: '32px', display: 'flex', alignItems: 'center', justifyContent: 'center', background: '#fff', border: '1px solid #cbd5e1', borderRadius: '4px' }}>
                                                {item.icon ? (
                                                    <img src={item.icon} alt="icon" style={{ width: '20px', height: '20px', objectFit: 'contain' }} />
                                                ) : (
                                                    <i className={getPlatformIcon(item.platform)} style={{ fontSize: '18px', color: '#1e293b' }}></i>
                                                )}
                                            </div>
                                            <span style={{ fontWeight: '600' }}>{item.platform || __('New Platform', 'greenergy')}</span>
                                        </div>
                                        <Button
                                            isDestructive
                                            isSmall
                                            variant="tertiary"
                                            onClick={() => removeItem(index)}
                                            icon="trash"
                                            label={__('Remove', 'greenergy')}
                                        />
                                    </div>

                                    <div className="social-item-fields" style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px' }}>
                                        <TextControl
                                            label={__('Platform Name', 'greenergy')}
                                            value={item.platform}
                                            onChange={(val) => updateItem(index, 'platform', val)}
                                            placeholder={__('Example: Twitter', 'greenergy')}
                                        />

                                        <TextControl
                                            label={__('URL', 'greenergy')}
                                            value={item.url}
                                            onChange={(val) => updateItem(index, 'url', val)}
                                            placeholder="https://"
                                        />
                                    </div>

                                    <div className="social-icon-picker" style={{ marginTop: '10px' }}>
                                        <MediaUploadCheck>
                                            <MediaUpload
                                                onSelect={(media) => {
                                                    updateItem(index, 'icon', media.url);
                                                    updateItem(index, 'iconId', media.id);
                                                }}
                                                allowedTypes={['image']}
                                                render={({ open }) => (
                                                    <Button variant="secondary" onClick={open} style={{ width: '100%' }}>
                                                        {item.icon ? __('Change Custom Icon', 'greenergy') : __('Upload Custom Icon (Optional)', 'greenergy')}
                                                    </Button>
                                                )}
                                            />
                                            {item.icon && (
                                                <Button
                                                    isDestructive
                                                    isLink
                                                    onClick={() => {
                                                        updateItem(index, 'icon', '');
                                                        updateItem(index, 'iconId', 0);
                                                    }}
                                                    style={{ marginTop: '5px' }}
                                                >
                                                    {__('Remove Custom Icon (use default)', 'greenergy')}
                                                </Button>
                                            )}
                                        </MediaUploadCheck>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="add-item-wrapper" style={{ marginTop: '20px' }}>
                            <Button variant="primary" icon="plus" onClick={addItem} style={{ width: '100%', height: '40px', justifyContent: 'center' }}>
                                {__('Add New Platform', 'greenergy')}
                            </Button>
                        </div>
                    </div>
                );
            },
            save: () => null,
        });
        console.log('Greenergy Admin: Block registered result:', result ? 'SUCCESS' : 'FAILED');
    } catch (e) {
        console.error('Greenergy Admin: Block registration EXCEPTION:', e);
    }
};

// Register immediately if Gutenberg is ready, otherwise wait
if (wp && wp.blocks && wp.blocks.registerBlockType) {
    registerSocialBlock();
} else if (wp.domReady) {
    wp.domReady(registerSocialBlock);
} else {
    document.addEventListener('DOMContentLoaded', registerSocialBlock);
}
