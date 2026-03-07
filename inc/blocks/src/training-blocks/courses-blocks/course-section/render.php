<?php

/**
 * Render Course Section Block
 * 
 * @var array $attributes Block attributes
 * @var string $content Block inner content
 * @var WP_Block $block Block instance
 */

$type = $attributes['type'] ?? 'paragraph';
$title = $attributes['title'] ?? '';
$text_content = $attributes['content'] ?? '';
$listItems = $attributes['listItems'] ?? [];
$audienceItems = $attributes['audienceItems'] ?? [];
$block_id = 'course-section-' . ($block->attributes['id'] ?? uniqid());

// Classes for consistently styled sections
$sectionClasses = "p-4 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden mb-6";
$titleClasses = "text-xl font-bold mb-4 text-neutral-950";

if ($type === 'paragraph') : ?>
    <section class="<?php echo $sectionClasses; ?>">
        <?php if ($title) : ?>
            <h2 class="<?php echo $titleClasses; ?>">
                <?php echo esc_html($title); ?>
            </h2>
        <?php endif; ?>
        <p class="text-base text-neutral-800 leading-6 ">
            <?php echo wp_kses_post($text_content); ?>
        </p>
    </section>

<?php elseif ($type === 'list') : ?>
    <section class="<?php echo $sectionClasses; ?>">
        <?php if ($title) : ?>
            <h2 class="<?php echo $titleClasses; ?>">
                <?php echo esc_html($title); ?>
            </h2>
        <?php endif; ?>
        <ul class="text-base text-neutral-800 leading-6 space-y-1 list-disc pr-8">
            <?php foreach ($listItems as $item) : ?>
                <?php if (trim($item)) : ?>
                    <li><?php echo esc_html($item); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </section>

<?php elseif ($type === 'target-audience') : ?>
    <section class="p-4 bg-white rounded-lg shadow-lg outline outline-1 outline-gray-200 space-y-4 text-right mb-6">
        <?php if ($title) : ?>
            <h2 class="<?php echo $titleClasses; ?>">
                <?php echo esc_html($title); ?>
            </h2>
        <?php endif; ?>

        <div id="<?php echo $block_id; ?>"
            style="scrollbar-width: none;"
            class="flex overflow-x-auto gap-4 px-1 py-4 cursor-pointer select-none">

            <?php foreach ($audienceItems as $item) : ?>
                <?php
                $icon_type = $item['iconType'] ?? 'system';
                $icon_size = $item['iconSize'] ?? 24;
                $icon_color = $item['iconColor'] ?? '#0ea5e9';
                $icon_style = "width: {$icon_size}px; height: {$icon_size}px;";
                $icon_html = '';

                if ($icon_type === 'image' && !empty($item['iconImage'])) {
                    $icon_html = '<img src="' . esc_url($item['iconImage']) . '" alt="" style="' . $icon_style . '" class="object-contain">';
                } elseif ($icon_type === 'font-awesome' && !empty($item['faIcon'])) {
                    $fa_style = "font-size: {$icon_size}px; color: {$icon_color};";
                    $icon_html = '<i class="' . esc_attr($item['faIcon']) . '" style="' . $fa_style . '"></i>';
                } else {
                    // Default to system SVG
                    $icon_file = !empty($item['icon']) ? $item['icon'] : 'Square Academic Cap.svg';
                    $icon_html = '<img src="' . get_template_directory_uri() . '/assets/images/vuesax/bold/' . esc_attr($icon_file) . '" alt="" style="' . $icon_style . '">';
                }
                ?>
                <div class="flex-1 min-w-[236px] p-3 bg-stone-50 rounded-xl outline outline-1 outline-sky-500 text-center space-y-2">
                    <div class="mx-auto flex items-center justify-center" style="height: 40px;">
                        <?php echo $icon_html; ?>
                    </div>
                    <h3 class="text-lg font-bold text-neutral-950"><?php echo esc_html($item['title'] ?? ''); ?></h3>
                    <p class="text-sm text-stone-500"><?php echo esc_html($item['desc'] ?? ''); ?></p>
                </div>
            <?php endforeach; ?>

        </div>

        <script>
            (function() {
                const el = document.getElementById("<?php echo $block_id; ?>");
                if (!el) return;

                let isDown = false;
                let startX;
                let scrollLeft;

                el.addEventListener("mousedown", e => {
                    isDown = true;
                    el.classList.add("cursor-grabbing");
                    startX = e.pageX - el.offsetLeft;
                    scrollLeft = el.scrollLeft;
                });

                el.addEventListener("mouseleave", () => isDown = false);
                el.addEventListener("mouseup", () => {
                    isDown = false;
                    el.classList.remove("cursor-grabbing");
                });

                el.addEventListener("mousemove", e => {
                    if (!isDown) return;
                    e.preventDefault();
                    const x = e.pageX - el.offsetLeft;
                    const walk = (x - startX) * 1.5;
                    el.scrollLeft = scrollLeft - walk;
                });
            })();
        </script>
    </section>
<?php endif; ?>