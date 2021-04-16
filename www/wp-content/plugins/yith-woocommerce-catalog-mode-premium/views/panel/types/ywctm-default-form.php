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

/**
 * The field options.
 *
 * @var array $field
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$field_id = $field['id'];
$values   = get_option( $field_id );
if ( empty( $values ) ) {
	$values = call_user_func_array( $field['callback_default_form'], array() );
	update_option( $field_id, $values );
}

$columns           = array(
	'name'        => array(
		'label'         => esc_html_x( 'Name', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => true,
		'type'          => 'text',
	),
	'type'        => array(
		'label'         => esc_html_x( 'Type', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => true,
		'default'       => 'text',
		'type'          => 'select',
		'class'         => 'wc-enhanced-select',
		'options'       => ywctm_get_field_types(),
	),
	'class'       => array(
		'label'         => esc_html_x( 'Class', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'description'   => esc_html_x( 'Separate classes with commas', 'Default form description', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => false,
		'type'          => 'text',
	),
	'label'       => array(
		'label'         => esc_html_x( 'Label', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => true,
		'type'          => 'text',
	),
	'label_class' => array(
		'label'         => esc_html_x( 'Label Class', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'description'   => esc_html_x( 'Separate classes with commas', 'Default form description', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => false,
		'type'          => 'text',
	),
	'placeholder' => array(
		'label'         => esc_html_x( 'Placeholder', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => false,
		'type'          => 'text',
		'deps'          => array(
			'id'     => 'type',
			'values' => 'text|email|textarea',
		),
	),
	'description' => array(
		'label'         => esc_html_x( 'Description', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'description'   => esc_html_x( 'You can use the shortcode [terms] and [privacy_policy]', 'Default form description', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => false,
		'type'          => 'textarea',
		'rows'          => 5,
		'columns'       => 10,
		'deps'          => array(
			'id'     => 'type',
			'values' => 'ywctm_acceptance',
		),
	),
	'position'    => array(
		'label'         => esc_html_x( 'Position', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => false,
		'type'          => 'select',
		'class'         => 'wc-enhanced-select',
		'options'       => ywctm_get_array_positions_form_field(),
		'default'       => 'form-row-wide',
	),
	'required'    => array(
		'label'         => esc_html_x( 'Required', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => true,
		'type'          => 'onoff',
		'default'       => 'no',
		'deps'          => array(
			'id'     => 'type',
			'values' => 'text|textarea',
		),
	),
	'enabled'     => array(
		'label'         => esc_html_x( 'Activate', 'Default form column', 'yith-woocommerce-catalog-mode' ),
		'show_on_table' => true,
		'show_on_popup' => false,
		'default'       => 'yes',
	),
	'actions'     => array(
		'label'         => '',
		'show_on_table' => true,
		'show_on_popup' => false,
	),
);
$custom_attributes = isset( $field['custom_attributes'] ) ? (array) $field['custom_attributes'] : '';
$custom_attributes = implode( ' ', $custom_attributes );

?>

<div class="ywctm-default-form" data-option-id="<?php echo esc_attr( $field_id ); ?>" data-callback="<?php echo esc_attr( $field['callback_default_form'] ); ?>" <?php echo esc_attr( $custom_attributes ); ?>>
	<form method="post" class="ywctm-default-form__form_table">
		<table class="ywctm-default-form-main-table">
			<thead>
			<tr>
				<?php
				foreach ( $columns as $key => $column ) :
					if ( isset( $column['show_on_table'] ) && $column['show_on_table'] ) :
						?>
						<th class="<?php echo esc_attr( $key ); ?>">
							<?php
							esc_html_e( $column['label'] ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
							?>
						</th>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tbody class="ui-sortable">
			<?php if ( $values ) : ?>
				<?php
				foreach ( $values as $name => $value ) :
					?>
					<tr>
						<?php
						foreach ( $columns as $key => $column ) :

							$current_default = isset( $column['default'] ) ? $column['default'] : '';
							if ( 'name' === $key ) {
								$current_value = $name;
							} else {
								$current_value = isset( $value[ $key ] ) ? $value[ $key ] : $current_default;
							}

							if ( is_array( $current_value ) ) {
								if ( empty( $current_value ) ) {
									$current_value = '';
								} else {
									$current_value = is_array( $current_value ) && ! empty( $current_value ) ? implode( ',', $current_value ) : $current_value;
								}
							}

							?>
							<input type="hidden" name="field_<?php echo esc_attr( $key ); ?>[]" data-name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $current_value ); ?>" data-default="<?php echo esc_attr( $current_default ); ?>" />
							<?php
							if ( isset( $column['type'] ) && 'select' === $column['type'] ) {
								$current_value = is_array( $current_value ) ? implode( ',', $current_value ) : $current_value;
							}

							if ( isset( $column['show_on_table'] ) && $column['show_on_table'] ) :

								if ( 'enabled' === $key ) :
									?>
									<td>
										<div class="yith-plugin-fw-onoff-container ">
											<input type="checkbox" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="yes" <?php checked( $current_value, 'yes' ); ?> class="on_off">
											<span class="yith-plugin-fw-onoff" data-text-on="<?php echo esc_attr_x( 'YES', 'YES/NO button: use MAX 3 characters!', 'yith-woocommerce-catalog-mode' ); ?>" data-text-off="<?php echo esc_attr_x( 'NO', 'YES/NO button: use MAX 3 characters!', 'yith-woocommerce-catalog-mode' ); ?>"></span>
										</div>
									</td>

								<?php elseif ( 'actions' === $key ) : ?>
									<td>
										<ul class="actions">
											<li class="action__edit"></li>
											<li class="action__sort"></li>
										</ul>
									</td>
								<?php elseif ( 'required' === $key ) : ?>
									<td>
										<?php
										if ( 'yes' === $current_value ) {
											echo '<div class="field_required"></div>';
										} else {
											echo '-';
										}
										?>
									</td>
								<?php else : ?>
									<?php
									if ( isset( $column['options'], $column['options'][ $current_value ] ) ) {
										$current_value = $column['options'][ $current_value ];
									}
									?>
									<td class="<?php echo esc_attr( $key ); ?>">
										<?php esc_html_e( $current_value ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText ?>
									</td>
								<?php endif; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
			<tfoot>

			</tfoot>
		</table>
	</form>

	<div class="ywctm-default-form__popup_wrapper">
		<form method="post" class="ywctm-default-form__form_row">
			<table id="yith_form_fields_table">

				<?php foreach ( $columns as $name => $column ) : ?>

					<?php
					$value             = ( isset( $column['default'] ) ? $column['default'] : '' );
					$show              = ( isset( $column['show_on_popup'] ) ? $column['show_on_popup'] : true );
					$custom_attributes = isset( $column['custom_attributes'] ) ? ' ' . $column['custom_attributes'] . ' ' : '';

					$custom_attributes .= isset( $column['class'] ) ? ' class="' . $column['class'] . '" ' : '';
					$custom_attributes .= isset( $column['deps'], $column['deps']['id'], $column['deps']['values'] ) ? ' data-deps="' . $column['deps']['id'] . '" data-deps_value="' . $column['deps']['values'] . '" ' : '';

					if ( ! $show ) {
						?>
						<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
						<?php
						continue;
					}

					?>
					<tr class="row-<?php echo esc_attr( $name ); ?>">
						<th class="label"> <?php isset( $column['label'] ) && esc_html_e( $column['label'] ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText ?> </th>
						<td>
							<?php
							switch ( $column['type'] ) {
								case 'text':
									?>
									<input type="text" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php echo wp_kses_post( $custom_attributes ); ?>/>
									<?php
									break;
								case 'select':
									if ( $column['options'] ) {
										?>
										<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" <?php echo wp_kses_post( $custom_attributes ); ?> >
											<?php foreach ( $column['options'] as $value => $label ) : ?>
												<option
													value="<?php echo wp_kses_post( $value ); ?>"><?php echo wp_kses_post( $label ); ?></option>
											<?php endforeach; ?>
										</select>
										<?php
									}
									break;
								case 'textarea':
									$col = isset( $column['colums'] ) ? $column['colums'] : 10;
									$row = isset( $column['rows'] ) ? $column['rows'] : 5;
									?>
									<textarea id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" cols="<?php echo esc_attr( $col ); ?>" rows="<?php echo esc_attr( $row ); ?>>" <?php echo wp_kses_post( $custom_attributes ); ?>></textarea>
									<?php
									break;
								case 'onoff':
									?>
									<div class="yith-plugin-fw-onoff-container ">
										<input type="checkbox" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="yes" checked="checked" class="on_off" <?php echo wp_kses_post( $custom_attributes ); ?>>
										<span class="yith-plugin-fw-onoff" data-text-on="<?php echo esc_attr_x( 'YES', 'YES/NO button: use MAX 3 characters!', 'yith-woocommerce-catalog-mode' ); ?>" data-text-off="<?php echo esc_attr_x( 'NO', 'YES/NO button: use MAX 3 characters!', 'yith-woocommerce-catalog-mode' ); ?>"></span>
									</div>
									<?php
									break;
							}
							?>

							<?php if ( isset( $column['description'] ) ) : ?>
								<div class="description"><?php esc_html_e( $column['description'] ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText ?></div>
							<?php endif; ?>
							<?php if ( 'name' === $name ) : ?>
								<div class="description field-exists">
									<?php esc_html_e( 'This field is already defined', 'yith-woocommerce-catalog-mode' ); ?>
								</div>
								<div class="description required">
									<?php esc_html_e( 'This field is required', 'yith-woocommerce-catalog-mode' ); ?>
								</div>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		</form>
	</div>
</div>
