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

if ( ! class_exists( 'YWCTM_Default_Form' ) ) {

	/**
	 * Default contact form class
	 *
	 * @class   YWCTM_Default_Form
	 * @since   2.1.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Default_Form {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			if ( is_admin() ) {
				require_once YWCTM_DIR . 'includes/integrations/forms/default/panel/class-ywctm-default-form-field.php';
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ), 15 );
			add_filter( 'woocommerce_form_field_ywctm_acceptance', array( $this, 'acceptance_type' ), 10, 3 );
			add_filter( 'script_loader_tag', array( $this, 'add_async_attribute' ), 10, 2 );
			add_action( 'wc_ajax_ywctm_submit_default_form', array( $this, 'submit_default_form' ) );
			add_filter( 'ywctm_form_fields', array( $this, 'filter_wpml_strings' ), 999, 1 );
			add_shortcode( 'ywctm-default-form', array( $this, 'get_form_template' ) );

			add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );

		}

		/**
		 * Call the template to show the form
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_form_template() {

			$args = array(
				'fields'     => ywctm_get_form_fields( false ),
				'product_id' => get_the_ID(),
			);

			wc_get_template( 'catalog-mode-default-form.php', $args, '', YWCTM_DIR . '/templates/' );
		}

		/**
		 * Enqueue Scripts and Styles
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enqueue_styles_scripts() {
			if ( is_product() ) {
				$product      = wc_get_product();
				$form_enabled = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_enabled', 'hidden' ), $product->get_id(), 'ywctm_inquiry_form_enabled' );
				$form_type    = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_form_type', 'default' ), $product->get_id(), 'ywctm_inquiry_form_type' );

				if ( 'hidden' !== $form_enabled && 'default' === $form_type && ( ywctm_exists_inquiry_forms() ) ) {

					wp_enqueue_script( 'ywctm-default-form', yit_load_js_file( YWCTM_ASSETS_URL . 'js/default-form.js' ), array( 'jquery' ), YWCTM_VERSION, true );
					wp_enqueue_style( 'ywctm-default-form', yit_load_css_file( YWCTM_ASSETS_URL . 'css/default-form.css' ), '', YWCTM_VERSION );

					if ( ywctm_check_recaptcha_options() && ( ! class_exists( 'WP_reCaptcha' ) || ( class_exists( 'WP_reCaptcha' ) && is_user_logged_in() ) ) ) {
						wp_enqueue_script( 'ywctm-recaptcha', '//www.google.com/recaptcha/api.js?onload=ywctm_recaptcha&render=explicit', array( 'ywctm-default-form' ), YWCTM_VERSION, false );
					}

					$form_localize_args = array(
						'ajaxurl'      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
						'err_msg'      => esc_html__( 'This is a required field.', 'yith-woocommerce-catalog-mode' ),
						'err_msg_mail' => esc_html__( 'The mail you have entered seems to be wrong.', 'yith-woocommerce-catalog-mode' ),
						'block_loader' => ywctm_get_ajax_default_loader(),
					);

					wp_localize_script( 'ywctm-default-form', 'ywctm_form', $form_localize_args );
				}
			}
		}

		/**
		 * Add async and defer to recaptcha script
		 *
		 * @param string $tag    The script tag.
		 * @param string $handle The script_handle.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_async_attribute( $tag, $handle ) {
			if ( 'ywctm-recaptcha' !== $handle ) {
				return $tag;
			}

			return str_replace( ' src', ' async="async" defer="defer" src', $tag );
		}

		/**
		 * The acceptance field template
		 *
		 * @param string $field The field HTML.
		 * @param string $key   The field ID.
		 * @param array  $args  The field args.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function acceptance_type( $field, $key, $args ) {
			$required        = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'yith-woocommerce-catalog-mode' ) . '">*</abbr>' : '';
			$container_class = ! empty( $args['class'] ) ? 'form-row ' . esc_attr( implode( ' ', $args['class'] ) ) : '';

			ob_start();
			?>
			<p id="<?php echo esc_attr( $args['id'] ); ?>_field" class="<?php echo esc_attr( $container_class ); ?>">
				<span class="ywctm_acceptance_description"><?php echo wp_kses_post( wc_replace_policy_page_link_placeholders( $args['description'] ) ); ?></span>
				<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $args['id'] ); ?>" <?php echo $args['required'] ? 'required' : ''; ?>>
				<label for="<?php echo esc_attr( $key ); ?>" class="ywctm_acceptance_label <?php echo esc_attr( implode( ' ', $args['label_class'] ) ); ?>"><?php echo esc_html( $args['label'] ) . wp_kses_post( $required ); ?></label>
			</p>
			<?php
			$field = ob_get_clean();

			return $field . ( ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '' );
		}

		/**
		 * Check the form validation and trigger the email message
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function submit_default_form() {

			// Check if the default form was submitted.
			if ( ! isset( $_REQUEST['ywctm_mail_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['ywctm_mail_wpnonce'] ) ), 'ywctm-default-form-request' ) ) {
				return;
			}

			$posted             = $_REQUEST;
			$errors             = array();
			$form_fields        = ywctm_get_form_fields();
			$filled_form_fields = array();

			// Validating fields.
			foreach ( $form_fields as $name => $form_field ) {

				if ( ! $form_field['enabled'] ) {
					continue;
				}

				$error = $this->validate_field( $posted, $name, $form_field );

				if ( $error ) {
					$errors[] = $error;
				}
			}

			// Validating recaptcha.
			if ( ywctm_check_recaptcha_options() ) {
				$captcha_error_string = sprintf( '<p>%s</p>', esc_html__( 'Please check the re-Captcha form.', 'yith-woocommerce-catalog-mode' ) );
				$captcha              = isset( $posted['g-recaptcha-response'] ) ? $posted['g-recaptcha-response'] : false;

				if ( ! $captcha ) {
					$errors[] = $captcha_error_string;
				} else {
					$secret_key = get_option( 'ywctm_reCAPTCHA_secretkey' );
					$response   = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $captcha );
					if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
						$errors[] = $captcha_error_string;
					} else {
						$response_keys = json_decode( $response['body'], true );
						if ( 1 !== intval( $response_keys['success'] ) ) {
							$errors[] = $captcha_error_string;
						}
					}
				}
			}

			if ( $errors ) {
				$results = array(
					'result'   => 'failure',
					'messages' => implode( '<br />', $errors ),
				);
			} else {
				try {

					foreach ( $form_fields as $key => $field ) {

						if ( 'no' === $field['enabled'] ) {
							continue;
						}

						if ( 'acceptance' === $key ) {
							$value = isset( $posted['acceptance'] ) ? esc_html__( 'Yes', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'No', 'yith-woocommerce-catalog-mode' );
						} else {
							$value = isset( $posted[ $key ] ) ? sanitize_text_field( $posted[ $key ] ) : '';
						}

						$filled_form_fields [ $key ] = array(
							'label' => $field['label'],
							'value' => $value,
						);

					}

					$filled_form_fields['lang']    = isset( $posted['lang'] ) ? sanitize_text_field( $posted['lang'] ) : '';
					$filled_form_fields['product'] = array(
						'label' => esc_html__( 'Product' ),
						'value' => array(
							'id'     => $posted['ywctm-product-id'],
							'params' => explode( ',', $posted['ywctm-params'] ),
						),
					);

					$filled_form_fields = apply_filters( 'ywctm_filled_form_fields', $filled_form_fields, $posted );

					do_action( 'ywctm_send_mail', $filled_form_fields );
					do_action( 'ywctm_send_customer_mail', $filled_form_fields );

					$results = array(
						'result'   => 'success',
						'messages' => esc_html__( 'Your message has been sent successfully. You\'ll find a copy in your mailbox.', 'yith-woocommerce-catalog-mode' ),
					);

				} catch ( Exception $e ) {
					$results = array(
						'result'   => 'failure',
						'messages' => $e->getMessage(),
					);
				}
			}
			wp_send_json( $results );
			exit();
		}

		/**
		 * Custom validation for fields
		 *
		 * @param array  $posted Array of posted params.
		 * @param string $key    Key of the field.
		 * @param array  $field  Field properties.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function validate_field( $posted, $key, $field ) {
			$message = '';

			if ( 'yes' === $field['required'] && isset( $posted[ $key ] ) && '' === $posted[ $key ] ) {
				/* translators: %s field name */
				$message .= sprintf( __( '%s is required.', 'yith-woocommerce-catalog-mode' ), '<strong>' . $field['label'] . '</strong>' );
			}

			if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
				foreach ( $field['validate'] as $rule ) {
					switch ( $rule ) {
						case 'email':
							$email = sanitize_email( strtolower( $posted[ $key ] ) );
							if ( ! is_email( $email ) ) {
								/* translators: %s email address */
								$message .= sprintf( esc_html__( ' %s is not a valid email address.', 'yith-woocommerce-catalog-mode' ), '<strong>' . $field['label'] . '</strong>' );
							}
							break;
						default:
							$message .= apply_filters( 'ywctm_default_form_validate_field', '', $posted, $key, $field );
							break;
					}
				}
			}

			return ltrim( $message );
		}

		/**
		 * WPML string translation.
		 *
		 * @param array $fields The fields array.
		 *
		 * @return  array
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function filter_wpml_strings( $fields ) {

			if ( $fields ) {
				foreach ( $fields as $key => &$single ) {
					$single = ywctm_field_filter_wpml_strings( $key, $single );
				}
			}

			return $fields;
		}

		/**
		 * Loads WC Mailer when needed
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function load_wc_mailer() {
			add_action( 'ywctm_send_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
			add_action( 'ywctm_send_customer_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
		}

		/**
		 * Filters WooCommerce available mails, to add Catalog mode related ones
		 *
		 * @param array $emails The array of available emails.
		 *
		 * @return  array
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_woocommerce_emails( $emails ) {
			$emails['YWCTM_Request_Mail']          = include YWCTM_DIR . 'includes/integrations/forms/default/emails/class-ywctm-request-mail.php';
			$emails['YWCTM_Request_Mail_Customer'] = include YWCTM_DIR . 'includes/integrations/forms/default/emails/class-ywctm-request-mail-customer.php';

			return $emails;
		}

	}

	new YWCTM_Default_Form();
}
