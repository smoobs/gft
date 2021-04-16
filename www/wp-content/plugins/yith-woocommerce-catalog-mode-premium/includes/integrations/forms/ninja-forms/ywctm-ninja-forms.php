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

if ( ! function_exists( 'ywctm_ninja_forms_active' ) ) {

	/**
	 * Check if Ninja Forms is active.
	 *
	 * @return boolean
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_ninja_forms_active() {
		return class_exists( 'Ninja_Forms' );
	}
}

if ( ! function_exists( 'ywctm_ninja_forms_get_contact_forms' ) ) {

	/**
	 * Get list of forms by Ninja Forms plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_ninja_forms_get_contact_forms() {

		if ( ! ywctm_ninja_forms_active() ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = Ninja_Forms()->form()->get_forms();

		if ( $forms ) {
			foreach ( $forms as $form ) {
				$active_forms[ $form->get_id() ] = $form->get_setting( 'title' );
			}
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_ninja_forms_message' ) ) {

	/**
	 * Append Product page permalink to mail body and to database entry (Ninja Forms)
	 *
	 * @param array $data Form data.
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_ninja_forms_message( $data ) {

		$field_value = '';
		$field_key   = false;
		foreach ( $data['fields'] as $key => $field ) {
			if ( 'ywctm-product-id' === $field['key'] ) {
				$field_value = (array) json_decode( $field['value'] );
				$field_key   = $key;
				break;
			}
		}

		$post_id = isset( $field_value['id'] ) ? $field_value['id'] : false;
		$params  = isset( $field_value['params'] ) ? $field_value['params'] : array();

		if ( $post_id ) {

			$ninja_forms = ywctm_get_localized_form( 'ninja-forms', $post_id );

			if ( $data['id'] === $ninja_forms && apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {
				$data['fields'][ $field_key ]['value'] = ywctm_get_product_link( $post_id, $params );
			}
		}

		return $data;
	}

	add_filter( 'ninja_forms_submit_data', 'ywctm_ninja_forms_message', 10 );

}
if ( ! function_exists( 'ywctm_enable_hidden_field' ) ) {

	/**
	 * Enable HTML fields for sanitize processing
	 *
	 * @param array $fields HTML enabled fields.
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_enable_hidden_field( $fields ) {
		$fields[] = 'hidden';

		return $fields;
	}

	add_filter( 'ninja_forms_get_html_safe_fields', 'ywctm_enable_hidden_field' );
}
