<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Buttons trait.
 *
 * Allows to use a group of controls for buttons.
 */
trait Buttons {

	/**
	 * Group of controls for buttons.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_buttons( $key = '', $args = array() ) {
		list(
			$states,
			$typography_group,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'states' => array(), // Controls states
			'typography_group' => true, // Typography group controls
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		if ( $typography_group ) {
			$this->add_var_group_control(
				$this->get_control_name_parameter( $key ),
				Settings_Tab_Base::VAR_TYPOGRAPHY,
				$default_args
			);
		}

		$text_decoration_states = $states;
		unset( $text_decoration_states['normal'] );
		$text_decoration_states = array_keys( $text_decoration_states );

		$this->add_controls_group(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::CONTROLS_STATES,
			array_merge_recursive(
				$default_args,
				array(
					'states' => $states,
					'background' => 'gradient',
					'text_decoration_states' => $text_decoration_states,
					'text_shadow' => true,
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'padding' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Padding', 'pediatric-clinic' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'padding_top' ) . ': {{TOP}}{{UNIT}};' .
							'--' . $this->get_control_prefix_parameter( $key, 'padding_right' ) . ': {{RIGHT}}{{UNIT}};' .
							'--' . $this->get_control_prefix_parameter( $key, 'padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
							'--' . $this->get_control_prefix_parameter( $key, 'padding_left' ) . ': {{LEFT}}{{UNIT}};',
					),
				)
			)
		);
	}

}
