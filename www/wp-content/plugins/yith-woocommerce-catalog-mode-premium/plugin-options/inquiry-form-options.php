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

$label       = esc_html__( 'Choose form', 'yith-woocommerce-catalog-mode' );
$description = esc_html__( 'Select the form to display on the product page.', 'yith-woocommerce-catalog-mode' );
$forms       = array( 'contact-form-7', 'formidable-forms', 'gravity-forms', 'ninja-forms', 'wpforms' );
$options     = array();
$fields      = array();

if ( ywctm_is_wpml_active() ) {
	$languages = apply_filters( 'wpml_active_languages', null, array() );
	foreach ( $forms as $form ) {

		foreach ( $languages as $language ) {
			$fields[ $language['language_code'] ] = array(
				'label'   => $language['translated_name'],
				'options' => ywctm_get_forms_list( $form ),
				'type'    => 'select',
				'std'     => '',
			);
		}

		$options[ $form ] = array(
			'name'      => $label,
			'type'      => 'yith-field',
			'yith-type' => 'inline-fields',
			'class'     => 'ywctm-multiple-languages',
			'desc'      => $description,
			'id'        => 'ywctm_inquiry_' . str_replace( '-', '_', $form ) . '_id_wpml' . ywctm_get_vendor_id(),
			'fields'    => $fields,
			'deps'      => array(
				'id'    => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
				'value' => $form,
				'type'  => 'hide-disable',
			),
		);
	}
} else {
	foreach ( $forms as $form ) {

		$options[ $form ] = array(
			'name'      => $label,
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'desc'      => $description,
			'id'        => 'ywctm_inquiry_' . str_replace( '-', '_', $form ) . '_id' . ywctm_get_vendor_id(),
			'options'   => ywctm_get_forms_list( $form ),
			'deps'      => array(
				'id'    => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
				'value' => $form,
				'type'  => 'hide-disable',
			),
		);

	}
}

