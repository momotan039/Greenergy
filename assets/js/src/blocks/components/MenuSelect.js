/**
 * Menu selection control
 */
import { createElement } from '../deps.js';
import { SelectControl } from '../deps.js';
import { useSelect } from '../deps.js';
import { __ } from '../deps.js';

export const MenuSelect = ({ label, value, onChange }) => {
    const menus = useSelect((select) => select('core').getMenus({ per_page: -1 }), []);
    const options = [
        { label: __('استخدام القائمة الافتراضية (Primary)', 'greenergy'), value: 0 },
        ...(menus || []).map((menu) => ({ label: menu.name, value: menu.id })),
    ];
    return createElement(SelectControl, {
        label,
        value,
        options,
        onChange: (val) => onChange(parseInt(val)),
    });
};
