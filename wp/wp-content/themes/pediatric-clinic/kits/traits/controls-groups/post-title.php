<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Post Title trait.
 *
 * Allows to use a group of controls for post title.
 */
trait Post_Title {

	/**
	 * Group of controls for post title.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_post_title( $key = '', $args = array() ) {
		list(
			$hover,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'hover' => false, // Hover color control
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

		if ( $hover ) {
			$this->add_control(
				$this->get_control_name_parameter( $key, 'hover' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Hover', 'pediatric-clinic' ),
						'type' => Controls_Manager::COLOR,
						'dynamic' => array(),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'hover' ) . ': {{VALUE}};',
						),
					)
				)
			);
		}

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
	}

}
