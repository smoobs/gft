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

if ( ! function_exists( 'ywctm_get_default_form_fields' ) ) {

	/**
	 * Get default fields
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_default_form_fields() {
		return array(
			'first_name' => array(
				'id'          => 'first_name',
				'type'        => 'text',
				'class'       => array(),
				'label'       => esc_html__( 'First Name', 'yith-woocommerce-catalog-mode' ),
				'placeholder' => '',
				'enabled'     => 'yes',
				'validate'    => array(),
				'required'    => 'yes',
				'standard'    => true,
				'position'    => 'form-row-wide',
			),
			'last_name'  => array(
				'id'          => 'last_name',
				'type'        => 'text',
				'class'       => array(),
				'label'       => esc_html__( 'Last Name', 'yith-woocommerce-catalog-mode' ),
				'placeholder' => '',
				'enabled'     => 'yes',
				'validate'    => array(),
				'required'    => 'yes',
				'standard'    => true,
				'position'    => 'form-row-wide',
			),
			'email'      => array(
				'id'          => 'email',
				'type'        => 'email',
				'class'       => array(),
				'label'       => esc_html__( 'Email', 'yith-woocommerce-catalog-mode' ),
				'placeholder' => '',
				'enabled'     => 'yes',
				'validate'    => array( 'email' ),
				'required'    => 'yes',
				'standard'    => true,
				'position'    => 'form-row-wide',
			),
			'message'    => array(
				'id'          => 'message',
				'type'        => 'textarea',
				'class'       => array(),
				'label'       => esc_html__( 'Message', 'yith-woocommerce-catalog-mode' ),
				'placeholder' => '',
				'validate'    => array(),
				'enabled'     => 'yes',
				'required'    => 'no',
				'standard'    => true,
				'position'    => 'form-row-wide',
			),
			'acceptance' => array(
				'id'          => 'acceptance',
				'type'        => 'ywctm_acceptance',
				'class'       => array(),
				'label'       => esc_html__( 'Acceptance', 'yith-woocommerce-catalog-mode' ),
				'placeholder' => '',
				'validate'    => array(),
				'enabled'     => 'no',
				'required'    => 'yes',
				'standard'    => true,
				'position'    => 'form-row-wide',
				'description' => esc_html__( 'I have read and accepted your [terms] and [privacy_policy]', 'yith-woocommerce-catalog-mode' ),
			),
		);
	}
}

if ( ! function_exists( 'ywctm_get_field_types' ) ) {

	/**
	 * Return the type of fields
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_field_types() {
		return array(
			'text'             => esc_html_x( 'Text', 'Text field', 'yith-woocommerce-catalog-mode' ),
			'email'            => esc_html_x( 'Email', 'Email field', 'yith-woocommerce-catalog-mode' ),
			'textarea'         => esc_html_x( 'Textarea', 'Textarea field', 'yith-woocommerce-catalog-mode' ),
			'ywctm_acceptance' => esc_html_x( 'Acceptance', 'Field to add the Acceptance on form', 'yith-woocommerce-catalog-mode' ),
		);
	}
}

if ( ! function_exists( 'ywctm_get_array_positions_form_field' ) ) {

	/**
	 * Get an array with all positions field
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_array_positions_form_field() {
		return array(
			'form-row-first' => __( 'First', 'yith-woocommerce-catalog-mode' ),
			'form-row-last'  => __( 'Last', 'yith-woocommerce-catalog-mode' ),
			'form-row-wide'  => __( 'Wide', 'yith-woocommerce-catalog-mode' ),
		);
	}
}

if ( ! function_exists( 'ywctm_get_form_fields' ) ) {

	/**
	 * Get request a quote fields
	 *
	 * @param boolean $validate Check if fields should be validated.
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_form_fields( $validate = false ) {

		// First check in options.
		$fields = apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_default_table_form', array() ), get_the_id(), 'ywctm_default_table_form' );

		// If options is empty gets the defaults form fields.
		if ( empty( $fields ) ) {
			$fields = ywctm_get_default_form_fields();
		}

		// First validate if is admin.
		if ( $validate ) {
			$fields = ywctm_validate_form_fields_option( $fields );
		}

		return apply_filters( 'ywctm_form_fields', $fields );
	}
}

if ( ! function_exists( 'ywctm_validate_form_fields_option' ) ) {

	/**
	 * Validate fields option and add defaults value
	 *
	 * @param array $fields Form fileds to validate.
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_validate_form_fields_option( $fields ) {

		if ( empty( $fields ) ) {
			return array();
		}

		foreach ( $fields as &$field ) {
			// Type standard text if not set.
			$field['type'] = ( isset( $field['type'] ) ) ? $field['type'] : 'text';
			// Label empty if not set.
			$field['label'] = ( isset( $field['label'] ) ) ? $field['label'] : '';
			// Placeholder empty if not set.
			$field['placeholder'] = ( isset( $field['placeholder'] ) ) ? $field['placeholder'] : '';
			// Set class and position for field.
			if ( isset( $field['class'] ) && is_array( $field['class'] ) ) {
				$positions = ywctm_get_array_positions_form_field();
				foreach ( $field['class'] as $key => $single_class ) {
					if ( is_array( $positions ) && array_key_exists( $single_class, $positions ) ) {
						$field['position'] = $single_class;
						unset( $field['class'][ $key ] );
						break;
					}
				}
				$field['class'] = implode( ',', $field['class'] );
			}
			// Set empty if position not set.
			$field['position'] = ( isset( $field['position'] ) ) ? $field['position'] : '';
			// Set label class foe field.
			$field['label_class'] = ( isset( $field['label_class'] ) && is_array( $field['label_class'] ) ) ? implode( ',', $field['label_class'] ) : '';
			// Set validation.
			$field['validate'] = ( isset( $field['validate'] ) && is_array( $field['validate'] ) ) ? implode( ',', $field['validate'] ) : '';
			// Set required ( default false ).
			$field['required'] = ( ! isset( $field['required'] ) || ! $field['required'] ) ? '0' : '1';
			// Set clear ( default false ).
			$field['clear'] = ( ! isset( $field['clear'] ) || ! $field['clear'] ) ? '0' : '1';
			// Set enabled ( default true ).
			$field['enabled'] = ( isset( $field['enabled'] ) && ! $field['enabled'] ) ? '0' : '1';
		}

		return $fields;
	}
}

if ( ! function_exists( 'ywctm_check_recaptcha_options' ) ) {

	/**
	 * Check if recaptcha is enabled and it can be added to the form.
	 *
	 * @return  boolean
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_check_recaptcha_options() {
		$recaptcha = get_option( 'ywctm_reCAPTCHA' );
		$sitekey   = get_option( 'ywctm_reCAPTCHA_sitekey' );
		$secretkey = get_option( 'ywctm_reCAPTCHA_secretkey' );

		$is_captcha = 'yes' === $recaptcha && ! empty( $sitekey ) && $secretkey;

		return $is_captcha;
	}
}

if ( ! function_exists( 'ywctm_get_ajax_default_loader' ) ) {

	/**
	 * Return the default loader.
	 *
	 * @return string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_get_ajax_default_loader() {

		$ajax_loader_default = YWCTM_ASSETS_URL . 'images/ajax-loader.gif';
		if ( defined( 'YITH_PROTEO_VERSION' ) ) {
			$ajax_loader_default = YWCTM_ASSETS_URL . 'images/proteo-loader.gif';
		}

		return apply_filters( 'ywctm_ajax_loader', $ajax_loader_default );
	}
}

if ( ! function_exists( 'ywctm_field_filter_wpml_strings' ) ) {

	/**
	 * Filter field strings for WPML translations
	 *
	 * @param string $field_key The field key.
	 * @param array  $field     The field options.
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_field_filter_wpml_strings( $field_key, $field ) {
		if ( ! class_exists( 'SitePress' ) ) {
			return $field;
		}
		// Get label if any.
		if ( isset( $field['label'] ) && $field['label'] ) {
			$field['label'] = apply_filters( 'wpml_translate_single_string', $field['label'], 'yith-woocommerce-catalog-mode', 'plugin_ywctm_' . $field_key . '_label' );
		}
		// Get placeholder if any.
		if ( isset( $field['placeholder'] ) && $field['placeholder'] ) {
			$field['placeholder'] = apply_filters( 'wpml_translate_single_string', $field['placeholder'], 'yith-woocommerce-catalog-mode', 'plugin_ywctm_' . $field_key . '_placeholder' );
		}

		if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {

			foreach ( $field['options'] as $option_key => $option ) {
				if ( '' === $option ) {
					continue;
				}
				// Register single option.
				$field['options'][ $option_key ] = apply_filters( 'wpml_translate_single_string', $option, 'yith-woocommerce-catalog-mode', 'plugin_ywctm_' . $field_key . '_' . $option_key );
			}
		}

		return $field;
	}
}


if ( ! function_exists( 'ywctm_mail_options' ) ) {

	/**
	 * Get options for admin email
	 *
	 * @param boolean $wc_settings Check if these options are printed in WC settings panel.
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_mail_options( $wc_settings = false ) {

		$types = array( 'plain' => esc_html__( 'Plain text', 'woocommerce' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = esc_html__( 'HTML', 'woocommerce' );
			$types['multipart'] = esc_html__( 'Multipart', 'woocommerce' );
		}
		$form_fields['ywctm_admin_mail_recipient'] = array(
			'title'       => esc_html__( 'Recipient(s)', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'text',
			'id'          => 'ywctm_admin_mail_recipient',
			/* translators: %s default email */
			'description' => sprintf( esc_html__( 'Enter recipients (comma separated) for this email. Defaults to %s', 'yith-woocommerce-catalog-mode' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
			'default'     => esc_attr( get_option( 'admin_email' ) ),
			'desc_tip'    => true,
		);
		$form_fields ['ywctm_mail_subject']        = array(
			'title'       => esc_html__( 'Email subject', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'text',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-catalog-mode' ), '<code>{site_title}, {site_address}, {site_url}</code>' ),
			'id'          => 'ywctm_mail_subject',
			'default'     => esc_html__( '[{site_title}] You have a new inquiry.', 'yith-woocommerce-catalog-mode' ),
			'desc_tip'    => true,
		);
		$form_fields ['ywctm_mail_heading']        = array(
			'title'       => esc_html__( 'Email heading', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'text',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-catalog-mode' ), '<code>{site_title}, {site_address}, {site_url}</code>' ),
			'id'          => 'ywctm_mail_heading',
			'default'     => esc_html__( 'Inquiry message', 'yith-woocommerce-catalog-mode' ),
			'desc_tip'    => true,
		);
		$form_fields ['ywctm_mail_body']           = array(
			'title'       => esc_html__( 'Email body', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'textarea',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-catalog-mode' ), '<code>{site_title}, {site_address}, {site_url}</code>' ),
			'id'          => 'ywctm_mail_body',
			'default'     => esc_html__( 'You have received a new inquiry message.', 'yith-woocommerce-catalog-mode' ),
			'css'         => 'width:400px; height: 75px;',
			'desc_tip'    => true,
		);
		$form_fields ['ywctm_mail_type']           = array(
			'title'       => esc_html__( 'Email type', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'select',
			'default'     => 'html',
			'id'          => 'ywctm_mail_type',
			'description' => esc_html__( 'Choose which format of email to send.', 'yith-woocommerce-catalog-mode' ),
			'class'       => 'email_type wc-enhanced-select',
			'options'     => $types,
			'desc_tip'    => true,
		);

		if ( ! $wc_settings ) {
			foreach ( $form_fields as $key => $field ) {
				$form_fields[ $key ]['yith-type'] = $field['type'];
				$form_fields[ $key ]['type']      = 'yith-field';
				$form_fields[ $key ]['desc']      = $field['description'];
				unset( $form_fields[ $key ]['description'] );
				unset( $form_fields[ $key ]['desc_tip'] );
			}
		}

		return $form_fields;
	}
}

if ( ! function_exists( 'ywctm_mail_customer_options' ) ) {

	/**
	 * Get options for customer email
	 *
	 * @param boolean $wc_settings Check if these options are printed in WC settings panel.
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_mail_customer_options( $wc_settings = false ) {

		$types = array( 'plain' => esc_html__( 'Plain text', 'woocommerce' ) );

		if ( class_exists( 'DOMDocument' ) ) {
			$types['html']      = esc_html__( 'HTML', 'woocommerce' );
			$types['multipart'] = esc_html__( 'Multipart', 'woocommerce' );
		}
		$form_fields ['ywctm_mail_customer_subject'] = array(
			'title'       => esc_html__( 'Email subject', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'text',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-catalog-mode' ), '<code>{site_title}, {site_address}, {site_url}</code>' ),
			'id'          => 'ywctm_mail_customer_subject',
			'default'     => esc_html__( '[{site_title}] We have received your inquiry.', 'yith-woocommerce-catalog-mode' ),
			'desc_tip'    => true,
		);
		$form_fields ['ywctm_mail_customer_heading'] = array(
			'title'       => esc_html__( 'Email heading', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'text',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-catalog-mode' ), '<code>{site_title}, {site_address}, {site_url}</code>' ),
			'id'          => 'ywctm_mail_customer_heading',
			'default'     => esc_html__( 'Your inquiry was received.', 'yith-woocommerce-catalog-mode' ),
			'desc_tip'    => true,
		);
		$form_fields ['ywctm_mail_customer_body']    = array(
			'title'       => esc_html__( 'Email body', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'textarea',
			/* translators: %s placeholders */
			'description' => sprintf( esc_html__( 'Available placeholders: %s', 'yith-woocommerce-catalog-mode' ), '<code>{site_title}, {site_address}, {site_url}</code>' ),
			'id'          => 'ywctm_mail_customer_body',
			'default'     => esc_html__( 'We have received your inquiry message. Here\'s a recap of the details you have submitted:', 'yith-woocommerce-catalog-mode' ),
			'css'         => 'width:400px; height: 75px;',
			'desc_tip'    => true,
		);
		$form_fields ['ywctm_mail_customer_type']    = array(
			'title'       => esc_html__( 'Email type', 'yith-woocommerce-catalog-mode' ),
			'type'        => 'select',
			'default'     => 'html',
			'id'          => 'ywctm_mail_customer_type',
			'description' => esc_html__( 'Choose which format of email to send.', 'yith-woocommerce-catalog-mode' ),
			'class'       => 'email_type wc-enhanced-select',
			'options'     => $types,
			'desc_tip'    => true,
		);

		if ( ! $wc_settings ) {
			foreach ( $form_fields as $key => $field ) {
				$form_fields[ $key ]['yith-type'] = $field['type'];
				$form_fields[ $key ]['type']      = 'yith-field';
				$form_fields[ $key ]['desc']      = $field['description'];
				unset( $form_fields[ $key ]['description'] );
				unset( $form_fields[ $key ]['desc_tip'] );
			}
		}

		return $form_fields;
	}
}
