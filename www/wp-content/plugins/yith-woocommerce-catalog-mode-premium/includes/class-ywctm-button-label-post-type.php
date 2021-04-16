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

if ( ! class_exists( 'YWCTM_Button_Label_Post_Type' ) ) {

	/**
	 * Button and label post type class
	 *
	 * @class   YWCTM_Button_Label_Post_Type
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Button_Label_Post_Type {

		/**
		 *  Post type name
		 *
		 * @var string
		 */
		protected $post_type = 'ywctm-button-label';

		/**
		 * Metabox is saved
		 *
		 * @var boolean
		 */
		private $saved_meta_box = false;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'remove_metaboxes' ) );
			add_action( 'admin_init', array( $this, 'add_capabilities' ) );
			add_action( 'admin_init', array( $this, 'duplicate_button' ), 30 );
			add_action( 'init', array( $this, 'add_post_type' ), 5 );
			add_action( 'save_post', array( $this, 'save_post' ), 1, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
			add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'set_custom_columns' ) );
			add_filter( "views_edit-$this->post_type", array( $this, 'set_views' ), 10, 1 );
			add_filter( "bulk_actions-edit-$this->post_type", array( $this, 'set_bulk_actions' ), 10 );
			add_filter( 'post_row_actions', array( $this, 'customize_row_actions' ), 10, 2 );
			add_filter( 'enter_title_here', array( $this, 'customize_title_placeholder' ), 10, 2 );
			add_filter( 'mce_buttons', array( $this, 'edit_mce_buttons' ), 15, 2 );
			add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 15, 2 );
			add_filter( 'mce_external_plugins', array( &$this, 'add_shortcodes_tinymce_plugin' ) );
			add_filter( 'yith_plugin_fw_icons_field_icons_' . YWCTM_SLUG, array( $this, 'add_retinaicons' ) );
			add_filter( 'ywctm_button_editor_fonts', array( $this, 'add_default_google_fonts' ) );
			add_filter( 'get_user_option_screen_layout_ywctm-button-label', '__return_true' );
			add_filter( 'wpml_post_edit_meta_box_context', array( $this, 'move_wpml_metabox' ) );
			add_action( 'edit_form_after_title', array( $this, 'option_metabox' ) );
		}

		/**
		 * Add a back button at the top of the page
		 *
		 * @param WP_Post $post The Post Object.
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_back_button( $post ) {

			$getted = $_GET; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ( isset( $getted['post_type'] ) && $this->post_type === $getted['post_type'] ) || ( $post && $post->post_type === $this->post_type ) ) {
				printf( '<a href="%1$s" class="ywctm_back_button" title="%2$s">%2$s <span style="font-size: 14px; font-weight: 600">&#x2934;</span></a>', esc_url( esc_url( add_query_arg( array( 'post_type' => $this->post_type ), admin_url( 'edit.php' ) ) ) ), esc_html__( 'Return to buttons list', 'yith-woocommerce-catalog-mode' ) );
			}
		}

		/**
		 * Move WPML metabox
		 *
		 * @param string $position Metabox position.
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function move_wpml_metabox( $position ) {

			global $post;

			if ( $post->post_type === $this->post_type ) {
				$position = 'normal';
			}

			return $position;
		}

		/**
		 * Add Button & Label post type
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_post_type() {

			$labels = array(
				'name'               => esc_html_x( 'Buttons & Labels', 'Post Type General Name', 'yith-woocommerce-catalog-mode' ),
				'singular_name'      => esc_html_x( 'Buttons & Labels', 'Post Type Singular Name', 'yith-woocommerce-catalog-mode' ),
				'add_new_item'       => esc_html__( 'Add button or label', 'yith-woocommerce-catalog-mode' ),
				'add_new'            => esc_html__( 'Create a new button or label', 'yith-woocommerce-catalog-mode' ),
				'new_item'           => esc_html__( 'New button or label', 'yith-woocommerce-catalog-mode' ),
				'edit_item'          => esc_html__( 'Edit button or label', 'yith-woocommerce-catalog-mode' ),
				'view_item'          => esc_html__( 'View button or label', 'yith-woocommerce-catalog-mode' ),
				'search_items'       => esc_html__( 'Search button or label', 'yith-woocommerce-catalog-mode' ),
				'not_found'          => esc_html__( 'Not found', 'yith-woocommerce-catalog-mode' ),
				'not_found_in_trash' => esc_html__( 'Not found in Trash', 'yith-woocommerce-catalog-mode' ),
			);

			$args = array(
				'labels'              => $labels,
				'supports'            => false,
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'menu_position'       => 10,
				'show_in_nav_menus'   => false,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-awards',
				'capability_type'     => 'ywctm-button-label',
				'capabilities'        => $this->get_capabilities(),
				'map_meta_cap'        => true,
				'rewrite'             => false,
				'publicly_queryable'  => false,
				'query_var'           => false,
			);

			register_post_type( $this->post_type, $args );

		}

		/**
		 * Add management capabilities to Admin and Shop Manager
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_capabilities() {

			$caps  = $this->get_capabilities();
			$roles = array(
				'administrator',
				'shop_manager',
			);

			if ( ywctm_is_multivendor_active() ) {
				$roles[] = 'yith_vendor';
			}

			foreach ( $roles as $role_slug ) {

				$role = get_role( $role_slug );

				if ( ! $role ) {
					continue;
				}

				foreach ( $caps as $key => $cap ) {
					$role->add_cap( $cap );
				}
			}

		}

		/**
		 * Get capabilities for custom post type
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_capabilities() {

			$capability_type = 'ywctm-button-label';

			return array(
				'edit_post'              => "edit_$capability_type",
				'read_post'              => "read_$capability_type",
				'delete_post'            => "delete_$capability_type",
				'edit_posts'             => "edit_{$capability_type}s",
				'edit_others_posts'      => "edit_others_{$capability_type}s",
				'publish_posts'          => "publish_{$capability_type}s",
				'read_private_posts'     => "read_private_{$capability_type}s",
				'delete_posts'           => "delete_{$capability_type}s",
				'delete_private_posts'   => "delete_private_{$capability_type}s",
				'delete_published_posts' => "delete_published_{$capability_type}s",
				'delete_others_posts'    => "delete_others_{$capability_type}s",
				'edit_private_posts'     => "edit_private_{$capability_type}s",
				'edit_published_posts'   => "edit_published_{$capability_type}s",
				'create_posts'           => "edit_{$capability_type}s",
				'manage_posts'           => "manage_{$capability_type}s",
			);

		}

		/**
		 * Set custom columns
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_custom_columns() {

			$columns = array(
				'cb'    => '<input type="checkbox" />',
				'title' => esc_html__( 'Title', 'yith-woocommerce-catalog-mode' ),
			);

			return $columns;

		}

		/**
		 * Add custom post type screen to WooCommerce list
		 *
		 * @param array $screen_ids Array of Screen IDs.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = 'edit-' . $this->post_type;
			$screen_ids[] = $this->post_type;

			return $screen_ids;

		}

		/**
		 * Filters views in custom post type
		 *
		 * @param array $views Views list.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_views( $views ) {

			if ( isset( $views['mine'] ) ) {
				unset( $views['mine'] );
			}

			return $views;

		}

		/**
		 * Filters bulk actions in custom post type
		 *
		 * @param array $actions Bulk actions array.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_bulk_actions( $actions ) {

			if ( isset( $actions['edit'] ) ) {
				unset( $actions['edit'] );
			}

			return $actions;
		}

		/**
		 * Customize row actions
		 *
		 * @param array   $actions Row actions.
		 * @param WP_Post $post    The Post object.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function customize_row_actions( $actions, $post ) {
			if ( $this->post_type === $post->post_type ) {

				if ( isset( $actions['edit'] ) && $actions['trash'] ) {

					$duplicate_args = array(
						'post_type' => $this->post_type,
						'action'    => 'duplicate_button',
						'post'      => $post->ID,
					);
					$duplicate_url  = wp_nonce_url( add_query_arg( $duplicate_args, admin_url( 'post.php' ) ), 'ywctm-duplicate-button_' . $post->ID );
					$duplicate_link = sprintf( '<a href="%s">%s</a>', $duplicate_url, esc_html__( 'Duplicate', 'yith-woocommerce-catalog-mode' ) );
					$edit_link      = $actions['edit'];
					$trash_link     = $actions['trash'];

					$actions = array(
						'edit'      => $edit_link,
						'duplicate' => $duplicate_link,
						'trash'     => $trash_link,
					);
				}
			}

			return $actions;
		}

		/**
		 * Customize title placeholder
		 *
		 * @param string  $value Option value.
		 * @param WP_Post $post  The Post object.
		 *
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function customize_title_placeholder( $value, $post ) {

			if ( $this->post_type === $post->post_type ) {
				$value = esc_html__( 'Add name', 'yith-woocommerce-catalog-mode' );
			}

			return $value;
		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts() {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( ! $screen || $screen->post_type !== $this->post_type ) {
				return;
			}

			$google_fonts = ywctm_enabled_google_fonts();
			$theme_font   = ywctm_get_theme_font();
			$font_names   = array();
			foreach ( $google_fonts as $name => $font ) {
				$font_names[] = str_replace( ' ', '+', $name ) . ':400,400i,700,700i';
			}

			// Add styles to the editor.
			add_editor_style( 'https://fonts.googleapis.com/css?family=' . implode( '|', $font_names ) . '&display=swap' );

			// Add page styles.
			wp_enqueue_media();
			wp_enqueue_style( 'yith-plugin-fw-fields' );
			wp_enqueue_style( 'ywctm-admin' );
			wp_enqueue_style( 'ywctm-google-fonts', 'https://fonts.googleapis.com/css?family=' . implode( '|', $font_names ) . '&display=swap', array(), YWCTM_VERSION );
			wp_enqueue_style( 'ywctm-admin-premium' );
			wp_enqueue_style( 'ywctm-retinaicon-font', yit_load_css_file( YWCTM_ASSETS_URL . 'css/retinaicon-font.css' ), array(), YWCTM_VERSION );
			wp_enqueue_style( 'ywctm-button-label', yit_load_css_file( YWCTM_ASSETS_URL . 'css/button-label.css' ), array(), YWCTM_VERSION );
			wp_enqueue_script( 'ywctm-button-label', yit_load_js_file( YWCTM_ASSETS_URL . 'js/button-label-admin.js' ), array( 'jquery', 'yith-plugin-fw-fields' ), YWCTM_VERSION, true );

			$css = '
			.ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-top:after,
			.ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-left:after {
				background-color:var(--hover-bg-color);
			}
			
			.ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-top:before,
			.ywctm-custom-button.ywctm-hover-effect.ywctm-effect-slide-left:before {
				background-color:var(--bg-color);
			}
				
			.ywctm-custom-button.ywctm-hover-effect.ywctm-effect-move-hover-color:before {
				background-color:var(--hover-bg-color);
				border-radius:var(--border-radius);
			}
			';

			wp_add_inline_style( 'ywctm-button-label', $css );

			wp_localize_script(
				'ywctm-button-label',
				'ywctm_btns',
				array(
					'list'         => YIT_Icons()->get_icons( YWCTM_SLUG ),
					'dialog_title' => esc_html__( 'Choose an icon', 'yith-woocommerce-catalog-mode' ),
					'close_label'  => esc_html__( 'Close', 'yith-woocommerce-catalog-mode' ),
					'button_title' => esc_html__( 'Insert Icon', 'yith-woocommerce-catalog-mode' ),
					'editor_font'  => $theme_font ? array_key_first( $theme_font ) : 'inherit',
				)
			);

			if ( ! ywctm_is_multivendor_active() || ! ywctm_is_multivendor_integration_active() ) {
				$css = '
					.yith-icon.yith-icon-arrow_down, .nav-subtab-wrap{display: none!important}
					.yith-plugin-fw-sub-tabs-nav{display: none}
					.yith-plugin-fw-wp-page-wrapper.ywctm-exclusions .wrap.subnav-wrap {margin-top: -2px!important;}
					.yith-plugin-fw-wp-page-wrapper .wrap.subnav-wrap .wrap {margin-top: 10px; border-top: 1px solid #d8d8d8;}
					';
				wp_add_inline_style( 'ywctm-button-label', $css );
			}

		}

		/**
		 * Add Retina Icons set
		 *
		 * @param array $icons Icons list.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_retinaicons( $icons ) {

			$icons['retinaicon-font'] = json_decode( wp_remote_get( YWCTM_ASSETS_URL . 'fonts/retinaicon-font.json', array( 'sslverify' => false ) )['body'], true );

			return $icons;
		}

		/**
		 * Sets editor buttons
		 *
		 * @param array  $buttons   TinyMCE buttons.
		 * @param string $editor_id Editor ID.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function edit_mce_buttons( $buttons, $editor_id ) {

			if ( 'ywctm_label_text' === $editor_id ) {

				$buttons = array(
					'fontselect',
					'fontsizeselect',
					'bold',
					'italic',
					'bullist',
					'numlist',
					'blockquote',
					'alignleft',
					'aligncenter',
					'alignright',
					'link',
					'wp_more',
					'spellchecke',
					'fullscreen',
					'wp_adv',
				);

			}

			return $buttons;
		}

		/**
		 * Sets editor settings
		 *
		 * @param array  $settings  TinyMCE Settings.
		 * @param string $editor_id Editor ID.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function tiny_mce_before_init( $settings, $editor_id ) {

			if ( 'ywctm_label_text' === $editor_id ) {

				$theme_font = ywctm_get_theme_font();
				$toolbar    = str_replace( 'wp_adv', '', $settings['toolbar1'] ) . $settings['toolbar2'] . ',ywctm_icons';
				// APPLY_FILTER: ywctm_button_editor_fonts: add or remove supported fonts from editor.
				$fonts = apply_filters(
					'ywctm_button_editor_fonts',
					array(
						'Andale Mono=andale mono,times',
						'Arial=arial,helvetica,sans-serif',
						'Arial Black=arial black,avant garde',
						'Book Antiqua=book antiqua,palatino',
						'Comic Sans MS=comic sans ms,sans-serif',
						'Courier New=courier new,courier',
						'Georgia=georgia,palatino',
						'Helvetica=helvetica',
						'Impact=impact,chicago',
						'Symbol=symbol',
						'Tahoma=tahoma,arial,helvetica,sans-serif',
						'Terminal=terminal,monaco',
						'Times New Roman=times new roman,times',
						'Trebuchet MS=trebuchet ms,geneva',
						'Verdana=verdana,geneva',
						'Webdings=webdings',
						'Wingdings=wingdings,zapf dingbats',
					)
				);

				asort( $fonts );

				if ( ! $theme_font ) {
					$fonts = array_merge(
						array( 'Theme Default=inherit' ),
						$fonts
					);
				}

				// APPLY_FILTER: ywctm_button_font_sizes: add or remove supported font sizes.
				$font_sizes = apply_filters(
					'ywctm_button_font_sizes',
					array(
						'9px',
						'10px',
						'12px',
						'13px',
						'14px',
						'16px',
						'18px',
						'21px',
						'24px',
						'28px',
						'32px',
						'36px',
					)
				);

				$settings['font_formats']       = implode( ';', $fonts );
				$settings['fontsize_formats']   = implode( ' ', $font_sizes );
				$settings['forced_root_block '] = 'div';
				$settings['toolbar1']           = $toolbar;
				$settings['toolbar2']           = '';
				$settings['content_style']      = '* {	font-size: 16px; font-family: ' . ( $theme_font ? reset( $theme_font ) : 'inherit' ) . '; }';
			}

			return $settings;

		}

		/**
		 * Add Google Fonts to TinyMCE  list
		 *
		 * @param array $fonts Fonts list.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_default_google_fonts( $fonts ) {

			$google_fonts = ywctm_enabled_google_fonts();

			foreach ( $google_fonts as $name => $google_font ) {
				$fonts[] = $name . '=' . $google_font;
			}

			return $fonts;
		}

		/**
		 * Add a script to TinyMCE script list
		 *
		 * @param array $plugin_array TinyMCE plugins.
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_shortcodes_tinymce_plugin( $plugin_array ) {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( ! $screen || $screen->post_type !== $this->post_type ) {
				return $plugin_array;
			}

			$plugin_array['ywctm_icons'] = yit_load_js_file( YWCTM_ASSETS_URL . 'js/tinymce-icons.js' );

			return $plugin_array;

		}

		/**
		 * Remove a metabox
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function remove_metaboxes() {
			remove_meta_box( 'submitdiv', $this->post_type, 'side' );
			remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		}

		/**
		 * The function to be called to output the meta box in Button & Label details page.
		 *
		 * @param WP_Post $post The Post object.
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function option_metabox( $post ) {

			if ( $post->post_type !== $this->post_type ) {
				return;
			}

			wp_nonce_field( 'ywctm_bl_save_data', 'ywctm_bl_meta_nonce' );
			$groups = array(
				'content' => array(
					'label'  => esc_html_x( 'Content', 'buttons & labels options group name', 'yith-woocommerce-catalog-mode' ),
					'fields' => array(
						array(
							'id'            => 'ywctm_label_text',
							'name'          => 'ywctm_label_text',
							'type'          => 'textarea-editor',
							'title'         => esc_html__( 'Label text', 'yith-woocommerce-catalog-mode' ),
							'desc'          => esc_html__( 'Enter a text for the label.', 'yith-woocommerce-catalog-mode' ),
							'textarea_rows' => 5,
							'wpautop'       => false,
							'default'       => sprintf( '<div class="btn-placeholder" style="text-align: center">%s</div>', esc_html_x( 'Contact Us', 'preview button placeholder text', 'yith-woocommerce-catalog-mode' ) ),
						),
						array(
							'title'   => esc_html__( 'Icon', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'select',
							'desc'    => esc_html__( 'Optional: add one of the default icons or upload a custom one.', 'yith-woocommerce-catalog-mode' ),
							'options' => array(
								'none'   => esc_html__( 'None', 'yith-woocommerce-catalog-mode' ),
								'icon'   => esc_html__( 'Choose from default icons', 'yith-woocommerce-catalog-mode' ),
								'custom' => esc_html__( 'Upload custom icon', 'yith-woocommerce-catalog-mode' ),
							),
							'default' => 'none',
							'class'   => 'wc-enhanced-select',
							'id'      => 'ywctm_icon_type',
							'name'    => 'ywctm_icon_type',
						),
						array(
							'title'        => esc_html__( 'Choose icon', 'yith-woocommerce-catalog-mode' ),
							'type'         => 'icons',
							'desc'         => esc_html__( 'Choose one of default icons.', 'yith-woocommerce-catalog-mode' ),
							'default'      => '',
							'filter_icons' => YWCTM_SLUG,
							'id'           => 'ywctm_selected_icon',
							'name'         => 'ywctm_selected_icon',
						),
						array(
							'title'   => esc_html__( 'Icon size', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'number',
							'desc'    => esc_html__( 'Set the icon size in pixels.', 'yith-woocommerce-catalog-mode' ),
							'default' => '14',
							'id'      => 'ywctm_selected_icon_size',
							'name'    => 'ywctm_selected_icon_size',
							'min'     => 0,
						),
						array(
							'title'   => esc_html__( 'Upload your icon', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'upload',
							'desc'    => esc_html__( 'Upload your custom icon.', 'yith-woocommerce-catalog-mode' ),
							'default' => '',
							'id'      => 'ywctm_custom_icon',
							'name'    => 'ywctm_custom_icon',
						),
						array(
							'title'   => esc_html__( 'Icon/image alignment', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'select',
							'desc'    => esc_html__( 'Set the icon/image vertical alignment.', 'yith-woocommerce-catalog-mode' ),
							'options' => array(
								'flex-start' => esc_html__( 'Top', 'yith-woocommerce-catalog-mode' ),
								'center'     => esc_html__( 'Middle', 'yith-woocommerce-catalog-mode' ),
								'flex-end'   => esc_html__( 'Bottom', 'yith-woocommerce-catalog-mode' ),
							),
							'default' => 'center',
							'class'   => 'wc-enhanced-select',
							'id'      => 'ywctm_selected_icon_alignment',
							'name'    => 'ywctm_selected_icon_alignment',
						),
						array(
							'id'           => 'ywctm_icon_color',
							'name'         => 'ywctm_icon_color',
							'type'         => 'multi-colorpicker',
							'desc'         => esc_html__( 'Set colors for icon.', 'yith-woocommerce-catalog-mode' ),
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
							'title'        => esc_html__( 'Icon colors', 'yith-woocommerce-catalog-mode' ),
						),
					),
				),
				'style'   => array(
					'label'  => esc_html_x( 'Style', 'buttons & labels options group name', 'yith-woocommerce-catalog-mode' ),
					'fields' => array(
						array(
							'id'      => 'ywctm_width_settings',
							'name'    => 'ywctm_width_settings',
							'type'    => 'inline-fields',
							'title'   => esc_html__( 'Width', 'yith-woocommerce-catalog-mode' ),
							'desc'    => esc_html__( 'Set a size for this button or label.', 'yith-woocommerce-catalog-mode' ),
							'fields'  => array(
								'width' => array(
									'std'  => '150',
									'type' => 'number',
									'min'  => 0,
								),
								'unit'  => array(
									'std'     => '',
									'type'    => 'select',
									'options' => array(
										'%' => '%',
										''  => 'px',
									),
									'class'   => 'wc-enhanced-select',
								),
							),
							'default' => array(
								'width' => '150',
								'unit'  => '',
							),
							'class'   => 'ywctm-multiple-mixed',
						),
						array(
							'id'           => 'ywctm_default_colors',
							'name'         => 'ywctm_default_colors',
							'type'         => 'multi-colorpicker',
							'desc'         => esc_html__( 'Set default colors.', 'yith-woocommerce-catalog-mode' ),
							'colorpickers' => array(
								array(
									'id'      => 'background',
									'name'    => esc_html__( 'Background', 'yith-woocommerce-catalog-mode' ),
									'default' => '#ffffff',
								),
								array(
									'id'      => 'text',
									'name'    => esc_html__( 'Text', 'yith-woocommerce-catalog-mode' ),
									'default' => '#247390',
								),
								array(
									'id'      => 'borders',
									'name'    => esc_html__( 'Borders', 'yith-woocommerce-catalog-mode' ),
									'default' => '#247390',
								),
							),
							'title'        => esc_html__( 'Default colors', 'yith-woocommerce-catalog-mode' ),
						),
						array(
							'id'           => 'ywctm_hover_colors',
							'name'         => 'ywctm_hover_colors',
							'type'         => 'multi-colorpicker',
							'desc'         => esc_html__( 'Set hover colors.', 'yith-woocommerce-catalog-mode' ),
							'colorpickers' => array(
								array(
									'id'      => 'background',
									'name'    => esc_html__( 'Background', 'yith-woocommerce-catalog-mode' ),
									'default' => '#ffffff',
								),
								array(
									'id'      => 'text',
									'name'    => esc_html__( 'Text', 'yith-woocommerce-catalog-mode' ),
									'default' => '#247390',
								),
								array(
									'id'      => 'borders',
									'name'    => esc_html__( 'Borders', 'yith-woocommerce-catalog-mode' ),
									'default' => '#247390',
								),
							),
							'title'        => esc_html__( 'Hover colors', 'yith-woocommerce-catalog-mode' ),
						),
						array(
							'id'      => 'ywctm_border_thickness',
							'name'    => 'ywctm_border_thickness',
							'title'   => esc_html__( 'Border thickness', 'yith-woocommerce-catalog-mode' ),
							'desc'    => esc_html__( 'Set the border thickness.', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'dimensions',
							'units'   => array( 'px' => 'px' ),
							'min'     => 0,
							'max'     => 50,
							'default' => array(
								'dimensions' => array(
									'top'    => 2,
									'right'  => 2,
									'bottom' => 2,
									'left'   => 2,
								),
								'unit'       => 'px',
								'linked'     => 'yes',
							),
						),
						array(
							'id'         => 'ywctm_border_radius',
							'name'       => 'ywctm_border_radius',
							'title'      => esc_html__( 'Border radius', 'yith-woocommerce-catalog-mode' ),
							'desc'       => esc_html__( 'Set the border radius.', 'yith-woocommerce-catalog-mode' ),
							'type'       => 'dimensions',
							'units'      => array( 'px' => 'px' ),
							'min'        => 0,
							'max'        => 50,
							'dimensions' => array(
								'top-left'     => esc_html__( 'Top', 'yith-woocommerce-catalog-mode' ),
								'top-right'    => esc_html__( 'Right', 'yith-woocommerce-catalog-mode' ),
								'bottom-right' => esc_html__( 'Bottom', 'yith-woocommerce-catalog-mode' ),
								'bottom-left'  => esc_html__( 'Left', 'yith-woocommerce-catalog-mode' ),
							),
							'default'    => array(
								'dimensions' => array(
									'top-left'     => 50,
									'top-right'    => 50,
									'bottom-right' => 50,
									'bottom-left'  => 50,
								),
								'unit'       => 'px',
								'linked'     => 'yes',
							),
						),
						array(
							'id'      => 'ywctm_margin_settings',
							'name'    => 'ywctm_margin_settings',
							'title'   => esc_html__( 'Margin', 'yith-woocommerce-catalog-mode' ),
							'desc'    => esc_html__( 'Set a margin to manage the space around the button or label.', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'dimensions',
							'units'   => array( 'px' => 'px' ),
							'default' => array(
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
						array(
							'id'      => 'ywctm_padding_settings',
							'name'    => 'ywctm_padding_settings',
							'title'   => esc_html__( 'Padding', 'yith-woocommerce-catalog-mode' ),
							'desc'    => esc_html__( 'Set a padding to manage the space inside the button or label.', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'dimensions',
							'units'   => array( 'px' => 'px' ),
							'default' => array(
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
				),
				'options' => array(
					'label'  => esc_html_x( 'Options', 'buttons & labels options group name', 'yith-woocommerce-catalog-mode' ),
					'fields' => array(
						array(
							'id'      => 'ywctm_button_url_type',
							'name'    => 'ywctm_button_url_type',
							'type'    => 'radio',
							'options' => array(
								'none'    => esc_html__( 'Unlinked text label', 'yith-woocommerce-catalog-mode' ),
								'product' => esc_html__( 'Product Page', 'yith-woocommerce-catalog-mode' ),
								'custom'  => esc_html__( 'Custom URL', 'yith-woocommerce-catalog-mode' ),
							),
							'default' => 'none',
							'title'   => esc_html__( 'Link To:', 'yith-woocommerce-catalog-mode' ),
							'desc'    => esc_html__( 'Select whether to link this button or remain as a static label.', 'yith-woocommerce-catalog-mode' ),
						),
						array(
							'id'    => 'ywctm_button_url',
							'name'  => 'ywctm_button_url',
							'type'  => 'text',
							'title' => esc_html__( 'Custom URL', 'yith-woocommerce-catalog-mode' ),
							'desc'  => esc_html__( 'Optional: add a custom URL.', 'yith-woocommerce-catalog-mode' ),
						),
						array(
							'title'   => esc_html__( 'Hover animation', 'yith-woocommerce-catalog-mode' ),
							'type'    => 'select',
							'desc'    => esc_html__( 'Choose a hover animation.', 'yith-woocommerce-catalog-mode' ),
							'options' => array(
								'none'             => esc_html__( 'None', 'yith-woocommerce-catalog-mode' ),
								'fade'             => esc_html__( 'Fade', 'yith-woocommerce-catalog-mode' ),
								'float'            => esc_html__( 'Float', 'yith-woocommerce-catalog-mode' ),
								'grow-button'      => esc_html__( 'Grow Button', 'yith-woocommerce-catalog-mode' ),
								'move-hover-color' => esc_html__( 'Move Hover Color', 'yith-woocommerce-catalog-mode' ),
								'press'            => esc_html__( 'Press', 'yith-woocommerce-catalog-mode' ),
								'push'             => esc_html__( 'Push', 'yith-woocommerce-catalog-mode' ),
								'shake'            => esc_html__( 'Shake', 'yith-woocommerce-catalog-mode' ),
								'slide-top'        => esc_html__( 'Slide Top', 'yith-woocommerce-catalog-mode' ),
								'slide-left'       => esc_html__( 'Slide Left', 'yith-woocommerce-catalog-mode' ),
							),
							'default' => 'none',
							'class'   => 'wc-enhanced-select',
							'id'      => 'ywctm_hover_animation',
							'name'    => 'ywctm_hover_animation',
						),
					),
				),
			);
			$values = array();

			if ( 'auto-draft' !== $post->post_status ) {
				$values = ywctm_get_button_label_settings( $post->ID );
			}

			?>
			<div class="ywctm-metabox-topbar">
				<?php
				printf( '<a href="%1$s" class="ywctm-back-button" title="%2$s">%2$s <span style="font-size: 14px; font-weight: 600">&#x2934;</span></a>', esc_url( esc_url( add_query_arg( array( 'post_type' => $this->post_type ), admin_url( 'edit.php' ) ) ) ), esc_html__( 'Return to buttons list', 'yith-woocommerce-catalog-mode' ) );
				?>
			</div>
			<div class="ywctm-metabox-wrapper">
				<div class="yith-plugin-ui yith-plugin-fw">
					<h1 class="wp-heading-inline">
						<?php
						if ( 'auto-draft' !== $post->post_status ) {
							esc_html_e( 'Edit button or label', 'yith-woocommerce-catalog-mode' );
						} else {
							esc_html_e( 'Create button or label', 'yith-woocommerce-catalog-mode' );
						}
						?>
					</h1>
					<table class="form-table">
						<tbody>
						<tr valign="top" class="yith-plugin-fw-panel-wc-row yith-plugin-fw--required">
							<th scope="row" class="titledesc">
								<label for="title"><?php esc_html_e( 'Name', 'yith-woocommerce-catalog-mode' ); ?></label>
							</th>
							<td class="forminp ">
								<input class="required" type="text" name="post_title" size="30" value="<?php echo esc_attr( $post->post_title ); ?>" id="title" spellcheck="true" autocomplete="off" data-message="<?php esc_html_e( 'This field is required.', 'yith-woocommerce-catalog-mode' ); ?>" />
								<span class="description"><?php esc_html_e( 'Enter a name to identify this button or label.', 'yith-woocommerce-catalog-mode' ); ?></span>
							</td>
						</tr>
						</tbody>
					</table>
					<div class="ywctm-metabox-tabs">
						<div class="ywctm-metabox-tabs-header">
							<?php foreach ( $groups as $key => $group ) : ?>
								<div class="ywctm-metabox-tab-button <?php echo esc_attr( $key ); ?> <?php echo esc_attr( 'content' === $key ? 'tab-active' : '' ); ?>" data-tab-name="<?php echo esc_attr( $key ); ?>">
									<span><?php echo esc_attr( $group['label'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
						<div class="ywctm-scrolling-content">
							<?php foreach ( $groups as $key => $group ) : ?>
								<div class="ywctm-settings-wrapper <?php echo esc_attr( $key ); ?> <?php echo esc_attr( 'content' === $key ? 'tab-active' : '' ); ?>">
									<table class="form-table">
										<tbody>
										<?php
										foreach ( $group['fields'] as $field ) :
											if ( $values ) {
												$field['value'] = $values[ str_replace( 'ywctm_', '', $field['id'] ) ];
											} else {
												$field['value'] = isset( $field['default'] ) ? $field['default'] : '';
											}
											?>
											<tr valign="top" class="yith-plugin-fw-panel-wc-row <?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $field['id'] ); ?>">
												<th scope="row" class="titledesc">
													<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_attr( $field['title'] ); ?></label>
												</th>
												<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
													<?php yith_plugin_fw_get_field( $field, true ); ?>
													<?php if ( isset( $field['desc'] ) ) : ?>
														<span class="description"><?php echo esc_attr( $field['desc'] ); ?></span>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
										</tbody>
									</table>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

				</div>
				<div class="ywctm-button-preview">
					<h1 class="wp-heading-inline">
						<?php esc_html_e( 'Preview', 'yith-woocommerce-catalog-mode' ); ?>
					</h1>
					<div class="ywctm-button-preview-container">
						<div class="ywctm-custom-button-container">
							<span class="ywctm-custom-button">
							<span class="ywctm-inquiry-title">
								<?php echo sprintf( '<div class="btn-placeholder" style="text-align: center">%s</div>', esc_html_x( 'Contact Us', 'preview button placeholder text', 'yith-woocommerce-catalog-mode' ) ); ?>
							</span>
						</span>
						</div>
					</div>
					<div id="submitpost" class="ywctm-metabox-buttons yith-plugin-ui">
						<?php
						if ( 'auto-draft' !== $post->post_status ) {
							$name  = 'save';
							$label = esc_html__( 'Update', 'yith-woocommerce-catalog-mode' );
						} else {
							$name  = 'publish';
							$label = esc_html__( 'Save', 'yith-woocommerce-catalog-mode' );
						}
						?>
						<input name="<?php echo esc_attr( $name ); ?>" type="submit" class="button button-primary button-xl" id="publish" value="<?php echo esc_html( $label ); ?>" />
					</div>
				</div>
			</div>
			<?php

		}

		/**
		 * Save meta box process
		 *
		 * @param integer $post_id The Post ID.
		 * @param WP_Post $post    The Post object.
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function save_post( $post_id, $post ) {

			// $post_id and $post are required.
			if ( empty( $post_id ) || empty( $post ) || $this->saved_meta_box ) {
				return;
			}

			// Don't save meta boxes for revisions or autosaves.
			if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the nonce.
			if ( empty( $_POST['ywctm_bl_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ywctm_bl_meta_nonce'] ) ), 'ywctm_bl_save_data' ) ) {
				return;
			}

			$posted = $_POST;

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
			if ( empty( $posted['post_ID'] ) || (int) $posted['post_ID'] !== $post_id ) {
				return;
			}

			// Check user has permission to edit.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// We need this save event to run once to avoid potential endless loops. This would have been perfect:
			// remove_action( current_filter(), __METHOD__ );.
			// But cannot be used due to https://github.com/woothemes/woocommerce/issues/6485
			// When that is patched in core we can use the above. For now.
			$this->saved_meta_box = true;

			$used_icons   = array();
			$used_fonts   = array();
			$icon_fonts   = array(
				'fontawesome'     => '{{fa',
				'dashicons'       => '{{dashicons',
				'retinaicon-font' => '{{retinaicon-font',
			);
			$google_fonts = ywctm_enabled_google_fonts();

			if ( 'icon' === $posted['ywctm_icon_type'] ) {
				$icon         = explode( ':', $posted['ywctm_selected_icon'] );
				$used_icons[] = strtolower( $icon[0] );
			}

			foreach ( $icon_fonts as $name => $icon_font ) {
				if ( substr_count( $posted['ywctm_label_text'], $icon_font, 0 ) > 0 ) {
					$used_icons[] = $name;
				}
			}

			foreach ( $google_fonts as $name => $google_font ) {
				if ( substr_count( $posted['ywctm_label_text'], "font-family: \'$name\'", 0 ) > 0 ) {
					$used_fonts[] = $name;
				}
			}

			$post_meta = array(
				'ywctm_label_text'              => $posted['ywctm_label_text'],
				'ywctm_icon_type'               => $posted['ywctm_icon_type'],
				'ywctm_custom_icon'             => $posted['ywctm_custom_icon'],
				'ywctm_selected_icon'           => $posted['ywctm_selected_icon'],
				'ywctm_selected_icon_size'      => $posted['ywctm_selected_icon_size'],
				'ywctm_selected_icon_alignment' => $posted['ywctm_selected_icon_alignment'],
				'ywctm_icon_color'              => $posted['ywctm_icon_color'],
				'ywctm_width_settings'          => $posted['ywctm_width_settings'],
				'ywctm_default_colors'          => $posted['ywctm_default_colors'],
				'ywctm_hover_colors'            => $posted['ywctm_hover_colors'],
				'ywctm_border_thickness'        => $posted['ywctm_border_thickness'],
				'ywctm_border_radius'           => $posted['ywctm_border_radius'],
				'ywctm_margin_settings'         => $posted['ywctm_margin_settings'],
				'ywctm_padding_settings'        => $posted['ywctm_padding_settings'],
				'ywctm_button_url_type'         => $posted['ywctm_button_url_type'],
				'ywctm_button_url'              => $posted['ywctm_button_url'],
				'ywctm_hover_animation'         => $posted['ywctm_hover_animation'],
				'ywctm_used_icons'              => array_unique( $used_icons ),
				'ywctm_used_fonts'              => array_unique( $used_fonts ),
			);

			foreach ( $post_meta as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}

		}

		/**
		 * Duplicate button
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function duplicate_button() {

			if ( ! current_user_can( 'manage_options' ) || ! isset( $_REQUEST['action'] ) || 'duplicate_button' !== $_REQUEST['action'] || ! isset( $_REQUEST['post'] ) ) {
				return;
			}

			global $wpdb;
			$old_post = sanitize_text_field( wp_unslash( $_REQUEST['post'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			check_admin_referer( 'ywctm-duplicate-button_' . $old_post );
			$results = $wpdb->get_results( $wpdb->prepare( "SELECT * from $wpdb->posts WHERE id=%d", $old_post ), ARRAY_A ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			if ( $results ) {
				foreach ( $results as $result ) {
					if ( $result['post_type'] === $this->post_type ) {
						unset( $result['ID'] );
						$result['post_title'] = sprintf( '%s %s', $result['post_title'], esc_html__( '(Copy)', 'yith-woocommerce-catalog-mode' ) );
						$new_post             = wp_insert_post( $result );
						$post_meta            = get_post_custom( $old_post );
						// set unique key and correct post id.
						$post_meta['_key'][0] = uniqid();
						$post_meta['id'][0]   = $new_post;

						if ( is_array( $post_meta ) ) {
							foreach ( $post_meta as $k => $v ) {
								update_post_meta( $new_post, $k, maybe_unserialize( $v[0] ) );
							}
						}
						$redirect_query = array(
							'action' => 'edit',
							'post'   => $new_post,
						);
						wp_safe_redirect( add_query_arg( $redirect_query, admin_url( 'post.php' ) ) );
					}
				}
			}

			exit;
		}

	}

	new YWCTM_Button_Label_Post_Type();

}
