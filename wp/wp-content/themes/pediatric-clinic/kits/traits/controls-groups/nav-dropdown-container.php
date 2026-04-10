<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Navigation Dropdown Container trait.
 *
 * Allows to use a group of controls for navigation dropdown container.
 */
trait Nav_Dropdown_Container {

	/**
	 * Group of controls for navigation dropdown container.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_nav_dropdown_container( $key = '', $args = array() ) {
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

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'width' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Width', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 500,
						),
					),
					'size_units' => array(
						'px',
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
			$default_args
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BORDER,
			$default_args
		);

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

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BOX_SHADOW
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
						'%',
					),
					'allowed_dimensions' => array( 'top', 'bottom' ),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'padding_top' ) . ': {{TOP}}{{UNIT}};' .
							'--' . $this->get_control_prefix_parameter( $key, 'padding_bottom' ) . ': {{BOTTOM}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'first_dropdown_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'First Dropdown', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'position' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Position', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'center' => esc_html__( 'Below Item', 'pediatric-clinic' ),
						'stretch' => esc_html__( 'Below Header', 'pediatric-clinic' ),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'position' ) . ': {{VALUE}};',
					),
					'toggle' => true,
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'hor_pos' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Horizontal Position', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'hor_pos' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'vert_gap' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Vertical Gap', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'vert_gap' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'second_dropdown_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Second Dropdown', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'hor_gap' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Horizontal Gap', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'hor_gap' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);
	}

}
