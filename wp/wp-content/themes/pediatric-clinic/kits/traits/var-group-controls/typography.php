<?php
namespace PediatricClinicSpace\Kits\Traits\VarGroupControls;

use Elementor\Group_Control_Typography;
use Elementor\Plugin as Elementor_Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Typography trait.
 *
 * Allows to use a group control typography with css vars.
 */
trait Typography {

	/**
	 * Add typography group control with css vars.
	 *
	 * @param string $key Control key.
	 * @param array $args Control arguments.
	 */
	protected function var_group_control_typography( $key = '', $args = array() ) {
		list( $name, $prefix ) = $this->get_control_parameters( $key, 'typography' );

		$general_settings_model = Elementor_Plugin::$instance->kits_manager->get_active_kit_for_frontend();
		$default_fonts = $general_settings_model->get_settings( 'default_generic_fonts' );

		if ( $default_fonts ) {
			$default_fonts = ", {$default_fonts}";
		}

		$default_args = array(
			'name' => $name,
			'label' => esc_html__( 'Typography', 'pediatric-clinic' ),
			'fields_options' => array(
				'font_family' => array(
					'selectors' => array(
						':root' => "--{$prefix}-font-family: \"{{VALUE}}\"{$default_fonts};",
					),
				),
				'font_size' => array(
					'selectors' => array(
						':root' => "--{$prefix}-font-size: {{SIZE}}{{UNIT}};",
					),
				),
				'font_weight' => array(
					'selectors' => array(
						':root' => "--{$prefix}-font-weight: {{VALUE}};",
					),
				),
				'text_transform' => array(
					'selectors' => array(
						':root' => "--{$prefix}-text-transform: {{VALUE}};",
					),
				),
				'font_style' => array(
					'selectors' => array(
						':root' => "--{$prefix}-font-style: {{VALUE}};",
					),
				),
				'text_decoration' => array(
					'selectors' => array(
						':root' => "--{$prefix}-text-decoration: {{VALUE}};",
					),
				),
				'line_height' => array(
					'selectors' => array(
						':root' => "--{$prefix}-line-height: {{SIZE}}{{UNIT}};",
					),
				),
				'letter_spacing' => array(
					'selectors' => array(
						':root' => "--{$prefix}-letter-spacing: {{SIZE}}{{UNIT}};",
					),
				),
				'word_spacing' => array(
					'selectors' => array(
						':root' => "--{$prefix}-word-spacing: {{SIZE}}{{UNIT}};",
					),
				),
			),
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array_replace_recursive( $default_args, $args )
		);
	}

}
