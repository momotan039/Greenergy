<?php
/**
 * Render for AD Block
 */
$attributes = isset($attributes) ? $attributes : [];
$imageUrl = isset($attributes['imageUrl']) ? $attributes['imageUrl'] : '';
$height = isset($attributes['height']) ? $attributes['height'] : '72';
$hasContainer = isset($attributes['hasContainer']) ? $attributes['hasContainer'] : true;
$containerClass = $hasContainer ? 'container' : '';
?>
<!-- AD section -->
<div class="<?php echo $containerClass; ?> w-full m-auto my-10" data-aos="zoom-in" data-aos-duration="1000">
    <div class="self-stretch w-full rounded-2xl border-2 border-green-700 overflow-hidden group relative" style="height: <?php echo esc_attr( $height ); ?>;">
        <?php if ( ! empty( $imageUrl ) ) : ?>
            <img class="self-stretch h-full rounded-2xl w-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?php echo esc_url( $imageUrl ); ?>" />
        <?php else : ?>
            <img class="self-stretch h-full rounded-2xl w-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?php echo get_template_directory_uri(); ?>/assets/images/google-ad.png" />
        <?php endif; ?>
    </div>
</div>