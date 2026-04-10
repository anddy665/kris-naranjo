<?php
namespace PediatricClinicSpace\Kits\Traits\ControlsGroups;

use PediatricClinicSpace\Kits\Controls\Controls_Manager as CmsmastersControls;
use PediatricClinicSpace\Kits\Settings\Base\Settings_Tab_Base;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Quotes trait.
 *
 * Allows to use a group of controls for quotes.
 */
trait Quotes {

	/**
	 * Group of controls for quotes.
	 *
	 * @param string $key Controls key.
	 * @param array $args Controls args.
	 */
	protected function controls_group_quotes( $key = '', $args = array() ) {
		list(
			$typography_group,
			$condition,
			$conditions
		) = $this->get_controls_group_required_args( $args, array(
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
			$this->get_control_name_parameter( $key, 'colors_bg' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Background', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_bg' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'colors_bd' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Border', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'colors_bd' ) . ': {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'border_border!' ) => 'none',
					),
				)
			)
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BORDER,
			array_merge_recursive(
				$default_args,
				array(
					'fields_options' => array(
						'width' => array(
							'label' => esc_html__( 'Border Width', 'pediatric-clinic' ),
						),
					),
					'exclude' => array( 'color' ),
					'separator' => 'before',
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

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key ),
			Settings_Tab_Base::VAR_BOX_SHADOW,
			$default_args
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

		$this->add_control(
			$this->get_control_name_parameter( $key, 'cite_heading_control' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Cite', 'pediatric-clinic' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			)
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key, 'cite' ),
			Settings_Tab_Base::VAR_TYPOGRAPHY,
			$default_args
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'cite_color' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Color', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'cite_color' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'cite_gap' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Gap Between', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 50,
						),
						'%' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'cite_gap' ) . ': {{SIZE}}{{UNIT}};',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'icon_visibility' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Icon', 'pediatric-clinic' ),
					'label_block' => false,
					'toggle' => true,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'separator' => 'before',
					'options' => array(
						'none' => esc_html__( 'Hide', 'pediatric-clinic' ),
						'flex' => esc_html__( 'Show', 'pediatric-clinic' ),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_visibility' ) . ': {{VALUE}};',
					),
				)
			)
		);

		$this->add_var_group_control(
			$this->get_control_name_parameter( $key, 'icon' ),
			Settings_Tab_Base::VAR_TYPOGRAPHY,
			array_merge_recursive(
				$default_args,
				array(
					'exclude' => array(
						'text_transform',
						'font_style',
						'text_decoration',
						'line_height',
						'letter_spacing',
						'word_spacing',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'icon_visibility' ) => 'flex',
					),
				)
			)
		);

		$this->add_control(
			$this->get_control_name_parameter( $key, 'icon_color' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Color', 'pediatric-clinic' ),
					'type' => Controls_Manager::COLOR,
					'dynamic' => array(),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_color' ) . ': {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'icon_visibility' ) => 'flex',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_horizontal_alignment' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Horizontal Alignment', 'pediatric-clinic' ),
					'label_block' => false,
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'flex-start' => array(
							'title' => esc_html__( 'Start', 'pediatric-clinic' ),
							'icon' => 'eicon-h-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'pediatric-clinic' ),
							'icon' => 'eicon-h-align-center',
						),
						'flex-end' => array(
							'title' => esc_html__( 'End', 'pediatric-clinic' ),
							'icon' => 'eicon-h-align-right',
						),
					),
					'toggle' => true,
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_horizontal_alignment' ) . ': {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'icon_visibility' ) => 'flex',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_horizontal_offset' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Horizontal Offset', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array(
						'px',
					),
					'range' => array(
						'px' => array(
							'min' => -100,
							'max' => 100,
						),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_horizontal_offset' ) . ': {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'icon_visibility' ) => 'flex',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_vertical_alignment' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Vertical Alignment', 'pediatric-clinic' ),
					'label_block' => false,
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'flex-start' => array(
							'title' => esc_html__( 'Start', 'pediatric-clinic' ),
							'icon' => 'eicon-v-align-top',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'pediatric-clinic' ),
							'icon' => ' eicon-v-align-middle',
						),
						'flex-end' => array(
							'title' => esc_html__( 'End', 'pediatric-clinic' ),
							'icon' => 'eicon-v-align-bottom',
						),
					),
					'toggle' => true,
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_vertical_alignment' ) . ': {{VALUE}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'icon_visibility' ) => 'flex',
					),
				)
			)
		);

		$this->add_responsive_control(
			$this->get_control_name_parameter( $key, 'icon_vertical_offset' ),
			array_merge_recursive(
				$default_args,
				array(
					'label' => esc_html__( 'Vertical Offset', 'pediatric-clinic' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array(
						'px',
					),
					'range' => array(
						'px' => array(
							'min' => -1000,
							'max' => 1000,
						),
					),
					'selectors' => array(
						':root' => '--' . $this->get_control_prefix_parameter( $key, 'icon_vertical_offset' ) . ': {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						$this->get_control_id_parameter( $key, 'icon_visibility' ) => 'flex',
					),
				)
			)
		);
	}

}
