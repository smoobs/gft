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

if ( ! class_exists( 'YWCTM_Vendors_Table' ) ) {

	/**
	 * Displays the exclusion table in YWCTM plugin admin tab
	 *
	 * @class   YWCTM_Vendors_Table
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Vendors_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {
			if ( ! isset( $_GET['sub_tab'] ) || ( isset( $_GET['sub_tab'] ) && 'exclusions-vendors' !== $_GET['sub_tab'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}
			add_action( 'init', array( $this, 'init' ), 15 );
		}

		/**
		 * Init page
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init() {
			add_action( 'ywctm_exclusions_vendors', array( $this, 'output' ) );
			add_filter( 'set-screen-option', array( $this, 'set_options' ), 10, 3 );
			add_action( 'current_screen', array( $this, 'add_options' ) );
		}

		/**
		 * Outputs the exclusion table template with insert form in plugin options panel
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function output() {

			global $wpdb;

			$table = new YITH_Custom_Table(
				array(
					'singular' => esc_html__( 'vendor', 'yith-woocommerce-catalog-mode' ),
					'plural'   => esc_html__( 'vendors', 'yith-woocommerce-catalog-mode' ),
					'id'       => 'vendor',
				)
			);

			$message     = array();
			$fields      = array();
			$object_name = '';
			$getted      = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), basename( __FILE__ ) ) ) {
				$posted = $_POST;

				if ( ! empty( $posted['item_ids'] ) ) {

					$exclusion_data = isset( $posted['_ywctm_vendor_override_exclusion'] ) ? 'yes' : 'no';

					$object_ids = ( ! is_array( $posted['item_ids'] ) ) ? explode( ',', $posted['item_ids'] ) : $posted['item_ids'];

					foreach ( $object_ids as $object_id ) {
						update_term_meta( $object_id, '_ywctm_vendor_override_exclusion', $exclusion_data );
					}

					if ( ! empty( $posted['insert'] ) ) {

						$singular = esc_html__( '1 vendor added successfully', 'yith-woocommerce-catalog-mode' );
						/* translators: %s number of vendors */
						$plural  = sprintf( esc_html__( '%s vendors added successfully', 'yith-woocommerce-catalog-mode' ), count( $object_ids ) );
						$message = array(
							'text' => count( $object_ids ) === 1 ? $singular : $plural,
							'type' => 'success',
						);

					} elseif ( ! empty( $posted['edit'] ) ) {

						$message = array(
							'text' => esc_html__( 'Vendor updated successfully', 'yith-woocommerce-catalog-mode' ),
							'type' => 'success',
						);

					}
				}
			}

			if ( ! empty( $getted['action'] ) && 'delete' !== $getted['action'] ) {

				$item = array(
					'ID'      => 0,
					'exclude' => 'yes',
				);

				if ( isset( $getted['id'] ) && ( 'edit' === $getted['action'] ) ) {

					$item        = array(
						'ID'      => $getted['id'],
						'exclude' => get_term_meta( $getted['id'], '_ywctm_vendor_override_exclusion', true ),
					);
					$vendor      = get_term( $getted['id'], 'yith_shop_vendor' );
					$object_name = $vendor->name;

				}

				$fields = $this->get_fields( $item, $object_name, $getted['action'] );

			}

			$table->options = array(
				'select_table'     => $wpdb->terms . ' a INNER JOIN ' . $wpdb->term_taxonomy . ' b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->termmeta . ' c ON c.term_id = a.term_id',
				'select_columns'   => array(
					'a.term_id AS ID',
					'a.name',
					'MAX( CASE WHEN c.meta_key = "_ywctm_vendor_override_exclusion" THEN c.meta_value ELSE NULL END ) AS exclude',
				),
				'select_where'     => 'b.taxonomy = "yith_shop_vendor" AND ( c.meta_key = "_ywctm_vendor_override_exclusion" )',
				'select_group'     => 'a.term_id',
				'select_order'     => 'a.name',
				'select_order_dir' => 'ASC',
				'per_page_option'  => 'vendors_per_page',
				'search_where'     => array(
					'a.name',
				),
				'count_table'      => $wpdb->terms . ' a INNER JOIN ' . $wpdb->term_taxonomy . ' b ON a.term_id = b.term_id INNER JOIN ' . $wpdb->termmeta . ' c ON c.term_id = a.term_id',
				'count_where'      => 'b.taxonomy = "yith_shop_vendor" AND c.meta_key = "_ywctm_vendor_override_exclusion"',
				'key_column'       => 'ID',
				'view_columns'     => array(
					'cb'      => '<input type="checkbox" />',
					'vendor'  => esc_html__( 'Vendor', 'yith-woocommerce-catalog-mode' ),
					'exclude' => esc_html__( 'Exclusion', 'yith-woocommerce-catalog-mode' ),
				),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'category' => array( 'name', true ),
				),
				'custom_columns'   => array(
					'column_vendor'  => function ( $item, $me ) {
						return ywctm_item_name_column( $item, $me );
					},
					'column_exclude' => function ( $item ) {
						ywctm_vendor_column( $item );
					},
				),
				'bulk_actions'     => array(
					'actions'   => array(
						'delete' => esc_html__( 'Remove from list', 'yith-woocommerce-catalog-mode' ),
					),
					'functions' => array(
						'function_delete' => function () {

							$getted = $_REQUEST; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

							if ( isset( $getted['nonce'] ) ) {
								return;
							}

							$ids = ! is_array( $getted['id'] ) ? sanitize_text_field( wp_unslash( $getted['id'] ) ) : $getted['id'];

							if ( ! is_array( $ids ) ) {
								$ids = explode( ',', $ids );
							}

							if ( ! empty( $ids ) ) {
								foreach ( $ids as $id ) {

									delete_term_meta( $id, '_ywctm_vendor_override_exclusion' );
								}
							}

						},
					),
				),
				'wp_cache_option'  => 'ywctm_vendors',
			);
			$table->prepare_items();

			if ( 'delete' === $table->current_action() ) {

				$ids      = $getted['id'];
				$deleted  = count( is_array( $ids ) ? $ids : explode( ',', $ids ) );
				$singular = esc_html__( '1 vendor removed successfully', 'yith-woocommerce-catalog-mode' );
				/* translators: %s number of vendors*/
				$plural  = sprintf( esc_html__( '%s vendors removed successfully', 'yith-woocommerce-catalog-mode' ), $deleted );
				$message = array(
					'text' => 1 === $deleted ? $singular : $plural,
					'type' => 'success',
				);

			}

			$this->print_template( $table, $fields, $message );

		}

		/**
		 * Print table template
		 *
		 * @param YITH_Custom_Table $table   The table object.
		 * @param array             $fields  Fields array.
		 * @param array             $message Messages.
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function print_template( $table, $fields, $message ) {

			$getted          = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$list_query_args = array(
				'page'    => $getted['page'],
				'tab'     => $getted['tab'],
				'sub_tab' => $getted['sub_tab'],
			);

			$list_url = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );

			?>
			<div class="yith-plugin-fw-wp-page-wrapper ywctm-exclusions">
				<div class="wrap">
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Vendor Exclusion list', 'yith-woocommerce-catalog-mode' ); ?></h1>
					<?php if ( empty( $getted['action'] ) || ( 'insert' !== $getted['action'] && 'edit' !== $getted['action'] ) ) : ?>
						<?php
						$query_args   = array_merge( $list_query_args, array( 'action' => 'insert' ) );
						$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
						<a class="page-title-action yith-add-button" href="<?php echo esc_attr( $add_form_url ); ?>"><?php echo esc_html__( 'Add vendor', 'yith-woocommerce-catalog-mode' ); ?></a>
					<?php endif; ?>
					<hr class="wp-header-end" />
					<?php if ( $message ) : ?>
						<div class="notice notice-<?php echo esc_attr( $message['type'] ); ?> is-dismissible"><p><?php echo esc_attr( $message['text'] ); ?></p></div>
					<?php endif; ?>
					<?php
					if ( ! empty( $getted['action'] ) && ( 'insert' === $getted['action'] || 'edit' === $getted['action'] ) ) :
						$query_args  = array_merge( $list_query_args, array() );
						$form_action = add_query_arg( $query_args, admin_url( 'admin.php' ) );
						$this->get_form( $fields, $getted, $form_action );
						?>

					<?php else : ?>

						<form id="custom-table" method="GET" action="<?php echo esc_attr( $list_url ); ?>">
							<?php $table->search_box( esc_html__( 'Search vendor', 'yith-woocommerce-catalog-mode' ), 'vendor' ); ?>
							<input type="hidden" name="page" value="<?php echo esc_attr( $getted['page'] ); ?>" />
							<input type="hidden" name="tab" value="<?php echo esc_attr( $getted['tab'] ); ?>" />
							<input type="hidden" name="sub_tab" value="<?php echo esc_attr( $getted['sub_tab'] ); ?>" />
							<?php $table->display(); ?>
						</form>

						<div class="ywctm-exclusion-list-popup-wrapper vendor-exclusion">
							<?php
							$item   = array(
								'ID'      => 0,
								'exclude' => 'yes',
							);
							$fields = $this->get_fields( $item, '', 'insert' );

							$this->get_form( $fields );
							?>
						</div>

					<?php endif; ?>
					<div class="clear"></div>
				</div>

			</div>
			<?php

		}

		/**
		 * Get field option for current screen
		 *
		 * @param array  $fields Fields array.
		 * @param array  $getted Values array.
		 * @param string $action Action url.
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_form( $fields, $getted = array(), $action = false ) {
			?>
			<form id="form" method="POST" action="<?php echo esc_url( $action ); ?>">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( basename( __FILE__ ) ) ); ?>" />
				<table class="form-table">
					<tbody>
					<?php foreach ( $fields as $field ) : ?>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $field['name'] ); ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
								<?php yith_plugin_fw_get_field( $field, true ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php if ( $action ) : ?>
					<input id="<?php echo esc_attr( $getted['action'] ); ?>" name="<?php echo esc_attr( $getted['action'] ); ?>" type="submit" class="<?php echo esc_attr( 'insert' === $getted['action'] ? 'yith-save-button' : 'yith-update-button' ); ?>" value="<?php echo( ( 'insert' === $getted['action'] ) ? esc_html__( 'Add vendor', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Update vendor', 'yith-woocommerce-catalog-mode' ) ); ?>" />
				<?php endif; ?>
			</form>
			<?php
		}

		/**
		 * Get field option for current screen
		 *
		 * @param array  $item   Data Array.
		 * @param string $name   Option Name.
		 * @param string $action Action name.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_fields( $item, $name, $action ) {

			$fields = array(
				0 => array(
					'id'       => 'item_ids',
					'name'     => 'item_ids',
					'type'     => 'ajax-terms',
					'multiple' => true,
					'data'     => array(
						'placeholder' => esc_html__( 'Search vendors', 'yith-woocommerce-catalog-mode' ),
						'taxonomy'    => 'yith_shop_vendor',
					),
					'title'    => esc_html__( 'Select vendors', 'yith-woocommerce-catalog-mode' ),
				),
				1 => array(
					'id'    => '_ywctm_vendor_override_exclusion',
					'name'  => '_ywctm_vendor_override_exclusion',
					'type'  => 'onoff',
					'title' => esc_html__( 'Enable Exclusion', 'yith-woocommerce-catalog-mode' ),
					'value' => $item['exclude'],
				),
			);

			if ( 'edit' === $action ) {
				$fields[0] = array(
					'id'                => 'item_ids',
					'name'              => 'item_ids',
					'type'              => 'text',
					'custom_attributes' => array(
						'disabled' => 'disabled',
					),
					'value'             => $name,
					'title'             => esc_html__( 'Vendor to edit', 'yith-woocommerce-catalog-mode' ),
				);
				$fields[8] = array(
					'id'    => 'item_id',
					'name'  => 'item_ids',
					'type'  => 'hidden',
					'value' => $item['ID'],
					'title' => '',
				);
			};

			ksort( $fields );

			return $fields;

		}

		/**
		 * Add screen options for exclusions list table template
		 *
		 * @param WP_Screen $current_screen The current screen.
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_options( $current_screen ) {

			if ( ( 'yith-plugins_page_yith_wc_catalog_mode_panel' === $current_screen->id ) && ( isset( $_GET['tab'] ) && 'exclusions' === $_GET['tab'] ) && ( ! isset( $_GET['action'] ) || ( 'edit' !== $_GET['action'] && 'insert' !== $_GET['action'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

				$option = 'per_page';
				$args   = array(
					'label'   => esc_html__( 'Vendors', 'yith-woocommerce-catalog-mode' ),
					'default' => 10,
					'option'  => 'vendors_per_page',
				);

				add_screen_option( $option, $args );

			}

		}

		/**
		 * Set screen options for exclusions list table template
		 *
		 * @param string $status Screen status.
		 * @param string $option Option name.
		 * @param string $value  Option value.
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_options( $status, $option, $value ) {

			return ( 'vendors_per_page' === $option ) ? $value : $status;

		}

	}

	new YWCTM_Vendors_Table();
}
