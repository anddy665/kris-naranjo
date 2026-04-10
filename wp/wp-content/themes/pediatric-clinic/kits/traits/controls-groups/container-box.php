<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Container Box trait.
 *
 * Allows to use a group of controls for container box.
 */
trait Container_Box {

	/**
	 * Group of controls for container box.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_container_box( $key = '', $args = array() ) {
		list(
			$popover,
			$popover_label,
			$excludes,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'popover' => true, // Controls in popover
			'popover_label' => esc_html__( 'Container', 'pediatric-clinic' ), // Controls popover label
			'excludes' => array(), // Array of excludes controls
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		if ( $popover ) {
			$this->add_control(
				$this->get_control_name_parameter( $key, 'toggle' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => $popover_label,
						'type' => Controls_Manager::POPOVER_TOGGLE,
						'separator' => 'before',
					)
				)
			);

			$this->start_popover();

			$default_args = array_merge_recursive(
				$default_args,
				array(
					'condition' => array( $this->get_control_id_parameter( $key, 'toggle' ) => 'yes' ),
				)
			);
		}

		if ( ! in_array( 'alignment', $excludes, true ) ) {
			$this->add_control(
				$this->get_control_name_parameter( $key, 'alignment' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Alignment', 'pediatric-clinic' ),
						'type' => Controls_Manager::CHOOSE,
						'label_block' => false,
						'options' => array(
							'left' => array(
								'icon' => 'eicon-text-align-left',
								'title' => esc_html__( 'Left', 'pediatric-clinic' ),
							),
							'center' => array(
								'icon' => 'eicon-text-align-center',
								'title' => esc_html__( 'Center', 'pediatric-clinic' ),
							),
							'right' => array(
								'icon' => 'eicon-text-align-right',
								'title' => esc_html__( 'Right', 'pediatric-clinic' ),
							),
						),
						'toggle' => true,
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'alignment' ) . ': {{VALUE}};',
						),
					)
				)
			);
		}

		if ( ! in_array( 'bg_color', $excludes, true ) ) {
			$this->add_control(
				$this->get_control_name_parameter( $key, 'bg_color' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Background Color', 'pediatric-clinic' ),
						'type' => Controls_Manager::COLOR,
						'dynamic' => array(),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'bg_color' ) . ': {{VALUE}};',
						),
					)
				)
			);
		}

		if ( ! in_array( 'border', $excludes, true ) ) {
			$this->add_var_group_control(
				$this->get_control_name_parameter( $key ),
				Settings_Tab_Base::VAR_BORDER,
				$default_args
			);
		}

		if ( ! in_array( 'bd_radius', $excludes, true ) ) {
			$this->add_control(
				$this->get_control_name_parameter( $key, 'bd_radius' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Border Radius', 'pediatric-clinic' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'bd_radius' ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				)
			);
		}

		if ( ! in_array( 'box_shadow', $excludes, true ) ) {
			$this->add_var_group_control(
				$this->get_control_name_parameter( $key ),
				Settings_Tab_Base::VAR_BOX_SHADOW,
				$default_args
			);
		}

		if ( ! in_array( 'padding', $excludes, true ) ) {
			$this->add_responsive_control(
				$this->get_control_name_parameter( $key, 'padding' ),
				array_merge_recursive(
					$default_args,
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
		}

		if ( ! in_array( 'margin', $excludes, true ) ) {
			$this->add_responsive_control(
				$this->get_control_name_parameter( $key, 'margin' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Margin', 'pediatric-clinic' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							':root' => '--' . $this->get_control_prefix_parameter( $key, 'margin_top' ) . ': {{TOP}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( $key, 'margin_right' ) . ': {{RIGHT}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( $key, 'margin_bottom' ) . ': {{BOTTOM}}{{UNIT}};' .
								'--' . $this->get_control_prefix_parameter( $key, 'margin_left' ) . ': {{LEFT}}{{UNIT}};',
						),
					)
				)
			);
		}

		if ( $popover ) {
			$this->end_popover();
		}
	}

}
