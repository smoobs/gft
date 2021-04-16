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

if ( ! function_exists( 'ywctm_formidable_forms_form_active' ) ) {

	/**
	 * Check if Formidable Forms is active
	 *
	 * @return boolean
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_formidable_forms_form_active() {
		return class_exists( 'FrmAppHelper' );
	}
}

if ( ! function_exists( 'ywctm_formidable_forms_get_contact_forms' ) ) {

	/**
	 * Get list of forms by Formidable Forms plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_formidable_forms_get_contact_forms() {

		if ( ! ywctm_formidable_forms_form_active() ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = FrmForm::getAll();

		if ( $forms ) {
			foreach ( $forms as $form ) {
				$active_forms[ $form->id ] = $form->name;
			}
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_formidable_forms_message' ) ) {

	/**
	 * Append Product page permalink to mail body and to database entry (Formidable Forms)
	 *
	 * @param array $values Form data.
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_formidable_forms_message( $values ) {

		if ( isset( $values['ywctm-product-id'] ) ) {

			$post_id         = $values['ywctm-product-id'];
			$params          = explode( ',', $values['ywctm-params'] );
			$form_id         = $values['form_id'];
			$field_id        = $values['ywctm-ff-field-id'];
			$formidable_form = ywctm_get_localized_form( 'formidable-forms', $post_id );

			if ( (int) $form_id === (int) $formidable_form && apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {
				$values['item_meta'][ $field_id ] = ywctm_get_product_link( $post_id, $params );
			}
		}

		return $values;

	}

	add_filter( 'frm_pre_create_entry', 'ywctm_formidable_forms_message' );

}
