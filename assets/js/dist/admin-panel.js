(() => {
  // inc/blocks/src/news-settings/index.js
  var { registerBlockType } = wp.blocks;
  var { __ } = wp.i18n;
  var { MediaUpload, MediaUploadCheck } = wp.blockEditor;
  var {
    Button,
    PanelBody,
    TextControl,
    SelectControl,
    CheckboxControl,
    BaseControl,
    ToggleControl
  } = wp.components;
  var Edit = ({ attributes, setAttributes }) => {
    const { MediaUpload: MediaUpload2 } = wp.blockEditor;
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
      { label: "\u0648\u0627\u062A\u0633\u0627\u0628", value: "whatsapp" },
      { label: "\u062A\u064A\u0644\u064A\u062C\u0631\u0627\u0645", value: "telegram" },
      { label: "\u0641\u064A\u0633\u0628\u0648\u0643", value: "facebook" },
      { label: "\u0625\u0646\u0633\u062A\u063A\u0631\u0627\u0645", value: "instagram" },
      { label: "\u064A\u0648\u062A\u064A\u0648\u0628", value: "youtube" },
      { label: "RSS", value: "rss" },
      { label: "\u0646\u0633\u062E \u0627\u0644\u0631\u0627\u0628\u0637", value: "copy" }
    ];
    const updateShareProviders = (checked, value) => {
      let newProviders = [...shareProviders || []];
      if (checked) {
        if (!newProviders.includes(value))
          newProviders.push(value);
      } else {
        newProviders = newProviders.filter((p) => p !== value);
      }
      setAttributes({ shareProviders: newProviders });
    };
    const openMediaLibrary = (type, callback) => {
      const frame = wp.media({
        title: type === "image" ? __("\u0627\u062E\u062A\u0631 \u0635\u0648\u0631\u0629", "greenergy") : __("\u0627\u062E\u062A\u0631 \u0641\u064A\u062F\u064A\u0648", "greenergy"),
        multiple: false,
        library: { type }
      });
      frame.on("select", () => {
        const attachment = frame.state().get("selection").first().toJSON();
        callback(attachment);
      });
      frame.open();
    };
    return /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-news-settings-block", style: { padding: "20px", background: "#fff", border: "1px solid #ccc" } }, /* @__PURE__ */ wp.element.createElement("h2", { style: { borderBottom: "1px solid #eee", paddingBottom: "10px", marginBottom: "20px" } }, __("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0635\u0641\u062D\u0629 \u0627\u0644\u062E\u0628\u0631", "greenergy")), /* @__PURE__ */ wp.element.createElement(PanelBody, { title: __("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0627\u0644\u0644\u0627\u0641\u062A\u0629 \u0627\u0644\u0639\u0627\u0645\u0629", "greenergy"), initialOpen: true }, /* @__PURE__ */ wp.element.createElement(
      SelectControl,
      {
        label: __("\u0646\u0648\u0639 \u0627\u0644\u062E\u0644\u0641\u064A\u0629", "greenergy"),
        value: bannerType,
        options: [
          { label: "\u0635\u0648\u0631\u0629", value: "image" },
          { label: "\u0641\u064A\u062F\u064A\u0648", value: "video" }
        ],
        onChange: (value) => setAttributes({ bannerType: value })
      }
    ), bannerType === "image" && /* @__PURE__ */ wp.element.createElement(BaseControl, { label: __("\u0635\u0648\u0631\u0629 \u0627\u0644\u062E\u0644\u0641\u064A\u0629", "greenergy") }, /* @__PURE__ */ wp.element.createElement("div", { style: { marginBottom: "15px" } }, bannerImage && /* @__PURE__ */ wp.element.createElement("img", { src: bannerImage, style: { maxWidth: "100%", maxHeight: "200px", marginBottom: "10px", display: "block", border: "1px solid #ddd", borderRadius: "4px" }, alt: "Banner" }), MediaUpload2 ? /* @__PURE__ */ wp.element.createElement(
      MediaUpload2,
      {
        onSelect: onSelectImage,
        allowedTypes: ["image"],
        value: bannerImage,
        render: ({ open }) => /* @__PURE__ */ wp.element.createElement(Button, { isSecondary: true, onClick: open }, bannerImage ? __("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0635\u0648\u0631\u0629", "greenergy") : __("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629", "greenergy"))
      }
    ) : /* @__PURE__ */ wp.element.createElement(Button, { isSecondary: true, onClick: () => openMediaLibrary("image", onSelectImage) }, bannerImage ? __("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0635\u0648\u0631\u0629", "greenergy") : __("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629 (\u064A\u062F\u0648\u064A)", "greenergy"))), /* @__PURE__ */ wp.element.createElement(
      TextControl,
      {
        label: __("\u0623\u0648 \u0623\u062F\u062E\u0644 \u0631\u0627\u0628\u0637 \u0627\u0644\u0635\u0648\u0631\u0629", "greenergy"),
        value: bannerImage,
        onChange: (value) => setAttributes({ bannerImage: value }),
        help: __("\u064A\u0645\u0643\u0646\u0643 \u0625\u062F\u062E\u0627\u0644 \u0631\u0627\u0628\u0637 \u062E\u0627\u0631\u062C\u064A \u0644\u0644\u0635\u0648\u0631\u0629 \u0645\u0628\u0627\u0634\u0631\u0629", "greenergy")
      }
    )), bannerType === "video" && /* @__PURE__ */ wp.element.createElement(BaseControl, { label: __("\u0641\u064A\u062F\u064A\u0648 \u0627\u0644\u062E\u0644\u0641\u064A\u0629", "greenergy") }, /* @__PURE__ */ wp.element.createElement("div", { style: { marginBottom: "15px" } }, bannerVideo && /* @__PURE__ */ wp.element.createElement("video", { src: bannerVideo, style: { maxWidth: "100%", marginBottom: "10px", display: "block" }, controls: true }), MediaUpload2 ? /* @__PURE__ */ wp.element.createElement(
      MediaUpload2,
      {
        onSelect: onSelectVideo,
        allowedTypes: ["video"],
        value: bannerVideo,
        render: ({ open }) => /* @__PURE__ */ wp.element.createElement(Button, { isSecondary: true, onClick: open }, bannerVideo ? __("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0641\u064A\u062F\u064A\u0648", "greenergy") : __("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629", "greenergy"))
      }
    ) : /* @__PURE__ */ wp.element.createElement(Button, { isSecondary: true, onClick: () => openMediaLibrary("video", onSelectVideo) }, bannerVideo ? __("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0641\u064A\u062F\u064A\u0648", "greenergy") : __("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629 (\u064A\u062F\u0648\u064A)", "greenergy"))), /* @__PURE__ */ wp.element.createElement(
      TextControl,
      {
        label: __("\u0623\u0648 \u0623\u062F\u062E\u0644 \u0631\u0627\u0628\u0637 \u0627\u0644\u0641\u064A\u062F\u064A\u0648", "greenergy"),
        value: bannerVideo,
        onChange: (value) => setAttributes({ bannerVideo: value }),
        help: __("\u064A\u0645\u0643\u0646\u0643 \u0625\u062F\u062E\u0627\u0644 \u0631\u0627\u0628\u0637 \u062E\u0627\u0631\u062C\u064A \u0644\u0644\u0641\u064A\u062F\u064A\u0648 (MP4) \u0645\u0628\u0627\u0634\u0631\u0629", "greenergy")
      }
    )), /* @__PURE__ */ wp.element.createElement("div", { style: { marginTop: "20px" } }, /* @__PURE__ */ wp.element.createElement(
      ToggleControl,
      {
        label: __("\u0639\u0631\u0636 \u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy"),
        checked: showBannerTitle,
        onChange: (value) => setAttributes({ showBannerTitle: value }),
        help: showBannerTitle ? __("\u0633\u064A\u0638\u0647\u0631 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0641\u064A \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy") : __("\u0633\u064A\u062A\u0645 \u0625\u062E\u0641\u0627\u0621 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0645\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy")
      }
    ), showBannerTitle && /* @__PURE__ */ wp.element.createElement(
      TextControl,
      {
        label: __("\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629 \u0627\u0644\u0627\u0641\u062A\u0631\u0627\u0636\u064A", "greenergy"),
        value: bannerTitle,
        onChange: (value) => setAttributes({ bannerTitle: value }),
        help: __("\u064A\u0633\u062A\u062E\u062F\u0645 \u0643\u0627\u0641\u062A\u0631\u0627\u0636\u064A \u0625\u0630\u0627 \u0644\u0645 \u064A\u062A\u0645 \u062A\u062D\u062F\u064A\u062F \u0639\u0646\u0648\u0627\u0646 \u0644\u0644\u062E\u0628\u0631", "greenergy")
      }
    ), /* @__PURE__ */ wp.element.createElement("div", { style: { height: "10px" } }), /* @__PURE__ */ wp.element.createElement(
      ToggleControl,
      {
        label: __("\u0639\u0631\u0636 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A", "greenergy"),
        checked: showBannerSubtitle,
        onChange: (value) => setAttributes({ showBannerSubtitle: value }),
        help: showBannerSubtitle ? __("\u0633\u064A\u0638\u0647\u0631 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A \u0641\u064A \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy") : __("\u0633\u064A\u062A\u0645 \u0625\u062E\u0641\u0627\u0621 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A \u0645\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy")
      }
    ), showBannerSubtitle && /* @__PURE__ */ wp.element.createElement(
      TextControl,
      {
        label: __("\u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A \u0627\u0644\u0627\u0641\u062A\u0631\u0627\u0636\u064A", "greenergy"),
        value: bannerSubtitle,
        onChange: (value) => setAttributes({ bannerSubtitle: value })
      }
    ))), /* @__PURE__ */ wp.element.createElement(PanelBody, { title: __("\u0623\u0632\u0631\u0627\u0631 \u0627\u0644\u0645\u0634\u0627\u0631\u0643\u0629", "greenergy"), initialOpen: false }, /* @__PURE__ */ wp.element.createElement("div", { style: { display: "grid", gridTemplateColumns: "1fr 1fr", gap: "10px" } }, shareOptions.map((option) => /* @__PURE__ */ wp.element.createElement(
      CheckboxControl,
      {
        key: option.value,
        label: option.label,
        checked: (shareProviders || []).includes(option.value),
        onChange: (checked) => updateShareProviders(checked, option.value)
      }
    )))));
  };
  var registerNewsBlock = () => {
    const blockName = "greenergy/news-settings";
    console.log("Greenergy Admin: Attempting to register block:", blockName);
    try {
      if (wp.blocks.getBlockType(blockName)) {
        console.log("Greenergy Admin: Block already registered, unregistering first.");
        wp.blocks.unregisterBlockType(blockName);
      }
      const result = registerBlockType(blockName, {
        title: __("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0627\u0644\u0623\u062E\u0628\u0627\u0631", "greenergy"),
        description: __("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0627\u0644\u0644\u0627\u0641\u062A\u0629 \u0648\u0627\u0644\u0645\u0634\u0627\u0631\u0643\u0629 \u0627\u0644\u062E\u0627\u0635\u0629 \u0628\u0635\u0641\u062D\u0629 \u0627\u0644\u0623\u062E\u0628\u0627\u0631.", "greenergy"),
        category: "theme",
        icon: "format-aside",
        attributes: {
          bannerType: { type: "string", default: "image" },
          bannerImage: { type: "string", default: "" },
          bannerVideo: { type: "string", default: "" },
          bannerTitle: { type: "string", default: "" },
          bannerSubtitle: { type: "string", default: "\u0627\u0644\u0623\u062E\u0628\u0627\u0631" },
          shareProviders: {
            type: "array",
            default: ["whatsapp", "telegram", "facebook", "instagram", "youtube", "rss", "copy"]
          }
        },
        edit: Edit,
        save: () => null
      });
      console.log("Greenergy Admin: News Settings Block registered result:", result ? "SUCCESS" : "FAILED");
    } catch (e) {
      console.error("Greenergy Admin: News Settings Block registration EXCEPTION:", e);
    }
  };
  if (wp && wp.blocks && wp.blocks.registerBlockType) {
    registerNewsBlock();
  } else if (wp.domReady) {
    wp.domReady(registerNewsBlock);
  } else {
    document.addEventListener("DOMContentLoaded", registerNewsBlock);
  }

  // assets/js/src/admin-panel.js
  var { createRoot, useState, useEffect } = wp.element;
  var { BlockEditorProvider, BlockTools, WritingFlow, ObserveTyping, BlockList } = wp.blockEditor;
  var { SlotFillProvider, Popover, Button: Button2, SnackbarList } = wp.components;
  var { registerBlockType: registerBlockType2 } = wp.blocks;
  var apiFetch = wp.apiFetch;
  var GreenergyAdmin = () => {
    const [blocks, updateBlocks] = useState([]);
    const [isSaving, setIsSaving] = useState(false);
    const [notices, setNotices] = useState([]);
    useEffect(() => {
      console.log("Greenergy Admin: Effect running.");
      if (typeof greenergySettings === "undefined") {
        console.error("Greenergy Admin: greenergySettings is UNDEFINED!");
        return;
      }
      console.log("Greenergy Admin: greenergySettings found:", greenergySettings);
      const savedBlocks = greenergySettings.blocks;
      const registeredBlocks = wp.blocks.getBlockTypes().map((b) => b.name);
      console.log("Greenergy Admin: Registered blocks count:", registeredBlocks.length);
      console.log("Greenergy Admin: Registered blocks list:", registeredBlocks);
      if (savedBlocks && typeof savedBlocks === "string" && savedBlocks.trim() !== "") {
        console.log("Greenergy Admin: Found saved blocks content (length):", savedBlocks.length);
        try {
          const parsedBlocks = wp.blocks.parse(savedBlocks);
          console.log("Greenergy Admin: Parsed blocks successfully. Count:", parsedBlocks.length);
          const newsBlockName2 = "greenergy/news-settings";
          const hasNewsBlock = parsedBlocks.some((block) => block.name === newsBlockName2);
          if (!hasNewsBlock && wp.blocks.getBlockType(newsBlockName2)) {
            console.log("Greenergy Admin: News block missing from saved data. Appending default.");
            const newsBlock = wp.blocks.createBlock(newsBlockName2);
            parsedBlocks.push(newsBlock);
          }
          updateBlocks(parsedBlocks);
        } catch (e) {
          console.error("Greenergy Admin: Error parsing saved blocks:", e);
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
    const onInput = (newBlocks) => {
      updateBlocks(newBlocks);
    };
    const onChange = (newBlocks) => {
      updateBlocks(newBlocks);
    };
    const saveSettings = async () => {
      setIsSaving(true);
      const serializedBlocks = wp.blocks.serialize(blocks);
      let newsSettingsData = {};
      blocks.forEach((block) => {
        if (block.name === "greenergy/news-settings") {
          newsSettingsData = block.attributes;
        }
      });
      const settingsData = {
        news_settings: newsSettingsData
      };
      try {
        await apiFetch({
          path: "/greenergy/v1/save-settings",
          method: "POST",
          data: {
            blocks: serializedBlocks,
            settings: settingsData
          }
        });
        createNotice("Settings saved successfully!", "success");
      } catch (error) {
        console.error(error);
        createNotice(error.message || "Error saving settings.", "error");
      } finally {
        setIsSaving(false);
      }
    };
    const createNotice = (message, status = "success") => {
      const notice = { id: Date.now(), content: message, status };
      setNotices([...notices, notice]);
      setTimeout(() => {
        setNotices((current) => current.filter((n) => n.id !== notice.id));
      }, 3e3);
    };
    const editorSettings = {
      mediaUpload: wp.blockEditor.MediaUpload,
      hasFixedToolbar: true,
      hasUploadPermissions: true
      // Crucial for MediaUploadCheck to work
    };
    return /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-admin-layout" }, /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-admin-header" }, /* @__PURE__ */ wp.element.createElement("h1", null, "\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0627\u0644\u0642\u0627\u0644\u0628"), /* @__PURE__ */ wp.element.createElement("div", { style: { background: "#eee", padding: "5px", margin: "0 10px", fontSize: "10px" } }, "Debug: React Loaded"), /* @__PURE__ */ wp.element.createElement(
      Button2,
      {
        variant: "primary",
        isBusy: isSaving,
        onClick: saveSettings
      },
      isSaving ? "\u062C\u0627\u0631\u064A \u0627\u0644\u062D\u0641\u0638..." : "\u062D\u0641\u0638 \u0627\u0644\u062A\u063A\u064A\u064A\u0631\u0627\u062A"
    )), /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-block-editor" }, /* @__PURE__ */ wp.element.createElement(SlotFillProvider, null, /* @__PURE__ */ wp.element.createElement(
      BlockEditorProvider,
      {
        value: blocks,
        onInput,
        onChange,
        settings: editorSettings
      },
      /* @__PURE__ */ wp.element.createElement(WritingFlow, null, /* @__PURE__ */ wp.element.createElement(ObserveTyping, null, /* @__PURE__ */ wp.element.createElement(BlockTools, null, /* @__PURE__ */ wp.element.createElement("div", { className: "editor-styles-wrapper" }, /* @__PURE__ */ wp.element.createElement(BlockList, null))))),
      /* @__PURE__ */ wp.element.createElement(Popover.Slot, null)
    ))), /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-notices" }, /* @__PURE__ */ wp.element.createElement(SnackbarList, { notices, onRemove: (id) => setNotices(notices.filter((n) => n.id !== id)) })));
  };
  var initAdminPanel = () => {
    console.log("Greenergy Admin: Initializing...");
    const rootElement = document.getElementById("greenergy-admin-app");
    if (rootElement) {
      if (!wp.element) {
        console.error("Greenergy Admin: wp.element is missing!");
        rootElement.innerHTML = '<div class="notice notice-error"><p>Error: WordPress Element (React) package is missing.</p></div>';
        return;
      }
      try {
        if (wp.element.createRoot) {
          console.log("Greenergy Admin: Using createRoot (React 18+)");
          const root = wp.element.createRoot(rootElement);
          root.render(/* @__PURE__ */ wp.element.createElement(GreenergyAdmin, null));
        } else if (wp.element.render) {
          console.log("Greenergy Admin: Using legacy render");
          wp.element.render(/* @__PURE__ */ wp.element.createElement(GreenergyAdmin, null), rootElement);
        } else {
          throw new Error("No render method found in wp.element");
        }
        console.log("Greenergy Admin: Render process started.");
      } catch (err) {
        console.error("Greenergy Admin: Render Error", err);
        rootElement.innerHTML = `
                <div class="notice notice-error" style="padding: 20px; border: 2px solid red;">
                    <h3>Critical Error: Admin Panel failed to load</h3>
                    <p><strong>Message:</strong> ${err.message}</p>
                    <p>Check the browser console for more details.</p>
                </div>
            `;
      }
    } else {
      console.error("Greenergy Admin: Root element #greenergy-admin-app not found.");
    }
  };
  if (wp.domReady) {
    wp.domReady(initAdminPanel);
  } else {
    document.addEventListener("DOMContentLoaded", initAdminPanel);
  }
})();
