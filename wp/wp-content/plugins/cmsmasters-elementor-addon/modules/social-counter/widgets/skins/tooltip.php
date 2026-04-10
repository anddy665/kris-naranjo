<?php
namespace CmsmastersElementor\Modules\SocialCounter\Widgets\Skins;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Tooltip extends Base {

	/**
	 * @since 1.0.0
	 */
	public function get_id() {
		return 'tooltip';
	}

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'ToolTip', 'cmsmasters-elementor' );
	}

	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Fixed notice "_skin".
	 */
	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->parent->start_injection(
			array( 'of' => 'color_numbers_hover' )
		);

		$this->add_responsive_control(
			'tooltip_numbers_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'default' => array(
					'top' => '15',
					'right' => '0',
					'bottom' => '10',
					'left' => '0',
					'isLinked' => false,
				),
				'selectors' => array(
					'{{WRAPPER}} .social-numbers' => '--tooltip-top-spacing: {{TOP}}{{UNIT}}; --tooltip-right-spacing: {{RIGHT}}{{UNIT}}; --tooltip-bottom-spacing: {{BOTTOM}}{{UNIT}}; --tooltip-left-spacing: {{LEFT}}{{UNIT}};',
				),
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->parent->end_injection();

		$this->register_controls_style_tooltip();
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_default_column() {
		return '0';
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_default_order() {
		return array( 'numbers', 'icon' );
	}

	/**
	 * Register control.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls_style_tooltip() {
		$this->start_controls_section(
			'section_style_tooltip',
			array(
				'label' => __( 'Tooltip', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'tooltip_bg_normal',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .social-link .social-numbers span' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'tooltip_bg_hover',
			array(
				'label' => __( 'Hover Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .social-link:hover .social-numbers span' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				),
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'tooltip_arrow_direction',
			array(
				'label' => __( 'Arrow Direction', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
				),
				'default' => 'top',
				'toggle' => false,
				'prefix_class' => 'cmsmasters--tooltip-direction-',
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_responsive_control(
			'tooltip_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .social-numbers span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'tooltip_arrow_size',
			array(
				'label' => __( 'Arrow Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-social-counter' => '--tooltip-arrow-size: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->add_control(
			'tooltip_bdrs',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 10,
						),
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .social-link .social-numbers span' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'conditions' => $this->parent->controls_contains_conditions( 'numbers' ),
			)
		);

		$this->end_controls_section();
	}
}
