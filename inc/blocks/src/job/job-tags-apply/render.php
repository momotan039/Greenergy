<?php
$post_id = get_the_ID();
$tags = get_the_tags($post_id);
?>
<div class="w-full pl-3 py-3 flex-wrap gap-2 inline-flex justify-between items-center shadow-lg outline outline-1 outline-gray-200 p-6 rounded-lg">
    <div class="flex justify-start items-start gap-3">
        <?php if ($tags) : foreach ($tags as $tag) : ?>
                <div class="h-8 px-4 bg-primary/10 rounded-[100px] w-fit flex items-center border border-primary/20">
                    <a href="<?php echo get_tag_link($tag->term_id); ?>">
                        <span class="text-right text-primary-700 text-sm font-medium leading-5"><?php echo esc_html($tag->name); ?></span>
                    </a>
                </div>
        <?php endforeach;
        endif; ?>
    </div>
    <a href="#">
        <div class="h-10 p-4 bg-gradient-to-br from-sky-500 to-blue-700 rounded-[55px] flex justify-end items-center gap-2">
            <div class="justify-start text-white text-base font-medium leading-5">تقديم الان</div>
            <i class="fa-solid fa-arrow-left text-white"></i>
        </div>
    </a>
</div>