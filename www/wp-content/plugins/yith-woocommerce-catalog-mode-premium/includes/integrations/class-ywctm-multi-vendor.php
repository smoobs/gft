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

if ( ! class_exists( 'YWCTM_Multi_Vendor' ) ) {

	/**
	 * Implements compatibility with YITH WooCommerce Multi Vendor
	 *
	 * @class   YWCTM_Multi_Vendor
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Multi_Vendor {

		/**
		 *  Yith WooCommerce Catalog Mode vendor panel page
		 *
		 * @var string
		 */
		protected $panel_page = 'yith_vendor_ctm_settings';

		/**
		 * Panel object
		 *
		 * @since   2.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $vendor_panel = null;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() && $this->check_ywctm_vendor_enabled() ) {
				add_action( 'admin_menu', array( $this, 'add_ywctm_vendor' ), 5 );
			}

			add_action( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
			add_action( 'yith_plugin_fw_wc_panel_screen_ids_for_assets', array( $this, 'add_screen_ids' ) );
			add_filter( 'ywctm_get_vendor_option', array( $this, 'get_vendor_option' ), 10, 3 );
			add_filter( 'ywctm_get_vendor_postmeta', array( $this, 'get_vendor_postmeta' ), 10, 3 );
			add_filter( 'ywctm_get_vendor_termmeta', array( $this, 'get_vendor_termmeta' ), 10, 4 );
			add_filter( 'ywctm_filled_form_fields', array( $this, 'add_vendor_emails_cc' ), 10, 2 );

		}

		/**
		 * Check if Catalog Mode for vendors allowed
		 *
		 * @return  boolean
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_ywctm_vendor_enabled() {
			return 'yes' === get_option( 'yith_wpv_vendors_enable_catalog_mode', 'no' );
		}

		/**
		 * Add custom post type screen to YITH Plugin list
		 *
		 * @param array $screen_ids Screen IDs array.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = 'toplevel_page_yith_vendor_ctm_settings';

			return $screen_ids;

		}

		/**
		 * Add Catalog Mode panel for vendors
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_ywctm_vendor() {

			if ( ! empty( $this->vendor_panel ) ) {
				return;
			}

			$tabs = array(
				'premium-settings' => esc_html_x( 'Settings', 'general settings tab name', 'yith-woocommerce-catalog-mode' ),
				'exclusions'       => esc_html_x( 'Exclusion List', 'exclusion settings tab name', 'yith-woocommerce-catalog-mode' ),
				'inquiry-form'     => esc_html_x( 'Inquiry Form', 'inquiry form settings tab name', 'yith-woocommerce-catalog-mode' ),
				'buttons-labels'   => esc_html_x( 'Buttons & Labels', 'buttons & labels settings tab name', 'yith-woocommerce-catalog-mode' ),
			);

			$args = array(
				'create_menu_page' => false,
				'parent_slug'      => '',
				'page_title'       => 'Catalog Mode',
				'menu_title'       => 'Catalog Mode',
				'capability'       => 'manage_vendor_store',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => $this->panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YWCTM_DIR . 'plugin-options/',
				'icon_url'         => 'dashicons-admin-settings',
				'position'         => 99,
				'class'            => yith_set_wrapper_class(),
			);

			$this->vendor_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Check if vendors options can be loaded
		 *
		 * @param YITH_Vendor $vendor The current Vendor.
		 *
		 * @return  boolean
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_override_check( $vendor ) {

			if ( 'yes' === get_option( 'ywctm_admin_override', 'no' ) ) {

				$admin_override = get_option( 'ywctm_admin_override_settings' );
				$behavior       = $admin_override['action'];
				$target         = $admin_override['target'];

				if ( 'disable' === $behavior && 'all' === $target ) {
					return true;
				} elseif ( 'enable' === $behavior && 'all' === $target ) {
					return false;
				} else {

					$has_exclusion = 'yes' === get_term_meta( $vendor->id, '_ywctm_vendor_override_exclusion', true );

					if ( ( 'disable' === $behavior && $has_exclusion ) || ( 'enable' === $behavior && ! $has_exclusion ) ) {
						return true;
					} elseif ( ( 'enable' === $behavior && $has_exclusion ) || ( 'disable' === $behavior && ! $has_exclusion ) ) {
						return false;
					}
				}
			}

			return true;

		}

		/**
		 * Get vendor options
		 *
		 * @param mixed   $value   The option value.
		 * @param integer $post_id The post ID.
		 * @param string  $option  The option name.
		 *
		 * @return  mixed
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_option( $value, $post_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && $this->admin_override_check( $vendor ) ) {
				$opt_val = get_option( $option . '_' . $vendor->id );
				$value   = ( '' !== $opt_val ) ? $opt_val : $value;
			}

			return $value;

		}

		/**
		 * Get vendor postmeta
		 *
		 * @param mixed   $value   The option value.
		 * @param integer $post_id The post ID.
		 * @param string  $option  The option name.
		 *
		 * @return  mixed
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_postmeta( $value, $post_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && $this->admin_override_check( $vendor ) ) {
				$product = wc_get_product( $post_id );
				$opt_val = $product->get_meta( $option . '_' . $vendor->id );
				$value   = ( '' !== $opt_val ) ? $opt_val : $value;
			}

			return $value;

		}

		/**
		 * Get vendor termmeta
		 *
		 * @param mixed   $value   The option value.
		 * @param integer $post_id The post ID.
		 * @param integer $term_id The term ID.
		 * @param string  $option  The option name.
		 *
		 * @return  mixed
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_termmeta( $value, $post_id, $term_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && $this->admin_override_check( $vendor ) ) {
				$opt_val = get_term_meta( $term_id, $option . '_' . $vendor->id, true );
				$value   = ( '' !== $opt_val ) ? $opt_val : $value;
			}

			return $value;

		}

		/**
		 * Get vendor admin emails
		 *
		 * @param array $filled_form_fields The form filled forms.
		 * @param array $posted             The posted fields.
		 *
		 * @return  array
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_vendor_emails_cc( $filled_form_fields, $posted ) {

			if ( 0 !== (int) $posted['ywctm-vendor-id'] ) {

				$vendor        = yith_get_vendor( $posted['ywctm-vendor-id'], 'vendor' );
				$vendor_admins = $vendor->get_admins();
				$vendor_emails = array();

				foreach ( $vendor_admins as $vendor_admin ) {
					$vendor_emails[] = get_userdata( $vendor_admin )->user_email;
				}

				$filled_form_fields['cc_emails'] = $vendor_emails;

			}

			return $filled_form_fields;
		}

	}

	new YWCTM_Multi_Vendor();

}
