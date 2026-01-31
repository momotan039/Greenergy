<?php
/**
 * Render for Stories Block
 */
$attributes = isset($attributes) ? $attributes : [];
$stories = isset($attributes['stories']) ? $attributes['stories'] : [];
?>
<!-- Status Section -->
<div class="container w-full m-auto">
    <div class="bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-3xl relative z-[2] max-w-7xl mx-auto px-4">
        <div class="py-5 overflow-x-auto">
            <div class="flex gap-4">
                <?php if ( ! empty( $stories ) ) : ?>
                    <?php foreach ( $stories as $index => $story ) : 
                        $is_unseen = !$story['seen']; // Force all to look active? Or maybe random? Let's stick to Active style.
                        if($is_unseen): ?>
                        <a href="<?php echo isset($story['link']) ? esc_url($story['link']) : '#'; ?>"
                            class="group block text-center transition-all duration-300 hover:scale-110 flex-shrink-0"
                            data-aos="fade-up" data-aos-delay="<?php echo esc_attr(100 + ($index * 50)); ?>">
                            <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-full mx-auto mb-3 p-1 border-4 border-[#00E704] transition-colors duration-300 group-hover:border-white shadow-lg">
                                <div class="w-full h-full rounded-full overflow-hidden border-2 border-[#00E704] bg-gray-100 group-hover:border-white transition-colors duration-300">
                                    <img src="<?php echo !empty($story['image']) ? esc_url($story['image']) : get_template_directory_uri() . '/assets/images/new-1.jpg'; ?>" 
                                         loading="lazy" decoding="async" alt="<?php echo esc_attr($story['label']); ?>"
                                         class="w-full h-full object-cover block grayscale group-hover:grayscale-0 group-hover:rotate-6 transition-all duration-500">
                                </div>
                            </div>
                            <span class="text-white text-xl leading-[1.3] group-hover:font-bold transition-all">
                                <?php echo esc_html($story['label']); ?>
                            </span>
                        </a>
                         <!-- Story seen -->
                          <?php else: ?>
                    <a href="#"
                        class="group block text-center transition-all duration-300 hover:scale-110 flex-shrink-0"
                        data-aos="fade-up" data-aos-delay="<?php echo esc_attr(100 + ($index * 50)); ?>">
                        <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-full mx-auto mb-3 p-1 border-4 border-white border-dashed transition-all duration-300 group-hover:border-solid shadow-lg">
                            <div class="w-full h-full rounded-full overflow-hidden border-2 border-white border-dashed group-hover:border-solid transition-all duration-300">
                                <img src="<?php echo !empty($story['image']) ? esc_url($story['image']) : get_template_directory_uri() . '/assets/images/new-1.jpg'; ?>" loading="lazy" decoding="async" alt="Story title"
                                    class="w-full h-full object-cover block grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-500">
                            </div>
                        </div>
                        <span class="text-white text-xl font-bold leading-[1.3] opacity-80 group-hover:opacity-100 transition-opacity">
                            ملخص تقرير عالمي
                        </span>
                    </a>
                    <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback if no stories -->
                    <a href="#" class="group block text-center transition-transform duration-200 hover:scale-105 flex-shrink-0">
                         <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-full mx-auto mb-3 p-1 border-4 border-[#00E704] ">
                            <div class="w-full h-full rounded-full overflow-hidden border-2 border-[#00E704] bg-gray-100">
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/new-1.jpg" loading="lazy" decoding="async" alt="Story title"
                                    class="w-full h-full object-cover block grayscale group-hover:grayscale-0 transition-all duration-300">
                            </div>
                        </div>
                        <span class="text-white text-xl font-bold leading-[1.3]">
                            أخبار عاجلة
                        </span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
