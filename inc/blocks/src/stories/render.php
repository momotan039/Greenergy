<?php
$stories = $attributes['stories'] ?? [];

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'greenergy-stories-block py-12 relative overflow-hidden bg-white',
] );
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-3xl  relative z-[2] max-w-7xl mx-auto px-4">
        <div class="swiper stories-swiper py-5 !overflow-visible" data-aos="zoom-out" data-aos-duration="1000">
            <div class="swiper-wrapper">
                <?php foreach ( $stories as $story ) : 
                    $img_src = $story['image'];
                    if ( ! empty( $story['imageId'] ) ) {
                        $lib_url = wp_get_attachment_image_url( $story['imageId'], 'thumbnail' );
                        if ( $lib_url ) {
                            $img_src = $lib_url;
                        }
                    }
                ?>
                    <div class="swiper-slide !w-auto h-auto">
                        <a href="<?php echo esc_url( $story['link'] ); ?>" 
                           class="story-item group block text-center transition-transform duration-200 hover:scale-105 w-[110px] lg:w-[130px]">
                            <!-- Circle -->
                            <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-full mx-auto mb-3 p-1 bg-gradient-to-tr from-[#229924] to-[#ABEF74]">
                                <div class="w-full h-full rounded-full overflow-hidden border-2 border-white bg-gray-100">
                                    <img src="<?php echo esc_url( $img_src ); ?>" 
                                         alt="<?php echo esc_attr( $story['label'] ); ?>" 
                                         class="w-full h-full object-cover block grayscale group-hover:grayscale-0 transition-all duration-300">
                                </div>
                            </div>
                            <span class="text-[#0B0C0B] text-xs lg:text-sm font-bold leading-[1.3] block px-1 line-clamp-2">
                                <?php echo esc_html( $story['label'] ); ?>
                            </span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php if ( ! is_admin() ) : ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.stories-swiper', {
                slidesPerView: 'auto',
                spaceBetween: 20,
                freeMode: true,
                grabCursor: true,
                breakpoints: {
                    640: { spaceBetween: 30 }
                }
            });
        }
    });
</script>
<?php endif; ?>
