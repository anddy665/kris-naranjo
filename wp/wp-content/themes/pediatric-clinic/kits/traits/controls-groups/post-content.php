<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Post Content trait.
 *
 * Allows to use a group of controls for post content.
 */
trait Post_Content {

	/**
	 * Group of controls for post content.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_post_content( $key = '', $args = array() ) {
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

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_TYPOGRAPHY,
			$default_args
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'color' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Color', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'color' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'excerpt_length' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Excerpt Length', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::NUMBER,
					'min' => 10,
					'step' => 1,
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'excerpt_length' ),
						55
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'box_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Container', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_controls_group(
			$this->get_control_name_parameter( $key, 'box' ),
			Settings_Tab_Base::CONTROLS_CONTAINER_BOX,
			array_merge_recursive(
				$default_args,
				array(
					'popover' => false,
					'excludes' => array( 'box_shadow' ),
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
					'separator' => 'before',
				)
			)
		);
	}

}
