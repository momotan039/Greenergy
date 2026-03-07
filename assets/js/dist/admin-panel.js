(() => {
  // assets/js/src/admin-panel.js
  var { createRoot, useState, useEffect } = wp.element;
  var { BlockEditorProvider, BlockTools, WritingFlow, ObserveTyping, BlockList } = wp.blockEditor;
  var { SlotFillProvider, Popover, Button, SnackbarList } = wp.components;
  var { registerBlockType } = wp.blocks;
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
          updateBlocks(parsedBlocks);
        } catch (e) {
          console.error("Greenergy Admin: Error parsing saved blocks:", e);
          updateBlocks([]);
        }
      } else {
        updateBlocks([]);
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
      try {
        await apiFetch({
          path: "/greenergy/v1/save-settings",
          method: "POST",
          data: {
            blocks: serializedBlocks,
            settings: {}
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
      Button,
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
