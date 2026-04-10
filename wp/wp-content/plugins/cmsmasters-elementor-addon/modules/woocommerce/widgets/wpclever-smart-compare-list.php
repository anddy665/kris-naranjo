<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Wpclever_Smart_Compare_list extends Base_Widget {

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.11.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array(
			Base_Document::WOO_WIDGETS_CATEGORY,
		);
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.11.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return 'cmsmasters-wpclever-compare-list';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Wpclever Smart Compare List', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.11.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-compare-list';
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the widget keywords.
	 *
	 * @since 1.11.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'compare',
			'list',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.16.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-woocommerce',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.11.0
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_wpclever_compare_style',
			array(
				'label' => __( 'Header', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$header_th = '#cmsmasters_body {{WRAPPER}} .woosc_list table.woosc_table thead th';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_th_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $header_th . ' a',
			)
		);

		$this->add_control(
			'cm_thead_title_color',
			array(
				'label' => esc_html__( 'Title Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$header_th . ' a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cm_thead_title_color_hover',
			array(
				'label' => esc_html__( 'Title Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$header_th . ' a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cm_thead_remove_color',
			array(
				'label' => esc_html__( 'Remove Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$header_th . ' .woosc-remove' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cm_thead_remove_color_hover',
			array(
				'label' => esc_html__( 'Remove Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$header_th . ' .woosc-remove:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cm_thead_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$header_th => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'cm_thead_padding',
			array(
				'label' => esc_html__( 'Items Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors' => array(
					$header_th => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_compare_body_style',
			array(
				'label' => __( 'Body', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$body_td = '#cmsmasters_body {{WRAPPER}} .woosc_list table.woosc_table td:not(.woocommerce-product-attributes-item__value)';
		$body_td_odd = '#cmsmasters_body {{WRAPPER}} .woosc_list table.woosc_table tbody tr:nth-child(odd) td:not(.woocommerce-product-attributes-item__value)';
		$body_td_even = '#cmsmasters_body {{WRAPPER}} .woosc_list table.woosc_table tbody tr:nth-child(even) td:not(.woocommerce-product-attributes-item__value)';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_td_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $body_td,
			)
		);

		$this->add_control(
			'cm_body_label_color',
			array(
				'label' => esc_html__( 'Label Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$body_td . '.td-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cm_body_text_color',
			array(
				'label' => esc_html__( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$body_td => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cm_tbody_even_bg_color',
			array(
				'label' => esc_html__( 'Even Line Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$body_td_even => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'cm_tbody_odd_bg_color',
			array(
				'label' => esc_html__( 'Odd Line Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$body_td_odd => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'cm_body_padding',
			array(
				'label' => esc_html__( 'Items Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors' => array(
					$body_td => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'compare_button_style_section',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$selector_button = "#cmsmasters_body {{WRAPPER}} table.woosc_table tbody a.button";

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'compare_button_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $selector_button,
			)
		);

		$this->start_controls_tabs( 'compare_button_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$this->start_controls_tab(
				"compare_button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';
			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_control(
				"compare_button_text_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_button . $state => "color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"compare_button_bg_{$key}_background",
				array(
					'label' => __( 'Background Type', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'color' => array(
							'title' => __( 'Color', 'cmsmasters-elementor' ),
							'icon' => 'eicon-paint-brush',
						),
						'gradient' => array(
							'title' => __( 'Gradient', 'cmsmasters-elementor' ),
							'icon' => 'eicon-barcode',
						),
					),
					'default' => 'color',
					'toggle' => false,
					'render_type' => 'ui',
				)
			);

			$this->add_control(
				"compare_button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector_button . $element => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"compare_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"compare_button_bg_{$key}_color_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 0,
					),
					'render_type' => 'ui',
					'condition' => array(
						"compare_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"compare_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"compare_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"compare_button_bg_{$key}_color_b_stop",
				array(
					'label' => __( 'Location', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default' => array(
						'unit' => '%',
						'size' => 100,
					),
					'render_type' => 'ui',
					'condition' => array(
						"compare_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"compare_button_bg_{$key}_gradient_type",
				array(
					'label' => __( 'Type', 'cmsmasters-elementor' ),
					'label_block' => false,
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'linear' => __( 'Linear', 'cmsmasters-elementor' ),
						'radial' => __( 'Radial', 'cmsmasters-elementor' ),
					),
					'default' => 'linear',
					'render_type' => 'ui',
					'condition' => array(
						"compare_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"compare_button_bg_{$key}_gradient_angle",
				array(
					'label' => __( 'Angle', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'deg' ),
					'default' => array(
						'unit' => 'deg',
						'size' => 180,
					),
					'range' => array(
						'deg' => array( 'step' => 10 ),
					),
					'selectors' => array(
						$selector_button . $element => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{compare_button_bg_{$key}_color_stop.SIZE}}{{compare_button_bg_{$key}_color_stop.UNIT}}, {{compare_button_bg_{$key}_color_b.VALUE}} {{compare_button_bg_{$key}_color_b_stop.SIZE}}{{compare_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"compare_button_bg_{$key}_background" => array( 'gradient' ),
						"compare_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"compare_button_bg_{$key}_gradient_position",
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'center center' => __( 'Center Center', 'cmsmasters-elementor' ),
						'center left' => __( 'Center Left', 'cmsmasters-elementor' ),
						'center right' => __( 'Center Right', 'cmsmasters-elementor' ),
						'top center' => __( 'Top Center', 'cmsmasters-elementor' ),
						'top left' => __( 'Top Left', 'cmsmasters-elementor' ),
						'top right' => __( 'Top Right', 'cmsmasters-elementor' ),
						'bottom center' => __( 'Bottom Center', 'cmsmasters-elementor' ),
						'bottom left' => __( 'Bottom Left', 'cmsmasters-elementor' ),
						'bottom right' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					),
					'default' => 'center center',
					'selectors' => array(
						$selector_button . $element => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{compare_button_bg_{$key}_color_stop.SIZE}}{{compare_button_bg_{$key}_color_stop.UNIT}}, {{compare_button_bg_{$key}_color_b.VALUE}} {{compare_button_bg_{$key}_color_b_stop.SIZE}}{{compare_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"compare_button_bg_{$key}_background" => array( 'gradient' ),
						"compare_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"compare_button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_button . $state => "border-color: {{VALUE}};",
					),
				)
			);

			$this->add_responsive_control(
				"compare_button_border_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						$selector_button . $state => "border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "compare_button_shadow_{$key}",
					'selector' => $selector_button . $state,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'compare_border_button',
				'separator' => 'before',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'None', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => $selector_button,
			)
		);

		$this->add_responsive_control(
			'compare_button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					$selector_button => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.11.0
	 */
	protected function render() {
		if ( ! class_exists( 'WPCleverWoosw' ) ) {
			return;
		}

		echo do_shortcode( shortcode_unautop( "[woosc_list]" ) );
	}
}
