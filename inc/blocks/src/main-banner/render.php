<?php
/**
 * Main Banner Block Template
 *
 * @package Greenergy
 */

$attributes = isset( $attributes ) ? $attributes : [];
$title      = isset( $attributes['title'] ) ? $attributes['title'] : 'اكتشف مستقبل الطاقة المتجددة';
$subtitle   = isset( $attributes['subtitle'] ) ? $attributes['subtitle'] : 'الاخبار';
$bg_image   = isset( $attributes['backgroundImage'] ) ? $attributes['backgroundImage'] : 'https://images.unsplash.com/photo-1506744038136-46273834b3fb';

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'bg-cover bg-center rounded-3xl inline-flex justify-between items-center',
    'style' => 'background-image: url(' . esc_url( $bg_image ) . ');',
] );
?>
<div <?php echo $wrapper_attributes; ?>>
    <div class="flex justify-center overflow-hidden pt-28 px-28 w-full">
        <div class="flex-1 h-44 inline-flex flex-col justify-start items-center gap-4">
            <div class="w-64 h-8 p-2.5 bg-white/20 rounded-[44px] backdrop-blur-[2px] inline-flex justify-center items-center gap-2.5">
                <div class="w-8 h-7 text-center justify-start text-white text-sm font-normal leading-5">
                    <?php echo esc_html( $subtitle ); ?>
                </div>
            </div>
            <div class="flex flex-col justify-center items-center gap-3">
                <div class="text-center justify-start text-white text-xl font-bold lg:text-5xl">
                    <?php echo esc_html( $title ); ?>
                </div>
            </div>
        </div>
    </div>
</div>
