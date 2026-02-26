<?php

/**
 * Share Buttons Component
 * 
 * @param array $args {
 *     @type int    $post_id  The post ID to share.
 *     @type string $label    The label text above buttons.
 *     @type string $class    Additional classes for the wrapper.
 * }
 */
$post_id = $args['post_id'] ?? get_the_ID();
$label   = $args['label'] ?? __('شارك المقال', 'greenergy');
$class   = $args['class'] ?? '';
$show    = $args['show'] ?? null;

if (!$show) {
    $news_settings = get_option('greenergy_news_settings', []);
    $share_providers = isset($news_settings['shareProviders']) ? $news_settings['shareProviders'] : [];

    if (empty($share_providers)) {
        return;
    }

    $show = [
        'whatsapp'  => in_array('whatsapp', $share_providers),
        'telegram'  => in_array('telegram', $share_providers),
        'facebook'  => in_array('facebook', $share_providers),
        'instagram' => in_array('instagram', $share_providers),
        'youtube'   => in_array('youtube', $share_providers),
        'rss'       => in_array('rss', $share_providers),
        'copy'      => in_array('copy', $share_providers),
    ];
}

$link = get_permalink($post_id);
$title = get_the_title($post_id);

// If nothing shown, exit
if (!array_filter($show)) {
    return;
}
?>

<div class="share-buttons-component relative bg-white/50 backdrop-blur-md rounded-2xl border border-white/40 shadow-xl p-6 transition-all duration-300 hover:shadow-2xl <?php echo esc_attr($class); ?>">
    <div class="flex items-center gap-4 mb-6">
        <div class="h-px flex-1 bg-gradient-to-l from-transparent via-gray-200 to-gray-200"></div>
        <div class="text-secondary-900 text-lg font-bold tracking-tight">
            <?php echo esc_html($label); ?>
        </div>
        <div class="h-px flex-1 bg-gradient-to-r from-transparent via-gray-200 to-gray-200"></div>
    </div>

    <div class="flex flex-wrap justify-center items-center gap-4">
        <?php if ($show['whatsapp']) : ?>
            <a href="https://wa.me/?text=<?php echo urlencode($title . ' ' . $link); ?>" target="_blank"
                class="group flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 rounded-xl bg-white text-[#25D366] shadow-sm border border-gray-100 transition-all duration-300 hover:bg-[#25D366] hover:text-white hover:shadow-lg hover:shadow-[#25D366]/20 hover:-translate-y-1">
                <i class="fab fa-whatsapp text-2xl group-hover:scale-110 transition-transform"></i>
            </a>
        <?php endif; ?>

        <?php if ($show['telegram']) : ?>
            <a href="https://t.me/share/url?url=<?php echo urlencode($link); ?>&text=<?php echo urlencode($title); ?>" target="_blank"
                class="group flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 rounded-xl bg-white text-[#0088cc] shadow-sm border border-gray-100 transition-all duration-300 hover:bg-[#0088cc] hover:text-white hover:shadow-lg hover:shadow-[#0088cc]/20 hover:-translate-y-1">
                <i class="fab fa-telegram-plane text-2xl group-hover:scale-110 transition-transform"></i>
            </a>
        <?php endif; ?>

        <?php if ($show['facebook']) : ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($link); ?>" target="_blank"
                class="group flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 rounded-xl bg-white text-[#1877F2] shadow-sm border border-gray-100 transition-all duration-300 hover:bg-[#1877F2] hover:text-white hover:shadow-lg hover:shadow-[#1877F2]/20 hover:-translate-y-1">
                <i class="fab fa-facebook-f text-xl group-hover:scale-110 transition-transform"></i>
            </a>
        <?php endif; ?>

        <?php if ($show['instagram']) : ?>
            <a href="#" class="group flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 rounded-xl bg-white text-[#E4405F] shadow-sm border border-gray-100 transition-all duration-300 hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:text-white hover:shadow-lg hover:shadow-[#ee2a7b]/20 hover:-translate-y-1">
                <i class="fab fa-instagram text-2xl group-hover:scale-110 transition-transform"></i>
            </a>
        <?php endif; ?>

        <?php if ($show['youtube']) : ?>
            <a href="#" class="group flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 rounded-xl bg-white text-[#FF0000] shadow-sm border border-gray-100 transition-all duration-300 hover:bg-[#FF0000] hover:text-white hover:shadow-lg hover:shadow-[#FF0000]/20 hover:-translate-y-1">
                <i class="fab fa-youtube text-2xl group-hover:scale-110 transition-transform"></i>
            </a>
        <?php endif; ?>

        <?php if ($show['rss']) : ?>
            <a href="<?php bloginfo('rss2_url'); ?>" class="group flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 rounded-xl bg-white text-[#f26522] shadow-sm border border-gray-100 transition-all duration-300 hover:bg-[#f26522] hover:text-white hover:shadow-lg hover:shadow-[#f26522]/20 hover:-translate-y-1">
                <i class="fas fa-rss text-2xl group-hover:scale-110 transition-transform"></i>
            </a>
        <?php endif; ?>

        <?php if ($show['copy']) : ?>
            <button onclick="copyPostLink(this, '<?php echo esc_js($link); ?>')"
                class="group flex items-center justify-center w-12 h-12 max-sm:w-10 max-sm:h-10 rounded-xl bg-white text-primary-600 shadow-sm border border-gray-100 transition-all duration-300 hover:bg-primary-600 hover:text-white hover:shadow-lg hover:shadow-primary-500/20 hover:-translate-y-1">
                <i class="far fa-copy text-2xl group-hover:scale-110 transition-transform"></i>
            </button>
        <?php endif; ?>
    </div>

    <!-- Copy Success Toast -->
    <div id="copy-toast" class="fixed top-8 left-1/2 -translate-x-1/2 z-[10000] opacity-0 pointer-events-none transition-all duration-500 translate-y-[-20px]">
        <div class="flex items-center gap-4 px-6 py-4 bg-white/90 backdrop-blur-xl border border-primary-100 shadow-2xl rounded-2xl">
            <div class="w-10 h-10 rounded-full bg-primary-500 text-white flex items-center justify-center shadow-lg shadow-primary-500/30">
                <i class="fas fa-check text-lg"></i>
            </div>
            <div>
                <div class="text-secondary-900 font-bold text-base leading-none mb-1">تمت العملية بنجاح</div>
                <div class="text-stone-500 text-sm">تم نسخ رابط المقالة للحافظة</div>
            </div>
        </div>
    </div>

    <script>
        function copyPostLink(btn, text) {
            navigator.clipboard.writeText(text).then(() => {
                const toast = document.getElementById('copy-toast');
                toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-[-20px]');
                toast.classList.add('opacity-100', 'translate-y-0');

                setTimeout(() => {
                    toast.classList.remove('opacity-100', 'translate-y-0');
                    toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-[-20px]');
                }, 3000);
            });
        }
    </script>
</div>