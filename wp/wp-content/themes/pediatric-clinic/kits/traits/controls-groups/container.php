<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Container trait.
 *
 * Allows to use a group of controls for container.
 */
trait Container {

	/**
	 * Group of controls for container.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_container( $key = '', $args = array() ) {
		list(
			$separator,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'separator' => 'before', // Controls separator
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'toggle' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Container Advanced', 'pediatric-clinic' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'separator' => $separator,
				)
			)
		);

		$this->start_popover();

		$default_popover_args = array_merge_recursive(
			$default_args,
			array(
				'condition' => array( $this->get_control_id_parameter( $key, 'toggle' ) => 'yes' ),
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'width' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Max Width', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 300,
							'max' => 2000,
							'step' => 10,
						),
						'%' => array(
							'min' => 10,
							'max' => 100,
						),
						'vw' => array(
							'min' => 10,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
						'%',
						'vw',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'width' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BACKGROUND,
			$default_popover_args
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BORDER,
			$default_popover_args
		);

		$this->end_popover();
	}

}
