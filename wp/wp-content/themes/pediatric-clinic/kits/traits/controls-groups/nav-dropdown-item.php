<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Navigation Dropdown Item trait.
 *
 * Allows to use a group of controls for navigation dropdown item.
 */
trait Nav_Dropdown_Item {

	/**
	 * Group of controls for navigation dropdown item.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_nav_dropdown_item( $key = '', $args = array() ) {
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
			Settings_Tab_Base::CONTROLS_NAV_ITEM,
			array_merge_recursive(
				$default_args,
				array( 'states' => $states )
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
	}

}
