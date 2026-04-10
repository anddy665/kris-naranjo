<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Custom HTML trait.
 *
 * Allows to use a group of controls for custom HTML.
 */
trait Custom_HTML {

	/**
	 * Group of controls for custom html.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_custom_html( $key = '', $args = array() ) {
		list(
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'text' ),
			array_merge_recursive(
				$default_args,
				array(
					'label_block' => true,
					'show_label' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::CODE,
				)
			)
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_TYPOGRAPHY,
			$default_args
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Colors', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_text' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Text', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_text' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_link' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Link', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_link' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_hover' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Link Hover', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_hover' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'apply_settings' ),
			array_merge_recursive(
				$default_args,
				array(
					'label_block' => true,
					'show_label' => false,
					'type' => Controls_Manager::BUTTON,
					'text' => esc_html__( 'Save & Reload', 'pediatric-clinic' ),
					'event' => 'cmsmasters:theme_settings:apply_settings',
				)
			)
		);
	}

}
