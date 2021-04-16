<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH WooCommerce Request A Quote Premium
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YWCTM_Default_Form_Field' ) ) {

	/**
	 * Default contact form field class
	 *
	 * @class   YWCTM_Default_Form_Field
	 * @since   2.1.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Default_Form_Field {

		/**
		 * Current option id
		 *
		 * @var string
		 */
		protected $option_id;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'manage_default_field_form' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 25 );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_styles_scripts() {
			wp_enqueue_script( 'ywctm-default-form-field', yit_load_js_file( YWCTM_ASSETS_URL . 'js/default-form-field.js' ), array( 'jquery', 'jquery-ui-dialog', 'yith-plugin-fw-fields' ), YWCTM_VERSION, true );
			wp_enqueue_style( 'ywctm-default-form-field', yit_load_css_file( YWCTM_ASSETS_URL . 'css/default-form-field.css' ), '', YWCTM_VERSION );
			wp_localize_script(
				'ywctm-default-form-field',
				'ywctm_default_form_field',
				array(
					'popup_title' => esc_html__( 'Edit field', 'yith-woocommerce-catalog-mode' ),
					'save'        => esc_html__( 'Save Field', 'yith-woocommerce-catalog-mode' ),
				)
			);
		}

		/**
		 * Save the plugin option.
		 *
		 * @return void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function manage_default_field_form() {

			$posted = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! isset( $posted['ywctm_default_form'], $posted['request'] ) ) {
				return;
			}

			$this->option_id = wc_clean( $posted['ywctm_default_form'] );
			$request         = 'handle_form_' . wc_clean( $posted['request'] );

			// Remove unnecessary elements.
			unset( $posted['ywctm_default_form'], $posted['request'] );

			$this->$request( $posted, $this->get_saved_option() );

		}

		/**
		 * Return the option saved on database
		 *
		 * @return  array
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_saved_option() {
			return get_option( $this->option_id, array() );
		}

		/**
		 * Save the option inside the database
		 *
		 * @param array $option Option array to save.
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function save_option( $option ) {
			update_option( $this->option_id, $option );
		}

		/**
		 * Save the new field inside the option.
		 *
		 * @param array $posted Posted info.
		 * @param array $option Saved option.
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function handle_form_save( $posted, $option ) {

			if ( isset( $posted['name'] ) ) {
				$name = $posted['name'];
				unset( $posted['name'] );

				$posted['enabled']  = isset( $posted['enabled'] ) ? $posted['enabled'] : 'yes';
				$posted['required'] = isset( $posted['required'] ) ? $posted['required'] : 'no';
				$posted['checked']  = isset( $posted['checked'] ) ? $posted['checked'] : 'no';

				$posted['class'] = isset( $posted['class'] ) ? explode( ',', $posted['class'] ) : array();
				$posted['class'] = array_filter( $posted['class'] );

				$posted['label_class'] = isset( $posted['label_class'] ) ? explode( ',', $posted['label_class'] ) : array();
				$posted['label_class'] = array_filter( $posted['label_class'] );
				$posted['options']     = isset( $posted['options'] ) && ! empty( $posted['options'] ) ? $this->create_options_array( $posted['options'], $posted['type'] ) : array();

				$option[ $name ] = $posted;
			}

			$this->save_option( $option );
		}

		/**
		 * Handles field activation.
		 *
		 * @param array $posted Posted info.
		 * @param array $option Saved option.
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function handle_form_activate( $posted, $option ) {

			if ( isset( $posted['row'], $posted['activated'], $option[ $posted['row'] ] ) ) {
				$posted['enabled']                   = isset( $posted['enabled'] ) ? $posted['enabled'] : 'yes';
				$option[ $posted['row'] ]['enabled'] = $posted['activated'];
			}

			$this->save_option( $option );
		}

		/**
		 * Order the fields of the form.
		 *
		 * @param array $posted Posted info.
		 * @param array $option Saved option.
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function handle_form_sort( $posted, $option ) {

			if ( isset( $posted['order'] ) ) {
				$new_option = array();
				foreach ( $posted['order'] as $key ) {
					if ( isset( $option[ $key ] ) ) {
						$new_option[ $key ] = $option[ $key ];
					}
				}

				$option = $new_option;
			}

			$this->save_option( $option );
		}

		/**
		 * Create options array for field
		 *
		 * @param string $options The options.
		 * @param string $type    The field type.
		 *
		 * @return  array
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		protected function create_options_array( $options, $type = '' ) {

			$options_array = array();

			// Create array from string.
			$options = array_map( 'wc_clean', explode( '|', $options ) );
			// Remove double entries.
			$options = array_unique( $options );

			// First of all add empty options for placeholder if type is option.
			if ( 'select' === $type ) {
				$options_array[''] = '';
			}

			foreach ( $options as $option ) {
				$has_key = strpos( $option, '::' );
				if ( $has_key ) {
					list( $key, $option ) = explode( '::', $option );
				} else {
					$key = $option;
				}

				// Create key.
				if ( 'radio' !== $type ) {
					$key = sanitize_title_with_dashes( $key );
				}

				$options_array[ $key ] = stripslashes( $option );
			}

			return $options_array;
		}

	}

	new YWCTM_Default_Form_Field();

}
