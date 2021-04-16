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
 * @var $fields     array The form fields.
 * @var $product_id array The product ID.
 */

?>
<div class="ywctm-mail-form-wrapper">
	<form id="ywctm-default-form" name="ywctm-default-form" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" id="ywctm-default-form-sent" name="ywctm-default-form-sent" value="1">
		<?php
		foreach ( $fields as $key => $field ) {
			if ( isset( $field['enabled'] ) && in_array( $field['enabled'], array( 1, 'yes' ), true ) ) {
				$default           = isset( $field['default'] ) ? $field['default'] : '';
				$field['required'] = isset( $field['required'] ) ? wc_string_to_bool( $field['required'] ) : false;
				$field['class']    = isset( $field['class'] ) ? (array) $field['class'] : array();
				isset( $field['position'] ) && array_push( $field['class'], $field['position'] );
				woocommerce_form_field( $key, $field );
			}
		}
		?>
		<?php if ( ywctm_check_recaptcha_options() ) : ?>
			<div class="form-row form-row-wide g-recaptcha" id="recaptcha_ctm" data-sitekey="<?php echo esc_attr( get_option( 'ywctm_reCAPTCHA_sitekey' ) ); ?>"></div>
		<?php endif; ?>
		<p class="form-row form-row-wide button-row">
			<?php if ( defined( 'ICL_LANGUAGE_CODE' ) ) : ?>
				<input type="hidden" class="lang_param" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>" />
			<?php endif ?>
			<input type="hidden" name="ywctm-product-id" value="<?php echo esc_attr( $product_id ); ?>" />
			<input type="hidden" name="ywctm-params" value="" />
			<input type="hidden" name="ywctm-vendor-id" value="<?php echo esc_attr( ywctm_get_vendor_id_frontend() ); ?>" />
			<input type="hidden" id="ywctm-mail-wpnonce" name="ywctm_mail_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'ywctm-default-form-request' ) ); ?>">
			<input class="button ywctm-send-request" type="submit" value="<?php echo esc_attr( apply_filters( 'ywctm_form_default_submit_label', esc_html__( 'Send Your Request', 'yith-woocommerce-catalog-mode' ) ) ); ?>">
		</p>
	</form>
</div>
