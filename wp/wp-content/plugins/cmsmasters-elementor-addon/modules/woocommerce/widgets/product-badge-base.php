<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils as Breakpoints_Manager;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Product_Badge_Base extends Base_Widget {

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	public function get_unique_keywords() {
		return array(
			'stock',
			'quantity',
			'badge',
			'sale',
			'in stock',
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

	abstract public function get_badge( $product );

	/**
	 * Register widget content section.
	 *
	 * Adds widget content settings controls.
	 *
	 * @since 1.0.0
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0
	 * @since 1.3.3 Added support custom breakpoints.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_product_badge_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$widget = $this->get_name();

		if ( 'cmsmasters-woo-badge-sale' === $widget ) {
			$selector = '{{WRAPPER}} .cmsmasters-woo-badge.elementor-widget-cmsmasters-woo-badge__sale .cmsmasters-woo-badge-inner';

			$this->add_control(
				'cmsmasters_badge_text_color',
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'separator' => 'before',
					'selectors' => array(
						$selector => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'cmsmasters_badge_bg_color',
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'background-color: {{VALUE}}',
						'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-type-triangle.elementor-widget-cmsmasters-woo-badge__sale .cmsmasters-woo-badge-inner:before' => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'cmsmasters_badge_typography',
					'selector' => $selector,
				)
			);
		} elseif ( 'cmsmasters-woo-badge-stock' === $widget ) {
			$this->start_controls_tabs( 'message_tabs' );

			$type = array(
				'in_stock' => __( 'In Stock', 'cmsmasters-elementor' ),
				'out_stock' => __( 'Out Stock', 'cmsmasters-elementor' ),
			);

			foreach ( $type as $key => $label ) {

				$this->start_controls_tab(
					"stock_{$key}",
					array(
						'label' => $label,
					)
				);

				if ( 'in_stock' === $key ) {
					$selector = '{{WRAPPER}} .cmsmasters-woo-badge.elementor-widget-cmsmasters-woo-badge__in-stock .cmsmasters-woo-badge-inner';
					$class = '.elementor-widget-cmsmasters-woo-badge__in-stock';
				} else {
					$selector = '{{WRAPPER}} .cmsmasters-woo-badge.elementor-widget-cmsmasters-woo-badge__out-stock .cmsmasters-woo-badge-inner';
					$class = '.elementor-widget-cmsmasters-woo-badge__out-stock';
				}

				$this->add_control(
					"cmsmasters_badge_text_color_{$key}",
					array(
						'label' => __( 'Text Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => 'color: {{VALUE}}',
						),
					)
				);

				$this->add_control(
					"cmsmasters_badge_bg_color_{$key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => 'background-color: {{VALUE}}',
							"{{WRAPPER}} .cmsmasters-woo-badge{$class}.cmsmasters-woo-badge-type-triangle .cmsmasters-woo-badge-inner:before" => 'background-color: {{VALUE}}',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => "cmsmasters_badge_typography_{$key}",
						'selector' => $selector,
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();
		}

		$this->add_control(
			'type_style',
			array(
				'label' => __( 'Type Style', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'label_block' => false,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'background',
				'types' => array( 'classic' ),
				'label_block' => true,
				'fields_options' => array(
					'background' => array(
						'type' => Controls_Manager::HIDDEN,
						'default' => 'image',
					),
					'image' => array(
						'condition' => '',
					),
					'position' => array(
						'condition' => array(
							'image[url]!' => '',
						),
					),
					'xpos' => array(
						'condition' => array(
							'position' => array( 'initial' ),
							'image[url]!' => '',
						),
					),
					'ypos' => array(
						'condition' => array(
							'position' => array( 'initial' ),
							'image[url]!' => '',
						),
					),
					'repeat' => array(
						'condition' => array(
							'image[url]!' => '',
						),
					),
					'size' => array(
						'condition' => array(
							'image[url]!' => '',
						),
					),
					'bg_width' => array(
						'condition' => array(
							'size' => array( 'initial' ),
							'image[url]!' => '',
						),
					),
					'color' => array(
						'type' => Controls_Manager::HIDDEN,
					),
					'attachment' => array(
						'type' => Controls_Manager::HIDDEN,
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-woo-badge.elementor-widget-cmsmasters-woo-badge__wrapper .cmsmasters-woo-badge-inner',
				'condition' => array(
					'type_style' => array( 'custom' ),
				),
			)
		);

		$this->add_control(
			'cmsmasters_badge_type',
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
					'type_style' => 'default',
				),
			)
		);

		$this->add_control(
			'cmsmasters_badge_position',
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
					'type_style' => 'default',
					'cmsmasters_badge_type' => array(
						'sloping_line',
						'triangle',
					),
				),
			)
		);

		$this->add_control(
			'cmsmasters_badge_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array(
					'unit' => 'px',
					'size' => 100,
				),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-type-line .cmsmasters-woo-badge-inner' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-type-square .cmsmasters-woo-badge-inner' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-type-circle .cmsmasters-woo-badge-inner' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array(
					'cmsmasters_badge_type' => array(
						'line',
						'square',
						'circle',
					),
					'type_style' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'cmsmasters_badge_triangle_distance',
			array(
				'label' => __( 'Distance', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 39,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-top_right .cmsmasters-woo-badge-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' ),
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-top_left .cmsmasters-woo-badge-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);' ),
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-bottom_right .cmsmasters-woo-badge-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' ),
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-bottom_left .cmsmasters-woo-badge-inner' => 'margin-top: {{SIZE}}{{UNIT}}; transform: ' . ( is_rtl() ? 'translateY(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' : 'translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(135deg);' ),
				),
				'separator' => 'before',
				'condition' => array(
					'cmsmasters_badge_type' => array(
						'sloping_line',
						'triangle',
					),
					'type_style' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'cmsmasters_badge_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-badge .cmsmasters-woo-badge-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'cmsmasters_badge_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-type-line .cmsmasters-woo-badge-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-woo-badge.cmsmasters-woo-badge-type-square .cmsmasters-woo-badge-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'type_style',
									'operator' => '===',
									'value' => 'default',
								),
								array(
									'relation' => 'or',
									'terms' => array(
										array(
											'name' => 'cmsmasters_badge_type',
											'operator' => '===',
											'value' => 'square',
										),
										array(
											'name' => 'cmsmasters_badge_type',
											'operator' => '===',
											'value' => 'line',
										),
									),
								),
							),
						),

						array(
							'name' => 'type_style',
							'operator' => '===',
							'value' => 'custom',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$product = wc_get_product();

		if ( ! $product ) {
			return;
		}

		$this->get_badge( $product );
	}
}
