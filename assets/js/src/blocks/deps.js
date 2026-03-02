/**
 * Shared WordPress & global dependencies for Greenergy Blocks.
 * Single place for imports - tree-shaking friendly.
 */
export const { registerBlockType } = wp.blocks;
export const { __ } = wp.i18n;
export const {
    useBlockProps,
    InspectorControls,
    MediaUpload,
    MediaUploadCheck,
    URLInput,
    InnerBlocks,
    PanelColorSettings,
    RichText,
} = wp.blockEditor;
export const {
    PanelBody,
    TextControl,
    TextareaControl,
    ToggleControl,
    SelectControl,
    Button,
    Dashicon,
    FormTokenField,
    RangeControl,
    BaseControl,
} = wp.components;
export const { useSelect, useDispatch } = wp.data;
export const { useState, useEffect } = wp.element;
export const ServerSideRender = wp.serverSideRender;
export const { Fragment, createElement } = wp.element;

export const getAssetsUri = () =>
    (typeof greenergyBlocks !== 'undefined' && greenergyBlocks?.assetsUri) || '';
