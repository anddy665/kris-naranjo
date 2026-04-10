<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Button with icon trait.
 *
 * Allows to use a group of controls for icon.
 */
trait Button_Icon {

	/**
	 * Group of controls for icon.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_button_icon( $key = '', $args = array() ) {
		list(
			$link,
			$states,
			$placeholder,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
			'link' => true, // Link control
			'states' => array(), // Controls states
			'placeholder' => '', // Button placeholder
			'condition' => array(), // Controls condition
			'conditions' => array(), // Controls conditions
		) );

		$default_args = array(
			'condition' => $condition,
			'conditions' => $conditions,
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'text' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Text', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => $placeholder,
					'dynamic' => array( 'active' => true ),
				)
			)
		);

		if ( $link ) {
			$this->add_control(
				$this->get_control_name_parameter( $key, 'link' ),
				array_merge_recursive(
					$default_args,
					array(
						'label' => esc_html__( 'Link', 'pediatric-clinic' ),
						'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
						'type' => Controls_Manager::URL,
						'dynamic' => array( 'active' => true ),
					)
				)
			);
		}

		$this->add_controls_group(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::CONTROLS_BUTTONS,
			array_merge_recursive(
				$default_args,
				array( 'states' => $states )
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'icon_toggle' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Icon', 'pediatric-clinic' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'separator' => 'before',
				)
			)
		);

		$this->start_popover();

		$default_popover_args = array_merge_recursive(
			$default_args,
			array(
				'condition' => array( $this->get_control_id_parameter( $key, 'icon_toggle' ) => 'yes' ),
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'icon' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::ICONS,
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_size' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Size', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
						'em' => array(
							'min' => 0.5,
							'max' => 4,
							'step' => 0.1,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_size' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_gap' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Gap', 'pediatric-clinic' ),
					'description' => esc_html__( 'Gap between icon and text.', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
						'em' => array(
							'min' => 0.5,
							'max' => 4,
							'step' => 0.1,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_gap' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'icon_position' ),
			array_merge_recursive(
				$default_popover_args,
				array(
					'label' => esc_html__( 'Position', 'pediatric-clinic' ),
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'before' => array(
							'icon' => 'eicon-h-align-left',
							'title' => esc_html__( 'Before', 'pediatric-clinic' ),
						),
						'after' => array(
							'icon' => 'eicon-h-align-right',
							'title' => esc_html__( 'After', 'pediatric-clinic' ),
						),
					),
					'default' => $this->get_default_setting(
						$this->get_control_name_parameter( $key, 'icon_position' ),
						'before'
					),
					'toggle' => false,
				)
			)
		);

		$this->end_popover();
	}

}
