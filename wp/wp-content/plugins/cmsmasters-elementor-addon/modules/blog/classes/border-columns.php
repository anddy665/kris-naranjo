<?php
namespace CmsmastersElementor\Modules\Blog\Classes;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Border Columns.
 *
 * Border Separator for grid.
 *
 * @since 1.0.0
 */
class Border_Columns {

	/**
	 * Addon widget.
	 *
	 * @since 1.0.0
	 *
	 * @var Base_Widget Addon base widget class.
	 */
	private $widget;

	/**
	 * Border Columns initial class constructor.
	 *
	 * @param Base_Widget $widget
	 */
	public function __construct( $widget ) {
		$this->widget = $widget;
	}

	/**
	 * Register border columns controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	public function add_controls() {
		$this->widget->add_control(
			'border_columns_type',
			array(
				'label' => __( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'solid' => __( 'Solid', 'cmsmasters-elementor' ),
					'double' => __( 'Double', 'cmsmasters-elementor' ),
					'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
					'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
					'groove' => __( 'Groove', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-border-columns' => 'border-style: {{VALUE}};',
				),
				'frontend_available' => true,
			)
		);

		$this->widget->add_control(
			'border_columns_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-border-columns' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'border_columns_type!' => '',
				),
			)
		);

		$this->widget->add_responsive_control(
			'border_vertical_width',
			array(
				'label' => __( 'Vertical Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 1,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 5,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-border-columns' => 'border-right-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'border_columns_type!' => '',
				),
				'frontend_available' => true,
			)
		);
	}
}
