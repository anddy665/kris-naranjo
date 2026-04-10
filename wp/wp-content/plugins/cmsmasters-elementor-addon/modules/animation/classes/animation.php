<?php
namespace CmsmastersElementor\Modules\Animation\Classes;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Animation {
	/**
	 * Register widget controls.
	 *
	 * Adds base animation controls.
	 *
	 * @since 1.0.0
	 * @since 1.6.0 Added size controls for animation text.
	 */
	public static function register_section_animation( Base_Widget $element, $is_multiple, $condition ) {
		if ( true === $is_multiple ) {
			$pointer_options = array(
				'none' => __( 'None', 'cmsmasters-elementor' ),
				'underline' => __( 'Underline', 'cmsmasters-elementor' ),
				'overline' => __( 'Overline', 'cmsmasters-elementor' ),
				'background' => __( 'Background', 'cmsmasters-elementor' ),
				'text' => __( 'Text', 'cmsmasters-elementor' ),
			);
		} else {
			$pointer_options = array(
				'none' => __( 'None', 'cmsmasters-elementor' ),
				'underline' => __( 'Underline', 'cmsmasters-elementor' ),
				'overline' => __( 'Overline', 'cmsmasters-elementor' ),
				'background' => __( 'Background', 'cmsmasters-elementor' ),
			);
		}

		$element->start_controls_section(
			'animation',
			array(
				'label' => __( 'Pointer Animation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => $condition,
			)
		);

		$element->add_control(
			'pointer',
			array(
				'label' => __( 'Hover Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => $pointer_options,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-pointer-',
				'style_transfer' => true,
			)
		);

		$element->add_control(
			'animation_line',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide' => 'Slide',
					'grow' => 'Grow',
					'drop-in' => 'Drop In',
					'drop-out' => 'Drop Out',
					'none' => 'None',
				),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-animation-',
				'condition' => array(
					'pointer' => array( 'overline' ),
				),
			)
		);

		$element->add_control(
			'animation_underline',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => array(
					'slide' => 'Slide',
					'grow' => 'Grow',
					'drop-in' => 'Drop In',
					'drop-out' => 'Drop Out',
					'none' => 'None',
				),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-animation-',
				'condition' => array(
					'pointer' => array( 'underline' ),
				),
			)
		);

		$element->add_control(
			'animation_background',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'sweep-filling' => array(
						'title' => __( 'Sweep', 'cmsmasters-elementor' ),
						'description' => __( 'Sweep Filling', 'cmsmasters-elementor' ),
					),
					'grow' => array(
						'title' => __( 'Grow', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'sweep-filling',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-animation-',
				'condition' => array( 'pointer' => 'background' ),
			)
		);

		$element->add_control(
			'animation_text',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'grow',
				'options' => array(
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sink' => 'Sink',
					'float' => 'Float',
					'skew' => 'Skew',
					'rotate' => 'Rotate',
				),
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-animation-',
				'condition' => array(
					'pointer' => 'text',
				),
			)
		);

