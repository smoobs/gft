<?php
//
// Recommended way to include parent theme styles.
//  (Please see http://codex.wordpress.org/Child_Themes#How_to_Create_a_Child_Theme)
//
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}
//
// Your code goes below
//

function woocommerce_after_shop_loop_item_title_short_description() {
	global $product;
	if ( ! $product->get_short_description() ) return; ?>
	<div itemprop="description">
	   <?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ) ?>
	</div>
	<?php
}

add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_after_shop_loop_item_title_short_description', 5);
