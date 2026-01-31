<?php
/**
 * News Filter Block Template.
 *
 * @param   array $attributes - Block attributes.
 * @param   array $content - Block content.
 * @param   array $block - Block instance.
 * @package Greenergy
 */

$wrapper_attributes = get_block_wrapper_attributes( [
    'class' => 'overflow-x-auto p-3 w-full rounded-2xl inline-flex justify-start items-center gap-6',
] );

// Get current category from URL
$current_cat = isset( $_GET['news_cat'] ) ? sanitize_text_field( $_GET['news_cat'] ) : '';
$current_sort = isset( $_GET['sort'] ) ? sanitize_text_field( $_GET['sort'] ) : 'latest';

// Get categories
$terms = get_terms( [
    'taxonomy'   => 'news_category',
    'hide_empty' => false,
] );

$all_news_class = empty( $current_cat ) 
    ? 'h-10 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex justify-center items-center gap-2.5 text-white' 
    : 'h-10 px-4 rounded-lg flex justify-center items-center gap-2.5 text-neutral-950 hover:bg-gray-100 transition-colors';

$all_news_text_class = empty( $current_cat ) ? 'text-white' : 'text-neutral-950';

?>

<div <?php echo $wrapper_attributes; ?>>
    <!-- categories -->
    <div class="overflow-x-auto bg-green-200 w-2/3 max-md:w-full h-14 p-1.5 bg-green-100 rounded-xl inline-flex justify-start items-center gap-4">
        <div class="inline-flex justify-end items-center gap-4">
            
            <!-- All News -->
            <a href="<?php echo esc_url( remove_query_arg( 'news_cat' ) ); ?>" class="<?php echo esc_attr( $all_news_class ); ?>">
                <div class="w-20 h-7 text-right justify-start <?php echo esc_attr( $all_news_text_class ); ?> text-sm font-normal capitalize leading-6">
                    جميع الاخبار
                </div>
            </a>

            <?php 
            if ( empty( $terms ) || is_wp_error( $terms ) ) {
                // Fallback Data
                $terms = [
                    (object) [ 'slug' => 'solar-energy', 'name' => 'طاقة شمسية' ],
                    (object) [ 'slug' => 'wind-energy', 'name' => 'طاقة رياح' ],
                    (object) [ 'slug' => 'green-tech', 'name' => 'تكنولوجيا خضراء' ],
                    (object) [ 'slug' => 'sustainability', 'name' => 'استدامة' ],
                    (object) [ 'slug' => 'projects', 'name' => 'مشاريع' ],
                ];
            }
            
            foreach ( $terms as $term ) : 
                $is_active = $current_cat === $term->slug;
                $item_class = $is_active 
                    ? 'h-10 px-4 bg-gradient-to-l from-green-700 via-lime-600 to-lime-300 rounded-lg flex justify-center items-center gap-2.5 text-white' 
                    : 'h-10 px-4 rounded-lg flex justify-center items-center gap-2.5 text-neutral-950 hover:bg-gray-100 transition-colors';
                $text_class = $is_active ? 'text-white' : 'text-neutral-950';
                $url = add_query_arg( 'news_cat', $term->slug );
            ?>
                <a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $item_class ); ?>">
                    <div class="w-20 h-7 text-right justify-start <?php echo esc_attr( $text_class ); ?> text-sm font-normal capitalize leading-6 truncate">
                        <?php echo esc_html( $term->name ); ?>
                    </div>
                </a>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- sort by -->
    <div class="max-md:hidden max-lg:flex-col flex flex-1">
        <div class="self-center pl-4 text-sm">
            ترتيب حسب :
        </div>
        
        <form method="get" class="flex-1 h-12 px-4 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-neutral-200 flex justify-between items-center cursor-pointer relative group">
            <?php 
            // Preserve other query params
            foreach ( $_GET as $key => $val ) {
                if ( 'sort' === $key ) continue;
                echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
            }
            ?>
            
            <select name="sort" onchange="this.form.submit()" class="appearance-none bg-transparent border-none w-full h-full text-right justify-start text-neutral-950 text-sm font-normal capitalize leading-6 focus:ring-0 cursor-pointer pr-8 py-2">
                <option value="latest" <?php selected( $current_sort, 'latest' ); ?>>الاحدث</option>
                <option value="oldest" <?php selected( $current_sort, 'oldest' ); ?>>الاقدم</option>
                <option value="popular" <?php selected( $current_sort, 'popular' ); ?>>الاكثر قراءة</option>
            </select>
            
            <svg class="w-6 h-6 self-center absolute left-4 pointer-events-none" aria-hidden="true">
                <use href="<?php echo get_template_directory_uri(); ?>/assets/images/vuesax/outline/arrow-down.svg"></use>
            </svg>
        </form>
    </div>
</div>
