<?php
namespace CmsmastersElementor\Modules\Ribbon;

use CmsmastersElementor\Base\Base_Module;
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Base_Module {
	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters Blog module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'ribbon';
	}

	/**
	 * Init actions.
	 *
	 * Initialize module actions.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	protected function init_actions() {
		add_action( 'elementor/element/common/_section_responsive/after_section_end', array( $this, 'register_controls_cmsmasters_ribbon' ) );
		add_action( 'elementor/widget/before_render_content', array( $this, 'render_cmsmasters_ribbon' ) );
		add_filter( 'elementor/widget/print_template', function ( $print_template ) {
			if ( ! empty( $print_template ) ) {
				$print_template = $this->render_cmsmasters_ribbon_template() . $print_template;
			}

			return $print_template;
		} );
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the module.
	 *
	 * @since 1.0.0
	 */
	public function register_controls_cmsmasters_ribbon( Widget_Base $element ) {
		$element->start_controls_section(
			'cmsmasters_section_ribbon',
			array(
				'label' => __( 'Ribbon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
			)
		);

		$element->add_control(
			'cmsmasters_ribbon_show',
			array(
				'label' => __( 'Show Ribbon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'render_type' => 'template',
			)
		);

		$element->add_control(
			'cmsmasters_ribbon_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'New',
				'dynamic' => array(
					'active' => true,
				),
				'separator' => 'before',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_control(
			'cmsmasters_ribbon_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'line' => __( 'Line', 'cmsmasters-elementor' ),
					'square' => __( 'Square', 'cmsmasters-elementor' ),
					'circle' => __( 'Circle', 'cmsmasters-elementor' ),
					'triangle' => __( 'Triangle', 'cmsmasters-elementor' ),
					'sloping_line' => __( 'Sloping Line', 'cmsmasters-elementor' ),
				),
				'default' => 'triangle',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_control(
			'cmsmasters_ribbon_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'top_left' => __( 'Top Left', 'cmsmasters-elementor' ),
					'top_right' => __( 'Top Right', 'cmsmasters-elementor' ),
					'bottom_left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
					'bottom_right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
				),
				'default' => 'top_left',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_control(
			'cmsmasters_ribbon_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array(
					'unit' => 'px',
					'size' => 50,
				),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-type-line .cmsmasters-ribbon-inner' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-type-square .cmsmasters-ribbon-inner' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-type-circle .cmsmasters-ribbon-inner' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array(
					'cmsmasters_ribbon_type' => array(
						'line',
						'square',
						'circle',
					),
				),
			)
		);

		$element->add_responsive_control(
			'cmsmasters_ribbon_triangle_distance',
			array(
				'label' => __( 'Distance', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 30,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-top_right .cmsmasters-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' ),
					'{{WRAPPER}} .cmsmasters-ribbon-top_left .cmsmasters-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' ),
					'{{WRAPPER}} .cmsmasters-ribbon-bottom_right .cmsmasters-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' ),
					'{{WRAPPER}} .cmsmasters-ribbon-bottom_left .cmsmasters-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' ),
				),
				'separator' => 'before',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
					'cmsmasters_ribbon_type' => array(
						'sloping_line',
						'triangle',
					),
				),
			)
		);

		$element->add_responsive_control(
			'cmsmasters_ribbon_line_distance_x',
			array(
				'label' => __( 'Distance X', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 15,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-bottom_right' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-top_right' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-bottom_left' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-top_left' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
					'cmsmasters_ribbon_type' => array(
						'line',
						'square',
						'circle',
					),
				),
			)
		);

		$element->add_responsive_control(
			'cmsmasters_ribbon_line_distance_y',
			array(
				'label' => __( 'Distance Y', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 15,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-bottom_right' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-top_right' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-bottom_left' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-top_left' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
					'cmsmasters_ribbon_type' => array(
						'line',
						'square',
						'circle',
					),
				),
			)
		);

		$element->add_control(
			'cmsmasters_ribbon_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-inner' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-ribbon-type-triangle .cmsmasters-ribbon-inner:before' => 'background-color: {{VALUE}}',
				),
				'separator' => 'before',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_control(
			'cmsmasters_ribbon_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-inner' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-ribbon-inner',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'cmsmasters_ribbon_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-ribbon-inner',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'cmsmasters_ribbon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'separator' => 'before',
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
				),
			)
		);

		$element->add_responsive_control(
			'cmsmasters_ribbon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-ribbon-type-line .cmsmasters-ribbon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-ribbon-type-square .cmsmasters-ribbon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'cmsmasters_ribbon_show' => 'yes',
					'cmsmasters_ribbon_type' => array(
						'line',
						'square',
					),
				),
			)
		);

		$element->end_controls_section();
	}

		/**
	 * Render image box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	public function render_cmsmasters_ribbon( Widget_Base $element ) {
		$settings = $element->get_settings_for_display();

		if ( ! empty( $settings['cmsmasters_ribbon_show'] ) && 'yes' === $settings['cmsmasters_ribbon_show'] ) {
			$element->add_render_attribute( 'cmsmasters_ribbon-wrapper', 'class', 'cmsmasters-ribbon' );

			if ( ! empty( $settings['cmsmasters_ribbon_position'] ) ) {
				$element->add_render_attribute( 'cmsmasters_ribbon-wrapper', 'class', 'cmsmasters-ribbon-' . $settings['cmsmasters_ribbon_position'] );
			}

			if ( ! empty( $settings['cmsmasters_ribbon_type'] ) ) {
				$element->add_render_attribute( 'cmsmasters_ribbon-wrapper', 'class', 'cmsmasters-ribbon-type-' . $settings['cmsmasters_ribbon_type'] );
			}

			echo '<div ' . $element->get_render_attribute_string( 'cmsmasters_ribbon-wrapper' ) . '>
				<div class="cmsmasters-ribbon-inner"><span class="cmsmasters-ribbon-inner-text">' . esc_html( $settings['cmsmasters_ribbon_title'] ) . '</span></div>
			</div>';
		}
	}

	public function render_cmsmasters_ribbon_template() {
		return "<#
			if ( settings.cmsmasters_ribbon_show ) {
				view.addRenderAttribute( 'cmsmasters_ribbon-wrapper', 'class', 'cmsmasters-ribbon' );

			if ( '' !== settings.cmsmasters_ribbon_position ) {
				view.addRenderAttribute( 'cmsmasters_ribbon-wrapper', 'class', 'cmsmasters-ribbon-' + settings.cmsmasters_ribbon_position );
			}

			if ( '' !== settings.cmsmasters_ribbon_type ) {
				view.addRenderAttribute( 'cmsmasters_ribbon-wrapper', 'class', 'cmsmasters-ribbon-type-' + settings.cmsmasters_ribbon_type );
			}

			#>
			<div {{{ view.getRenderAttributeString( 'cmsmasters_ribbon-wrapper' ) }}} >
				<div class=\"cmsmasters-ribbon-inner\"><span class=\"cmsmasters-ribbon-inner-text\">{{{ settings.cmsmasters_ribbon_title }}}</span></div>
			</div>
			<#
		}
		#>";
	}
}
