<?php
/**
 * Render for AD Block
 */
$attributes = isset($attributes) ? $attributes : [];
$imageUrl = isset($attributes['imageUrl']) ? $attributes['imageUrl'] : '';
?>
<!-- AD section -->
<div class="container w-full m-auto my-10" data-aos="zoom-in" data-aos-duration="1000">
    <div class="self-stretch h-72 max-sm:h-52 w-full rounded-2xl border-2 border-green-700 overflow-hidden group">
        <img class="self-stretch h-full rounded-2xl w-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?php echo (empty($imageUrl) ? get_template_directory_uri() . '/assets/images/google-ad.png' : $imageUrl); ?>" />
    </div>
</div>