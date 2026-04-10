<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Navigation Title Item trait.
 *
 * Allows to use a group of controls for navigation title item.
 */
trait Nav_Title_Item {

	/**
	 * Group of controls for navigation title item.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_nav_title_item( $key = '', $args = array() ) {
		list(
			$states,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'states' => array(), // Controls states
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		$this->add_controls_group(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::CONTROLS_BUTTONS,
			array_merge_recursive(
				$default_args,
				array( 'states' => $states )
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'gap' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Gap Between', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
						'rem' => array(
							'min' => 0.5,
							'max' => 4,
							'step' => 0.1,
						),
					),
					'size_units' => array(
						'px',
						'rem',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'gap' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'accent_visibility' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Accent', 'pediatric-clinic' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'none' => esc_html__( 'Hide', 'pediatric-clinic' ),
						'block' => esc_html__( 'Show', 'pediatric-clinic' ),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'accent_visibility' ) . ': {{VALUE}};',
					),
					'toggle' => true,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'child_indicator_visibility' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Child menu indicator', 'pediatric-clinic' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'none' => esc_html__( 'Hide', 'pediatric-clinic' ),
						'block' => esc_html__( 'Show', 'pediatric-clinic' ),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'child_indicator_visibility' ) . ': {{VALUE}};',
					),
					'toggle' => true,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'divider_toggle' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Divider', 'pediatric-clinic' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
				)
			)
		);

		$this->start_popover();

		$default_popover_args = array_merge_recursive(
			$default_args,
			array(
				'condition' => array( $this->get_control_id_parameter( $key, 'divider_toggle' ) => 'yes' ),
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'divider_visibility' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Visibility', 'pediatric-clinic' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'none' => esc_html__( 'Hide', 'pediatric-clinic' ),
						'block' => esc_html__( 'Show', 'pediatric-clinic' ),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'divider_visibility' ) . ': {{VALUE}};',
					),
					'toggle' => true,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'divider_color' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Color', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'divider_color' ) . ': {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'divider_visibility' ) => 'block',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'divider_width' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Width', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'divider_width' ) . ': {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'divider_visibility' ) => 'block',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'divider_height' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Height', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'divider_height' ) . ': {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'divider_visibility' ) => 'block',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'divider_bd_radius' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Border Radius', 'pediatric-clinic' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'divider_bd_radius' ) . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'divider_visibility' ) => 'block',
					),
				)
			)
		);

		$this->end_popover();
	}

}
