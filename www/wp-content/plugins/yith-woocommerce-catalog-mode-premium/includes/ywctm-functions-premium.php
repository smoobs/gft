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

if ( ! function_exists( 'ywctm_is_multivendor_active' ) ) {

	/**
	 * Check if YITH WooCommerce Multi Vendor is active
	 *
	 * @return  boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_is_multivendor_active() {
		return defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM;
	}
}

if ( ! function_exists( 'ywctm_is_multivendor_integration_active' ) ) {

	/**
	 * Check if YITH WooCommerce Multi Vendor integration is active
	 *
	 * @return  boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_is_multivendor_integration_active() {
		return get_option( 'yith_wpv_vendors_enable_catalog_mode' ) === 'yes';
	}
}

if ( ! function_exists( 'ywctm_get_vendor_id' ) ) {

	/**
	 * Get current vendor ID
	 *
	 * @param boolean $id_only ID-only checker.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_vendor_id( $id_only = false ) {

		$vendor_id = '';

		if ( ywctm_is_multivendor_active() && ywctm_is_multivendor_integration_active() ) {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( 0 < $vendor->id && ! $vendor->is_super_user() ) {

				$vendor_id = ( $id_only ? $vendor->id : '_' . $vendor->id );

			}
		}

		return $vendor_id;

	}
}

if ( ! function_exists( 'ywctm_get_vendor_id_frontend' ) ) {

	/**
	 * Get current vendor ID for frontend pages.
	 *
	 * @return  string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_vendor_id_frontend() {

		if ( ywctm_is_multivendor_active() && ywctm_is_multivendor_integration_active() ) {
			$vendor = yith_get_vendor( get_post(), 'product' );
			if ( 0 < $vendor->id ) {
				return $vendor->id;
			}
		}

		return '';

	}
}

if ( ! function_exists( 'ywctm_get_exclusion_fields' ) ) {

	/**
	 * Get the exclusion fiedls for Product, Category & Tag page
	 *
	 * @param array $item Product, Category or Tag excclusion data.
	 *
	 * @return  array
	 * @since   2.0.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_exclusion_fields( $item ) {
		return array(
			array(
				'id'    => 'ywctm_enable_inquiry_form',
				'name'  => 'ywctm_enable_inquiry_form',
				'type'  => 'onoff',
				'title' => esc_html__( 'Inquiry Form', 'yith-woocommerce-catalog-mode' ),
				'value' => $item['enable_inquiry_form'],
				'desc'  => esc_html__( 'Choose whether to show or hide the inquiry form on these product pages.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'    => 'ywctm_enable_atc_custom_options',
				'name'  => 'ywctm_enable_atc_custom_options',
				'type'  => 'onoff',
				'title' => esc_html__( 'Use custom options for "Add to Cart"', 'yith-woocommerce-catalog-mode' ),
				'value' => $item['enable_atc_custom_options'],
				'desc'  => esc_html__( 'Enable to override the default settings for the "Add to cart" button.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'      => 'ywctm_atc_status',
				'name'    => 'ywctm_atc_status',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'show' => esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' ),
					'hide' => esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ),
				),
				'title'   => esc_html__( 'Set "Add to Cart" as:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['atc_status'],
			),
			array(
				'id'      => 'ywctm_custom_button',
				'name'    => 'ywctm_custom_button',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => ywctm_get_buttons_labels(),
				'default' => 'none',
				'title'   => esc_html__( 'Replace "Add to Cart" in the product page with:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['custom_button'],
			),
			array(
				'id'      => 'ywctm_custom_button_url',
				'name'    => 'ywctm_custom_button_url',
				'type'    => 'text',
				'default' => '',
				'title'   => esc_html__( 'Override URL:', 'yith-woocommerce-catalog-mode' ),
				'value'   => isset( $item['custom_button_url'] ) ? $item['custom_button_url'] : '',
				'desc'    => esc_html__( 'Replace the button URL with a custom one. Leave empty to use the default URL.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'      => 'ywctm_custom_button_loop',
				'name'    => 'ywctm_custom_button_loop',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => ywctm_get_buttons_labels(),
				'default' => 'none',
				'title'   => esc_html__( 'Replace "Add to Cart" in shop pages with:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['custom_button_loop'],
			),
			array(
				'id'      => 'ywctm_custom_button_loop_url',
				'name'    => 'ywctm_custom_button_loop_url',
				'type'    => 'text',
				'default' => '',
				'title'   => esc_html__( 'Override URL:', 'yith-woocommerce-catalog-mode' ),
				'value'   => isset( $item['custom_button_loop_url'] ) ? $item['custom_button_loop_url'] : '',
				'desc'    => esc_html__( 'Replace the button URL with a custom one. Leave empty to use the default URL.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'    => 'ywctm_enable_price_custom_options',
				'name'  => 'ywctm_enable_price_custom_options',
				'type'  => 'onoff',
				'title' => esc_html__( 'Use custom options for price', 'yith-woocommerce-catalog-mode' ),
				'value' => $item['enable_price_custom_options'],
				'desc'  => esc_html__( 'Enable to override the default settings for price.', 'yith-woocommerce-catalog-mode' ),
			),
			array(
				'id'      => 'ywctm_price_status',
				'name'    => 'ywctm_price_status',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => array(
					'show' => esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' ),
					'hide' => esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ),
				),
				'title'   => esc_html__( 'Set price as:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['price_status'],
			),
			array(
				'id'      => 'ywctm_custom_price_text',
				'name'    => 'ywctm_custom_price_text',
				'type'    => 'select',
				'class'   => 'wc-enhanced-select',
				'options' => ywctm_get_buttons_labels(),
				'default' => 'none',
				'title'   => esc_html__( 'Replace price with:', 'yith-woocommerce-catalog-mode' ),
				'value'   => $item['custom_price_text'],
			),
			array(
				'id'      => 'ywctm_custom_price_text_url',
				'name'    => 'ywctm_custom_price_text_url',
				'type'    => 'text',
				'default' => '',
				'title'   => esc_html__( 'Override URL:', 'yith-woocommerce-catalog-mode' ),
				'value'   => isset( $item['custom_price_text_url'] ) ? $item['custom_price_text_url'] : '',
				'desc'    => esc_html__( 'Replace the button URL with a custom one. Leave empty to use the default URL.', 'yith-woocommerce-catalog-mode' ),
			),
		);
	}
}

/**
 * CUSTOM BUTTON RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_get_buttons_labels' ) ) {

	/**
	 * Get the list of all buttons and labels
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_buttons_labels() {

		$data = get_posts(
			array(
				'post_type'        => 'ywctm-button-label',
				'suppress_filters' => false,
				'numberposts'      => - 1,
			)
		);
		$list = array(
			'none' => esc_html__( 'Nothing', 'yith-woocommerce-catalog-mode' ),
		);
		if ( $data ) {
			foreach ( $data as $post ) {
				$list[ $post->ID ] = '' !== $post->post_title ? $post->post_title : esc_html__( '(no name)', 'yith-woocommerce-catalog-mode' );
			}
		}

		return $list;

	}
}

if ( ! function_exists( 'ywctm_get_active_buttons_id' ) ) {

	/**
	 * Get the IDs of all buttons and labels
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_active_buttons_id() {
		$list = get_posts(
			array(
				'post_type'   => 'ywctm-button-label',
				'numberposts' => - 1,
				'fields'      => 'ids',
			)
		);

		return $list;
	}
}

if ( ! function_exists( 'ywctm_get_buttons_label_name' ) ) {

	/**
	 * Get the list of all buttons and labels
	 *
	 * @param integer $id Button ID.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_buttons_label_name( $id ) {

		$post  = get_post( $id );
		$title = $post ? $post->post_title : esc_html__( 'Nothing', 'yith-woocommerce-catalog-mode' );
		$title = '' !== $title ? $title : esc_html__( '(no name)', 'yith-woocommerce-catalog-mode' );

		return '<strong>' . $title . '</strong>';
	}
}

if ( ! function_exists( 'ywctm_get_icon_class' ) ) {

	/**
	 * Get Icon Class
	 *
	 * @param string $icon Icon class.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_icon_class( $icon ) {

		$icon_data  = explode( ':', $icon );
		$icon_class = '';

		switch ( $icon_data[0] ) {
			case 'FontAwesome':
				$icon_class = 'fa fa-' . $icon_data[1];
				break;
			case 'Dashicons':
				$icon_class = 'dashicons dashicons-' . $icon_data[1];
				break;
			case 'retinaicon-font':
				$icon_class = 'retinaicon-font ' . $icon_data[1];
				break;
			default:
		}

		return $icon_class;

	}
}

if ( ! function_exists( 'ywctm_get_button_label_settings' ) ) {

	/**
	 * Get settings of selected custom button
	 *
	 * @param integer $id Button ID.
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_button_label_settings( $id ) {

		if ( ! $id ) {
			return array();
		}

		$settings = apply_filters(
			'ywctm_button_label_settings',
			array(
				'label_text'              => get_post_meta( $id, 'ywctm_label_text', true ),
				'icon_type'               => get_post_meta( $id, 'ywctm_icon_type', true ),
				'selected_icon'           => get_post_meta( $id, 'ywctm_selected_icon', true ),
				'selected_icon_size'      => get_post_meta( $id, 'ywctm_selected_icon_size', true ),
				'selected_icon_alignment' => get_post_meta( $id, 'ywctm_selected_icon_alignment', true ),
				'icon_color'              => get_post_meta( $id, 'ywctm_icon_color', true ),
				'custom_icon'             => get_post_meta( $id, 'ywctm_custom_icon', true ),
				'default_colors'          => get_post_meta( $id, 'ywctm_default_colors', true ),
				'hover_colors'            => get_post_meta( $id, 'ywctm_hover_colors', true ),
				'border_radius'           => get_post_meta( $id, 'ywctm_border_radius', true ),
				'border_thickness'        => get_post_meta( $id, 'ywctm_border_thickness', true ),
				'width_settings'          => get_post_meta( $id, 'ywctm_width_settings', true ),
				'margin_settings'         => get_post_meta( $id, 'ywctm_margin_settings', true ),
				'padding_settings'        => get_post_meta( $id, 'ywctm_padding_settings', true ),
				'button_url_type'         => get_post_meta( $id, 'ywctm_button_url_type', true ),
				'button_url'              => get_post_meta( $id, 'ywctm_button_url', true ),
				'hover_animation'         => get_post_meta( $id, 'ywctm_hover_animation', true ),
			),
			$id
		);

		if ( ! isset( $settings['margin_settings']['dimensions'] ) ) {
			$dimensions = $settings['margin_settings'];

			$settings['margin_settings'] = array(
				'dimensions' => $dimensions,
				'unit'       => 'px',
				'linked'     => 'no',
			);
		}

		if ( ! isset( $settings['padding_settings']['dimensions'] ) ) {
			$dimensions = $settings['padding_settings'];

			$settings['padding_settings'] = array(
				'dimensions' => $dimensions,
				'unit'       => 'px',
				'linked'     => 'no',
			);
		}

		if ( empty( $settings['border_radius'] ) ) {
			$old_radius = get_post_meta( $id, 'ywctm_border_style', true )['radius'];

			$settings['border_radius'] = array(
				'dimensions' => array(
					'top-left'     => $old_radius,
					'top-right'    => $old_radius,
					'bottom-right' => $old_radius,
					'bottom-left'  => $old_radius,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			);
		}

		if ( empty( $settings['border_thickness'] ) ) {
			$old_thickness = get_post_meta( $id, 'ywctm_border_style', true )['thickness'];

			$settings['border_thickness'] = array(
				'dimensions' => array(
					'top'    => $old_thickness,
					'right'  => $old_thickness,
					'bottom' => $old_thickness,
					'left'   => $old_thickness,
				),
				'unit'       => 'px',
				'linked'     => 'no',
			);
		}

		if ( empty( $settings['default_colors'] ) ) {
			$old_default_text       = get_post_meta( $id, 'ywctm_text_color', true )['default'];
			$old_default_background = get_post_meta( $id, 'ywctm_background_color', true )['default'];
			$old_default_border     = get_post_meta( $id, 'ywctm_border_color', true )['default'];

			$settings['default_colors'] = array(
				'background' => $old_default_background,
				'text'       => $old_default_text,
				'borders'    => $old_default_border,
			);
		}

		if ( empty( $settings['hover_colors'] ) ) {
			$old_hover_text       = get_post_meta( $id, 'ywctm_text_color', true )['hover'];
			$old_hover_background = get_post_meta( $id, 'ywctm_background_color', true )['hover'];
			$old_hover_border     = get_post_meta( $id, 'ywctm_border_color', true )['hover'];

			$settings['hover_colors'] = array(
				'background' => $old_hover_background,
				'text'       => $old_hover_text,
				'borders'    => $old_hover_border,
			);
		}

		if ( empty( $settings['hover_animation'] ) ) {
			$settings['hover_animation'] = 'none';
		}

		return $settings;

	}
}

if ( ! function_exists( 'ywctm_check_hover_effect' ) ) {

	/**
	 * Check if the button has a special hover effect and return alternative CSS value
	 *
	 * @param string $effect    The effect ID.
	 * @param string $value     The normal value.
	 * @param string $alt_value The alternative value.
	 *
	 * @return  string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_check_hover_effect( $effect, $value, $alt_value ) {

		$special_hover_effects = array(
			'slide-top',
			'slide-left',
		);

		return in_array( $effect, $special_hover_effects, true ) ? $alt_value : $value;

	}
}

if ( ! function_exists( 'ywctm_set_custom_button_css' ) ) {

	/**
	 * Create CSS rules for each custom button
	 *
	 * @param string $button_id       The ID of the button.
	 * @param array  $button_settings The button settings array.
	 *
	 * @return  string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_set_custom_button_css( $button_id, $button_settings ) {

		$hover_effect        = $button_settings['hover_animation'];
		$color               = $button_settings['default_colors']['text'];
		$base_bg_color       = $button_settings['default_colors']['background'];
		$base_hover_bg_color = $button_settings['hover_colors']['background'];
		$bg_color            = ywctm_check_hover_effect( $hover_effect, $base_bg_color, 'none' );
		$border_color        = $button_settings['default_colors']['borders'];
		$border_radius       = ywctm_sanitize_dimension_field( $button_settings['border_radius']['dimensions'], array( 'top-left', 'top-right', 'bottom-right', 'bottom-left' ), 'px' );
		$border_width        = ywctm_sanitize_dimension_field( $button_settings['border_thickness']['dimensions'], array( 'top', 'right', 'bottom', 'left' ), 'px' );
		$margin              = ywctm_sanitize_dimension_field( $button_settings['margin_settings']['dimensions'], array( 'top', 'right', 'bottom', 'left' ), 'px' );
		$padding             = ywctm_sanitize_dimension_field( $button_settings['padding_settings']['dimensions'], array( 'top', 'right', 'bottom', 'left' ), 'px' );
		$width               = ywctm_sanitize_width_field( $button_settings['width_settings'] );
		$hover_color         = $button_settings['hover_colors']['text'];
		$hover_bg_color      = ywctm_check_hover_effect( $hover_effect, $base_hover_bg_color, 'none' );
		$hover_border_color  = $button_settings['hover_colors']['borders'];

		$css = "
		.ywctm-button-$button_id .ywctm-custom-button {
			color:$color;
			background-color:$bg_color;
			border-style:solid;
			border-color:$border_color;
			border-radius:$border_radius;
			border-width:$border_width;
			margin:$margin;
			padding:$padding;
			width:$width;
		}
	
		.ywctm-button-$button_id .ywctm-custom-button:hover {
			color:$hover_color;
			background-color:$hover_bg_color;
			border-color:$hover_border_color;
		}
		";

		if ( 'icon' === $button_settings['icon_type'] ) {
			$icon_size        = $button_settings['selected_icon_size'] . 'px';
			$icon_color       = $button_settings['icon_color']['default'];
			$hover_icon_color = $button_settings['icon_color']['hover'];
			$icon_align       = $button_settings['selected_icon_alignment'];

			$css .= "
				.ywctm-button-$button_id .ywctm-custom-button .ywctm-icon-form {
					font-size:$icon_size;
					color:$icon_color;
					align-self:$icon_align;
				}
		
				.ywctm-button-$button_id .ywctm-custom-button:hover .ywctm-icon-form {
					color:$hover_icon_color;
				}
			";
		} elseif ( 'custom' === $button_settings['icon_type'] ) {
			$icon_align = $button_settings['selected_icon_alignment'];

			$css .= "
				.ywctm-button-$button_id .ywctm-custom-button .ywctm-icon-form {
					align-self:$icon_align;
				}
			";
		}

		switch ( $hover_effect ) {
			case 'slide-top':
			case 'slide-left':
				$css .= "
				.ywctm-button-$button_id .ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-top:after,
				.ywctm-button-$button_id .ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-left:after {
					background-color:$base_hover_bg_color;
				}
				.ywctm-button-$button_id .ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-top:before,
				.ywctm-button-$button_id .ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-left:before {
					background-color:$base_bg_color;
				}
				";
				break;

			case 'move-hover-color':
				$css .= "
				.ywctm-button-$button_id .ywctm-custom-button.ywctm-hover-effect.ywctm-effect-move-hover-color:before {
					background-color:$base_hover_bg_color;
					border-radius:$border_radius;
				}
				";
				break;
		}

		return $css;

	}
}

if ( ! function_exists( 'ywctm_sanitize_dimension_field' ) ) {

	/**
	 * Sanitize dimension fields for possible missing values
	 *
	 * @param array  $option          The available option array.
	 * @param array  $required_values The needed values array.
	 * @param string $unit            The dimension unit.
	 *
	 * @return  string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_sanitize_dimension_field( $option, $required_values, $unit ) {
		$dimensions = array();
		foreach ( $required_values as $required_value ) {
			if ( ! isset( $option[ $required_value ] ) || '' === $option[ $required_value ] || '0' === $option[ $required_value ] ) {
				$dimensions[] = 0;
			} else {
				$dimensions[] = $option[ $required_value ] . $unit;
			}
		}

		return implode( ' ', $dimensions );
	}
}

if ( ! function_exists( 'ywctm_sanitize_width_field' ) ) {

	/**
	 * Sanitize width field for possible missing values
	 *
	 * @param array $option The option array.
	 *
	 * @return  string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_sanitize_width_field( $option ) {

		$width = '0';

		if ( isset( $option['width'] ) ) {
			$width = $option['width'] . ( '%' !== $option['unit'] ? 'px' : '%' );
		}

		return $width;
	}
}

if ( ! function_exists( 'ywctm_enabled_google_fonts' ) ) {

	/**
	 * Get enabled Google Fonts
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_enabled_google_fonts() {

		$google_fonts = array(
			'Roboto'          => 'Roboto,sans-serif',
			'Slabo 27px'      => 'Slabo 27px,serif',
			'Oswald'          => 'Oswald,sans-serif',
			'Montserrat'      => 'Montserrat,sans-serif',
			'Source Sans Pro' => 'Source Sans Pro,sans-serif',
			'Dancing Script'  => 'Dancing Script,cursive',
			'Lora'            => 'Lora,serif',
			'Gochi Hand'      => 'GochiHand,cursive',
		);

		$theme_font = ywctm_get_theme_font();
		if ( $theme_font ) {
			$google_fonts = array_merge( $google_fonts, $theme_font );
		}

		// APPLY_FILTER: ywctm_google_fonts: add or remove supported Google Fonts.
		return apply_filters( 'ywctm_google_fonts', $google_fonts );
	}
}

if ( ! function_exists( 'ywctm_parse_icons' ) ) {

	/**
	 * Replaces the placeholders with icons HTML
	 *
	 * @param string $text Icon placeholder.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_parse_icons( $text ) {
		$pattern     = '/{{(((\w+-?)*) ((\w+\d*-?)*))}}/m';
		$replacement = '<i class="$1"></i>';

		return preg_replace( $pattern, $replacement, $text );
	}
}

if ( ! function_exists( 'ywctm_get_custom_button_url_override' ) ) {

	/**
	 * Get the custom URL override
	 *
	 * @param WC_Product $product The Product object.
	 * @param string     $type    Button type.
	 * @param boolean    $is_loop Loop checker.
	 *
	 * @return  string
	 * @since   2.0.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_custom_button_url_override( $product, $type, $is_loop = false ) {

		if ( ! $is_loop && 'atc' === $type ) {
			$option = 'custom_button_url';
		} elseif ( $is_loop && 'atc' === $type ) {
			$option = 'custom_button_loop_url';
		} else {
			$option = 'custom_price_text_url';
		}

		if ( ! $product instanceof WC_Product ) {
			return '';
		}

		$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $product->get_id(), '_ywctm_exclusion_settings' );

		if ( $product_exclusion ) {

			if ( 'yes' === $product_exclusion[ 'enable_' . $type . '_custom_options' ] ) {
				return $product_exclusion[ $option ];
			}
		}

		$product_cats = wp_get_object_terms( $product->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
		foreach ( $product_cats as $cat_id ) {

			$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $product->get_id(), $cat_id, '_ywctm_exclusion_settings' );
			if ( $product_exclusion ) {

				if ( 'yes' === $product_exclusion[ 'enable_' . $type . '_custom_options' ] ) {

					return $product_exclusion[ $option ];
				}
			}
		}

		$product_tags = wp_get_object_terms( $product->get_id(), 'product_tag', array( 'fields' => 'ids' ) );
		foreach ( $product_tags as $tag_id ) {

			$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $product->get_id(), $tag_id, '_ywctm_exclusion_settings' );
			if ( $product_exclusion ) {

				if ( 'yes' === $product_exclusion[ 'enable_' . $type . '_custom_options' ] ) {

					return $product_exclusion[ $option ];
				}
			}
		}

		return '';

	}
}

if ( ! function_exists( 'ywctm_buttons_id_with_custom_url' ) ) {

	/**
	 * Get the IDs of all buttons and labels with custom URL
	 *
	 * @return  array
	 * @since   2.0.3
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_buttons_id_with_custom_url() {
		$list = get_posts(
			array(
				'post_type'   => 'ywctm-button-label',
				'numberposts' => - 1,
				'fields'      => 'ids',
				'meta_key'    => 'ywctm_button_url_type', //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'  => 'custom', //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		return $list;
	}
}

if ( ! function_exists( 'ywctm_get_theme_font' ) ) {

	/**
	 * Get main theme font
	 *
	 * @return  array|boolean
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_theme_font() {

		$theme_name = strtolower( ywctm_get_theme_name() );
		$theme_font = false;

		switch ( $theme_name ) {
			case 'yith proteo':
				$font       = json_decode( get_theme_mod( 'yith_proteo_body_font' ), true );
				$theme_font = array(
					$font['font'] => $font['font'] . ',' . $font['category'],
				);
				break;
		}

		return $theme_font;
	}
}

/**
 * EXCLUSION TABLE RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_add_to_cart_column' ) ) {

	/**
	 * Print the add to cart column in the exclusion table
	 *
	 * @param array $item Exclusion item.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_add_to_cart_column( $item ) {

		$exclusion = maybe_unserialize( $item['exclusion'] );
		$replace   = '';

		if ( 'no' === $exclusion['enable_atc_custom_options'] ) {
			$atc_global = get_option( 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id() );
			$status     = 'hide' === $atc_global['action'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
		} else {
			$status = 'hide' === $exclusion['atc_status'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
			if ( 'none' !== $exclusion['custom_button'] && 'hide' === $exclusion['atc_status'] ) {
				/* translators: %s button name */
				$replace .= ' <br />' . sprintf( esc_html__( 'Replaced with %s in product page', 'yith-woocommerce-catalog-mode' ), ywctm_get_buttons_label_name( $exclusion['custom_button'] ) );
			}
			if ( 'none' !== $exclusion['custom_button_loop'] && 'hide' === $exclusion['atc_status'] ) {
				/* translators: %s button name */
				$replace .= ' <br />' . sprintf( esc_html__( 'Replaced with %s in shop page', 'yith-woocommerce-catalog-mode' ), ywctm_get_buttons_label_name( $exclusion['custom_button_loop'] ) );
			}
		}

		return sprintf( '%s%s', $status, $replace );

	}
}

