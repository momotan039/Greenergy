# Greenergy Blocks – Modular Structure

## Overview

بلوكات محرر Greenergy مقسمة إلى وحدات منظمة لتسهيل التطوير والصيانة.

## Structure

```
blocks/
├── deps.js              # WordPress dependencies (unused if components use inline wp refs)
├── components/         # Reusable block editor controls
│   ├── ImageControl.js       # Image upload + URL fallback
│   ├── PostSelection.js      # Post picker (FormTokenField)
│   ├── TermSelect.js         # Taxonomy term picker
│   ├── MenuSelect.js         # Menu dropdown
│   ├── SocialLinksControl.js # Social links editor
│   ├── NavLinksControl.js    # Navigation links editor
│   └── index.js              # Barrel exports
├── index.js             # Exports components as Greenergy* for blocks.js
└── README.md
```

## Entry Point

`assets/js/src/blocks.js` imports components from `./blocks/index.js` and contains:
- Block registration (single-news-content, registry loop, ad-block, job-section)
- `GreenergyBlockEdit` – universal edit component with inspector panels

## Adding a New Component

1. Create `components/MyControl.js` and export it
2. Add export to `components/index.js`
3. Add alias in `blocks/index.js` (e.g. `GreenergyMyControl`)
4. Import in `blocks.js` and use in inspector