return array(
	'inquiry-form' => array(
		'inquiry_form_title'       => array(
			'name' => esc_html__( 'Inquiry Form', 'yith-woocommerce-catalog-mode' ),
			'type' => 'title',
		),
		'inquiry_form_setting'     => array(
			'name'      => esc_html__( 'Set inquiry form as:', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => esc_html__( 'Choose to make the inquiry form visible to all products or only to the ones in the exclusion list.', 'yith-woocommerce-catalog-mode' ),
			'options'   => array(
				'hidden'    => esc_html__( 'Hidden in all products', 'yith-woocommerce-catalog-mode' ),
				'visible'   => esc_html__( 'Visible in all products', 'yith-woocommerce-catalog-mode' ),
				'exclusion' => esc_html__( 'Visible in products added to the Exclusion List only', 'yith-woocommerce-catalog-mode' ),
			),
			'default'   => 'hidden',
			'id'        => 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(),
		),
		'inquiry_form'             => array(
			'name'      => esc_html__( 'Form to show', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			/* translators: %s list of supported plugins */
			'desc'      => sprintf( esc_html__( 'Select which form to show. You can use the default form or it is possible to use another plugin and show a form created with %s', 'yith-woocommerce-catalog-mode' ), 'Contact Form 7, Formidable Forms, Gravity Forms, Ninja Forms, WPForms' ),
			'options'   => ywctm_get_active_form_plugins(),
			'default'   => 'none',
			'id'        => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
		),
		'contact_form_7'           => $options['contact-form-7'],
		'ninja_forms'              => $options['ninja-forms'],
		'formidable_forms'         => $options['formidable-forms'],
		'gravity_forms'            => $options['gravity-forms'],
		'wpforms'                  => $options['wpforms'],
		'default_table_form_title' => array(
			'id'        => 'ywctm_default_table_form_title',
			'type'      => 'yith-field',
			'yith-type' => 'title',
			'desc'      => esc_html_x( 'Default form fields', 'Admin options title', 'yith-woocommerce-catalog-mode' ),
			'deps'      => array(
				'id'    => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
				'value' => 'default',
			),
		),
		'default_table_form'       => array(
			'id'                    => 'ywctm_default_table_form' . ywctm_get_vendor_id(),
			'type'                  => 'yith-field',
			'yith-type'             => 'ywctm-default-form',
			'yith-display-row'      => false,
			'callback_default_form' => 'ywctm_get_default_form_fields',
			'deps'                  => array(
				'id'    => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
				'value' => 'default',
				'type'  => 'hide',
			),
		),
		'reCAPTCHA'                => array(
			'name'      => esc_html__( 'Add a reCAPTCHA to the default form', 'yith-woocommerce-catalog-mode' ),
			/* translators: %1$s line break, %2$s link opening, %3$s link closing */
			'desc'      => sprintf( esc_html_x( 'Enable to add reCAPTCHA option in default form. %1$s To start using reCAPTCHA V2, you need to %2$s sign up for an API key %3$s pair for your site.', 'string with placeholder do not translate or remove it', 'yith-woocommerce-catalog-mode' ), '<br>', '<a href="https://www.google.com/recaptcha/admin">', '</a>' ),
			'id'        => 'ywctm_reCAPTCHA' . ywctm_get_vendor_id(),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'deps'      => array(
				'id'    => 'ywctm_inquiry_form_type' . ywctm_get_vendor_id(),
				'value' => 'default',
				'type'  => 'hide',
			),
			'default'   => 'no',
		),
		'reCAPTCHA_sitekey'        => array(
			'name'      => esc_html__( 'Site key', 'yith-woocommerce-catalog-mode' ),
			'desc'      => esc_html__( 'Enter the reCAPTCHA site key', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_reCAPTCHA_sitekey' . ywctm_get_vendor_id(),
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
			'deps'      => array(
				'id'    => 'ywctm_reCAPTCHA' . ywctm_get_vendor_id(),
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'reCAPTCHA_secretkey'      => array(
			'name'      => esc_html__( 'Secret key', 'yith-woocommerce-catalog-mode' ),
			'desc'      => esc_html__( 'Enter reCAPTCHA secret key', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_reCAPTCHA_secretkey' . ywctm_get_vendor_id(),
			'class'     => 'regular-input',
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'default'   => '',
			'deps'      => array(
				'id'    => 'ywctm_reCAPTCHA' . ywctm_get_vendor_id(),
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'form_title_options'       => array(
			'id'        => 'ywctm_form_title_options' . ywctm_get_vendor_id(),
			'type'      => 'yith-field',
			'yith-type' => 'title',
			'desc'      => esc_html_x( 'Form options', 'Admin options title', 'yith-woocommerce-catalog-mode' ),
		),
		'where_show'               => array(
			'name'      => esc_html__( 'Show form in:', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'tab'  => esc_html__( 'WooCommerce Tabs', 'yith-woocommerce-catalog-mode' ),
				'desc' => esc_html__( 'Short description area', 'yith-woocommerce-catalog-mode' ),
			),
			'default'   => 'tab',
			'id'        => 'ywctm_inquiry_form_where_show' . ywctm_get_vendor_id(),
			'desc'      => esc_html__( 'Choose to show the inquiry form inside a WooCommerce tab or in the short description area.', 'yith-woocommerce-catalog-mode' ),

		),
		'form_position'            => array(
			'name'      => esc_html__( 'Form Position', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'class'     => 'wc-enhanced-select',
			'options'   => array(
				'15' => esc_html__( 'After price', 'yith-woocommerce-catalog-mode' ),
				'25' => esc_html__( 'After short description', 'yith-woocommerce-catalog-mode' ),
				'35' => esc_html__( 'After "Add to cart" button', 'yith-woocommerce-catalog-mode' ),
			),
			'default'   => '15',
			'id'        => 'ywctm_inquiry_form_position' . ywctm_get_vendor_id(),
			'deps'      => array(
				'id'    => 'ywctm_inquiry_form_where_show' . ywctm_get_vendor_id(),
				'value' => 'desc',
				'type'  => 'fadeIn',
			),
		),
		'form_style'               => array(
			'name'      => esc_html__( 'Form style', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'classic' => esc_html__( 'Classic', 'yith-woocommerce-catalog-mode' ),
				'toggle'  => esc_html__( 'Hidden in toggle', 'yith-woocommerce-catalog-mode' ),
			),
			'default'   => 'classic',
			'id'        => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
			'deps'      => array(
				'id'    => 'ywctm_inquiry_form_where_show' . ywctm_get_vendor_id(),
				'value' => 'desc',
				'type'  => 'fadeIn',
			),
			'desc'      => esc_html__( 'Choose whether to show the form is visible in the page or it is hidden in a toggled section.', 'yith-woocommerce-catalog-mode' ),
		),
		'tab_title'                => array(
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'name'              => esc_html__( 'Tab title', 'yith-woocommerce-catalog-mode' ),
			'id'                => 'ywctm_inquiry_form_tab_title' . ywctm_get_vendor_id(),
			'default'           => esc_html__( 'Inquiry form', 'yith-woocommerce-catalog-mode' ),
			'custom_attributes' => array(
				'required' => 'required',
			),
		),
		'text_before'              => array(
			'type'          => 'yith-field',
			'yith-type'     => 'textarea-editor',
			'media_buttons' => false,
			'wpautop'       => false,
			'textarea_rows' => 5,
			'name'          => esc_html__( 'Text before form', 'yith-woocommerce-catalog-mode' ),
			'id'            => 'ywctm_text_before_form' . ywctm_get_vendor_id(),
			'desc'          => esc_html__( 'Enter a custom text to show before the inquiry form.', 'yith-woocommerce-catalog-mode' ),
		),
		'toggle_button'            => array(
			'type'      => 'yith-field',
			'yith-type' => 'text',
			'name'      => esc_html__( 'Toggle button text', 'yith-woocommerce-catalog-mode' ),
			'id'        => 'ywctm_toggle_button_text' . ywctm_get_vendor_id(),
			'default'   => esc_html__( 'Send an inquiry', 'yith-woocommerce-catalog-mode' ),
			'deps'      => array(
				'id'    => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
				'value' => 'toggle',
				'type'  => 'fadeIn',
			),
		),
		'button_text_colors'       => array(
			'id'           => 'ywctm_toggle_button_text_color' . ywctm_get_vendor_id(),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'id'      => 'default',
					'name'    => esc_html__( 'Default', 'yith-woocommerce-catalog-mode' ),
					'default' => '#247390',
				),
				array(
					'id'      => 'hover',
					'name'    => esc_html__( 'Hover', 'yith-woocommerce-catalog-mode' ),
					'default' => '#FFFFFF',
				),
			),
			'name'         => esc_html__( 'Toggle button text colors', 'yith-woocommerce-catalog-mode' ),
			'deps'         => array(
				'id'    => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
				'value' => 'toggle',
				'type'  => 'fadeIn',
			),
		),
		'button_background'        => array(
			'id'           => 'ywctm_toggle_button_background_color' . ywctm_get_vendor_id(),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'id'      => 'default',
					'name'    => esc_html__( 'Default', 'yith-woocommerce-catalog-mode' ),
					'default' => '#FFFFFF',
				),
				array(
					'id'      => 'hover',
					'name'    => esc_html__( 'Hover', 'yith-woocommerce-catalog-mode' ),
					'default' => '#247390',
				),
			),
			'name'         => esc_html__( 'Toggle button colors', 'yith-woocommerce-catalog-mode' ),
			'deps'         => array(
				'id'    => 'ywctm_inquiry_form_style' . ywctm_get_vendor_id(),
				'value' => 'toggle',
				'type'  => 'fadeIn',
			),
		),
		'product_permalink'        => array(
			'name'      => esc_html__( 'Include product Permalink', 'yith-woocommerce-catalog-mode' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			/* translators: %s line break */
			'desc'      => sprintf( esc_html__( 'Use this option to include the product permalink in the email body.%sIn this way, you can identify from which product page the message was sent.', 'yith-woocommerce-catalog-mode' ), '<br />' ),
			'id'        => 'ywctm_inquiry_product_permalink' . ywctm_get_vendor_id(),
			'default'   => 'no',
		),
		'inquiry_form_end'         => array(
			'type' => 'sectionend',
		),
	),
);
