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
			'start_hour'    => '',
			'start_minutes' => '',
			'end_hour'      => '',
			'end_minutes'   => '',
			'days'          => array( 'all' ),
		),
	);
}

$index = 0;
/* translators: 5s 'Plus' sign */
$label = sprintf( esc_html__( '%s Add rule', 'yith-woocommerce-catalog-mode' ), '+' )
?>

<div id="<?php echo esc_attr( $field_id ); ?>" class="ywctm-time-ranges-wrapper">
	<div class="ywctm-time-ranges">
		<?php foreach ( $value as $timerange ) : ?>
			<div class="ywctm-time-range-row" data-index="<?php echo esc_attr( $index ); ?>">
				<?php
				yith_plugin_fw_get_field(
					array(
						'type'   => 'inline-fields',
						'id'     => $field_id . '_' . $index,
						'name'   => $name . "[$index]",
						'value'  => $timerange,
						'fields' => array(
							'start_hour'    => array(
								'inline-label'      => esc_html_x( 'From', 'Part of the sentence: "From [time] To [time] on [days of the week]"', 'yith-woocommerce-catalog-mode' ),
								'std'               => '00',
								'type'              => 'text',
								'custom_attributes' => array(
									'pattern'     => '([0-1]?[0-9]|2[0-3])',
									'placeholder' => esc_html_x( 'HH', 'Abbreviation for "Hours"', 'yith-woocommerce-catalog-mode' ),
									'required'    => 'required',
								),
								'class'             => 'ywctm-hours',
							),
							'start_minutes' => array(
								'inline-label'      => ':',
								'std'               => '00',
								'type'              => 'text',
								'custom_attributes' => array(
									'pattern'     => '[0-5][0-9]',
									'placeholder' => esc_html_x( 'MM', 'Abbreviation for "Minutes"', 'yith-woocommerce-catalog-mode' ),
									'required'    => 'required',
								),
								'class'             => 'ywctm-minutes',
							),
							'end_hour'      => array(
								'inline-label'      => esc_html_x( 'To', 'Part of the sentence: "From [time] To [time] on [days of the week]"', 'yith-woocommerce-catalog-mode' ),
								'std'               => '00',
								'type'              => 'text',
								'custom_attributes' => array(
									'pattern'     => '([0-1]?[0-9]|2[0-3])',
									'placeholder' => esc_html_x( 'HH', 'Abbreviation for "Hours"', 'yith-woocommerce-catalog-mode' ),
									'required'    => 'required',
								),
								'class'             => 'ywctm-hours',
							),
							'end_minutes'   => array(
								'inline-label'      => ':',
								'std'               => '00',
								'type'              => 'text',
								'custom_attributes' => array(
									'pattern'     => '[0-5][0-9]',
									'placeholder' => esc_html_x( 'MM', 'Abbreviation for "Minutes"', 'yith-woocommerce-catalog-mode' ),
									'required'    => 'required',
								),
								'class'             => 'ywctm-minutes',
							),
							'days'          => array(
								'inline-label' => esc_html_x( 'on', 'Part of the sentence: "From [time] To [time] on [days of the week]"', 'yith-woocommerce-catalog-mode' ),
								'options'      => array(
									'all' => esc_html__( 'All days', 'yith-woocommerce-catalog-mode' ),
									'1'   => esc_html__( 'Monday', 'yith-woocommerce-catalog-mode' ),
									'2'   => esc_html__( 'Tuesday', 'yith-woocommerce-catalog-mode' ),
									'3'   => esc_html__( 'Wednesday', 'yith-woocommerce-catalog-mode' ),
									'4'   => esc_html__( 'Thursday', 'yith-woocommerce-catalog-mode' ),
									'5'   => esc_html__( 'Friday', 'yith-woocommerce-catalog-mode' ),
									'6'   => esc_html__( 'Saturday', 'yith-woocommerce-catalog-mode' ),
									'7'   => esc_html__( 'Sunday', 'yith-woocommerce-catalog-mode' ),
								),
								'std'          => 'all',
								'type'         => 'select',
								'multiple'     => true,
							),
							'trash'         => array(
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
	<div class="new_time_range">
		<a href="#" id="ywctm-new-time-range" class="ywctm-new-time-range"><?php echo esc_attr( $label ); ?></a>
	</div>
</div>

<script type="text/template" id="tmpl-ywctm-time-range-row">
	<div class="ywctm-time-range-row" data-index="{{{data.index}}}">
		<?php
		yith_plugin_fw_get_field(
			array(
				'type'   => 'inline-fields',
				'id'     => $field_id . '_{{{data.index}}}',
				'name'   => $name . '[{{{data.index}}}]',
				'value'  => array(
					'start_hour'    => '',
					'start_minutes' => '',
					'end_hour'      => '',
					'end_minutes'   => '',
					'days'          => array( 'all' ),
				),
				'fields' => array(
					'start_hour'    => array(
						'inline-label'      => esc_html_x( 'From', 'Part of the sentence: "From [time] To [time] on [days of the week]"', 'yith-woocommerce-catalog-mode' ),
						'std'               => '00',
						'type'              => 'text',
						'custom_attributes' => array(
							'pattern'     => '([0-1]?[0-9]|2[0-3])',
							'placeholder' => esc_html_x( 'HH', 'Abbreviation for "Hours"', 'yith-woocommerce-catalog-mode' ),
							'required'    => 'required',
						),
						'class'             => 'ywctm-hours',
					),
					'start_minutes' => array(
						'inline-label'      => ':',
						'std'               => '00',
						'type'              => 'text',
						'custom_attributes' => array(
							'pattern'     => '[0-5][0-9]',
							'placeholder' => esc_html_x( 'MM', 'Abbreviation for "Minutes"', 'yith-woocommerce-catalog-mode' ),
							'required'    => 'required',
						),
						'class'             => 'ywctm-minutes',
					),
					'end_hour'      => array(
						'inline-label'      => esc_html_x( 'To', 'Part of the sentence: "From [time] To [time] on [days of the week]"', 'yith-woocommerce-catalog-mode' ),
						'std'               => '00',
						'type'              => 'text',
						'custom_attributes' => array(
							'pattern'     => '([0-1]?[0-9]|2[0-3])',
							'placeholder' => esc_html_x( 'HH', 'Abbreviation for "Hours"', 'yith-woocommerce-catalog-mode' ),
							'required'    => 'required',
						),
						'class'             => 'ywctm-hours',
					),
					'end_minutes'   => array(
						'inline-label'      => ':',
						'std'               => '00',
						'type'              => 'text',
						'custom_attributes' => array(
							'pattern'     => '[0-5][0-9]',
							'placeholder' => esc_html_x( 'MM', 'Abbreviation for "Minutes"', 'yith-woocommerce-catalog-mode' ),
							'required'    => 'required',
						),
						'class'             => 'ywctm-minutes',
					),
					'days'          => array(
						'inline-label' => esc_html_x( 'on', 'Part of the sentence: "From [time] To [time] on [days of the week]"', 'yith-woocommerce-catalog-mode' ),
						'options'      => array(
							'all'       => esc_html__( 'All days', 'yith-woocommerce-catalog-mode' ),
							'sunday'    => esc_html__( 'Sunday', 'yith-woocommerce-catalog-mode' ),
							'monday'    => esc_html__( 'Monday', 'yith-woocommerce-catalog-mode' ),
							'tuesday'   => esc_html__( 'Tuesday', 'yith-woocommerce-catalog-mode' ),
							'wednesday' => esc_html__( 'Wednesday', 'yith-woocommerce-catalog-mode' ),
							'thursday'  => esc_html__( 'Thursday', 'yith-woocommerce-catalog-mode' ),
							'friday'    => esc_html__( 'Friday', 'yith-woocommerce-catalog-mode' ),
							'saturday'  => esc_html__( 'Saturday', 'yith-woocommerce-catalog-mode' ),
						),
						'std'          => 'all',
						'type'         => 'select',
						'multiple'     => true,
					),
					'trash'         => array(
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
