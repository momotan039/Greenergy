const { createRoot, useState, useEffect } = wp.element;
const { BlockEditorProvider, BlockTools, WritingFlow, ObserveTyping } = wp.blockEditor;
const { SlotFillProvider, Popover, Button, SnackbarList } = wp.components;
const { registerBlockType } = wp.blocks;
const apiFetch = wp.apiFetch;

// Import our custom blocks code
import '../../../inc/blocks/src/social-media-settings';

const GreenergyAdmin = () => {
    // Initial blocks state from DB
    const [blocks, updateBlocks] = useState([]);
    const [isSaving, setIsSaving] = useState(false);
    const [notices, setNotices] = useState([]);

    // Load initial blocks on mount
    useEffect(() => {
        console.log('Greenergy Admin: Effect running.');

        if (typeof greenergySettings === 'undefined') {
            console.error('Greenergy Admin: greenergySettings is UNDEFINED!');
            return;
        }

        console.log('Greenergy Admin: greenergySettings found:', greenergySettings);

        const savedBlocks = greenergySettings.blocks;

        // Log registered blocks to check if ours is there
        const registeredBlocks = wp.blocks.getBlockTypes().map(b => b.name);
        console.log('Greenergy Admin: Registered blocks count:', registeredBlocks.length);
        console.log('Greenergy Admin: Registered blocks list:', registeredBlocks);

        if (savedBlocks && typeof savedBlocks === 'string' && savedBlocks.trim() !== '') {
            console.log('Greenergy Admin: Found saved blocks content (length):', savedBlocks.length);
            try {
                const parsedBlocks = wp.blocks.parse(savedBlocks);
                console.log('Greenergy Admin: Parsed blocks successfully. Count:', parsedBlocks.length);
                updateBlocks(parsedBlocks);
            } catch (e) {
                console.error('Greenergy Admin: Error parsing saved blocks:', e);
            }
        } else {
            console.log('Greenergy Admin: No saved blocks or empty. Attempting default init.');
            const blockName = 'greenergy/social-media-settings';
            const blockType = wp.blocks.getBlockType(blockName);
            if (blockType) {
                console.log('Greenergy Admin: Block type found. Creating default.');
                const defaultBlock = wp.blocks.createBlock(blockName);
                updateBlocks([defaultBlock]);
            } else {
                console.error(`Greenergy Admin: Block "${blockName}" NOT registered.`);
            }
        }
    }, []);

    // Handle block changes
    const onInput = (newBlocks) => {
        updateBlocks(newBlocks);
    };

    const onChange = (newBlocks) => {
        updateBlocks(newBlocks);
    };

    // Save Settings
    const saveSettings = async () => {
        setIsSaving(true);

        // Serialize blocks for editor state restoration
        const serializedBlocks = wp.blocks.serialize(blocks);

        // Extracting Social Media Data manually for now to send cleaner data
        // We find the block(s) and take the items from the attributes
        let socialData = [];
        blocks.forEach(block => {
            if (block.name === 'greenergy/social-media-settings' && block.attributes && block.attributes.items) {
                socialData = [...socialData, ...block.attributes.items];
            }
        });

        const settingsData = {
            social_media: socialData
        };

        try {
            await apiFetch({
                path: '/greenergy/v1/save-settings',
                method: 'POST',
                data: {
                    blocks: serializedBlocks,
                    settings: settingsData
                },
            });

            createNotice('Settings saved successfully!', 'success');
        } catch (error) {
            console.error(error);
            createNotice(error.message || 'Error saving settings.', 'error');
        } finally {
            setIsSaving(false);
        }
    };

    const createNotice = (message, status = 'success') => {
        const notice = { id: Date.now(), content: message, status };
        setNotices([...notices, notice]);
        // Auto dismiss
        setTimeout(() => {
            setNotices(current => current.filter(n => n.id !== notice.id));
        }, 3000);
    };

    // Editor settings
    const editorSettings = {
        mediaUpload: wp.blockEditor.MediaUpload,
        hasFixedToolbar: true,
    };

    return (
        <div className="greenergy-admin-layout">
            <div className="greenergy-admin-header">
                <h1>{'إعدادات القالب'}</h1>
                <div style={{ background: '#eee', padding: '5px', margin: '0 10px', fontSize: '10px' }}>Debug: React Loaded</div>
                <Button
                    variant="primary"
                    isBusy={isSaving}
                    onClick={saveSettings}
                >
                    {isSaving ? 'جاري الحفظ...' : 'حفظ التغييرات'}
                </Button>
            </div>

            <div className="greenergy-block-editor">
                <SlotFillProvider>
                    <BlockEditorProvider
                        value={blocks}
                        onInput={onInput}
                        onChange={onChange}
                        settings={editorSettings}
                    >
                        <WritingFlow>
                            <ObserveTyping>
                                <BlockTools>
                                    <div className="editor-styles-wrapper">
                                        <wp.blockEditor.BlockList />
                                    </div>
                                </BlockTools>
                            </ObserveTyping>
                        </WritingFlow>
                        <Popover.Slot />
                    </BlockEditorProvider>
                </SlotFillProvider>
            </div>

            <div className="greenergy-notices">
                <SnackbarList notices={notices} onRemove={(id) => setNotices(notices.filter(n => n.id !== id))} />
            </div>
        </div>
    );
};

// Initialize app
const initAdminPanel = () => {
    console.log('Greenergy Admin: Initializing...');
    const rootElement = document.getElementById('greenergy-admin-app');
    if (rootElement) {
        if (!wp.element) {
            console.error('Greenergy Admin: wp.element is missing!');
            rootElement.innerHTML = '<div class="error"><p>Error: WordPress dependencies incorrectly loaded.</p></div>';
            return;
        }

        try {
            const root = createRoot(rootElement);
            root.render(<GreenergyAdmin />);
            console.log('Greenergy Admin: Rendered.');
        } catch (err) {
            console.error('Greenergy Admin: Render Error', err);
            rootElement.innerHTML = '<div class="error"><p>Critical Error: ' + err.message + '</p></div>';
        }
    } else {
        console.error('Greenergy Admin: Root element #greenergy-admin-app not found.');
    }
};

if (wp.domReady) {
    wp.domReady(initAdminPanel);
} else {
    // Fallback if wp-dom-ready not loaded
    document.addEventListener('DOMContentLoaded', initAdminPanel);
}
