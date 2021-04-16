<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Catalog Mode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(
	'yith-catalog-mode-button'       => array(
		'title'          => esc_html__( 'YITH Catalog Mode Button', 'yith-woocommerce-catalog-mode' ),
		'shortcode_name' => 'ywctm-button',
		'do_shortcode'   => true,
		'icon'           => 'eicon-button',
		'section_title'  => esc_html__( 'YITH Catalog Mode Button', 'yith-woocommerce-catalog-mode' ),
		'options'        => array(
			'wc_style_warning1' => array(
				'type'            => 'raw_html',
				/* translators: %1$s: open <b> tag - %2$s: close </b> tag - %3$s: open link tag - %4$s: close link tag */
				'raw'             => sprintf( esc_html__( 'This widget inherits the style from the settings of %1$sYITH Catalog Mode%2$s plugin that you can edit %3$shere%4$s', 'yith-woocommerce-catalog-mode' ), '<b>', '</b>', '[<a target="_blank" href="' . get_admin_url( null, 'edit.php?post_type=ywctm-button-label' ) . '">', '</a>]' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			),
		),
	),
	'yith-catalog-mode-inquiry-form' => array(
		'title'          => esc_html__( 'YITH Catalog Mode Inquiry Form', 'yith-woocommerce-catalog-mode' ),
		'shortcode_name' => 'ywctm-inquiry-form',
		'do_shortcode'   => true,
		'icon'           => 'eicon-form-horizontal',
		'section_title'  => esc_html__( 'YITH Catalog Mode Inquiry Form', 'yith-woocommerce-catalog-mode' ),
		'options'        => array(
			'wc_style_warning1' => array(
				'type'            => 'raw_html',
				/* translators: %1$s: open <b> tag - %2$s: close </b> tag - %3$s: open link tag - %4$s: close link tag */
				'raw'             => sprintf( esc_html__( 'This widget inherits the style from the settings of %1$sYITH Catalog Mode%2$s plugin that you can edit %3$shere%4$s', 'yith-woocommerce-catalog-mode' ), '<b>', '</b>', '[<a href="' . get_admin_url( null, 'admin.php?page=yith_wc_catalog_mode_panel&tab=inquiry-form' ) . '">', '</a>]' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			),
		),
	),
);
