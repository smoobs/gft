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
 * @var $email_heading array The email header.
 * @var $email         WC_Email The email object.
 * @var $mail_body     array The email body.
 * @var $form_data     array The data from the form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( wp_strip_all_tags( wptexturize( $mail_body ) ) );
echo "\n\n";

foreach ( $form_data as $key => $field ) {

	if ( ! isset( $field['label'] ) || ! $field['value'] ) {
		continue;
	}

	if ( 'product' === $key ) {
		echo esc_html( $field['label'] . ': ' . ywctm_get_product_link( $field['value']['id'], $field['value']['params'], false ) );
	} else {
		echo esc_html( $field['label'] . ': ' . $field['value'] );
	}
	echo "\n";
}

echo "\n\n----------------------------------------\n\n";

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
