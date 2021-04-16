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

if ( ! function_exists( 'ywctm_wpforms_active' ) ) {

	/**
	 * Check if WPForms is active.
	 *
	 * @return boolean
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_wpforms_active() {
		return ( defined( 'WPFORMS_VERSION' ) && version_compare( WPFORMS_VERSION, '1.6.4.1', '>=' ) );
	}
}

if ( ! function_exists( 'ywctm_wpforms_get_contact_forms' ) ) {

	/**
	 * Get list of forms by WPForms plugin
	 *
	 * @return  array|string
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_wpforms_get_contact_forms() {
		if ( ! ywctm_wpforms_active() ) {
			return 'inactive';
		}

		$active_forms = array();
		$forms        = wpforms()->form->get();

		if ( $forms ) {
			foreach ( $forms as $form ) {
				$active_forms[ $form->ID ] = $form->post_title;
			}
		}

		if ( array() === $active_forms ) {
			return 'no-forms';
		}

		return $active_forms;

	}
}

if ( ! function_exists( 'ywctm_wpforms_entry_email_atts' ) ) {

	/**
	 * Append Product page permalink to mail body (WPForms)
	 *
	 * @param array $email  Email data.
	 * @param array $fields Unused.
	 * @param array $entry  The entry data.
	 *
	 * @return  array
	 * @since   2.1.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_wpforms_entry_email_atts( $email, $fields, $entry ) {

		if ( isset( $entry['ywctm-product-id'] ) ) {

			$post_id = $entry['ywctm-product-id'];
			$params  = explode( ',', $entry['ywctm-params'] );
			$wpforms = ywctm_get_localized_form( 'wpforms', $post_id );

			if ( (int) $entry['id'] === (int) $wpforms && apply_filters( 'ywctm_get_vendor_option', get_option( 'ywctm_inquiry_product_permalink' ), $post_id, 'ywctm_inquiry_product_permalink' ) === 'yes' ) {

				$field_label = esc_html__( 'Product', 'yith-woocommerce-catalog-mode' );

				if ( 'none' !== wpforms_setting( 'email-template', 'default' ) ) {
					ob_start();
					?>
					<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top:1px solid #dddddd; display:block;min-width: 100%;border-collapse: collapse;width:100%;">
						<tbody>
						<tr>
							<td style="color:#333333;padding-top: 20px;padding-bottom: 3px;"><strong><?php echo esc_attr( $field_label ); ?></strong></td>
						</tr>
						<tr>
							<td style="color:#555555;padding-top: 3px;padding-bottom: 20px;"><?php echo wp_kses_post( ywctm_get_product_link( $post_id, $params ) ); ?></td>
						</tr>
						</tbody>
					</table>
					<?php
					$email['message'] .= ob_get_clean();
				} else {
					$email['message'] .= '--- ' . esc_attr( $field_label ) . " ---\r\n\r\n";
					$email['message'] .= ywctm_get_product_link( $post_id, $params, false ) . "\r\n\r\n";
				}
			}
		}

		return $email;
	}

	add_filter( 'wpforms_entry_email_atts', 'ywctm_wpforms_entry_email_atts', 10, 3 );

}
