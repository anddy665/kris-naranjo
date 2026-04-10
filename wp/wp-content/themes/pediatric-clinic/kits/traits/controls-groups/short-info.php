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
 * Short Info trait.
 *
 * Allows to use a group of controls for short info.
 */
trait Short_Info {

	/**
	 * Group of controls for short info.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_short_info( $key = '', $args = array() ) {
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

		$this->controls_group_short_info_repeater( $key, $args );

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_TYPOGRAPHY,
			$default_args
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Colors', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_text' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Text', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_text' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_link' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Link', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_link' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_hover' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Link Hover', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_hover' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_icon' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Icon', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_icon' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_divider' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Divider', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_divider' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'items_gap' ),
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
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'items_gap' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'icon_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Icon', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_size' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Size', 'pediatric-clinic' ),
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
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_size' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_gap' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Gap', 'pediatric-clinic' ),
					'description' => esc_html__( 'Gap between icon and text', 'pediatric-clinic' ),
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
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_gap' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'icon_position' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Position', 'pediatric-clinic' ),
					'label_block' => false,
					'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'before' => array(
							'title' => esc_html__( 'Before', 'pediatric-clinic' ),
						),
						'after' => array(
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
	 * Short info controls group repeater.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_short_info_repeater( $key = '', $args = array() ) {
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

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label' => esc_html__( 'Text', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label' => esc_html__( 'Link', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::URL,
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label' => esc_html__( 'Icon', 'pediatric-clinic' ),
				'description' => esc_html__( 'This setting will be applied after save and reload.', 'pediatric-clinic' ),
				'type' => Controls_Manager::ICONS,
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'items' ),
			array_merge_recursive(
				$default_args,
				array(
					'show_label' => false,
					'type' => CmsmastersControls::CUSTOM_REPEATER,
					'fields' => $repeater->get_controls(),
					'title_field' => '<span><i class="{{{ icon.value }}}"></i> {{ text }}</span>',
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
