/**
 * Greenergy Blocks - Modular Entry Point
 * Components extracted for maintainability; main edit logic remains in blocks.js
 * which imports components from here.
 */
import {
    ImageControl,
    PostSelection,
    TermSelect,
    MenuSelect,
    SocialLinksControl,
    NavLinksControl,
} from './components/index.js';

// Export as Greenergy* for backward compatibility with blocks.js
export const GreenergyImageControl = ImageControl;
export const GreenergyPostSelection = PostSelection;
export const GreenergyTermSelect = TermSelect;
export const GreenergyMenuSelect = MenuSelect;
export const GreenergySocialLinksControl = SocialLinksControl;
export const GreenergyNavLinksControl = NavLinksControl;