		$element->add_control(
			'animation_grow_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1.05,
						'max' => 1.3,
						'step' => 0.05,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 1.2,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-animation_grow_size: {{SIZE}};',
				),
				'condition' => array(
					'pointer' => 'text',
					'animation_text' => 'grow',
				),
			)
		);

		$element->add_control(
			'animation_shrink_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0.7,
						'max' => 0.95,
						'step' => 0.05,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 0.8,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-animation_shrink_size: {{SIZE}};',
				),
				'condition' => array(
					'pointer' => 'text',
					'animation_text' => 'shrink',
				),
			)
		);

		$element->add_control(
			'animation_sink_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => 8,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-animation_sink_size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'pointer' => 'text',
					'animation_text' => 'sink',
				),
			)
		);

		$element->add_control(
			'animation_float_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => -10,
						'max' => -1,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => -8,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-animation_float_size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'pointer' => 'text',
					'animation_text' => 'float',
				),
			)
		);

		$element->add_control(
			'animation_skew_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => -20,
						'max' => 20,
					),
				),
				'default' => array(
					'unit' => 'deg',
					'size' => -8,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-animation_skew_size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'pointer' => 'text',
					'animation_text' => 'skew',
				),
			)
		);

		$element->add_control(
			'animation_rotate_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'range' => array(
					'deg' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'default' => array(
					'unit' => 'deg',
					'size' => 6,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-animation_rotate_size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'pointer' => 'text',
					'animation_text' => 'rotate',
				),
			)
		);

		$element->add_control(
			'animation_filling_direction',
			array(
				'label' => __( 'Sweep Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'sweep-left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'description' => __( 'Sweep Left', 'cmsmasters-elementor' ),
					),
					'sweep-right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'description' => __( 'Sweep Right', 'cmsmasters-elementor' ),
					),
					'sweep-top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'description' => __( 'Sweep Top', 'cmsmasters-elementor' ),
					),
					'sweep-bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'description' => __( 'Sweep Bottom', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => true,
				'toggle' => false,
				'render_type' => 'template',
				'default' => 'sweep-top',
				'prefix_class' => 'cmsmasters-direction-',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '=',
							'value' => 'background',
						),
						array(
							'name' => 'animation_background',
							'operator' => '=',
							'value' => 'sweep-filling',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_background_side',
			array(
				'label' => __( 'Side', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Left', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'Right', 'cmsmasters-elementor' ) ),
					'top' => array( 'title' => __( 'Top', 'cmsmasters-elementor' ) ),
					'bottom' => array( 'title' => __( 'Bottom', 'cmsmasters-elementor' ) ),
				),
				'label_block' => true,
				'toggle' => false,
				'render_type' => 'template',
				'default' => 'bottom',
				'prefix_class' => 'cmsmasters-animation-side-',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '=',
							'value' => 'background',
						),
						array(
							'name' => 'animation_background',
							'operator' => '=',
							'value' => 'advanced-filling-xy',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_background_position_y',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => array( 'title' => __( 'Top', 'cmsmasters-elementor' ) ),
					'center' => array( 'title' => __( 'Center', 'cmsmasters-elementor' ) ),
					'bottom' => array( 'title' => __( 'Bottom', 'cmsmasters-elementor' ) ),
				),
				'label_block' => true,
				'toggle' => false,
				'default' => 'center',
				'selectors_dictionary' => array(
					'top' => 'top: 0;',
					'bottom' => 'bottom: 0;',
					'center' => 'top: 0; bottom: 0;',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-animation-advanced-filling-xy .cmsmasters-animation:after' => '{{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '=',
							'value' => 'background',
						),
						array(
							'name' => 'animation_background',
							'operator' => '=',
							'value' => 'advanced-filling-xy',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'animation_background_side',
									'operator' => '=',
									'value' => 'left',
								),
								array(
									'name' => 'animation_background_side',
									'operator' => '=',
									'value' => 'right',
								),
							),
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_background_position_x',
			array(
				'label' => __( 'Start Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Left', 'cmsmasters-elementor' ) ),
					'center' => array( 'title' => __( 'Center', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'Right', 'cmsmasters-elementor' ) ),
				),
				'label_block' => true,
				'toggle' => false,
				'default' => 'center',
				'selectors_dictionary' => array(
					'left' => 'left: 0;',
					'right' => 'right: 0;',
					'center' => 'left: 0; right: 0;',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-animation-advanced-filling-xy .cmsmasters-animation:after' => '{{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '=',
							'value' => 'background',
						),
						array(
							'name' => 'animation_background',
							'operator' => '=',
							'value' => 'advanced-filling-xy',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'animation_background_side',
									'operator' => '=',
									'value' => 'top',
								),
								array(
									'name' => 'animation_background_side',
									'operator' => '=',
									'value' => 'bottom',
								),
							),
						),
					),
				),
			)
		);

		self::register_section_style_animation_advanced( $element, $is_multiple );

		$element->end_controls_section();
	}

	/**
	 * Register advanced widget controls.
	 *
	 * Adds additional controls, that adds lots of variations.
	 *
	 * @since 1.0.0
	 */
	public static function register_section_style_animation_advanced( Base_Widget $element, $is_multiple ) {
		$element->add_control(
			'animation_advanced_size',
			array(
				'label' => __( 'Line Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '4',
				),
				'size_units' => array( 'px', '%', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-line-size: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'background',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_line_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-line-background-position: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'background',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'pointer',
									'operator' => '=',
									'value' => 'underline',
								),
								array(
									'name' => 'pointer',
									'operator' => '=',
									'value' => 'overline',
								),
							),
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_use_gradient',
			array(
				'label' => __( 'Use Gradient', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-animation-use-gradient-',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_color_stop',
			array(
				'label' => __( 'Location', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => 0,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-color-stop: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
						array(
							'name' => 'animation_use_gradient',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_second_color',
			array(
				'label' => __( 'Second Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-second-color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
						array(
							'name' => 'animation_use_gradient',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_second_color_stop',
			array(
				'label' => __( 'Location', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-second-color-stop: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
						array(
							'name' => 'animation_use_gradient',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_gradient_type',
			array(
				'label' => _x( 'Type', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'linear' => _x( 'Linear', 'Background Control', 'cmsmasters-elementor' ),
					'radial' => _x( 'Radial', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => 'linear',
				'prefix_class' => 'cmsmasters-color-gradient-',
				'render_type' => 'template',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
						array(
							'name' => 'animation_use_gradient',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_gradient_angle',
			array(
				'label' => _x( 'Angle', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default' => array(
					'unit' => 'deg',
					'size' => 90,
				),
				'range' => array(
					'deg' => array( 'step' => 10 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-gradient-angle: {{SIZE}}{{UNIT}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
						array(
							'name' => 'animation_use_gradient',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'name' => 'animation_gradient_type',
							'operator' => '=',
							'value' => 'linear',
						),
					),
				),
			)
		);

		$element->add_control(
			'animation_gradient_position',
			array(
				'label' => _x( 'Position', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'center center' => _x( 'Center Center', 'Background Control', 'cmsmasters-elementor' ),
					'center left' => _x( 'Center Left', 'Background Control', 'cmsmasters-elementor' ),
					'center right' => _x( 'Center Right', 'Background Control', 'cmsmasters-elementor' ),
					'top center' => _x( 'Top Center', 'Background Control', 'cmsmasters-elementor' ),
					'top left' => _x( 'Top Left', 'Background Control', 'cmsmasters-elementor' ),
					'top right' => _x( 'Top Right', 'Background Control', 'cmsmasters-elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'cmsmasters-elementor' ),
					'bottom left' => _x( 'Bottom Left', 'Background Control', 'cmsmasters-elementor' ),
					'bottom right' => _x( 'Bottom Right', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => 'center center',
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-gradient-radial: at {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'text',
						),
						array(
							'name' => 'animation_use_gradient',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'name' => 'animation_gradient_type',
							'operator' => '=',
							'value' => 'radial',
						),
					),
				),
			)
		);

		$element->add_control(
			'divider_after_gradient',
			array(
				'type' => Controls_Manager::DIVIDER,
				'condition' => array( 'animation_use_gradient' => 'yes' ),
			)
		);

		$element->add_control(
			'animation_advanced_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 0.3 ),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--animation-transition-duration: {{SIZE}}s;',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'pointer',
							'operator' => '!==',
							'value' => 'none',
						),
					),
				),
			)
		);
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	public static function register_sections_controls( Base_Widget $element, $is_multiple = true, $condition = '' ) {
		self::register_section_animation( $element, $is_multiple, $condition );
	}

	/**
	 * Get animation class.
	 *
	 * @since 1.0.0
	 */
	public static function get_animation_class() {
		$class = 'cmsmasters-animation';

		return $class;
	}
}