if ( ! function_exists( 'ywctm_price_column' ) ) {

	/**
	 * Print the price column in the exclusion table
	 *
	 * @param array $item Exclusion item.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_price_column( $item ) {

		$exclusion = maybe_unserialize( $item['exclusion'] );
		$replace   = '';

		if ( 'no' === $exclusion['enable_price_custom_options'] ) {
			$price_global = get_option( 'ywctm_hide_price_settings' . ywctm_get_vendor_id() );
			$status       = 'hide' === $price_global['action'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
		} else {
			$status = 'hide' === $exclusion['price_status'] ? esc_html__( 'Hidden', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Visible', 'yith-woocommerce-catalog-mode' );
			if ( 'none' !== $exclusion['custom_price_text'] && 'hide' === $exclusion['price_status'] ) {
				/* translators: %s button name */
				$replace = ' <br />' . sprintf( esc_html__( 'Replaced with %s', 'yith-woocommerce-catalog-mode' ), ywctm_get_buttons_label_name( $exclusion['custom_price_text'] ) );
			}
		}

		return sprintf( '%s%s', $status, $replace );

	}
}

if ( ! function_exists( 'ywctm_item_type_column' ) ) {

	/**
	 * Print the item type column in the exclusion table
	 *
	 * @param string $item_type Item type name.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_item_type_column( $item_type ) {

		$item_types = array(
			'product'  => esc_html__( 'Product', 'yith-woocommerce-catalog-mode' ),
			'category' => esc_html__( 'Category', 'yith-woocommerce-catalog-mode' ),
			'tag'      => esc_html__( 'Tag', 'yith-woocommerce-catalog-mode' ),
		);

		return $item_types[ $item_type ];

	}
}

if ( ! function_exists( 'ywctm_item_name_column' ) ) {

	/**
	 * Print item name with action links in the exclusion table
	 *
	 * @param array             $item  Exclusion item.
	 * @param YITH_Custom_Table $table Table object.
	 *
	 * @return  string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_item_name_column( $item, $table ) {

		$getter     = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$query_args = array(
			'page'    => $getter['page'],
			'tab'     => $getter['tab'],
			'sub_tab' => $getter['sub_tab'],
			'id'      => $item['ID'],
		);

		if ( isset( $getter['paged'] ) ) {
			$query_args['return_page'] = $getter['paged'];
		}

		$section = str_replace( 'exclusions-', '', $getter['sub_tab'] );

		if ( 'items' === $section ) {
			$query_args['item_type'] = $item['item_type'];
			$section                 = $item['item_type'];
		}

		$items      = array(
			'product'  => array(
				'edit_label' => esc_html__( 'Edit product', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'post'   => $item['ID'],
							'action' => 'edit',
						),
						admin_url( 'post.php' )
					)
				),
				'view_link'  => get_permalink( $item['ID'] ),
			),
			'category' => array(
				'edit_label' => esc_html__( 'Edit category', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'taxonomy'  => 'product_cat',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit',
						),
						admin_url( 'edit-tags.php' )
					)
				),
				'view_link'  => get_term_link( intval( $item['ID'] ), 'product_cat' ),
			),
			'tag'      => array(
				'edit_label' => esc_html__( 'Edit tag', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'taxonomy'  => 'product_tag',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit',
						),
						admin_url( 'edit-tags.php' )
					)
				),
				'view_link'  => get_term_link( intval( $item['ID'] ), 'product_tag' ),
			),
			'vendors'  => array(
				'edit_label' => esc_html__( 'Edit vendor', 'yith-woocommerce-catalog-mode' ),
				'edit_link'  => esc_url(
					add_query_arg(
						array(
							'taxonomy'  => 'yith_shop_vendor',
							'post_type' => 'product',
							'tag_ID'    => $item['ID'],
							'action'    => 'edit',
						),
						admin_url( 'edit-tags.php' )
					)
				),
				'view_link'  => get_term_link( intval( $item['ID'] ), 'yith_shop_vendor' ),
			),
		);
		$edit_url   = esc_url( add_query_arg( array_merge( $query_args, array( 'action' => 'edit' ) ), admin_url( 'admin.php' ) ) );
		$delete_url = esc_url( add_query_arg( array_merge( $query_args, array( 'action' => 'delete' ) ), admin_url( 'admin.php' ) ) );
		$actions    = array(
			'edit'   => sprintf( '<a href="%s">%s</a>', $edit_url, esc_html__( 'Edit exclusion', 'yith-woocommerce-catalog-mode' ) ),
			'item'   => sprintf( '<a target="_blank" href="%s">%s</a>', $items[ $section ]['edit_link'], $items[ $section ]['edit_label'] ),
			'delete' => sprintf( '<a href="%s">%s</a>', $delete_url, esc_html__( 'Remove from list', 'yith-woocommerce-catalog-mode' ) ),
			'view'   => sprintf( '<a target="_blank" href="%s">%s</a>', $items[ $section ]['view_link'], esc_html__( 'View', 'yith-woocommerce-catalog-mode' ) ),
		);

		if ( '' !== ywctm_get_vendor_id( true ) && 'products' !== $section ) {
			unset( $actions['item'] );
		}

		return sprintf( '<strong><a class="row-title" href="%s" data-tip="%s">#%d %s </a></strong> %s', $edit_url, esc_html__( 'Edit exclusion', 'yith-woocommerce-catalog-mode' ), $item['ID'], $item['name'], $table->__call( 'row_actions', array( $actions ) ) );

	}
}

if ( ! function_exists( 'ywctm_inquiry_form_column' ) ) {

	/**
	 * Print the inquiry form column in the exclusion table
	 *
	 * @param array $item Exclusion item.
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_inquiry_form_column( $item ) {

		$exclusion = maybe_unserialize( $item['exclusion'] );

		$args = array(
			'id'    => 'enable_inquiry_form_' . $item['item_type'] . '_' . $item['ID'],
			'name'  => 'enable_inquiry_form',
			'type'  => 'onoff',
			'value' => $exclusion['enable_inquiry_form'],
			'data'  =>
				array(
					'item-id' => $item['ID'],
					'section' => $item['item_type'],
				),
		);

		yith_plugin_fw_get_field( $args, true );

	}
}

if ( ! function_exists( 'ywctm_vendor_column' ) ) {

	/**
	 * Print the exclude vendor column in the exclusion table
	 *
	 * @param array $item Exclusion item.
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_vendor_column( $item ) {

		$exclusion = $item['exclude'];

		$args = array(
			'id'    => 'exclude_vendor' . $item['ID'],
			'name'  => 'exclude_vendor',
			'type'  => 'onoff',
			'value' => $exclusion,
		);

		yith_plugin_fw_get_field( $args, true );

	}
}

if ( ! function_exists( 'ywctm_enable_inquiry_form' ) ) {

	/**
	 * Enable/disable inquiry from exclusion list overview
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_enable_inquiry_form() {

		try {

			if ( ! isset( $_POST['_wpnonce'] ) || ! isset( $_POST['item_id'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'bulk-items' ) ) {
				return;
			}

			$posted      = $_POST;
			$option_name = ( '' !== $posted['vendor_id'] ? '_ywctm_exclusion_settings_' . $posted['vendor_id'] : '_ywctm_exclusion_settings' );

			switch ( $posted['section'] ) {

				case 'category':
				case 'tag':
					$exclusion_data                        = get_term_meta( $posted['item_id'], $option_name, true );
					$exclusion_data['enable_inquiry_form'] = $posted['enabled'];

					update_term_meta( $posted['item_id'], $option_name, $exclusion_data );

					break;
				default:
					$product                               = wc_get_product( $posted['item_id'] );
					$exclusion_data                        = $product->get_meta( $option_name );
					$exclusion_data['enable_inquiry_form'] = $posted['enabled'];
					$product->update_meta_data( $option_name, $exclusion_data );
					$product->save();
			}

			wp_send_json( array( 'success' => true ) );

		} catch ( Exception $e ) {

			wp_send_json(
				array(
					'success' => false,
					'error'   => $e->getMessage(),
				)
			);

		}
	}

	add_action( 'wp_ajax_ywctm_enable_inquiry_form', 'ywctm_enable_inquiry_form' );

}

if ( ! function_exists( 'ywctm_exclude_vendor' ) ) {

	/**
	 * Enable/disable vendor exclusion
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_exclude_vendor() {

		try {

			if ( ! isset( $_POST['_wpnonce'] ) || ! isset( $_POST['item_id'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'bulk-vendors' ) ) {
				return;
			}

			$exclusion_data = isset( $_POST['enabled'] ) ? sanitize_text_field( wp_unslash( $_POST['enabled'] ) ) : 'no';

			update_term_meta( sanitize_text_field( wp_unslash( $_POST['item_id'] ) ), '_ywctm_vendor_override_exclusion', $exclusion_data );
			wp_send_json( array( 'success' => true ) );

		} catch ( Exception $e ) {

			wp_send_json(
				array(
					'success' => false,
					'error'   => $e->getMessage(),
				)
			);

		}
	}

	add_action( 'wp_ajax_ywctm_exclude_vendor', 'ywctm_exclude_vendor' );

}

if ( ! function_exists( 'ywctm_set_table_columns' ) ) {

	/**
	 * Prepare columns for exclusion table
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_set_table_columns() {

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'item_name'   => esc_html__( 'Item Name', 'yith-woocommerce-catalog-mode' ),
			'item_type'   => esc_html__( 'Item Type', 'yith-woocommerce-catalog-mode' ),
			'add_to_cart' => esc_html__( 'Add to cart', 'yith-woocommerce-catalog-mode' ),
			'show_price'  => esc_html__( 'Price', 'yith-woocommerce-catalog-mode' ),
		);

		$enabled = get_option( 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(), 'hidden' );

		if ( 'hidden' !== $enabled && ywctm_exists_inquiry_forms() ) {
			$columns['inquiry_form'] = esc_html__( 'Inquiry form', 'yith-woocommerce-catalog-mode' );
		}

		return $columns;
	}
}

/**
 * INQUIRY FORM RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_exists_inquiry_forms' ) ) {

	/**
	 * Check if at least a form plugin is active
	 *
	 * @return  boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_exists_inquiry_forms() {

		$form_plugins = ywctm_get_active_form_plugins();

		return ( ! empty( $form_plugins ) );

	}
}

if ( ! function_exists( 'ywctm_get_active_form_plugins' ) ) {

	/**
	 * Get active form plugins
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_active_form_plugins() {

		$active_plugins = array(
			'default' => esc_html__( 'Default', 'yith-woocommerce-catalog-mode' ),
		);

		if ( ywctm_contact_form_7_active() ) {
			$active_plugins['contact-form-7'] = 'Contact Form 7';
		}

		if ( ywctm_formidable_forms_form_active() ) {
			$active_plugins['formidable-forms'] = 'Formidable Forms';
		}

		if ( ywctm_gravity_forms_active() ) {
			$active_plugins['gravity-forms'] = 'Gravity Forms';
		}

		if ( ywctm_ninja_forms_active() ) {
			$active_plugins['ninja-forms'] = 'Ninja Forms';
		}

		if ( ywctm_wpforms_active() ) {
			$active_plugins['wpforms'] = 'WPForms';
		}

		return $active_plugins;
	}
}

if ( ! function_exists( 'ywctm_get_forms_list' ) ) {

	/**
	 * Get list of forms
	 *
	 * @param string $form_plugin Form plugin slug.
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_forms_list( $form_plugin ) {

		$forms = '';

		switch ( $form_plugin ) {
			case 'contact-form-7':
				$forms = ywctm_contact_form_7_get_contact_forms();
				break;
			case 'ninja-forms':
				$forms = ywctm_ninja_forms_get_contact_forms();
				break;
			case 'formidable-forms':
				$forms = ywctm_formidable_forms_get_contact_forms();
				break;
			case 'gravity-forms':
				$forms = ywctm_gravity_forms_get_contact_forms();
				break;
			case 'wpforms':
				$forms = ywctm_wpforms_get_contact_forms();
				break;
		}

		if ( ! is_array( $forms ) ) {

			if ( 'inactive' === $forms ) {
				$form_list = array( 'none' => esc_html__( 'Plugin not activated or not installed', 'yith-woocommerce-catalog-mode' ) );
			} else {
				$form_list = array( 'none' => esc_html__( 'No contact form found', 'yith-woocommerce-catalog-mode' ) );
			}
		} else {
			$form_list = $forms;
		}

		return $form_list;

	}
}

if ( ! function_exists( 'ywctm_get_localized_form' ) ) {

	/**
	 * Get form id for current language
	 *
	 * @param string  $form_type Form type.
	 * @param integer $post_id   The Post ID.
	 *
	 * @return  integer
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_localized_form( $form_type, $post_id ) {

		if ( ywctm_is_wpml_active() ) {
			$option_name  = 'ywctm_inquiry_' . str_replace( '-', '_', $form_type ) . '_id_wpml';
			$options      = apply_filters( 'ywctm_get_vendor_option', get_option( $option_name, '' ), $post_id, $option_name );
			$default_form = isset( $options[ wpml_get_default_language() ] ) ? $options[ wpml_get_default_language() ] : '';
			$form_id      = isset( $options[ wpml_get_current_language() ] ) ? $options[ wpml_get_current_language() ] : $default_form;

		} else {
			$option_name = 'ywctm_inquiry_' . str_replace( '-', '_', $form_type ) . '_id';
			$form_id     = apply_filters( 'ywctm_get_vendor_option', get_option( $option_name, '' ), $post_id, $option_name );
		}

		return $form_id;

	}
}

if ( ! function_exists( 'ywctm_get_formatted_product_name' ) ) {

	/**
	 * Get formatted product name
	 *
	 * @param integer $product_id The product ID.
	 *
	 * @return  string
	 * @since   2.0.15
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_formatted_product_name( $product_id ) {

		$product = wc_get_product( $product_id );

		if ( $product->get_sku() ) {
			$identifier = $product->get_sku();
		} else {
			$identifier = '#' . $product->get_id();
		}

		return sprintf( '%2$s (%1$s)', $identifier, $product->get_name() );
	}
}

if ( ! function_exists( 'ywctm_get_product_url' ) ) {

	/**
	 * Get formatted product URL
	 *
	 * @param integer $product_id The product ID.
	 * @param array   $params     The product params.
	 *
	 * @return  string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_product_url( $product_id, $params ) {

		$product_url = wc_get_product( $product_id )->get_permalink();
		$separator   = '';
		$querystring = '';

		if ( ! empty( $params ) ) {
			$querystring = implode( '&', $params );
			$separator   = false !== strpos( $product_url, '?' ) ? '&' : '?';
		}

		return $product_url . $separator . $querystring;
	}
}

if ( ! function_exists( 'ywctm_get_product_link' ) ) {

	/**
	 * Get product link
	 *
	 * @param integer $product_id The product ID.
	 * @param array   $params     The product params.
	 * @param boolean $html       Check if content should be HTML rendered.
	 *
	 * @return  string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_product_link( $product_id, $params, $html = true ) {

		$product_link = ywctm_get_product_url( $product_id, $params );
		$product_name = ywctm_get_formatted_product_name( $product_id );

		if ( $html ) {
			return sprintf( '<a href="%s" target="_blank">%s</a>', $product_link, $product_name );
		} else {
			return sprintf( '%s - %s', $product_name, $product_link );
		}
	}
}

/**
 * GEOLOCATION RELATED FUNCTIONS
 */
if ( ! function_exists( 'ywctm_get_ip_address' ) ) {

	/**
	 * Get user IP address
	 *
	 * @return  string
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_ip_address() {

		$ip_addr = false;

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip_addr = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip_addr = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip_addr = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		if ( false === $ip_addr ) {
			$ip_addr = '0.0.0.0';

			return $ip_addr;
		}

		if ( strpos( $ip_addr, ',' ) !== false ) {
			$x       = explode( ',', $ip_addr );
			$ip_addr = trim( end( $x ) );
		}

		if ( ! ywctm_validate_ip( $ip_addr ) ) {
			$ip_addr = '0.0.0.0';
		}

		return $ip_addr;

	}
}

if ( ! function_exists( 'ywctm_validate_ip' ) ) {

	/**
	 * Validate IP Address
	 *
	 * @param string $ip    IP address.
	 * @param string $which IPv4 or IPv6.
	 *
	 * @return  boolean
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_validate_ip( $ip, $which = '' ) {

		$which = strtolower( $which );

		// First check if filter_var is available.
		if ( is_callable( 'filter_var' ) ) {
			switch ( $which ) {
				case 'ipv4':
					$flag = FILTER_FLAG_IPV4;
					break;

				case 'ipv6':
					$flag = FILTER_FLAG_IPV6;
					break;

				default:
					$flag = '';
					break;
			}

			return (bool) filter_var( $ip, FILTER_VALIDATE_IP, $flag );
		}

		if ( 'ipv6' !== $which && 'ipv4' !== $which ) {
			if ( strpos( $ip, ':' ) !== false ) {
				$which = 'ipv6';
			} elseif ( strpos( $ip, '.' ) !== false ) {
				$which = 'ipv4';
			} else {
				return false;
			}
		}

		return call_user_func( 'validate_' . $which, $ip );
	}
}

if ( ! function_exists( 'ywctm_validate_ipv4' ) ) {

	/**
	 * Validate IPv4 Address
	 *
	 * @param string $ip IP address.
	 *
	 * @return  boolean
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_validate_ipv4( $ip ) {

		$ip_segments = explode( '.', $ip );

		// Always 4 segments needed.
		if ( count( $ip_segments ) !== 4 ) {
			return false;
		}
		// IP can not start with 0.
		if ( '0' === $ip_segments[0][0] ) {
			return false;
		}

		// Check each segment.
		foreach ( $ip_segments as $segment ) {
			// IP segments must be digits and can not be longer than 3 digits or greater then 255.
			if ( '' === $segment || preg_match( '/[^0-9]/', $segment ) || $segment > 255 || strlen( $segment ) > 3 ) {
				return false;
			}
		}

		return true;
	}
}

if ( ! function_exists( 'ywctm_validate_ipv6' ) ) {

	/**
	 * Validate IPv6 Address
	 *
	 * @param string $str IPv6 address.
	 *
	 * @return  boolean
	 * @since   1.3.4
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_validate_ipv6( $str ) {

		// 8 groups, separated by : 0-ffff per group one set of consecutive 0 groups can be collapsed to ::
		$groups    = 8;
		$collapsed = false;
		$chunks    = array_filter( preg_split( '/(:{1,2})/', $str, null, PREG_SPLIT_DELIM_CAPTURE ) );

		// Rule out easy nonsense.
		if ( current( $chunks ) === ':' || end( $chunks ) === ':' ) {
			return false;
		}

		// PHP supports IPv4-mapped IPv6 addresses, so we'll expect those as well.
		if ( strpos( end( $chunks ), '.' ) !== false ) {
			$ipv4 = array_pop( $chunks );
			if ( ! ywctm_validate_ipv4( $ipv4 ) ) {
				return false;
			}
			$groups --;
		}

		$seg = array_pop( $chunks );
		while ( $seg ) {
			if ( ':' === $seg[0] ) {
				if ( 0 === -- $groups ) {
					return false; // too many groups.
				}
				if ( strlen( $seg ) > 2 ) {
					return false; // long separator.
				}
				if ( '::' === $seg ) {
					if ( $collapsed ) {
						return false; // multiple collapsed.
					}
					$collapsed = true;
				}
			} elseif ( preg_match( '/[^0-9a-f]/i', $seg ) || strlen( $seg ) > 4 ) {
				return false; // invalid segment.
			}
		}

		return $collapsed || 1 === $groups;
	}
}
