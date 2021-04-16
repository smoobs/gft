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

list ( $field_id, $class, $name, $std, $value ) = yith_plugin_fw_extract( $field, 'id', 'class', 'name', 'std', 'value' );

if ( empty( $value ) ) {
	$value = array(
		array(
			'start_date' => '',
			'end_date'   => '',
		),
	);
}

$index = 0;
/* translators: 5s 'Plus' sign */
$label = sprintf( esc_html__( '%s Add rule', 'yith-woocommerce-catalog-mode' ), '+' )
?>

<div id="<?php echo esc_attr( $field_id ); ?>" class="ywctm-date-ranges-wrapper">
	<div class="ywctm-date-ranges">
		<?php foreach ( $value as $daterange ) : ?>
			<div class="ywctm-date-range-row" data-index="<?php echo esc_attr( $index ); ?>">
				<?php
				yith_plugin_fw_get_field(
					array(
						'type'   => 'inline-fields',
						'id'     => $field_id . '_' . $index,
						'name'   => $name . "[$index]",
						'value'  => $daterange,
						'fields' => array(
							'start_date' => array(
								'inline-label'      => esc_html_x( 'From', 'Part of the sentence: "From [date] To [date]"', 'yith-woocommerce-catalog-mode' ),
								'type'              => 'datepicker',
								'custom_attributes' => array(
									'pattern'     => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
									'placeholder' => 'YYYY-MM-DD',
									'maxlenght'   => 10,
									'required'    => 'required',
								),
								'data'              => array(
									'date-format' => 'yy-mm-dd',
									'min-date'    => 0,
								),
							),
							'end_date'   => array(
								'inline-label'      => esc_html_x( 'To', 'Part of the sentence: "From [date] To [date]"', 'yith-woocommerce-catalog-mode' ),
								'type'              => 'datepicker',
								'custom_attributes' => array(
									'pattern'     => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
									'placeholder' => 'YYYY-MM-DD',
									'maxlenght'   => 10,
									'required'    => 'required',
								),
								'data'              => array(
									'date-format' => 'yy-mm-dd',
									'min-date'    => 0,
								),
							),
							'trash'      => array(
								'inline-label' => '',
								'type'         => 'html',
								'html'         => ( $index > 0 ? '<span class="yith-icon yith-icon-trash"></span>' : '' ),
								'max'          => 59,
							),
						),
					),
					true,
					true
				);
				?>
			</div>
			<?php $index ++; ?>
		<?php endforeach; ?>
	</div>
	<div class="new_date_range">
		<a href="#" id="ywctm-new-date-range" class="ywctm-new-date-range"><?php echo esc_attr( $label ); ?></a>
	</div>
</div>

<script type="text/template" id="tmpl-ywctm-date-range-row">
	<div class="ywctm-date-range-row" data-index="{{{data.index}}}">
		<?php
		yith_plugin_fw_get_field(
			array(
				'type'   => 'inline-fields',
				'id'     => $field_id . '_{{{data.index}}}',
				'name'   => $name . '[{{{data.index}}}]',
				'value'  => array(
					'start_date' => '',
					'end_date'   => '',
				),
				'fields' => array(
					'start_date' => array(
						'inline-label'      => esc_html_x( 'From', 'Part of the sentence: "From [date] To [date]"', 'yith-woocommerce-catalog-mode' ),
						'type'              => 'datepicker',
						'custom_attributes' => array(
							'pattern'     => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
							'placeholder' => 'YYYY-MM-DD',
							'maxlenght'   => 10,
							'required'    => 'required',
						),
						'data'              => array(
							'date-format' => 'yy-mm-dd',
							'min-date'    => 0,
						),
					),
					'end_date'   => array(
						'inline-label'      => esc_html_x( 'To', 'Part of the sentence: "From [date] To [date]"', 'yith-woocommerce-catalog-mode' ),
						'type'              => 'datepicker',
						'custom_attributes' => array(
							'pattern'     => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
							'placeholder' => 'YYYY-MM-DD',
							'maxlenght'   => 10,
							'required'    => 'required',
						),
						'data'              => array(
							'date-format' => 'yy-mm-dd',
							'min-date'    => 0,
						),
					),
					'trash'      => array(
						'inline-label' => '',
						'type'         => 'html',
						'html'         => '<span class="yith-icon yith-icon-trash"></span>',
					),
				),
			),
			true,
			false
		);
		?>
	</div>
</script>
