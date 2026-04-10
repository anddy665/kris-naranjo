<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Content trait.
 *
 * Allows to use a group of controls for content.
 */
trait Content {

	/**
	 * Group of controls for content.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_content( $key = '', $args = array() ) {
		list(
			$separator,
			$elementor_padding,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'separator' => 'before', // Controls separator
			'elementor_padding' => false, // Inject elementor paddings to settings
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
					'label' => esc_html__( 'Content Advanced', 'pediatric-clinic' ),
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

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'padding' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Padding', 'pediatric-clinic' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
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

		if ( $elementor_padding ) {
			$this->add_responsive_control(
				$this->get_control_name_parameter( $key, 'elementor_padding' ),
				array_merge_recursive(
					$default_popover_args,
					array(
						'label' => esc_html__( 'Elementor page custom padding', 'pediatric-clinic' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'elementor_padding_top' ) . ': {{TOP}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( $key, 'elementor_padding_right' ) . ': {{RIGHT}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( $key, 'elementor_padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( $key, 'elementor_padding_left' ) . ': {{LEFT}}{{UNIT}};',
						),
					)
				)
			);
		}

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BORDER,
			$default_popover_args
		);

		$this->end_popover();
	}

}
