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

if ( ! function_exists( 'ywctm_create_sample_buttons' ) ) {

	/**
	 * Run plugin upgrade to version 2.0.0
	 *
	 * @return  void
	 * @since   2.0.0
	 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
	 */
	function ywctm_create_sample_buttons() {

		$sample_buttons = array(
			array(
				'name'    => esc_html__( 'Sample Button 1', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					'label_text'              => '<div style="text-align: center;"><strong><span style="font-family: inherit; font-size: 14px;">' . esc_html__( 'ASK INFO', 'yith-woocommerce-catalog-mode' ) . '</span></strong></div>',
					'default_colors'          => array(
						'text'       => '#ffffff',
						'background' => '#e09004',
						'border'     => '#e09004',
					),
					'hover_colors'            => array(
						'text'       => '#ffffff',
						'background' => '#b97600',
						'border'     => '#b97600',
					),
					'border_thickness'        => array(
						'dimensions' => array(
							'top'    => 1,
							'right'  => 1,
							'bottom' => 1,
							'left'   => 1,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'border_radius'           => array(
						'dimensions' => array(
							'top'    => 50,
							'right'  => 50,
							'bottom' => 50,
							'left'   => 50,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => 200,
						'unit'  => '',
					),
					'margin_settings'         => array(
						'dimensions' => array(
							'top'    => 0,
							'right'  => 0,
							'bottom' => 0,
							'left'   => 0,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'padding_settings'        => array(
						'dimensions' => array(
							'top'    => 5,
							'right'  => 10,
							'bottom' => 5,
							'left'   => 10,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
				),
			),
			array(
				'name'    => esc_html__( 'Sample Button 2', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					'label_text'              => '<div style="text-align: center;"><strong><span style="font-family: inherit; font-size: 14px;">' . esc_html__( 'SEND INQUIRY', 'yith-woocommerce-catalog-mode' ) . '</span></strong></div>',
					'default_colors'          => array(
						'text'       => '#ffffff',
						'background' => '#36809a',
						'border'     => '#215d72',
					),
					'hover_colors'            => array(
						'text'       => '#ffffff',
						'background' => '#36809a',
						'border'     => '#215d72',
					),
					'button_url'              => '',
					'border_thickness'        => array(
						'dimensions' => array(
							'top'    => 1,
							'right'  => 1,
							'bottom' => 1,
							'left'   => 1,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'border_radius'           => array(
						'dimensions' => array(
							'top'    => 50,
							'right'  => 50,
							'bottom' => 50,
							'left'   => 50,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => 200,
						'unit'  => '',
					),
					'margin_settings'         => array(
						'dimensions' => array(
							'top'    => 0,
							'right'  => 0,
							'bottom' => 0,
							'left'   => 0,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'padding_settings'        => array(
						'dimensions' => array(
							'top'    => 5,
							'right'  => 10,
							'bottom' => 5,
							'left'   => 10,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
				),
			),
			array(
				'name'    => esc_html__( 'Sample Button 3', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					'label_text'              => '<div style="text-align: center;"><strong><span style="font-family: inherit; font-size: 12px;">' . esc_html__( 'LOGIN TO SEE PRICE', 'yith-woocommerce-catalog-mode' ) . '</span></strong></div>',
					'default_colors'          => array(
						'text'       => '#247390',
						'background' => '#ffffff',
						'border'     => '#247390',
					),
					'hover_colors'            => array(
						'text'       => '#ffffff',
						'background' => '#247390',
						'border'     => '#247390',
					),
					'button_url'              => '',
					'border_thickness'        => array(
						'dimensions' => array(
							'top'    => 1,
							'right'  => 1,
							'bottom' => 1,
							'left'   => 1,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'border_radius'           => array(
						'dimensions' => array(
							'top'    => 50,
							'right'  => 50,
							'bottom' => 50,
							'left'   => 50,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => 150,
						'unit'  => '',
					),
					'margin_settings'         => array(
						'dimensions' => array(
							'top'    => 0,
							'right'  => 0,
							'bottom' => 0,
							'left'   => 0,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'padding_settings'        => array(
						'dimensions' => array(
							'top'    => 5,
							'right'  => 10,
							'bottom' => 5,
							'left'   => 10,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
				),
			),
			array(
				'name'    => esc_html__( 'Sample Label', 'yith-woocommerce-catalog-mode' ),
				'options' => array(
					/* translators: %s sample phone number */
					'label_text'              => '<div><span style="color: #9f4300; font-size: 16px;"><strong><span style="font-family: inherit;">' . esc_html__( 'Contact us to inquire about this product', 'yith-woocommerce-catalog-mode' ) . '</span></strong></span><br /><br /><span style="font-size: 14px;">' . sprintf( esc_html__( 'If you love this product and wish for a customized quote contact us at number %s and we will be happy to provide you with more info!', 'yith-woocommerce-catalog-mode' ), '<strong>+01234567890</strong>' ) . '</span></div>',
					'default_colors'          => array(
						'text'       => '#4b4b4b',
						'background' => '#f9f5f2',
						'border'     => '#e3bdaf',
					),
					'hover_colors'            => array(
						'text'       => '#4b4b4b',
						'background' => '#f9f5f2',
						'border'     => '#e3bdaf',
					),
					'button_url'              => '',
					'border_thickness'        => array(
						'dimensions' => array(
							'top'    => 1,
							'right'  => 1,
							'bottom' => 1,
							'left'   => 1,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'border_radius'           => array(
						'dimensions' => array(
							'top'    => 5,
							'right'  => 5,
							'bottom' => 5,
							'left'   => 5,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'icon_type'               => 'none',
					'selected_icon'           => '',
					'selected_icon_size'      => '',
					'selected_icon_alignment' => 'flex-start',
					'custom_icon'             => '',
					'width_settings'          => array(
						'width' => '',
						'unit'  => '',
					),
					'margin_settings'         => array(
						'dimensions' => array(
							'top'    => 0,
							'right'  => 0,
							'bottom' => 0,
							'left'   => 0,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
					'padding_settings'        => array(
						'dimensions' => array(
							'top'    => 5,
							'right'  => 10,
							'bottom' => 5,
							'left'   => 10,
						),
						'unit'       => 'px',
						'linked'     => 'no',
					),
				),
			),
		);

		foreach ( $sample_buttons as $sample_button ) {

			$button_data = array(
				'post_title'   => $sample_button['name'],
				'post_content' => '',
				'post_excerpt' => '',
				'post_status'  => 'publish',
				'post_author'  => 0,
				'post_type'    => 'ywctm-button-label',
			);
			$button_id   = wp_insert_post( $button_data );
			foreach ( $sample_button['options'] as $key => $value ) {
				update_post_meta( $button_id, 'ywctm_' . $key, $value );
			}
		}

		update_option( 'ywctm_update_version', YWCTM_VERSION );

	}

	add_action( 'admin_init', 'ywctm_create_sample_buttons' );
}
