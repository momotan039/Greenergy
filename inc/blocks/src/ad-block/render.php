<?php
/**
 * Ad Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @package Greenergy
 */

$adType    = $attributes['adType'] ?? 'image';
$imageUrl  = $attributes['imageUrl'] ?? '';
$imageId   = $attributes['imageId'] ?? 0;
$adLink    = $attributes['adLink'] ?? '#';
$adCode    = $attributes['adCode'] ?? '';
$fullWidth = $attributes['fullWidth'] ?? false;

if ( ! empty( $imageId ) ) {
    $lib_url = wp_get_attachment_image_url( $imageId, 'full' );
    if ( $lib_url ) {
        $imageUrl = $lib_url;
    }
}

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'container mx-auto greenergy-ad-block my-12 text-center relative'
] );

// Placeholder image if empty and type is image
if ( $adType === 'image' && empty( $imageUrl ) ) {
    $imageUrl = 'https://ps.w.org/adrotate/assets/banner-1544x500.jpg?rev=3117289';
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="ad-container h-64 overflow-hidden rounded-xl shadow-sm hover:shadow-md transition-shadow" data-aos="flip-up" data-aos-duration="1000">
        <?php if ( $adType === 'image' ) : ?>
            <a href="<?php echo esc_url( $adLink ); ?>" target="_blank" rel="nofollow" class="block">
                <img src="<?php echo esc_url( $imageUrl ); ?>" 
                     alt="<?php echo esc_attr__( 'Advertisement', 'greenergy' ); ?>" 
                     class="w-full h-64 block object-fill">
            </a>
        <?php else : ?>
            <div class="custom-ad-code">
                <?php 
                // We don't escape adCode here because it's expected to be a script or iframe, 
                // but in a production environment, we should be careful.
                echo $adCode; 
                ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ( is_admin() ) : ?>
        <div class="mt-2 text-xs text-gray-400 uppercase tracking-widest font-bold">
            <?php esc_html_e( 'Advertisement Area', 'greenergy' ); ?>
        </div>
    <?php endif; ?>
</div>
