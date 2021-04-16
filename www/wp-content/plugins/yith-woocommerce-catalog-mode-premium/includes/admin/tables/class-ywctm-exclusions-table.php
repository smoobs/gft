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

if ( ! class_exists( 'YWCTM_Exclusions_Table' ) ) {

	/**
	 * Displays the exclusion table in YWCTM plugin admin tab
	 *
	 * @class   YWCTM_Exclusions_Table
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Exclusions_Table {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {
			if ( ! isset( $_GET['sub_tab'] ) || ( isset( $_GET['sub_tab'] ) && 'exclusions-vendors' === $_GET['sub_tab'] ) ) {  //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}
			add_action( 'init', array( $this, 'init' ), 25 );
		}

		/**
		 * Init page
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function init() {

			add_action( 'ywctm_exclusions_items', array( $this, 'output' ) );
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
					'singular' => esc_html__( 'item', 'yith-woocommerce-catalog-mode' ),
					'plural'   => esc_html__( 'items', 'yith-woocommerce-catalog-mode' ),
				)
			);

			$message     = array();
			$fields      = array();
			$object_name = '';
			$getted      = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), basename( __FILE__ ) ) ) {
				$posted = $_POST;

				$item_type = $posted['item_type'];
				$item_ids  = $posted[ $item_type . '_ids' ];

				if ( ! empty( $item_ids ) ) {

					$exclusion_data = array(
						'enable_inquiry_form'         => isset( $posted['ywctm_enable_inquiry_form'] ) ? 'yes' : 'no',
						'enable_atc_custom_options'   => isset( $posted['ywctm_enable_atc_custom_options'] ) ? 'yes' : 'no',
						'atc_status'                  => $posted['ywctm_atc_status'],
						'custom_button'               => $posted['ywctm_custom_button'],
						'custom_button_url'           => $posted['ywctm_custom_button_url'],
						'custom_button_loop'          => $posted['ywctm_custom_button_loop'],
						'custom_button_loop_url'      => $posted['ywctm_custom_button_loop_url'],
						'enable_price_custom_options' => isset( $posted['ywctm_enable_price_custom_options'] ) ? 'yes' : 'no',
						'price_status'                => $posted['ywctm_price_status'],
						'custom_price_text'           => $posted['ywctm_custom_price_text'],
						'custom_price_text_url'       => $posted['ywctm_custom_price_text_url'],
					);

					$object_ids = ( ! is_array( $item_ids ) ) ? explode( ',', $item_ids ) : $item_ids;

					foreach ( $object_ids as $object_id ) {

						if ( 'product' === $item_type ) {
							$product = wc_get_product( $object_id );
							$product->add_meta_data( '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), $exclusion_data, true );
							$product->save();
						} else {
							update_term_meta( $object_id, '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), $exclusion_data );
						}
					}

					if ( ! empty( $_POST['insert'] ) ) {

						$singular = esc_html__( '1 exclusion added successfully', 'yith-woocommerce-catalog-mode' );
						/* translators: %s: number of excluisions added */
						$plural  = sprintf( esc_html__( '%s exclusions added successfully', 'yith-woocommerce-catalog-mode' ), count( $object_ids ) );
						$message = array(
							'text' => count( $object_ids ) === 1 ? $singular : $plural,
							'type' => 'success',
						);

					} elseif ( ! empty( $_POST['edit'] ) ) {

						$message = array(
							'text' => esc_html__( 'Exclusion updated successfully', 'yith-woocommerce-catalog-mode' ),
							'type' => 'success',
						);

					}
				}
			}

			if ( ! empty( $getted['action'] ) && 'delete' !== $getted['action'] ) {

				$item = $this->get_default_values();

				if ( isset( $getted['id'] ) && ( 'edit' === $getted['action'] ) ) {

					switch ( $getted['item_type'] ) {
						case 'category':
							$exclusion_data = get_term_meta( $getted['id'], '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), true );
							$item           = array_merge( array( 'ID' => $getted['id'] ), $exclusion_data );
							$category       = get_term( $getted['id'], 'product_cat' );
							$object_name    = $category->name;
							break;
						case 'tag':
							$exclusion_data = get_term_meta( $getted['id'], '_ywctm_exclusion_settings' . ywctm_get_vendor_id(), true );
							$item           = array_merge( array( 'ID' => $getted['id'] ), $exclusion_data );
							$tag            = get_term( $getted['id'], 'product_tag' );
							$object_name    = $tag->name;
							break;
						default:
							$product        = wc_get_product( $getted['id'] );
							$exclusion_data = $product->get_meta( '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
							$item           = array_merge( array( 'ID' => $getted['id'] ), $exclusion_data );
							$object_name    = $product->get_formatted_name();
					}
				}

				$fields = $this->get_fields( isset( $getted['item_type'] ) ? $getted['item_type'] : '', $item, $object_name, $getted['action'] );

			}

			$table->options = array(
				'select_table'     => "(
				                        (SELECT a.ID, a.post_title AS name, MAX(CASE WHEN b.meta_key = '_ywctm_exclusion_settings' THEN b.meta_value ELSE NULL END) AS exclusion, 'product' AS item_type
										FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
										WHERE a.post_type = 'product' AND b.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.ID)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'category' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_cat' AND c.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.term_id)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'tag' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_tag' AND c.meta_key = '_ywctm_exclusion_settings' 
										GROUP BY a.term_id)
										) AS items",
				'select_columns'   => array(
					'ID',
					'name',
					'exclusion',
					'item_type',
					'concat(ID, "-", item_type) AS idtype',
				),
				'select_where'     => isset( $_REQUEST['type'] ) && '' !== $_REQUEST['type'] ? "item_type = '" . sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) . " '" : '',
				'select_group'     => '',
				'select_order'     => 'name',
				'select_order_dir' => 'ASC',
				'per_page_option'  => 'items_per_page',
				'search_where'     => array(
					'name',
				),
				'count_table'      => "(
				                        (SELECT a.ID, a.post_title AS name, MAX(CASE WHEN b.meta_key = '_ywctm_exclusion_settings' THEN b.meta_value ELSE NULL END) AS exclusion, 'product' AS item_type
										FROM $wpdb->posts a INNER JOIN $wpdb->postmeta b ON a.ID = b.post_id
										WHERE a.post_type = 'product' AND b.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.ID)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'category' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_cat' AND c.meta_key = '_ywctm_exclusion_settings'
										GROUP BY a.term_id)
										UNION
										(SELECT a.term_id AS ID, a.name, MAX(CASE WHEN c.meta_key = '_ywctm_exclusion_settings' THEN c.meta_value ELSE NULL END) AS exclusion, 'tag' AS item_type
										FROM $wpdb->terms a INNER JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id INNER JOIN $wpdb->termmeta c ON c.term_id = a.term_id
										WHERE b.taxonomy = 'product_tag' AND c.meta_key = '_ywctm_exclusion_settings' 
										GROUP BY a.term_id)
										) AS items",
				'count_where'      => isset( $_REQUEST['type'] ) && '' !== $_REQUEST['type'] ? "item_type = '" . sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) . " '" : '',
				'key_column'       => 'idtype',
				'view_columns'     => ywctm_set_table_columns(),
				'hidden_columns'   => array(),
				'sortable_columns' => array(
					'item_name' => array( 'name', true ),
				),
				'custom_columns'   => array(

					'column_item_name'    => function ( $item, $me ) {
						return ywctm_item_name_column( $item, $me );
					},
					'column_item_type'    => function ( $item ) {
						return ywctm_item_type_column( $item['item_type'] );
					},
					'column_add_to_cart'  => function ( $item ) {
						return ywctm_add_to_cart_column( $item );
					},
					'column_show_price'   => function ( $item ) {
						return ywctm_price_column( $item );
					},
					'column_inquiry_form' => function ( $item ) {
						ywctm_inquiry_form_column( $item );
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

									$data = explode( '-', $id );

									if ( ( isset( $data[1] ) && 'product' === $data[1] ) || ( isset( $_GET['item_type'] ) && 'product' === $_GET['item_type'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

										$product = wc_get_product( $data[0] );
										$product->delete_meta_data( '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
										$product->save();
									} else {
										delete_term_meta( $data[0], '_ywctm_exclusion_settings' . ywctm_get_vendor_id() );
									}
								}
							}
						},
					),
				),
				'wp_cache_option'  => 'ywctm_exclusions',
				'extra_tablenav'   => function () {
					$current_type = isset( $_REQUEST['type'] ) && ! empty( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$options      = array(
						''         => esc_html__( 'All types', 'yith-woocommerce-catalog-mode' ),
						'product'  => esc_html__( 'Products', 'yith-woocommerce-catalog-mode' ),
						'category' => esc_html__( 'Categories', 'yith-woocommerce-catalog-mode' ),
						'tag'      => esc_html__( 'Tags', 'yith-woocommerce-catalog-mode' ),
					);
					?>
					<div class="alignleft actions">
						<select name="type" id="type">
							<?php foreach ( $options as $key => $option ) : ?>
								<option
									value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_type, $key ); ?>><?php echo esc_html( $option ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php
					submit_button(
						esc_html_x( 'Filter', 'Label for filter button in exclusion list', 'yith-woocommerce-catalog-mode' ),
						'button',
						false,
						false,
						array(
							'id' => 'ywctm-filter-submit',
						)
					);
				},
			);
			$table->prepare_items();

			if ( 'delete' === $table->current_action() ) {

				$ids      = $getted['id'];
				$deleted  = count( is_array( $ids ) ? $ids : explode( ',', $ids ) );
				$singular = esc_html__( '1 exclusion removed successfully', 'yith-woocommerce-catalog-mode' );
				/* translators: number of excluisions deleted */
				$plural  = sprintf( esc_html__( '%s exclusions removed successfully', 'yith-woocommerce-catalog-mode' ), $deleted );
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

			$list_url     = esc_url( add_query_arg( $list_query_args, admin_url( 'admin.php' ) ) );
			$form_enabled = get_option( 'ywctm_inquiry_form_enabled' . ywctm_get_vendor_id(), 'hidden' );

			?>
			<div class="yith-plugin-fw-wp-page-wrapper ywctm-exclusions">
				<div class="wrap">
					<h1 class="wp-heading-inline"><?php esc_html_e( 'Exclusion list', 'yith-woocommerce-catalog-mode' ); ?></h1>
					<?php if ( empty( $getted['action'] ) || ( 'insert' !== $getted['action'] && 'edit' !== $getted['action'] ) ) : ?>
						<?php
						$query_args   = array_merge( $list_query_args, array( 'action' => 'insert' ) );
						$add_form_url = esc_url( add_query_arg( $query_args, admin_url( 'admin.php' ) ) );
						?>
						<a class="page-title-action yith-add-button" href="<?php echo esc_attr( $add_form_url ); ?>"><?php esc_html_e( 'Add exclusion', 'yith-woocommerce-catalog-mode' ); ?></a>
					<?php endif; ?>
					<hr class="wp-header-end" />
					<?php if ( $message ) : ?>
						<div class="notice notice-<?php echo esc_attr( $message['type'] ); ?> is-dismissible"><p><?php echo esc_attr( $message['text'] ); ?></p></div>
					<?php endif; ?>
					<?php if ( ! empty( $getted['action'] ) && ( 'insert' === $getted['action'] || 'edit' === $getted['action'] ) ) : ?>

						<?php
						$query_args = array_merge( $list_query_args, array() );

						if ( isset( $getted['return_page'] ) ) {
							$query_args['paged'] = $getted['return_page'];
						}

						$form_action = add_query_arg( $query_args, admin_url( 'admin.php' ) );
						$this->get_form( $fields, $form_enabled, $getted, $form_action );
						?>
					<?php else : ?>

						<form id="custom-table" method="GET" action="<?php esc_attr( $list_url ); ?>">
							<?php $table->search_box( esc_html__( 'Search item', 'yith-woocommerce-catalog-mode' ), 'item' ); ?>
							<input type="hidden" name="page" value="<?php echo esc_attr( $getted['page'] ); ?>" />
							<input type="hidden" name="tab" value="<?php echo esc_attr( $getted['tab'] ); ?>" />
							<input type="hidden" name="sub_tab" value="<?php echo esc_attr( $getted['sub_tab'] ); ?>" />
							<?php $table->display(); ?>
						</form>

						<div class="ywctm-exclusion-list-popup-wrapper">
							<?php
							$fields = $this->get_fields( '', $this->get_default_values(), '', 'insert' );

							$this->get_form( $fields, $form_enabled );
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
		 * @param array  $fields       Fields array.
		 * @param string $form_enabled Check if inquiry formis enabled.
		 * @param array  $getted       Values array.
		 * @param string $action       Action url.
		 *
		 * @return  void
		 * @since   2.1.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_form( $fields, $form_enabled, $getted = array(), $action = false ) {
			?>
			<form id="form" method="POST" action="<?php echo esc_url( $action ); ?>">
				<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( basename( __FILE__ ) ) ); ?>" />
				<table class="form-table <?php echo( 'hidden' !== $form_enabled && ywctm_exists_inquiry_forms() ? '' : 'no-active-form' ); ?>">
					<tbody>
					<?php foreach ( $fields as $field ) : ?>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $field['name'] ); ?>">
							<th scope="row" class="titledesc">
								<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label>
							</th>
							<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
								<?php yith_plugin_fw_get_field( $field, true ); ?>
								<?php if ( isset( $field['desc'] ) && '' !== $field['desc'] ) : ?>
									<span class="description"><?php echo esc_attr( $field['desc'] ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<?php if ( $action ) : ?>
					<input id="<?php echo esc_attr( $getted['action'] ); ?>" name="<?php echo esc_attr( $getted['action'] ); ?>" type="submit" class="<?php echo esc_attr( 'insert' === $getted['action'] ? 'yith-save-button' : 'yith-update-button' ); ?>" value="<?php echo( ( 'insert' === $getted['action'] ) ? esc_html__( 'Add exclusion', 'yith-woocommerce-catalog-mode' ) : esc_html__( 'Update exclusion', 'yith-woocommerce-catalog-mode' ) ); ?>" />
				<?php endif; ?>
			</form>
			<?php
		}

		/**
		 * Get field option for current screen
		 *
		 * @param string $type   Item type.
		 * @param array  $item   Data Array.
		 * @param string $name   Option Name.
		 * @param string $action Action name.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_fields( $type, $item, $name, $action ) {

			if ( 'edit' === $action ) {

				$item_type = ywctm_item_type_column( $type );
				$fields    = array(
					array(
						'id'                => 'item_ids',
						'name'              => 'item_ids',
						'type'              => 'text',
						'custom_attributes' => array(
							'disabled' => 'disabled',
						),
						'value'             => $name,
						/* translators: %s item type */
						'title'             => sprintf( esc_html__( '%s to edit', 'yith-woocommerce-catalog-mode' ), $item_type ),
					),
					array(
						'id'    => 'item_id',
						'name'  => $type . '_ids',
						'type'  => 'hidden',
						'value' => $item['ID'],
						'title' => '',
					),
					array(
						'id'    => 'item_type',
						'name'  => 'item_type',
						'type'  => 'hidden',
						'value' => $type,
						'title' => '',
					),
				);
			} else {
				$fields = array(
					array(
						'id'      => 'item_type',
						'name'    => 'item_type',
						'type'    => 'select',
						'class'   => 'wc-enhanced-select',
						'options' => array(
							'product'  => esc_html__( 'Products', 'yith-woocommerce-catalog-mode' ),
							'category' => esc_html__( 'Categories', 'yith-woocommerce-catalog-mode' ),
							'tag'      => esc_html__( 'Tags', 'yith-woocommerce-catalog-mode' ),
						),
						'title'   => esc_html__( 'Item type', 'yith-woocommerce-catalog-mode' ),
						'value'   => '',
						'desc'    => esc_html__( 'Choose whether to add specific products, categories or tags to the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
					array(
						'id'       => 'product_ids',
						'name'     => 'product_ids',
						'type'     => 'ajax-products',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search products', 'yith-woocommerce-catalog-mode' ),
						),
						'title'    => esc_html__( 'Select products', 'yith-woocommerce-catalog-mode' ),
						'desc'     => esc_html__( 'Select which products to add to the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
					array(
						'id'       => 'category_ids',
						'name'     => 'category_ids',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search categories', 'yith-woocommerce-catalog-mode' ),
							'taxonomy'    => 'product_cat',
						),
						'title'    => esc_html__( 'Select categories', 'yith-woocommerce-catalog-mode' ),
						'desc'     => esc_html__( 'Select which product categories to add in the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
					array(
						'id'       => 'tag_ids',
						'name'     => 'tag_ids',
						'type'     => 'ajax-terms',
						'multiple' => true,
						'data'     => array(
							'placeholder' => esc_html__( 'Search tags', 'yith-woocommerce-catalog-mode' ),
							'taxonomy'    => 'product_tag',
						),
						'title'    => esc_html__( 'Select tags', 'yith-woocommerce-catalog-mode' ),
						'desc'     => esc_html__( 'Select which product tags to add in the exclusion list.', 'yith-woocommerce-catalog-mode' ),
					),
				);
			}

			return array_merge( $fields, ywctm_get_exclusion_fields( $item ) );

		}

		/**
		 * Get default values
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		private function get_default_values() {

			$atc_global         = get_option( 'ywctm_hide_add_to_cart_settings' . ywctm_get_vendor_id() );
			$button_global      = get_option( 'ywctm_custom_button_settings' . ywctm_get_vendor_id() );
			$button_loop_global = get_option( 'ywctm_custom_button_settings_loop' . ywctm_get_vendor_id() );
			$price_global       = get_option( 'ywctm_hide_price_settings' . ywctm_get_vendor_id() );
			$label_global       = get_option( 'ywctm_custom_price_text_settings' . ywctm_get_vendor_id() );

			return array(
				'ID'                          => 0,
				'enable_inquiry_form'         => 'yes',
				'enable_atc_custom_options'   => 'no',
				'atc_status'                  => $atc_global['action'],
				'custom_button'               => $button_global,
				'custom_button_loop'          => $button_loop_global,
				'enable_price_custom_options' => 'no',
				'price_status'                => $price_global['action'],
				'custom_price_text'           => $label_global,
			);

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

			if ( ( 'yith-plugins_page_yith_wc_catalog_mode_panel' === $current_screen->id || 'toplevel_page_yith_vendor_ctm_settings' === $current_screen->id ) && ( isset( $_GET['tab'] ) && 'exclusions' === $_GET['tab'] ) && ( ! isset( $_GET['action'] ) || ( 'edit' !== $_GET['action'] && 'insert' !== $_GET['action'] ) ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended

				$option = 'per_page';
				$args   = array(
					'label'   => esc_html__( 'Exclusions', 'yith-woocommerce-catalog-mode' ),
					'default' => 10,
					'option'  => 'items_per_page',
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

			return ( 'items_per_page' === $option ) ? $value : $status;

		}

	}

	new YWCTM_Exclusions_Table();
}
