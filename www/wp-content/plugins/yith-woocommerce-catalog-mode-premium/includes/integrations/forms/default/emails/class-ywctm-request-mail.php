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

if ( ! class_exists( 'YWCTM_Request_Mail' ) ) {

	/**
	 * Implements Request Mail for Catalog Mode plugin
	 *
	 * @class   YWCTM_Request_Mail
	 * @since   2.1.0
	 * @author  Your Inspiration Themes
	 * @extends WC_Email
	 *
	 * @package Yithemes
	 */
	class YWCTM_Request_Mail extends WC_Email {

		/**
		 * The language of the email.
		 *
		 * @var string $lang
		 */
		public $lang;

		/**
		 * The form data.
		 *
		 * @var array $form_data
		 */
		public $form_data;

		/**
		 * Constructor
		 *
		 * Initialize email type and set templates paths
		 *
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->title          = esc_html__( 'Catalog Mode: Admin Email', 'yith-woocommerce-catalog-mode' );
			$this->id             = 'yith-catalog-mode-admin';
			$this->description    = esc_html__( 'Inquiry email for the admin', 'yith-woocommerce-catalog-mode' );
			$this->customer_email = false;
			$this->template_base  = YWCTM_TEMPLATE_PATH;
			$this->template_html  = 'emails/catalog-mode-admin-email.php';
			$this->template_plain = 'emails/plain/catalog-mode-admin-email.php';

			global $woocommerce_wpml;

			$is_wpml_configured = apply_filters( 'wpml_setting', false, 'setup_complete' );
			if ( $is_wpml_configured && defined( 'WCML_VERSION' ) && $woocommerce_wpml ) {
				add_filter( 'ywctm_send_mail_notification', array( $this, 'refresh_email_lang' ), 10, 1 );
			}

			add_filter( 'ywctm_send_mail_notification', array( $this, 'trigger' ), 15, 1 );

			parent::__construct();
			$this->recipient = $this->get_option( 'ywctm_admin_mail_recipient' );
			$this->plugin_id = '';

		}

		/**
		 * Trigger email send
		 *
		 * @param array $form_data The form data.
		 *
		 * @return  boolean
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function trigger( $form_data ) {

			$this->lang      = isset( $form_data['lang'] ) ? $form_data['lang'] : '';
			$this->heading   = apply_filters( 'wpml_translate_single_string', $this->get_subject(), 'admin_texts_ywctm_mail_subject', 'ywctm_mail_subject', $this->lang );
			$this->subject   = apply_filters( 'wpml_translate_single_string', $this->get_heading(), 'admin_texts_ywctm_mail_heading', 'ywctm_mail_heading', $this->lang );
			$this->form_data = $form_data;
			$this->recipient = $this->get_option( 'ywctm_admin_mail_recipient' );

			if ( ! $this->get_recipient() ) {
				return false;
			}

			return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		}

		/**
		 * Set email headers.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_headers() {
			$headers = parent::get_headers();

			if ( isset( $this->form_data['cc_emails'] ) && ! empty( $this->form_data['cc_emails'] ) ) {
				foreach ( $this->form_data['cc_emails'] as $cc_email ) {
					$headers .= 'Cc: ' . $cc_email . "\r\n";
				}
			}

			return $headers;
		}

		/**
		 * Refresh email language
		 *
		 * @param array $form_data The form data.
		 *
		 * @return  array
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function refresh_email_lang( $form_data ) {

			if ( isset( $form_data['lang'] ) ) {
				global $sitepress;
				$sitepress->switch_lang( $form_data['lang'], true );
			}

			return $form_data;

		}

		/**
		 * Return content from the Mail Body field.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_mail_body() {
			$content = $this->get_option( 'ywctm_mail_body' );

			return apply_filters( 'ywctm_mail_body', $this->format_string( $content ), $this->object, $this );
		}

		/**
		 * Get HTML content
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_content_html() {
			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'email_heading' => $this->get_heading(),
					'mail_body'     => $this->get_mail_body(),
					'form_data'     => $this->form_data,
					'lang'          => $this->lang,
					'sent_to_admin' => true,
					'plain_text'    => false,
					'email'         => $this,
				),
				false,
				$this->template_base
			);

			return ob_get_clean();
		}

		/**
		 * Get Plain content
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_content_plain() {
			ob_start();
			wc_get_template(
				$this->template_plain,
				array(
					'email_heading' => $this->get_heading(),
					'mail_body'     => $this->get_mail_body(),
					'form_data'     => $this->form_data,
					'lang'          => $this->lang,
					'sent_to_admin' => true,
					'plain_text'    => true,
					'email'         => $this,
				),
				false,
				$this->template_base
			);

			return ob_get_clean();
		}

		/**
		 * Checks if this email is enabled and will be sent.
		 *
		 * @return  boolean
		 * @since   1.1.4
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function is_enabled() {
			return true;
		}

		/**
		 * Get email type.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_email_type() {
			return $this->get_option( 'ywctm_mail_type' );
		}

		/**
		 * Admin Panel Options Processing - Saves the options to the DB
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function process_admin_options() {
			woocommerce_update_options( $this->form_fields );
		}

		/**
		 * Override option key.
		 *
		 * @param string $key The field key.
		 *
		 * @return  string
		 * @since   2.1.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_field_key( $key ) {
			return $key;
		}

		/**
		 * Get plugin option.
		 *
		 * @param string $key         The field key.
		 * @param mixed  $empty_value Defaut empty value.
		 *
		 * @return  mixed
		 * @since   2.1.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_option( $key, $empty_value = null ) {

			$setting = get_option( $key );

			// Get option default if unset.
			if ( ! $setting ) {
				$form_fields = $this->get_form_fields();
				$setting     = isset( $form_fields[ $key ] ) ? $this->get_field_default( $form_fields[ $key ] ) : '';
			}

			return $setting;

		}

		/**
		 * Get email subject.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_subject() {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->get_option( 'ywctm_mail_subject', $this->get_default_subject() ) ), $this->object );
		}

		/**
		 * Get email heading.
		 *
		 * @return  string
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_heading() {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->get_option( 'ywctm_mail_heading', $this->get_default_subject() ) ), $this->object );
		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init_form_fields() {

			if ( ! function_exists( 'ywctm_mail_options' ) ) {
				include_once YWCTM_DIR . 'includes/integrations/forms/default/ywctm-default-form.php';
			}

			$this->form_fields = ywctm_mail_options( true );

		}

	}

}

return new YWCTM_Request_Mail();
