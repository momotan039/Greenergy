const { createRoot, useState, useEffect } = wp.element;
const { BlockEditorProvider, BlockTools, WritingFlow, ObserveTyping, BlockList } = wp.blockEditor;
const { SlotFillProvider, Popover, Button, SnackbarList } = wp.components;
const { registerBlockType } = wp.blocks;
const apiFetch = wp.apiFetch;

// Import our custom blocks code
import '../../../inc/blocks/src/news-settings';

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

                // Check if News Settings block is missing and append it
                const newsBlockName = 'greenergy/news-settings';
                const hasNewsBlock = parsedBlocks.some(block => block.name === newsBlockName);
                
                if (!hasNewsBlock && wp.blocks.getBlockType(newsBlockName)) {
                     console.log('Greenergy Admin: News block missing from saved data. Appending default.');
                     const newsBlock = wp.blocks.createBlock(newsBlockName);
                     parsedBlocks.push(newsBlock);
                }

                updateBlocks(parsedBlocks);
            } catch (e) {
                console.error('Greenergy Admin: Error parsing saved blocks:', e);
            }
            const initialBlocks = [];
            
            if (wp.blocks.getBlockType(newsBlockName)) {
                initialBlocks.push(wp.blocks.createBlock(newsBlockName));
            }

            if (initialBlocks.length > 0) {
                updateBlocks(initialBlocks);
            } else {
                console.error(`Greenergy Admin: Default blocks NOT registered.`);
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

        // Extraction Logic
        let newsSettingsData = {};

        blocks.forEach(block => {
            if (block.name === 'greenergy/news-settings') {
                newsSettingsData = block.attributes;
            }
        });

        const settingsData = {
            news_settings: newsSettingsData
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
        hasUploadPermissions: true, // Crucial for MediaUploadCheck to work
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
                                        <BlockList />
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
            rootElement.innerHTML = '<div class="notice notice-error"><p>Error: WordPress Element (React) package is missing.</p></div>';
            return;
        }

        try {
            // Check for React 18+ createRoot
            if (wp.element.createRoot) {
                console.log('Greenergy Admin: Using createRoot (React 18+)');
                const root = wp.element.createRoot(rootElement);
                root.render(<GreenergyAdmin />);
            } else if (wp.element.render) {
                // Fallback for older WordPress versions
                console.log('Greenergy Admin: Using legacy render');
                wp.element.render(<GreenergyAdmin />, rootElement);
            } else {
                throw new Error('No render method found in wp.element');
            }
            console.log('Greenergy Admin: Render process started.');
        } catch (err) {
            console.error('Greenergy Admin: Render Error', err);
            rootElement.innerHTML = `
                <div class="notice notice-error" style="padding: 20px; border: 2px solid red;">
                    <h3>Critical Error: Admin Panel failed to load</h3>
                    <p><strong>Message:</strong> ${err.message}</p>
                    <p>Check the browser console for more details.</p>
                </div>
            `;
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
