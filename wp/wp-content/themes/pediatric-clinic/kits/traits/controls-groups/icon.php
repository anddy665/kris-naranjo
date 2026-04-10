<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Icon trait.
 *
 * Allows to use a group of controls for icon.
 */
trait Icon {

	/**
	 * Group of controls for icon.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_icon( $key = '', $args = array() ) {
		list(
			$states,
			$icon_states,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'states' => array(), // Controls states
			'icon_states' => array(), // array - states where show icon control; 'out' - if icon control is out of states; empty array - if icon control isn't in use.
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		$this->add_controls_group(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::CONTROLS_STATES,
			array_merge_recursive(
				$default_args,
				array(
					'states' => $states,
					'icon_states' => $icon_states,
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_size' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Icon Size', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_size' ) . ': {{SIZE}}{{UNIT}};',
					),
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

		if ( ! empty( $icon_states ) ) {
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

}
