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

if ( ! function_exists( 'ywctm_gravity_forms_active' ) ) {

	/**
	 * Check if Gravity Forms is active.
	 *
	 * @return boolean
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_gravity_forms_active() {
		return class_exists( 'GFForms' );
	}
}

if ( ! function_exists( 'ywctm_gravity_forms_get_contact_forms' ) ) {

	/**
	 * Get list of forms by Gravity Forms plugin
	 *
	 * @return  array|string
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_gravity_forms_get_contact_forms() {

		if ( ! ywctm_gravity_forms_active() ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = GFFormsModel::get_forms( null, 'title' );

		if ( $forms ) {
			foreach ( $forms as $form ) {
				$active_forms[ $form->id ] = $form->title;
			}
		}
		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_gravity_forms_message' ) ) {

	/**
	 * Append Product page permalink to mail body (Gravity Forms)
	 *
	 * @param array  $components  Form data.
	 * @param string $mail_format Mail format.
	 *
	 * @return  array
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_gravity_forms_message( $components, $mail_format ) {

		$request = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( isset( $request['ywctm-product-id'] ) ) {

			$post_id = $request['ywctm-product-id'];
			$params  = explode( ',', $request['ywctm-params'] );

			if ( apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {

				$field       = '';
				$lead        = '';
				$field_label = esc_html__( 'Product', 'yith-woocommerce-catalog-mode' );

				if ( 'html' !== $mail_format ) {
					$field_data = $field_label . ': ' . ywctm_get_product_link( $post_id, $params, false ) . "\n\n";
				} else {

					ob_start();
					?>
					<table width="99%" border="0" cellpadding="1" cellspacing="0" bgcolor="#EAEAEA">
						<tr>
							<td>
								<table width="100%" border="0" cellpadding="5" cellspacing="0" bgcolor="#FFFFFF">
									<tr bgcolor="<?php echo esc_attr( apply_filters( 'gform_email_background_color_label', '#EAF2FA', $field, $lead ) ); ?>">
										<td colspan="2">
											<font
												style="font-family: sans-serif; font-size:12px;"><strong><?php echo esc_attr( $field_label ); ?></strong></font>
										</td>
									</tr>
									<tr bgcolor="<?php echo esc_attr( apply_filters( 'gform_email_background_color_data', '#FFFFFF', $field, $lead ) ); ?>">
										<td width="20">&nbsp;</td>
										<td>
											<?php echo wp_kses_post( ywctm_get_product_link( $post_id, $params ) ); ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<br />
					<?php
					$field_data = ob_get_clean();

				}

				$components['message'] = $field_data . $components['message'];

			}
		}

		return $components;

	}

	add_filter( 'gform_pre_send_email', 'ywctm_gravity_forms_message', 10, 2 );

}
