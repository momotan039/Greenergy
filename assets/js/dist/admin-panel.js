(() => {
  // inc/blocks/src/social-media-settings/index.js
  var { registerBlockType } = wp.blocks;
  var { __ } = wp.i18n;
  var { useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
  var { Button, TextControl } = wp.components;
  console.log("Greenergy Admin: Social Media Block JS executed.");
  var registerSocialBlock = () => {
    const blockName = "greenergy/social-media-settings";
    const getPlatformIcon = (platform) => {
      const p = platform.toLowerCase().trim();
      if (p.includes("twitter") || p.includes(" x"))
        return "fab fa-twitter";
      if (p.includes("facebook"))
        return "fab fa-facebook-f";
      if (p.includes("instagram"))
        return "fab fa-instagram";
      if (p.includes("linkedin"))
        return "fab fa-linkedin-in";
      if (p.includes("youtube"))
        return "fab fa-youtube";
      if (p.includes("whatsapp"))
        return "fab fa-whatsapp";
      if (p.includes("tiktok"))
        return "fab fa-tiktok";
      if (p.includes("snapchat"))
        return "fab fa-snapchat-ghost";
      if (p.includes("telegram"))
        return "fab fa-telegram-plane";
      return "fas fa-link";
    };
    try {
      console.log("Greenergy Admin: Attempting to register block:", blockName);
      if (wp.blocks.getBlockType(blockName)) {
        console.log("Greenergy Admin: Block already registered, unregistering first.");
        wp.blocks.unregisterBlockType(blockName);
      }
      const result = registerBlockType(blockName, {
        title: __("Social Media Settings", "greenergy"),
        icon: "share",
        category: "theme",
        attributes: {
          items: {
            type: "array",
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
                { platform: "", url: "", icon: "", iconId: 0 }
              ]
            });
          };
          const removeItem = (index) => {
            const newItems = items.filter((_, i) => i !== index);
            setAttributes({ items: newItems });
          };
          return /* @__PURE__ */ wp.element.createElement("div", { ...useBlockProps({ className: "greenergy-card-block" }) }, /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-block-header" }, /* @__PURE__ */ wp.element.createElement("h2", null, __("Social Media Links", "greenergy")), /* @__PURE__ */ wp.element.createElement("p", null, __("Add links to your social media profiles. These will appear in the header and footer.", "greenergy"))), /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-social-list" }, items.map((item, index) => /* @__PURE__ */ wp.element.createElement("div", { key: index, className: "greenergy-social-item", style: { border: "1px solid #e2e8f0", padding: "15px", borderRadius: "8px", marginBottom: "15px", background: "#f8fafc" } }, /* @__PURE__ */ wp.element.createElement("div", { className: "social-item-header", style: { display: "flex", justifyContent: "space-between", alignItems: "center", marginBottom: "10px" } }, /* @__PURE__ */ wp.element.createElement("div", { style: { display: "flex", alignItems: "center", gap: "10px" } }, /* @__PURE__ */ wp.element.createElement("span", { className: "item-count", style: { fontWeight: "bold", color: "#64748b" } }, "#", index + 1), /* @__PURE__ */ wp.element.createElement("div", { style: { width: "32px", height: "32px", display: "flex", alignItems: "center", justifyContent: "center", background: "#fff", border: "1px solid #cbd5e1", borderRadius: "4px" } }, item.icon ? /* @__PURE__ */ wp.element.createElement("img", { src: item.icon, alt: "icon", style: { width: "20px", height: "20px", objectFit: "contain" } }) : /* @__PURE__ */ wp.element.createElement("i", { className: getPlatformIcon(item.platform), style: { fontSize: "18px", color: "#1e293b" } })), /* @__PURE__ */ wp.element.createElement("span", { style: { fontWeight: "600" } }, item.platform || __("New Platform", "greenergy"))), /* @__PURE__ */ wp.element.createElement(
            Button,
            {
              isDestructive: true,
              isSmall: true,
              variant: "tertiary",
              onClick: () => removeItem(index),
              icon: "trash",
              label: __("Remove", "greenergy")
            }
          )), /* @__PURE__ */ wp.element.createElement("div", { className: "social-item-fields", style: { display: "grid", gridTemplateColumns: "1fr 1fr", gap: "15px" } }, /* @__PURE__ */ wp.element.createElement(
            TextControl,
            {
              label: __("Platform Name", "greenergy"),
              value: item.platform,
              onChange: (val) => updateItem(index, "platform", val),
              placeholder: __("Example: Twitter", "greenergy")
            }
          ), /* @__PURE__ */ wp.element.createElement(
            TextControl,
            {
              label: __("URL", "greenergy"),
              value: item.url,
              onChange: (val) => updateItem(index, "url", val),
              placeholder: "https://"
            }
          )), /* @__PURE__ */ wp.element.createElement("div", { className: "social-icon-picker", style: { marginTop: "10px" } }, /* @__PURE__ */ wp.element.createElement(MediaUploadCheck, null, /* @__PURE__ */ wp.element.createElement(
            MediaUpload,
            {
              onSelect: (media) => {
                updateItem(index, "icon", media.url);
                updateItem(index, "iconId", media.id);
              },
              allowedTypes: ["image"],
              render: ({ open }) => /* @__PURE__ */ wp.element.createElement(Button, { variant: "secondary", onClick: open, style: { width: "100%" } }, item.icon ? __("Change Custom Icon", "greenergy") : __("Upload Custom Icon (Optional)", "greenergy"))
            }
          ), item.icon && /* @__PURE__ */ wp.element.createElement(
            Button,
            {
              isDestructive: true,
              isLink: true,
              onClick: () => {
                updateItem(index, "icon", "");
                updateItem(index, "iconId", 0);
              },
              style: { marginTop: "5px" }
            },
            __("Remove Custom Icon (use default)", "greenergy")
          )))))), /* @__PURE__ */ wp.element.createElement("div", { className: "add-item-wrapper", style: { marginTop: "20px" } }, /* @__PURE__ */ wp.element.createElement(Button, { variant: "primary", icon: "plus", onClick: addItem, style: { width: "100%", height: "40px", justifyContent: "center" } }, __("Add New Platform", "greenergy"))));
        },
        save: () => null
      });
      console.log("Greenergy Admin: Block registered result:", result ? "SUCCESS" : "FAILED");
    } catch (e) {
      console.error("Greenergy Admin: Block registration EXCEPTION:", e);
    }
  };
  if (wp && wp.blocks && wp.blocks.registerBlockType) {
    registerSocialBlock();
  } else if (wp.domReady) {
    wp.domReady(registerSocialBlock);
  } else {
    document.addEventListener("DOMContentLoaded", registerSocialBlock);
  }

  // inc/blocks/src/news-settings/index.js
  var { registerBlockType: registerBlockType2 } = wp.blocks;
  var { __: __2 } = wp.i18n;
  var { MediaUpload: MediaUpload2, MediaUploadCheck: MediaUploadCheck2 } = wp.blockEditor;
  var {
    Button: Button2,
    PanelBody,
    TextControl: TextControl2,
    SelectControl,
    CheckboxControl,
    BaseControl,
    ToggleControl
  } = wp.components;
  var Edit = ({ attributes, setAttributes }) => {
    const { MediaUpload: MediaUpload3 } = wp.blockEditor;
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
        title: type === "image" ? __2("\u0627\u062E\u062A\u0631 \u0635\u0648\u0631\u0629", "greenergy") : __2("\u0627\u062E\u062A\u0631 \u0641\u064A\u062F\u064A\u0648", "greenergy"),
        multiple: false,
        library: { type }
      });
      frame.on("select", () => {
        const attachment = frame.state().get("selection").first().toJSON();
        callback(attachment);
      });
      frame.open();
    };
    return /* @__PURE__ */ wp.element.createElement("div", { className: "greenergy-news-settings-block", style: { padding: "20px", background: "#fff", border: "1px solid #ccc" } }, /* @__PURE__ */ wp.element.createElement("h2", { style: { borderBottom: "1px solid #eee", paddingBottom: "10px", marginBottom: "20px" } }, __2("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0635\u0641\u062D\u0629 \u0627\u0644\u062E\u0628\u0631", "greenergy")), /* @__PURE__ */ wp.element.createElement(PanelBody, { title: __2("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0627\u0644\u0644\u0627\u0641\u062A\u0629 \u0627\u0644\u0639\u0627\u0645\u0629", "greenergy"), initialOpen: true }, /* @__PURE__ */ wp.element.createElement(
      SelectControl,
      {
        label: __2("\u0646\u0648\u0639 \u0627\u0644\u062E\u0644\u0641\u064A\u0629", "greenergy"),
        value: bannerType,
        options: [
          { label: "\u0635\u0648\u0631\u0629", value: "image" },
          { label: "\u0641\u064A\u062F\u064A\u0648", value: "video" }
        ],
        onChange: (value) => setAttributes({ bannerType: value })
      }
    ), bannerType === "image" && /* @__PURE__ */ wp.element.createElement(BaseControl, { label: __2("\u0635\u0648\u0631\u0629 \u0627\u0644\u062E\u0644\u0641\u064A\u0629", "greenergy") }, /* @__PURE__ */ wp.element.createElement("div", { style: { marginBottom: "15px" } }, bannerImage && /* @__PURE__ */ wp.element.createElement("img", { src: bannerImage, style: { maxWidth: "100%", maxHeight: "200px", marginBottom: "10px", display: "block", border: "1px solid #ddd", borderRadius: "4px" }, alt: "Banner" }), MediaUpload3 ? /* @__PURE__ */ wp.element.createElement(
      MediaUpload3,
      {
        onSelect: onSelectImage,
        allowedTypes: ["image"],
        value: bannerImage,
        render: ({ open }) => /* @__PURE__ */ wp.element.createElement(Button2, { isSecondary: true, onClick: open }, bannerImage ? __2("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0635\u0648\u0631\u0629", "greenergy") : __2("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629", "greenergy"))
      }
    ) : /* @__PURE__ */ wp.element.createElement(Button2, { isSecondary: true, onClick: () => openMediaLibrary("image", onSelectImage) }, bannerImage ? __2("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0635\u0648\u0631\u0629", "greenergy") : __2("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629 (\u064A\u062F\u0648\u064A)", "greenergy"))), /* @__PURE__ */ wp.element.createElement(
      TextControl2,
      {
        label: __2("\u0623\u0648 \u0623\u062F\u062E\u0644 \u0631\u0627\u0628\u0637 \u0627\u0644\u0635\u0648\u0631\u0629", "greenergy"),
        value: bannerImage,
        onChange: (value) => setAttributes({ bannerImage: value }),
        help: __2("\u064A\u0645\u0643\u0646\u0643 \u0625\u062F\u062E\u0627\u0644 \u0631\u0627\u0628\u0637 \u062E\u0627\u0631\u062C\u064A \u0644\u0644\u0635\u0648\u0631\u0629 \u0645\u0628\u0627\u0634\u0631\u0629", "greenergy")
      }
    )), bannerType === "video" && /* @__PURE__ */ wp.element.createElement(BaseControl, { label: __2("\u0641\u064A\u062F\u064A\u0648 \u0627\u0644\u062E\u0644\u0641\u064A\u0629", "greenergy") }, /* @__PURE__ */ wp.element.createElement("div", { style: { marginBottom: "15px" } }, bannerVideo && /* @__PURE__ */ wp.element.createElement("video", { src: bannerVideo, style: { maxWidth: "100%", marginBottom: "10px", display: "block" }, controls: true }), MediaUpload3 ? /* @__PURE__ */ wp.element.createElement(
      MediaUpload3,
      {
        onSelect: onSelectVideo,
        allowedTypes: ["video"],
        value: bannerVideo,
        render: ({ open }) => /* @__PURE__ */ wp.element.createElement(Button2, { isSecondary: true, onClick: open }, bannerVideo ? __2("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0641\u064A\u062F\u064A\u0648", "greenergy") : __2("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629", "greenergy"))
      }
    ) : /* @__PURE__ */ wp.element.createElement(Button2, { isSecondary: true, onClick: () => openMediaLibrary("video", onSelectVideo) }, bannerVideo ? __2("\u062A\u063A\u064A\u064A\u0631 \u0627\u0644\u0641\u064A\u062F\u064A\u0648", "greenergy") : __2("\u0627\u062E\u062A\u064A\u0627\u0631 \u0645\u0646 \u0627\u0644\u0645\u0643\u062A\u0628\u0629 (\u064A\u062F\u0648\u064A)", "greenergy"))), /* @__PURE__ */ wp.element.createElement(
      TextControl2,
      {
        label: __2("\u0623\u0648 \u0623\u062F\u062E\u0644 \u0631\u0627\u0628\u0637 \u0627\u0644\u0641\u064A\u062F\u064A\u0648", "greenergy"),
        value: bannerVideo,
        onChange: (value) => setAttributes({ bannerVideo: value }),
        help: __2("\u064A\u0645\u0643\u0646\u0643 \u0625\u062F\u062E\u0627\u0644 \u0631\u0627\u0628\u0637 \u062E\u0627\u0631\u062C\u064A \u0644\u0644\u0641\u064A\u062F\u064A\u0648 (MP4) \u0645\u0628\u0627\u0634\u0631\u0629", "greenergy")
      }
    )), /* @__PURE__ */ wp.element.createElement("div", { style: { marginTop: "20px" } }, /* @__PURE__ */ wp.element.createElement(
      ToggleControl,
      {
        label: __2("\u0639\u0631\u0636 \u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy"),
        checked: showBannerTitle,
        onChange: (value) => setAttributes({ showBannerTitle: value }),
        help: showBannerTitle ? __2("\u0633\u064A\u0638\u0647\u0631 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0641\u064A \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy") : __2("\u0633\u064A\u062A\u0645 \u0625\u062E\u0641\u0627\u0621 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0645\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy")
      }
    ), showBannerTitle && /* @__PURE__ */ wp.element.createElement(
      TextControl2,
      {
        label: __2("\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629 \u0627\u0644\u0627\u0641\u062A\u0631\u0627\u0636\u064A", "greenergy"),
        value: bannerTitle,
        onChange: (value) => setAttributes({ bannerTitle: value }),
        help: __2("\u064A\u0633\u062A\u062E\u062F\u0645 \u0643\u0627\u0641\u062A\u0631\u0627\u0636\u064A \u0625\u0630\u0627 \u0644\u0645 \u064A\u062A\u0645 \u062A\u062D\u062F\u064A\u062F \u0639\u0646\u0648\u0627\u0646 \u0644\u0644\u062E\u0628\u0631", "greenergy")
      }
    ), /* @__PURE__ */ wp.element.createElement("div", { style: { height: "10px" } }), /* @__PURE__ */ wp.element.createElement(
      ToggleControl,
      {
        label: __2("\u0639\u0631\u0636 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A", "greenergy"),
        checked: showBannerSubtitle,
        onChange: (value) => setAttributes({ showBannerSubtitle: value }),
        help: showBannerSubtitle ? __2("\u0633\u064A\u0638\u0647\u0631 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A \u0641\u064A \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy") : __2("\u0633\u064A\u062A\u0645 \u0625\u062E\u0641\u0627\u0621 \u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A \u0645\u0646 \u0627\u0644\u0644\u0627\u0641\u062A\u0629", "greenergy")
      }
    ), showBannerSubtitle && /* @__PURE__ */ wp.element.createElement(
      TextControl2,
      {
        label: __2("\u0627\u0644\u0639\u0646\u0648\u0627\u0646 \u0627\u0644\u0641\u0631\u0639\u064A \u0627\u0644\u0627\u0641\u062A\u0631\u0627\u0636\u064A", "greenergy"),
        value: bannerSubtitle,
        onChange: (value) => setAttributes({ bannerSubtitle: value })
      }
    ))), /* @__PURE__ */ wp.element.createElement(PanelBody, { title: __2("\u0623\u0632\u0631\u0627\u0631 \u0627\u0644\u0645\u0634\u0627\u0631\u0643\u0629", "greenergy"), initialOpen: false }, /* @__PURE__ */ wp.element.createElement("div", { style: { display: "grid", gridTemplateColumns: "1fr 1fr", gap: "10px" } }, shareOptions.map((option) => /* @__PURE__ */ wp.element.createElement(
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
      const result = registerBlockType2(blockName, {
        title: __2("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0627\u0644\u0623\u062E\u0628\u0627\u0631", "greenergy"),
        description: __2("\u0625\u0639\u062F\u0627\u062F\u0627\u062A \u0627\u0644\u0644\u0627\u0641\u062A\u0629 \u0648\u0627\u0644\u0645\u0634\u0627\u0631\u0643\u0629 \u0627\u0644\u062E\u0627\u0635\u0629 \u0628\u0635\u0641\u062D\u0629 \u0627\u0644\u0623\u062E\u0628\u0627\u0631.", "greenergy"),
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
  var { SlotFillProvider, Popover, Button: Button3, SnackbarList } = wp.components;
  var { registerBlockType: registerBlockType3 } = wp.blocks;
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
          const newsBlockName = "greenergy/news-settings";
          const hasNewsBlock = parsedBlocks.some((block) => block.name === newsBlockName);
          if (!hasNewsBlock && wp.blocks.getBlockType(newsBlockName)) {
            console.log("Greenergy Admin: News block missing from saved data. Appending default.");
            const newsBlock = wp.blocks.createBlock(newsBlockName);
            parsedBlocks.push(newsBlock);
          }
          updateBlocks(parsedBlocks);
        } catch (e) {
          console.error("Greenergy Admin: Error parsing saved blocks:", e);
        }
      } else {
        console.log("Greenergy Admin: No saved blocks or empty. Attempting default init.");
        const socialBlockName = "greenergy/social-media-settings";
        const newsBlockName = "greenergy/news-settings";
        const initialBlocks = [];
        if (wp.blocks.getBlockType(socialBlockName)) {
          initialBlocks.push(wp.blocks.createBlock(socialBlockName));
        }
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
      let socialData = [];
      let newsSettingsData = {};
      blocks.forEach((block) => {
        if (block.name === "greenergy/social-media-settings" && block.attributes && block.attributes.items) {
          socialData = [...socialData, ...block.attributes.items];
        }
        if (block.name === "greenergy/news-settings") {
          newsSettingsData = block.attributes;
        }
      });
      const settingsData = {
        social_media: socialData,
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
      Button3,
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
