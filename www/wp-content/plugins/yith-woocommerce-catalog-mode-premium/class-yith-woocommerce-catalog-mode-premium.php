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

if ( ! class_exists( 'YITH_WooCommerce_Catalog_Mode_Premium' ) ) {

	/**
	 * Implements features of YITH WooCommerce Catalog Mode plugin
	 *
	 * @class   YITH_WooCommerce_Catalog_Mode_Premium
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @package Yithemes
	 */
	class YITH_WooCommerce_Catalog_Mode_Premium extends YITH_WooCommerce_Catalog_Mode {

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WooCommerce_Catalog_Mode_Premium
		 * @since 1.3.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;

		}

		/**
		 * User geolocation info.
		 *
		 * @var array
		 */
		protected $user_geolocation = null;

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			parent::__construct();

			add_action( 'init', array( $this, 'geolocate_user' ) );
			add_action( 'init', array( $this, 'init_multivendor_integration' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_premium_scripts_admin' ), 15 );
			add_action( 'product_cat_edit_form', array( $this, 'write_taxonomy_options' ), 99 );
			add_action( 'product_tag_edit_form', array( $this, 'write_taxonomy_options' ), 99 );
			add_action( 'edited_product_cat', array( $this, 'save_taxonomy_options' ) );
			add_action( 'edited_product_tag', array( $this, 'save_taxonomy_options' ) );

			if ( ! is_admin() || $this->is_quick_view() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_premium_styles' ) );
				add_filter( 'woocommerce_product_tabs', array( $this, 'add_inquiry_form_tab' ) );
				add_filter( 'woocommerce_product_tabs', array( $this, 'disable_reviews_tab' ), 98 );
				add_action( 'woocommerce_before_single_product', array( $this, 'add_inquiry_form_page' ), 5 );
				add_action( 'woocommerce_before_single_product', array( $this, 'show_wapo_if_hidden' ), 5 );
				add_action( 'woocommerce_single_product_summary', array( $this, 'show_custom_button' ), 20 );
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'show_custom_button' ), 20 );
				add_filter( 'ywctm_get_exclusion', array( $this, 'get_exclusion' ), 10, 4 );
				add_filter( 'woocommerce_product_get_price', array( $this, 'show_product_price' ), 10, 2 );
				add_filter( 'woocommerce_get_price_html', array( $this, 'show_product_price' ), 12, 2 );
				add_filter( 'yith_ywraq_hide_price_template', array( $this, 'show_product_price' ), 12, 2 );
				add_filter( 'yith_wcpb_woocommerce_get_price_html', array( $this, 'show_product_price' ), 12, 2 );
				add_filter( 'woocommerce_is_purchasable', array( $this, 'unlock_purchase_if_ywcp_is_enabled' ), 99 );
				add_filter( 'yith_wcpb_ajax_update_price_enabled', array( $this, 'hide_price_bundle' ), 10, 2 );
				add_filter( 'yith_wcpb_show_bundled_items_prices', array( $this, 'hide_price_bundled_items' ), 10, 3 );
				add_filter( 'ywctm_check_price_hidden', array( $this, 'check_price_hidden' ), 10, 2 );
				add_filter( 'woocommerce_product_is_on_sale', array( $this, 'hide_on_sale' ), 10, 2 );
				add_filter( 'ywctm_css_classes', array( $this, 'hide_price_single_page' ) );
				// Remove discount table from product (YITH WooCommerce Dynamic Discount Product).
				add_filter( 'ywdpd_exclude_products_from_discount', array( $this, 'hide_discount_quantity_table' ), 10, 2 );
			}

			// Compatibility with quick view.
			add_action( 'yith_wcqv_product_summary', array( $this, 'check_quick_view' ) );
			add_shortcode( 'ywctm-button', array( $this, 'print_custom_button_shortcode' ) );
			add_shortcode( 'ywctm-inquiry-form', array( $this, 'print_inquiry_form_shortcode' ) );
			add_action( 'after_setup_theme', array( $this, 'themes_integration' ) );
			add_action( 'plugins_loaded', array( $this, 'handle_elementor' ), 20 );

			if ( is_admin() ) {
				// Register plugin to licence/update system.
				add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
				add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
				add_filter( 'yith_plugin_fw_get_field_template_path', array( $this, 'get_yith_panel_custom_template' ), 10, 2 );
			}

		}

		/**
		 * Premium files inclusion
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function include_files() {

			parent::include_files();

			include_once 'includes/ywctm-functions-premium.php';
			include_once 'includes/class-ywctm-button-label-post-type.php';
			include_once 'includes/integrations/forms/default/class-ywctm-default-form.php';
			include_once 'includes/integrations/forms/contact-form-7/ywctm-contact-form-7.php';
			include_once 'includes/integrations/forms/default/ywctm-default-form.php';
			include_once 'includes/integrations/forms/formidable-forms/ywctm-formidable-forms.php';
			include_once 'includes/integrations/forms/gravity-forms/ywctm-gravity-forms.php';
			include_once 'includes/integrations/forms/ninja-forms/ywctm-ninja-forms.php';
			include_once 'includes/integrations/forms/wpforms/ywctm-wpforms.php';

			if ( is_admin() ) {

				include_once 'includes/admin/class-yith-custom-table.php';
				include_once 'includes/admin/meta-boxes/class-ywctm-product-metabox.php';
				include_once 'includes/admin/tables/class-ywctm-exclusions-table.php';

				if ( ywctm_is_multivendor_active() && ywctm_is_multivendor_integration_active() ) {
					include_once 'includes/admin/tables/class-ywctm-vendors-table.php';
				}
			}

		}

		/**
		 * Gutenberg Integration
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function handle_elementor() {
			$blocks = include_once 'includes/integrations/elementor/ywctm-elementor.php';
			yith_plugin_fw_register_elementor_widgets( $blocks );
		}

		/**
		 * Check if country has catalog mode active
		 *
		 * @param boolean $apply   Catalog mode apply check.
		 * @param integer $post_id Post ID.
		 *
		 * @return  boolean
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function country_check( $apply, $post_id ) {

			$geolocation_enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_enable_geolocation', 'no' ), $post_id, 'ywctm_enable_geolocation' );

			if ( 'yes' === $geolocation_enabled ) {
				$geolocation   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_geolocation_settings' ), $post_id, 'ywctm_geolocation_settings' );
				$countries     = maybe_unserialize( $geolocation['countries'] );
				$users_match   = 'all' === $geolocation['users'] || ! is_user_logged_in();
				$country_match = in_array( $this->user_geolocation, $countries, true );

				$apply = $users_match && $country_match;

				if ( 'disable' === $geolocation['action'] ) {
					$apply = ! $apply;
				}
			}

			return $apply;

		}

		/**
		 * Check if there's a timeframe in which the catalog mode needs to be enabled
		 *
		 * @param boolean $apply Catalog mode apply check.
		 *
		 * @return  boolean
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function timeframe_check( $apply ) {

			if ( 'yes' === get_option( 'ywctm_disable_shop_timerange' ) ) {

				$apply      = false;
				$timeranges = get_option( 'ywctm_disable_shop_timerange_ranges' );

				try {
					$current_time = new DateTime( 'now', new DateTimeZone( wc_timezone_string() ) );

					foreach ( $timeranges as $timerange ) {
						$start_time = new DateTime( $timerange['start_hour'] . ':' . $timerange['start_minutes'], new DateTimeZone( wc_timezone_string() ) );
						$end_time   = new DateTime( $timerange['end_hour'] . ':' . $timerange['end_minutes'], new DateTimeZone( wc_timezone_string() ) );

						if ( $start_time > $end_time ) {
							// If end time is minor than the start time it's moved to the next day.
							$end_time = new DateTime( $timerange['end_hour'] . ':' . $timerange['end_minutes'] . '+ 1 DAYS', new DateTimeZone( wc_timezone_string() ) );
						}

						$day_of_week = (string) gmdate( 'N', $current_time->getTimestamp() );

						if ( in_array( $day_of_week, $timerange['days'], true ) || in_array( 'all', $timerange['days'], true ) ) {
							if ( $current_time >= $start_time && $current_time <= $end_time ) {
								$apply = true;
								break;
							}
						}
					}
				} catch ( Exception $e ) {
					// Do nothing.
					return $apply;
				}
			}

			return $apply;
		}

		/**
		 * Check if there's a dateframe in which the catalog mode needs to be enabled
		 *
		 * @param boolean $apply Catalog mode apply check.
		 *
		 * @return  boolean
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function dateframe_check( $apply ) {

			if ( 'yes' === get_option( 'ywctm_disable_shop_daterange' ) ) {

				$apply      = false;
				$dateranges = get_option( 'ywctm_disable_shop_daterange_ranges' );

				try {
					$current_date = new DateTime( 'today', new DateTimeZone( wc_timezone_string() ) );

					foreach ( $dateranges as $daterange ) {
						$start_date = new DateTime( $daterange['start_date'], new DateTimeZone( wc_timezone_string() ) );
						$end_date   = new DateTime( $daterange['end_date'], new DateTimeZone( wc_timezone_string() ) );

						if ( $current_date >= $start_date && $current_date <= $end_date ) {
							$apply = true;
							break;
						}
					}
				} catch ( Exception $e ) {
					// Do nothing.
					return $apply;
				}
			}

			return $apply;
		}

		/**
		 * Get user country from IP Address
		 *
		 * @return  void
		 * @since   1.3.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function geolocate_user() {

			if ( 'yes' === get_option( 'ywctm_enable_geolocation', 'no' ) ) {
				$ip_address = ywctm_get_ip_address();
				$request    = wp_remote_get( "https://freegeoip.app/json/$ip_address" );
				$response   = json_decode( wp_remote_retrieve_body( $request ) );

				if ( ! $response || '' === $response->country_code ) {
					$wc_geo_ip   = WC_Geolocation::geolocate_ip( $ip_address );
					$geolocation = $wc_geo_ip['country'];
				} else {
					$geolocation = $response->country_code;
				}

				if ( '' === $geolocation ) {
					$geolocation = wc_get_base_location()['country'];
				}

				$this->user_geolocation = $geolocation;
			}
		}

		/**
		 * ADMIN FUNCTIONS
		 */

		/**
		 * Multi Vendor integration init function
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_multivendor_integration() {
			if ( ywctm_is_multivendor_active() ) {
				include_once 'includes/integrations/class-ywctm-multi-vendor.php';
			}
		}

		/**
		 * Enqueue script file
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_premium_scripts_admin() {

			wp_register_style( 'ywctm-admin-premium', yit_load_css_file( YWCTM_ASSETS_URL . 'css/admin-premium.css' ), array(), YWCTM_VERSION );
			wp_register_script( 'ywctm-admin-premium', yit_load_css_file( YWCTM_ASSETS_URL . 'js/admin-premium.js' ), array( 'jquery', 'jquery-tiptip', 'jquery-ui-dialog' ), YWCTM_VERSION, false );
			$getted = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$args   = array(
				'vendor_id'          => ywctm_get_vendor_id( true ),
				'error_messages'     => array(
					'product'       => esc_html__( 'Select at least one product', 'yith-woocommerce-catalog-mode' ),
					'category'      => esc_html__( 'Select at least one category', 'yith-woocommerce-catalog-mode' ),
					'tag'           => esc_html__( 'Select at least one tag', 'yith-woocommerce-catalog-mode' ),
					/* translators: %1$s start hours value - %2$s end hours value */
					'error_hours'   => sprintf( esc_html__( 'Please only insert a number between %1$s and %2$s', 'yith-woocommerce-catalog-mode' ), '00', '24' ),
					/* translators: %1$s start minutes value - %2$s end minutes value */
					'error_minutes' => sprintf( esc_html__( 'Please only insert a number between %1$s and %2$s', 'yith-woocommerce-catalog-mode' ), '00', '59' ),
				),
				'popup_labels'       => array(
					'title' => esc_html_x( 'Add exclusion in list', 'Exclusion page popup title label ', 'yith-woocommerce-catalog-mode' ),
					'save'  => esc_html_x( 'Add exclusion to list', 'Exclusion page popup save button label ', 'yith-woocommerce-catalog-mode' ),
				),
				'buttons_custom_url' => ywctm_buttons_id_with_custom_url(),
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
			);
			wp_localize_script( 'ywctm-admin-premium', 'ywctm', $args );

			if ( ! empty( $getted['page'] ) && ( $getted['page'] === $this->panel_page || 'yith_vendor_ctm_settings' === $getted['page'] ) ) {

				wp_enqueue_script( 'ywctm-admin-premium' );
				wp_enqueue_style( 'ywctm-admin-premium' );

				if ( ! ywctm_is_multivendor_active() || ! ywctm_is_multivendor_integration_active() ) {
					$css = '
					.yith-icon.yith-icon-arrow_down, .nav-subtab-wrap{display: none!important}
					.yith-plugin-fw-sub-tabs-nav{display: none}
					.yith-plugin-fw-wp-page-wrapper.ywctm-exclusions .wrap.subnav-wrap {margin-top: -2px!important;}
					.yith-plugin-fw-wp-page-wrapper .wrap.subnav-wrap .wrap {margin-top: 10px; border-top: 1px solid #d8d8d8;}
					';
					wp_add_inline_style( 'ywctm-admin-premium', $css );
				}
			}

			if ( ! empty( $getted['taxonomy'] ) && ( 'product_cat' === $getted['taxonomy'] || 'product_tag' === $getted['taxonomy'] ) ) {
				wp_enqueue_style( 'yith-plugin-fw-fields' );
				wp_enqueue_script( 'yith-plugin-fw-fields' );
				wp_enqueue_style( 'ywctm-admin-premium' );
				wp_enqueue_script( 'ywctm-admin-premium' );
			}

		}

		/**
		 * Add YWCTM fields in category/tag edit page
		 *
		 * @param WP_Term $taxonomy The Term Object.
		 *
		 * @return  void
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function write_taxonomy_options( $taxonomy ) {

			$item          = get_term_meta( $taxonomy->term_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), true );
			$has_exclusion = 'yes';

			if ( ! $item ) {
				$atc_global         = get_option( 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id() );
				$button_global      = get_option( 'ywctm_custom_button_settings' . ywctm_get_vendor_id() );
				$button_loop_global = get_option( 'ywctm_custom_button_settings_loop' . ywctm_get_vendor_id() );
				$price_global       = get_option( 'ywctm_hide_price_settings' . ywctm_get_vendor_id() );
				$label_global       = get_option( 'ywctm_custom_price_text_settings' . ywctm_get_vendor_id() );
				$has_exclusion      = 'no';

				$item = array(
					'enable_inquiry_form'         => 'yes',
					'enable_atc_custom_options'   => 'no',
					'atc_status'                  => $atc_global['action'],
					'custom_button'               => $button_global,
					'custom_button_loop'          => $button_loop_global,
					'enable_price_custom_options' => 'no',
					'price_status'                => $price_global['action'],
					'custom_price_text'           => $label_global,
				);
			}

			$fields  = array_merge(
				array(
					array(
						'id'    => 'ywctm_has_exclusion',
						'name'  => 'ywctm_has_exclusion',
						'type'  => 'onoff',
						'title' => esc_html__( 'Add to exclusion list', 'yith-woocommerce-catalog-mode' ),
						'value' => $has_exclusion,
					),
				),
				ywctm_get_exclusion_fields( $item )
			);
			$enabled = get_option( 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(), 'hidden' );

			?>
			<div class="ywctm-taxonomy-panel ywctm-exclusions yith-plugin-ui woocommerce">
				<h2><?php esc_html_e( 'Catalog Mode Options', 'yith-woocommerce-catalog-mode' ); ?></h2>
				<table class="form-table <?php echo( 'hidden' !== $enabled && ywctm_exists_inquiry_forms() ? '' : 'no-active-form' ); ?>">
					<tbody>
					<?php foreach ( $fields as $field ) : ?>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $field['name'] ); ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
								<?php yith_plugin_fw_get_field( $field, true ); ?>
								<?php if ( isset( $field['desc'] ) ) : ?>
									<span class="description"><?php echo wp_kses_post( $field['desc'] ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php

		}

		/**
		 * Save YWCTM category/tag options
		 *
		 * @param integer $taxonomy_id The term ID.
		 *
		 * @return  void
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function save_taxonomy_options( $taxonomy_id ) {

			global $pagenow;

			if ( ! $taxonomy_id || 'edit-tags.php' !== $pagenow ) {
				return;
			}

			$posted = $_POST; //phpcs:ignore WordPress.Security.NonceVerification.Missing

			if ( isset( $posted['ywctm_has_exclusion'] ) ) {

				$exclusion_data = array(
					'enable_inquiry_form'         => isset( $posted['ywctm_enable_inquiry_form'] ) ? 'yes' : 'no',
					'enable_atc_custom_options'   => isset( $posted['ywctm_enable_atc_custom_options'] ) ? 'yes' : 'no',
					'atc_status'                  => $posted['ywctm_atc_status'],
					'custom_button'               => $posted['ywctm_custom_button'],
					'custom_button_url'           => $posted['ywctm_custom_button_url'],
					'custom_button_loop'          => $posted['ywctm_custom_button_loop'],
					'custom_button_loop_url'      => $posted['ywctm_custom_button_loop_url'],
					'enable_price_custom_options' => isset( $posted['ywctm_enable_price_custom_options'] ) ? 'yes' : 'no',
					'price_status'                => $posted['ywctm_price_status'],
					'custom_price_text'           => $posted['ywctm_custom_price_text'],
					'custom_price_text_url'       => $posted['ywctm_custom_price_text_url'],
				);

				update_term_meta( $taxonomy_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), $exclusion_data );
			} else {
				delete_term_meta( $taxonomy_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
			}

		}

		/**
		 * Add custom panel fields.
		 *
		 * @param string $template Template ID.
		 * @param array  $field    Field options.
		 *
		 * @return string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_yith_panel_custom_template( $template, $field ) {
			$custom_option_types = array(
				'ywctm-default-form',
				'ywctm-multiple-times',
				'ywctm-multiple-dates',
			);

			$field_type = $field['type'];

			if ( isset( $field['type'] ) && in_array( $field['type'], $custom_option_types, true ) ) {
				$template = YWCTM_DIR . "views/panel/types/$field_type.php";
			}

			return $template;
		}

		/**
		 * FRONTEND FUNCTIONS
		 */

		/**
		 * Get exclusion
		 *
		 * @param string  $value        The value.
		 * @param integer $post_id      The Post ID.
		 * @param string  $option       The option.
		 * @param string  $global_value THe global value.
		 *
		 * @return  mixed
		 * @since   1.3.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_exclusion( $value, $post_id, $option, $global_value = '' ) {

			$product = wc_get_product( $post_id );

			if ( ! $product ) {
				return $value;
			}

			if ( 'atc' === $option || 'price' === $option ) {
				$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

				if ( $product_exclusion ) {
					if ( 'yes' === $product_exclusion[ 'enable_' . $option . '_custom_options' ] ) {
						return $product_exclusion[ $option . '_status' ];
					} else {
						return $global_value;
					}
				}

				$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
				foreach ( $product_cats as $cat_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion ) {
						if ( 'yes' === $product_exclusion[ 'enable_' . $option . '_custom_options' ] ) {
							return $product_exclusion[ $option . '_status' ];
						} else {
							return $global_value;
						}
					}
				}

				$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
				foreach ( $product_tags as $tag_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion ) {
						if ( 'yes' === $product_exclusion[ 'enable_' . $option . '_custom_options' ] ) {
							return $product_exclusion[ $option . '_status' ];
						} else {
							return $global_value;
						}
					}
				}

				return $value;
			} elseif ( 'inquiry_form' === $option ) {
				$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

				if ( $product_exclusion ) {
					return 'yes' === $product_exclusion['enable_inquiry_form'];
				}

				$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
				foreach ( $product_cats as $cat_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion ) {
						return 'yes' === $product_exclusion['enable_inquiry_form'];
					}
				}

				$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
				foreach ( $product_tags as $tag_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion ) {
						return 'yes' === $product_exclusion['enable_inquiry_form'];
					}
				}

				return $value;
			} elseif ( 'custom_button' === $option || 'custom_button_loop' === $option ) {
				$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

				if ( $product_exclusion && 'yes' === $product_exclusion['enable_atc_custom_options'] ) {
					return $product_exclusion[ $option ];
				}

				$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
				foreach ( $product_cats as $cat_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion && 'yes' === $product_exclusion['enable_atc_custom_options'] ) {
						return $product_exclusion[ $option ];
					}
				}

				$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
				foreach ( $product_tags as $tag_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion && 'yes' === $product_exclusion['enable_atc_custom_options'] ) {
						return $product_exclusion[ $option ];
					}
				}
			} elseif ( 'price_label' === $option ) {
				$product_exclusion = apply_filters( 'ywctm_get_vendor_postmeta', $product->get_meta( '_ywctm_exclusion_settings' ), $post_id, '_ywctm_exclusion_settings' );

				if ( $product_exclusion && 'yes' === $product_exclusion['enable_price_custom_options'] ) {
					return $product_exclusion['custom_price_text'];
				}

				$product_cats = wp_get_object_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
				foreach ( $product_cats as $cat_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $cat_id, '_ywctm_exclusion_settings', true ), $post_id, $cat_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion && 'yes' === $product_exclusion['enable_price_custom_options'] ) {
						return $product_exclusion['custom_price_text'];
					}
				}

				$product_tags = wp_get_object_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
				foreach ( $product_tags as $tag_id ) {

					$product_exclusion = apply_filters( 'ywctm_get_vendor_termmeta', get_term_meta( $tag_id, '_ywctm_exclusion_settings', true ), $post_id, $tag_id, '_ywctm_exclusion_settings' );
					if ( $product_exclusion && 'yes' === $product_exclusion['enable_price_custom_options'] ) {
						return $product_exclusion['custom_price_text'];
					}
				}
			}

			return $value;
		}

		/**
		 * Enqueue css file
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_premium_styles() {

			if ( is_product() ) {
				$product      = wc_get_product();
				$form_enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $product->get_id(), 'ywctm_inquiry_form_enabled' );

				if ( 'hidden' !== $form_enabled && ( ywctm_exists_inquiry_forms() ) ) {

					$form_custom_css = '';
					$form_type       = 'none';

					// Add styles for inquiry form.
					if ( 'hidden' !== $form_enabled ) {

						$in_desc   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $product->get_id(), 'ywctm_inquiry_form_where_show' );
						$style     = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_style', 'classic' ), $product->get_id(), 'ywctm_inquiry_form_style' );
						$form_type = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_type' ), $product->get_id(), 'ywctm_inquiry_form_type' );

						if ( 'desc' === $in_desc && 'toggle' === $style ) {

							$tg_text_color = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_toggle_button_text_color' ), $product->get_id(), 'ywctm_toggle_button_text_color' );
							$tg_back_color = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_toggle_button_background_color' ), $product->get_id(), 'ywctm_toggle_button_background_color' );

							$form_custom_css .= '.ywctm-inquiry-form-wrapper.has-toggle .ywctm-toggle-button{ color:' . $tg_text_color['default'] . '; background-color:' . $tg_back_color['default'] . ';}';
							$form_custom_css .= '.ywctm-inquiry-form-wrapper.has-toggle .ywctm-toggle-button:hover{ color:' . $tg_text_color['hover'] . '; background-color:' . $tg_back_color['hover'] . ';}';

						}
					}

					wp_enqueue_script( 'ywctm-inquiry-form', yit_load_js_file( YWCTM_ASSETS_URL . 'js/inquiry-form.js' ), array( 'jquery' ), YWCTM_VERSION, false );
					wp_localize_script(
						'ywctm-inquiry-form',
						'ywctm',
						array(
							'form_type'  => $form_type,
							'product_id' => $product->get_id(),
						)
					);

					wp_enqueue_style( 'ywctm-inquiry-form', yit_load_css_file( YWCTM_ASSETS_URL . 'css/inquiry-form.css' ), array(), YWCTM_VERSION );
					wp_add_inline_style( 'ywctm-inquiry-form', $form_custom_css );

				}
			}

			// Add styles for custom button replacing add to cart or price.
			$buttons = ywctm_get_active_buttons_id();

			if ( $buttons ) {

				$button_custom_css = '';
				$icon_sets         = array();
				$google_fonts      = array();

				wp_enqueue_style( 'ywctm-button-label', yit_load_css_file( YWCTM_ASSETS_URL . 'css/button-label.css' ), array(), YWCTM_VERSION );
				wp_enqueue_script( 'ywctm-button-label', yit_load_js_file( YWCTM_ASSETS_URL . 'js/button-label-frontend.js' ), array( 'jquery' ), YWCTM_VERSION, false );

				foreach ( $buttons as $button ) {
					if ( 0 === (int) $button ) {
						continue;
					}
					$button_settings = ywctm_get_button_label_settings( $button );
					$used_icons      = get_post_meta( $button, 'ywctm_used_icons', true );
					$icon_sets       = array_unique( array_merge( $icon_sets, ( '' === $used_icons ? array() : $used_icons ) ) );
					$used_fonts      = get_post_meta( $button, 'ywctm_used_fonts', true );
					$google_fonts    = array_unique( array_merge( $google_fonts, ( '' === $used_fonts ? array() : $used_fonts ) ) );

					if ( $button_settings ) {
						$button_custom_css .= ywctm_set_custom_button_css( $button, $button_settings );

						$button_custom_css = str_replace( array( "\n", "\t", "\r" ), '', $button_custom_css );
					}
				}

				if ( ! empty( $icon_sets ) ) {
					foreach ( $icon_sets as $icon_set ) {
						switch ( $icon_set ) {
							case 'fontawesome':
								wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0' );
								break;
							case 'dashicons':
								wp_enqueue_style( 'dashicons' );
								break;
							case 'retinaicon-font':
								wp_enqueue_style( 'ywctm-retinaicon-font', yit_load_css_file( YWCTM_ASSETS_URL . 'css/retinaicon-font.css' ), array(), YWCTM_VERSION );
								break;
						}
					}
				}

				if ( ! empty( $google_fonts ) ) {
					$font_names = array();
					foreach ( $google_fonts as $google_font ) {
						$font_names[] = str_replace( ' ', '+', $google_font ) . ':400,400i,700,700i';
					}
					wp_enqueue_style( 'ywctm-google-fonts', 'https://fonts.googleapis.com/css?family=' . implode( '|', $font_names ) . '&display=swap', array(), YWCTM_VERSION );
				}

				wp_add_inline_style( 'ywctm-button-label', $button_custom_css );

			}

		}

		/**
		 * Removes reviews tab from single page product
		 *
		 * @param array $tabs Array of tabs.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function disable_reviews_tab( $tabs ) {

			global $post;

			$disable_review = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_disable_review' ), $post->ID, 'ywctm_disable_review' );

			if ( 'yes' === $disable_review && ! is_user_logged_in() ) {
				unset( $tabs['reviews'] );
			}

			return $tabs;

		}

		/**
		 * Add inquiry form tab to single product page
		 *
		 * @param array $tabs Array of tabs.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_inquiry_form_tab( $tabs ) {

			global $post;

			$enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $post->ID, 'ywctm_inquiry_form_enabled' );
			$in_tab  = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $post->ID, 'ywctm_inquiry_form_where_show' );

			if ( 'hidden' !== $enabled && 'tab' === $in_tab ) {

				$show_form = apply_filters( 'ywctm_get_exclusion', ( 'exclusion' !== $enabled ), $post->ID, 'inquiry_form' );

				if ( ! $show_form ) {
					return $tabs;
				}

				$active_form = $this->get_active_inquiry_form( $post->ID );

				if ( ! empty( $active_form ) && '' !== $active_form['form_id'] ) {

					$tab_title = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_tab_title' ), $post->ID, 'ywctm_inquiry_form_tab_title' );

					// APPLY_FILTER: ywctm_inquiry_form_title: last chance to change the Form tab title.
					$tab_title            = apply_filters( 'ywctm_inquiry_form_title', $tab_title );
					$tabs['inquiry_form'] = array(
						'title'     => $tab_title,
						'priority'  => 40,
						'callback'  => array( $this, 'get_inquiry_form' ),
						'form_type' => $active_form['form_type'],
						'form_id'   => $active_form['form_id'],
					);

				}
			}

			return $tabs;

		}

		/**
		 * Get active inquiry form
		 *
		 * @param integer $post_id The Post ID.
		 *
		 * @return  array
		 * @since   1.5.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_active_inquiry_form( $post_id ) {

			$active_form = array();
			$form_type   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_type' ), $post_id, 'ywctm_inquiry_form_type' );

			if ( 'default' !== $form_type && ( ywctm_exists_inquiry_forms() ) ) {
				$active_form = array(
					'form_type' => $form_type,
					'form_id'   => ywctm_get_localized_form( $form_type, $post_id ),
				);
			} elseif ( 'default' === $form_type ) {
				$active_form = array(
					'form_type' => $form_type,
					'form_id'   => 'default',
				);
			}

			return $active_form;

		}

		/**
		 * Check if YITH WooCommerce Add-ons options should be printed
		 *
		 * @return  void
		 * @since   2.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_wapo_if_hidden() {

			global $post;

			/* Show YITH WooCommerce Product Add-Ons*/
			if ( function_exists( 'YITH_WAPO' ) && $this->check_price_hidden( false, $post->ID ) && ( ! class_exists( 'YITH_YWRAQ_Frontend' ) ) ) {
				$priority = apply_filters( 'ywctm_wapo_position', 15 );
				add_action( 'woocommerce_single_product_summary', array( $this, 'show_wapo_options' ), $priority );
			}

		}

		/**
		 * Print YITH WooCommerce Add-ons options
		 *
		 * @return  void
		 * @since   2.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_wapo_options() {

			global $product;

			echo '<form class="cart" action="' . esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ) . '" method="post" enctype="multipart/form-data">';
			echo do_shortcode( '[yith_wapo_show_options]' );
			echo '</form>';

		}

		/**
		 * Add inquiry form directly to single product page
		 *
		 * @return  void
		 * @since   1.5.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_inquiry_form_page() {

			global $post;

			if ( 'woodmart' === ywctm_get_theme_name() ) {
				if ( isset( $GLOBALS['woodmart_loop']['is_quick_view'] ) && 'quick-view' === $GLOBALS['woodmart_loop']['is_quick_view'] ) {
					return;
				}
			}

			$enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $post->ID, 'ywctm_inquiry_form_enabled' );
			$in_desc = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_where_show', 'tab' ), $post->ID, 'ywctm_inquiry_form_where_show' );

			if ( 'hidden' !== $enabled && 'desc' === $in_desc ) {

				$show_form = apply_filters( 'ywctm_get_exclusion', ( 'exclusion' !== $enabled ), $post->ID, 'inquiry_form' );

				if ( ! $show_form ) {
					return;
				}

				$priority = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_position', '15' ), $post->ID, 'ywctm_inquiry_form_position' );
				// APPLY_FILTER: ywctm_inquiry_form_hook: hook where print the inquiry form.
				$hook = apply_filters( 'ywctm_inquiry_form_hook', 'woocommerce_single_product_summary' );
				// APPLY_FILTER: ywctm_inquiry_form_priority: priority to apply to the function.
				$priority = apply_filters( 'ywctm_inquiry_form_priority', $priority );

				add_action( $hook, array( $this, 'inquiry_form_shortcode' ), $priority );

			}

		}

		/**
		 * Print Inquiry form on product page
		 *
		 * @return  void
		 * @since   1.5.1
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function inquiry_form_shortcode() {

			global $post;

			$active_form = $this->get_active_inquiry_form( $post->ID );

			if ( ! empty( $active_form ) && '' !== $active_form['form_id'] ) {

				$tab_title   = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_tab_title' ), $post->ID, 'ywctm_inquiry_form_tab_title' );
				$button_text = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_toggle_button_text' ), $post->ID, 'ywctm_toggle_button_text' );
				$form_style  = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_style' ), $post->ID, 'ywctm_inquiry_form_style' );

				// APPLY_FILTER: ywctm_inquiry_form_title: last chance to change the Form tab title.
				$tab_title = apply_filters( 'ywctm_inquiry_form_title', $tab_title );
				// APPLY_FILTER: ywctm_inquiry_form_title_wrapper: the wrapper of the form title.
				$title_wrapper = apply_filters( 'ywctm_inquiry_form_title_wrapper', 'h3' );
				?>
				<div class="ywctm-inquiry-form-wrapper <?php echo ( 'toggle' === $form_style ) ? 'has-toggle' : ''; ?>">
					<?php
					if ( 'toggle' === $form_style ) {
						?>
						<div class="ywctm-toggle-button"><?php echo esc_attr( $button_text ); ?></div>
						<?php
					} else {
						echo wp_kses_post( sprintf( '<%1$s class="ywctm-form-title">%2$s</%1$s>', $title_wrapper, $tab_title ) );
					}
					?>
					<div class="ywctm-toggle-content">
						<?php $this->get_inquiry_form( 'inquiry_form', $active_form ); ?>
					</div>
				</div>
				<?php
			}

		}

		/**
		 * Inquiry form tab template
		 *
		 * @param integer $key Tab key.
		 * @param array   $tab Tab options.
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_inquiry_form( $key, $tab ) {

			if ( 'inquiry_form' !== $key ) {
				return;
			}

			global $product;

			$product_id = $product ? $product->get_id() : 0;

			switch ( $tab['form_type'] ) {
				case 'contact-form-7':
					$shortcode = '[contact-form-7 id="' . $tab['form_id'] . '"]';
					break;
				case 'ninja-forms':
					$shortcode = '[ninja_form  id=' . $tab['form_id'] . ']';
					break;
				case 'formidable-forms':
					$shortcode = '[formidable  id=' . $tab['form_id'] . ']';
					break;
				case 'gravity-forms':
					$shortcode = '[gravityform  id=' . $tab['form_id'] . apply_filters( 'ywctm_gravity_ajax', ' ajax=true' ) . ']';
					break;
				case 'wpforms':
					$shortcode = '[wpforms  id=' . $tab['form_id'] . ']';
					break;
				default:
					$shortcode = '[ywctm-default-form]';
			}

			// DO_ACTION: ywctm_before_inquiry_form: execute code before printing the inquiry form.
			do_action( 'ywctm_before_inquiry_form', $product );

			echo wp_kses_post( apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_text_before_form' ), $product_id, 'ywctm_text_before_form' ) );
			echo do_shortcode( $shortcode );

			// DO_ACTION: ywctm_after_inquiry_form: execute code after printing the inquiry form.
			do_action( 'ywctm_after_inquiry_form', $product );

		}

		/**
		 * Add a custom button into a shortcode
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function print_custom_button_shortcode() {

			ob_start();
			$this->show_custom_button( true );

			return ob_get_clean();
		}

		/**
		 * Add inquiry form into a shortcode
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function print_inquiry_form_shortcode() {

			global $post;

			$enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $post->ID, 'ywctm_inquiry_form_enabled' );

			ob_start();

			$show_form = apply_filters( 'ywctm_get_exclusion', ( 'exclusion' !== $enabled ), $post->ID, 'inquiry_form' );

			if ( $show_form ) {
				$this->inquiry_form_shortcode();
			}

			return ob_get_clean();
		}

		/**
		 * Add a custom button in product details and shop page
		 *
		 * @param boolean $in_shortcode The button is inside a shortcode.
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_custom_button( $in_shortcode = false ) {

			global $post;

			if ( ! $post || ! $post instanceof WP_Post ) {
				return;
			}

			// APPLY_FILTER: ywctm_allowed_page_hooks: hooks enabled for single product page.
			$page_actions = apply_filters( 'ywctm_allowed_page_hooks', array( 'woocommerce_single_product_summary' ) );
			// APPLY_FILTER: ywctm_allowed_shop_hooks: hooks enabled for shop page.
			$loop_actions = apply_filters( 'ywctm_allowed_shop_hooks', array( 'woocommerce_after_shop_loop_item' ) );
			$is_loop      = in_array( current_action(), $loop_actions, true );
			$is_page      = in_array( current_action(), $page_actions, true ) || $in_shortcode;
			$button_id    = 'none';

			if ( $is_page && $this->check_hide_add_cart( true ) ) {
				$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button_settings' ), $post->ID, 'ywctm_custom_button_settings' );
				$button_id = apply_filters( 'ywctm_get_exclusion', $button_id, $post->ID, 'custom_button' );
			}

			if ( $is_loop && $this->check_hide_add_cart( false, false, true ) ) {
				$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button_settings_loop' ), $post->ID, 'ywctm_custom_button_settings_loop' );
				$button_id = apply_filters( 'ywctm_get_exclusion', $button_id, $post->ID, 'custom_button_loop' );
			}

			if ( ywctm_is_wpml_active() ) {
				$button_id = yit_wpml_object_id( $button_id, 'ywctm-button-label', true, wpml_get_current_language() );
			}

			if ( $this->apply_catalog_mode( $post->ID ) && 'none' !== $button_id ) {
				$this->get_custom_button_template( $button_id, 'atc', $is_loop );
			}

		}

		/**
		 * Get custom button template
		 *
		 * @param integer|boolean $button_id The button ID.
		 * @param string          $replaces  What replaces.
		 * @param boolean         $is_loop   Loop checker.
		 *
		 * @return  void
		 * @since   1.0.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_custom_button_template( $button_id = false, $replaces = 'atc', $is_loop = false ) {

			global $post, $product;

			if ( ! isset( $post ) || ! $product || ( $product && ! $product instanceof WC_Product ) ) {
				return;
			}

			if ( false === $button_id ) {

				if ( 'price' === $replaces ) {
					$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_price_text_settings' ), $post->ID, 'ywctm_custom_price_text_settings' );
				} else {
					$button_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_button_settings' ), $post->ID, 'ywctm_custom_button_settings' );
				}
			}

			$button_settings = ywctm_get_button_label_settings( $button_id );
			$is_published    = 'publish' === get_post_status( $button_id );

			if ( ! $button_settings || ( ! $is_published && 'legacy' !== $button_id ) || 0 === (int) $button_id ) {
				return;
			}

			// APPLY_FILTER: ywctm_custom_button_additional_classes: additional classes for custom button.
			$custom_classes = apply_filters( 'ywctm_custom_button_additional_classes', '', $button_id );
			$classes        = array( 'ywctm-custom-button', $custom_classes );

			if ( 'none' !== $button_settings['hover_animation'] ) {
				$classes[] = 'ywctm-hover-effect ywctm-effect-' . $button_settings['hover_animation'];
			}

			switch ( $button_settings['button_url_type'] ) {
				case 'custom':
					$custom_url  = ywctm_get_custom_button_url_override( $product, $replaces, $is_loop );
					$button_type = 'a';
					$button_url  = 'href="' . ( '' === $custom_url ? $button_settings['button_url'] : $custom_url ) . '"';
					break;
				case 'product':
					$button_type = 'a';
					$button_url  = 'href="' . $product->get_permalink() . '"';
					break;
				default:
					$button_type = 'span';
					$button_url  = '';
			}
			// APPLY_FILTER: ywctm_custom_button_open_new_page: check if button link opens in new page.
			if ( apply_filters( 'ywctm_custom_button_open_new_page', false, $button_id ) && 'none' !== $button_settings['button_url_type'] ) {
				$button_url .= ' target="_blank"';
			}

			$button_text = '<span class="ywctm-inquiry-title">' . ywctm_parse_icons( $button_settings['label_text'] ) . '</span>';

			switch ( $button_settings['icon_type'] ) {
				case 'icon':
					$button_icon = '<span class="ywctm-icon-form ' . ywctm_get_icon_class( $button_settings['selected_icon'] ) . '"></span>';
					break;
				case 'custom':
					$button_icon = '<span class="custom-icon"><img src="' . $button_settings['custom_icon'] . '"></span>';
					break;
				default:
					$button_icon = '';
			}

			?>
			<div class="ywctm-custom-button-container ywctm-button-<?php echo esc_attr( $button_id ); ?>" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
				<?php echo wp_kses_post( sprintf( '<%1$s class="%2$s" %3$s>%4$s%5$s</%1$s>', $button_type, implode( ' ', $classes ), $button_url, $button_icon, $button_text ) ); ?>
			</div>
			<?php

		}

		/**
		 * Hides product price from single product page
		 *
		 * @param array $classes Classes array.
		 *
		 * @return  array
		 * @since   1.4.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_price_single_page( $classes ) {

			if ( $this->check_hide_price() ) {

				$args = array(
					'.woocommerce-variation-price',
				);

				// APPLY_FILTER: ywctm_catalog_price_classes: CSS classes of price element.
				$classes = array_merge( $classes, apply_filters( 'ywctm_catalog_price_classes', $args ) );

			}

			return $classes;

		}

		/**
		 * Hides on-sale badge if price is hidden
		 *
		 * @param boolean    $is_on_sale Check if product is on sale.
		 * @param WC_Product $product    Product object.
		 *
		 * @return  boolean
		 * @since   1.5.5
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_on_sale( $is_on_sale, $product ) {

			if ( $this->check_hide_price( $product->get_id() ) ) {
				$is_on_sale = false;
			}

			return $is_on_sale;

		}

		/**
		 * Check if price is hidden
		 *
		 * @param boolean $hide       Hide check.
		 * @param integer $product_id The product ID.
		 *
		 * @return  boolean
		 * @since   1.4.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_price_hidden( $hide, $product_id ) {

			if ( $this->check_hide_price( $product_id ) && $this->apply_catalog_mode( $product_id ) ) {
				$hide = true;
			}

			return $hide;

		}

		/**
		 * Check if price is hidden
		 *
		 * @param integer|boolean $product_id The product ID.
		 *
		 * @return  boolean
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_hide_price( $product_id = false ) {

			global $post;

			if ( ! $product_id && ! isset( $post ) ) {
				return false;
			}

			$product_id = ( $product_id ) ? $product_id : $post->ID;

			if ( ywctm_is_wpml_active() && apply_filters( 'ywctm_wpml_use_default_language_settings', false ) ) {
				$product_id = yit_wpml_object_id( $product_id, 'product', true, wpml_get_default_language() );
			}

			$product = wc_get_product( $product_id );

			if ( ! $product ) {
				return false;
			}

			$price_settings_general = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_hide_price_settings' ), $product_id, 'ywctm_hide_price_settings' );
			$behavior               = $price_settings_general['action'];

			if ( 'all' !== $price_settings_general['items'] ) {
				$behavior = apply_filters( 'ywctm_get_exclusion', ( 'hide' === $behavior ? 'show' : 'hide' ), $product_id, 'price', $behavior );
			}

			return ( 'hide' === $behavior && $this->apply_catalog_mode( $product_id ) );

		}

		/**
		 * Check for which users will not see the price
		 *
		 * @param string  $price   The product Price.
		 * @param integer $product The Product Object.
		 *
		 * @return  string
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function show_product_price( $price, $product ) {

			if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || defined( 'WOOCOMMERCE_CART' ) || apply_filters( 'ywctm_ajax_admin_check', is_admin(), $product ) || ( apply_filters( 'ywctm_prices_only_on_cart', false ) && ( current_filter() === 'woocommerce_get_price' || current_filter() === 'woocommerce_product_get_price' ) ) ) {
				return $price;
			}

			if ( $product instanceof WC_Product ) {
				$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
			} else {
				$product_id = $product;
			}

			if ( $this->check_hide_price( $product_id ) && $this->apply_catalog_mode( $product_id ) ) {

				if ( ( current_filter() === 'woocommerce_get_price' || current_filter() === 'woocommerce_product_get_price' ) ) {
					$current_product = $product instanceof WC_Product ? $product : wc_get_product( $product );
					if ( ( class_exists( 'YITH_Request_Quote_Premium' ) && get_option( 'ywraq_show_button_near_add_to_cart' ) === 'yes' ) || is_account_page() || 'yith-composite' === $current_product->get_type() ) {
						$value = 0;
					} else {
						$value = '';
					}

					$price = apply_filters( 'ywctm_hidden_price_meta', $value );

				} elseif ( current_filter() === 'yith_ywraq_hide_price_template' ) {
					$price = '';
				} else {

					$label_id = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_custom_price_text_settings' ), $product_id, 'ywctm_custom_price_text_settings' );
					$label_id = apply_filters( 'ywctm_get_exclusion', $label_id, $product_id, 'price_label' );

					if ( ywctm_is_wpml_active() ) {
						$label_id = yit_wpml_object_id( $label_id, 'ywctm-button-label', true, wpml_get_current_language() );
					}

					if ( 'none' !== $label_id ) {
						ob_start();
						$this->get_custom_button_template( $label_id, 'price' );
						$price = ob_get_clean();
					} else {
						$price = '';
					}
				}
			}

			return apply_filters( 'ywctm_hide_price_anyway', $price, $product_id );

		}

		/**
		 * Set products as purchasable if YITH Composite Products for WooCommerce is enabled
		 *
		 * @param boolean $value Check if the product is purchasable.
		 *
		 * @return  boolean
		 * @since   2.0.16
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function unlock_purchase_if_ywcp_is_enabled( $value ) {
			if ( class_exists( 'YITH_WCP' ) ) {
				$value = true;
			}

			return $value;

		}

		/**
		 * Hide price for bulndle product
		 *
		 * @param boolean    $per_items_pricing Check if bundle has "Per items pricing" enabled.
		 * @param WC_Product $product           Product Object.
		 *
		 * @return boolean
		 * @since   2.0.15
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_price_bundle( $per_items_pricing, $product ) {
			if ( $this->check_price_hidden( false, $product->get_id() ) ) {
				$per_items_pricing = false;
			}

			return $per_items_pricing;
		}

		/**
		 * Hide price for bulndle product
		 *
		 * @param boolean    $value        Bundled item price value.
		 * @param mixed      $bundled_item Unused.
		 * @param WC_Product $product      Product Object.
		 *
		 * @return boolean
		 * @since   2.0.15
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function hide_price_bundled_items( $value, $bundled_item, $product ) {
			if ( $this->check_price_hidden( false, $product->get_id() ) ) {
				$value = false;
			}

			return $value;
		}

		/**
		 * Hide discount quantity table from YITH WooCommerce Dynamic Pricing Discount id the catalog mode is active
		 *
		 * @param boolean    $value   Unused.
		 * @param WC_Product $product Product Object.
		 *
		 * @return boolean
		 * @since  2.0.0
		 * @author Francesco Licandro
		 */
		public function hide_discount_quantity_table( $value, $product ) {
			return $product && $this->check_hide_add_cart( true, $product->get_id() );
		}

		/**
		 * Hides product price and add to cart in YITH Quick View
		 *
		 * @return  void
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function check_quick_view() {
			if ( $this->is_quick_view() ) {
				$this->hide_add_to_cart_quick_view();
				$this->hide_price_quick_view();
			}
		}

		/**
		 * Hide price for product in quick view
		 *
		 * @return  void
		 * @since   1.0.7
		 * @author  Francesco Licandro
		 */
		public function hide_price_quick_view() {

			if ( $this->check_hide_price() ) {

				$args = array(
					'.single_variation_wrap .single_variation',
					'.yith-quick-view .price',
					'.price-wrapper',
				);

				// APPLY_FILTER: ywctm_catalog_price_classes: CSS classes of price element.
				$classes = implode( ', ', apply_filters( 'ywctm_catalog_price_classes', $args ) );
				ob_start();

				?>
				<style type="text/css">
					<?php echo esc_attr( $classes ); ?>
					{
						display: none !important
					;
					}
				</style>
				<?php

				echo wp_kses_post( ob_get_clean() );

			}

		}

		/**
		 * Themes Integration
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function themes_integration() {

			$theme_name = strtolower( ywctm_get_theme_name() );

			switch ( $theme_name ) {
				case 'flatsome':
					add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_add_to_cart_quick_view' ) );
					add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_price_quick_view' ) );
					add_action( 'woocommerce_single_product_lightbox_summary', array( $this, 'show_custom_button' ), 20 );
					add_filter( 'ywctm_allowed_shop_hooks', array( $this, 'flatsome_support' ) );
					add_filter( 'ywctm_ajax_admin_check', '__return_false' );
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );

					if ( 'list' === get_theme_mod( 'category_grid_style', 'grid' ) ) {
						remove_action( 'woocommerce_after_shop_loop_item', array( $this, 'show_custom_button' ), 20 );
						add_action( 'flatsome_product_box_after', array( $this, 'show_custom_button' ), 20 );
					}
					break;
				case 'oceanwp':
					add_action( 'ocean_woo_quick_view_product_content', array( $this, 'hide_add_to_cart_quick_view' ) );
					add_action( 'ocean_woo_quick_view_product_content', array( $this, 'hide_price_quick_view' ) );
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );
					break;
				case 'astra':
					add_action( 'astra_woo_quick_view_product_summary', array( $this, 'hide_add_to_cart_quick_view' ) );
					add_action( 'astra_woo_quick_view_product_summary', array( $this, 'hide_price_quick_view' ) );
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );
					break;
				case 'avada':
				case 'electro':
					add_filter( 'ywctm_modify_woocommerce_after_shop_loop_item', '__return_false' );
					break;
				case 'woodmart':
					add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_add_to_cart_quick_view' ) );
					add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'hide_price_quick_view' ) );
					add_filter( 'ywctm_ajax_admin_check', '__return_false' );
					break;
			}

		}

		/**
		 * Adds support for flatsome Quci View
		 *
		 * @param array $hooks The allowed quickview hooks.
		 *
		 * @return  array
		 * @since   2.0.12
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function flatsome_support( $hooks ) {
			$hooks[] = 'woocommerce_single_product_lightbox_summary';

			if ( 'list' === get_theme_mod( 'category_grid_style', 'grid' ) ) {
				$hooks[] = 'flatsome_product_box_after';
			}

			return $hooks;
		}

		/**
		 * Checks if product price needs to be hidden
		 *
		 * @param boolean         $x          Unused.
		 * @param integer|boolean $product_id The Product ID.
		 *
		 * @return  boolean
		 * @since   1.0.2
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_product_price_single( $x = true, $product_id = false ) {
			return $this->check_hide_price( $product_id );
		}

		/**
		 * YITH FRAMEWORK
		 */

		/**
		 * Register plugins for activation tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once 'plugin-fw/licence/lib/yit-licence.php';
				require_once 'plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YWCTM_INIT, YWCTM_SECRET_KEY, YWCTM_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) {
				require_once 'plugin-fw/lib/yit-upgrade.php';
			}
			YIT_Upgrade()->register( YWCTM_SLUG, YWCTM_INIT );
		}

		/**
		 * Plugin row meta
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array  $new_row_meta_args Row meta args.
		 * @param array  $plugin_meta       Plugin meta.
		 * @param string $plugin_file       Plugin File.
		 * @param array  $plugin_data       Plugin data.
		 * @param string $status            Status.
		 * @param string $init_file         Init file.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 * @use     plugin_row_meta
		 */
		public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YWCTM_INIT' ) {
			$new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

			if ( defined( $init_file ) && constant( $init_file ) === $plugin_file ) {
				$new_row_meta_args['is_premium'] = true;
			}

			return $new_row_meta_args;
		}

		/**
		 * Action Links
		 *
		 * Add the action links to plugin admin page
		 *
		 * @param array $links links plugin array.
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			$links = yith_add_action_links( $links, $this->panel_page, true, YWCTM_SLUG );

			return $links;
		}

	}

}
