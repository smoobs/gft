<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );

// END ENQUEUE PARENT ACTION


function woocommerce_after_shop_loop_item_title_short_description() {
	global $product;
	if ( ! $product->get_short_description() ) return; ?>
	<div itemprop="description">
	   <?php echo apply_filters( 'woocommerce_short_description', $product->get_short_description() ) ?>
	</div>
	<?php
}

add_action('woocommerce_after_shop_loop_item_title', 'woocommerce_after_shop_loop_item_title_short_description', 5);

/**
 * Replace the home link URL
 */
add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );
function woo_custom_breadrumb_home_url() {
    return '/catalogue';
}

/**
 * Rename "home" in breadcrumb
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'wcc_change_breadcrumb_home_text' );
function wcc_change_breadcrumb_home_text( $defaults ) {
    // Change the breadcrumb home text from 'Home' to 'Apartment'
	$defaults['home'] = 'Golden Fleece Catalogue';
	return $defaults;
}

