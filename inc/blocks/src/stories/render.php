<?php
/**
 * Stories Block
 */
$attrs   = $attributes ?? [];
$stories = $attrs['stories'] ?? [];

$default_image = get_template_directory_uri() . '/assets/images/new-1.jpg';

$stories = $stories ?: [[
    'image' => $default_image,
    'label' => 'أخبار عاجلة',
    'seen'  => false,
    'link'  => '#',
]];

$swiper_options = [
    'slidesPerView' => 'auto',
    'spaceBetween' => 0,
    'loop'          => true,
    'freeMode'      => true,
    'grabCursor'    => true,
    'autoplay'      => [
        'delay' => 900,
        'disableOnInteraction' => false,
    ],
    'breakpoints' => [
        768 => ['spaceBetween' => 0],
    ],
];
?>

<div id="stories-block-wrapper" class="relative w-full transition-all">
    <div id="stories-block-inner" class="container mx-auto relative z-30 transition-all">
        <div class="py-6 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-3xl shadow-lg max-w-7xl mx-auto">
            
            <div class="swiper js-swiper-init py-6"
                 data-swiper-config="<?= esc_attr(wp_json_encode($swiper_options)) ?>">
                 
                <div class="swiper-wrapper items-center">
                    <?php foreach ($stories as $i => $story):
                        $seen  = !empty($story['seen']);
                        $img   = esc_url($story['image'] ?? $default_image);
                        $link  = esc_url($story['link'] ?? '#');
                        $label = esc_html($story['label'] ?? 'Story');

                        $border = $seen
                            ? 'border-white border-dashed group-hover:border-solid'
                            : 'border-[#00E704]';

                        $imgAnim = $seen
                            ? 'group-hover:scale-116'
                            : 'group-hover:rotate-6';
                    ?>
                        <a href="<?= $link ?>"
                           class="swiper-slide !w-[160px] max-md:!w-[115px] group flex flex-col items-center justify-center gap-5 transition hover:scale-105"
                           data-aos="fade-up"
                           data-aos-delay="<?= 100 + $i * 50 ?>">

                            <div class="justify-self-center w-[120px] h-[120px] max-md:w-[70px] max-md:h-[70px] rounded-full p-1 border-4 <?= $border ?> transition group-hover:border-white shadow-lg">
                                <div class="rounded-full overflow-hidden border-2 <?= $border ?> bg-gray-100 transition">
                                    <img src="<?= $img ?>"
                                         alt="<?= $label ?>"
                                         loading="lazy"
                                         decoding="async"
                                         class="block w-full h-full object-cover grayscale group-hover:grayscale-0 <?= $imgAnim ?> transition duration-500">
                                </div>
                            </div>

                            <span class="mt-2 text-center w-full truncate text-white text-xl max-md:text-sm line-clamp-2 transition group-hover:scale-105">
                            <?php echo $label?>                                
                            </span>

                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
// document.addEventListener('DOMContentLoaded', () => {
//     const wrapper = document.getElementById('stories-block-wrapper');
//     const inner   = document.getElementById('stories-block-inner');
//     if (!wrapper || !inner) return;

//     const STICKY_TOP = 116;
//     let offsetTop = wrapper.offsetTop;

//     const setSticky = (on) => {
//         inner.classList.toggle('fixed', on);
//         inner.classList.toggle('relative', !on);
//         inner.classList.toggle('is-sticky-stories', on);
//         wrapper.style.height = on ? inner.offsetHeight + 'px' : 'auto';

//         if (on) {
//             inner.classList.add('top-[116px]', 'max-md:top-[100px]', 'left-0', 'right-0', 'z-40');
//         } else {
//             inner.classList.remove('top-[116px]', 'max-md:top-[100px]', 'left-0', 'right-0', 'z-40');
//         }
//     };

//     window.addEventListener('resize', () => offsetTop = wrapper.offsetTop);
//     window.addEventListener('scroll', () => {
//         setSticky(window.scrollY > offsetTop - STICKY_TOP);
//     }, { passive: true });
// });
</script>
