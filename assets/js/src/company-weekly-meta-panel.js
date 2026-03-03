/**
 * لوحة إعدادات "شركة الأسبوع" في محرر الشركات.
 * تظهر عند تحرير شركة وتُحفظ الحقول عبر REST مع الحفظ.
 */
(function () {
    'use strict';

    if (typeof wp === 'undefined' || !wp.editPost || !wp.plugins || !wp.data || !wp.element || !wp.components) {
        return;
    }

    var postType = wp.data.select('core/editor') && wp.data.select('core/editor').getCurrentPostType && wp.data.select('core/editor').getCurrentPostType();
    if (postType !== 'companies') {
        return;
    }

    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var useSelect = wp.data.useSelect;
    var useDispatch = wp.data.useDispatch;
    var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
    var registerPlugin = wp.plugins.registerPlugin;
    var __ = wp.i18n.__;

    var META_KEYS = {
        company_weekly_description: { label: 'وصف شركة الأسبوع (للعرض عند السحب من القاعدة)', type: 'textarea' },
        company_years_experience: { label: 'سنة خبرة (رقم أو نص)', type: 'text' },
        company_customer_rating: { label: 'تقييم العملاء (رقم أو نص)', type: 'text' },
        company_projects_completed: { label: 'مشاريع مكتملة (رقم أو نص)', type: 'text' },
        company_contact_url: { label: 'رابط تواصل معنا (URL)', type: 'url' }
    };

    function CompanyWeeklyPanel() {
        var postId = useSelect(function (select) {
            return select('core/editor') && select('core/editor').getCurrentPostId && select('core/editor').getCurrentPostId();
        }, []);
        var meta = useSelect(function (select) {
            if (!postId) return {};
            var record = select('core').getEditedEntityRecord('postType', 'companies', postId);
            return (record && record.meta) ? record.meta : {};
        }, [postId]);
        var editEntityRecord = useDispatch('core').editEntityRecord;

        var updateMeta = function (key, value) {
            if (!postId) return;
            var newMeta = Object.assign({}, meta, { [key]: value });
            editEntityRecord('postType', 'companies', postId, { meta: newMeta });
        };

        if (!postId) return null;

        var controls = Object.keys(META_KEYS).map(function (key) {
            var config = META_KEYS[key];
            var value = meta[key] !== undefined && meta[key] !== null ? String(meta[key]) : '';
            if (config.type === 'textarea') {
                return el(TextareaControl, {
                    key: key,
                    label: config.label,
                    value: value,
                    onChange: function (v) { updateMeta(key, v || ''); },
                    rows: 3,
                    className: 'greenergy-company-weekly-meta'
                });
            }
            return el(TextControl, {
                key: key,
                label: config.label,
                value: value,
                onChange: function (v) { updateMeta(key, v || ''); },
                type: config.type === 'url' ? 'url' : 'text',
                placeholder: config.type === 'url' ? 'https://' : '',
                className: 'greenergy-company-weekly-meta'
            });
        });

        return el(
            PluginDocumentSettingPanel,
            {
                name: 'greenergy-company-weekly',
                title: __('بيانات شركة الأسبوع (عند السحب من القاعدة)', 'greenergy'),
                className: 'greenergy-company-weekly-panel'
            },
            el(PanelBody, { title: __('الوصف والإحصائيات ورابط التواصل', 'greenergy'), initialOpen: true },
                el('p', { className: 'description', style: { marginBottom: '12px' } },
                    __('املأ هذه الحقول لتظهر عند اختيار هذه الشركة في كتلة "شركة الأسبوع" بالمصدر "من القاعدة".', 'greenergy')
                ),
                controls
            )
        );
    }

    registerPlugin('greenergy-company-weekly-panel', {
        render: CompanyWeeklyPanel
    });
})();
