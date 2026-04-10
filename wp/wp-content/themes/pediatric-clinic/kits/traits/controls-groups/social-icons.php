<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;
use Elementor\Repeater;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Social Icons trait.
 *
 * Allows to use a group of controls for social icons.
 */
trait Social_Icons {

	/**
	 * Group of controls for social icons.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_social_icons( $key = '', $args = array() ) {
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

		$this->controls_group_social_icons_repeater( $key, $args );

		$this->start_controls_tabs(
			$this->get_control_name_parameter( $key, 'states_tabs' ),
			array_merge_recursive(
				$default_args,
				array( 'separator' => 'before' )
			)
		);

		foreach ( $states as $state_key => $state_label ) {
			$this->start_controls_tab(
				$this->get_control_name_parameter( $key, "states_{$state_key}_tab" ),
				array( 'label' => $state_label )
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, "{$state_key}_colors_color" ),
				array(
					'label' => esc_html__( 'Color', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, "{$state_key}_colors_color" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, "{$state_key}_colors_bg" ),
				array(
					'label' => esc_html__( 'Background', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, "{$state_key}_colors_bg" ) . ': {{VALUE}};',
					),
				)
			);

			$this->add_control(
				$this->get_control_name_parameter( $key, "{$state_key}_colors_bd" ),
				array(
					'label' => esc_html__( 'Border', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, "{$state_key}_colors_bd" ) . ': {{VALUE}};',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'size' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Icons Size', 'pediatric-clinic' ),
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
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'size' ) . ': {{SIZE}}{{UNIT}};',
					),
					'separator' => 'before',
				)
			)
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BORDER,
			array_merge_recursive(
				$default_args,
				array(
					'exclude' => array( 'color' ),
				)
			)
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

		$this->add_responsive_control(
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

	/**
	 * Social icons controls group repeater.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_social_icons_repeater( $key = '', $args = array() ) {
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

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'show_label' => false,
				'type' => Controls_Manager::ICONS,
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label' => esc_html__( 'Title', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Icon title', 'pediatric-clinic' ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label' => esc_html__( 'Link', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::URL,
				'default' => array(
					'is_external' => 'true',
				),
			)
		);

		$repeater->add_control(
			'states_tabs_heading_control',
			array(
				'label' => esc_html__( 'States', 'pediatric-clinic' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$repeater->start_controls_tabs( 'states_tabs' );

		$parent_class = str_replace( '_', '-', $this->get_control_id( $key ) );

		foreach ( $states as $state_key => $state_label ) {
			$selector = ".{$parent_class} {{CURRENT_ITEM}}";

			if ( 'hover' === $state_key ) {
				$selector .= ':hover';
			}

			$repeater->start_controls_tab(
				"states_{$state_key}_tab",
				array(
					'label' => $state_label,
				)
			);

			$repeater->add_control(
				"{$state_key}_colors_color",
				array(
					'label' => esc_html__( 'Color', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			$repeater->add_control(
				"{$state_key}_colors_bg",
				array(
					'label' => esc_html__( 'Background', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$repeater->add_control(
				"{$state_key}_colors_bd",
				array(
					'label' => esc_html__( 'Border', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
					),
				)
			);

			$repeater->end_controls_tab();
		}

		$repeater->end_controls_tabs();

		$this->add_control(
			$this->get_control_name_parameter( $key, 'items' ),
			array_merge_recursive(
				$default_args,
				array(
					'show_label' => false,
					'type' => CmsmastersControls::CUSTOM_REPEATER,
					'fields' => $repeater->get_controls(),
					'title_field' => '<span><i class="{{{ icon.value }}}"></i> {{ title }}</span>',
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'items_notice' ),
			array_merge_recursive(
				$default_args,
				array(
					'raw' => esc_html__( 'This setting will be applied after save and reload', 'pediatric-clinic' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-control-field-description',
					'render_type' => 'ui',
				)
			)
		);
	}

}
