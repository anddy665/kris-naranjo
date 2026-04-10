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


class Wpclever_Smart_Wishlist extends Base_Widget {

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
	 * @since 1.0.0
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
		return 'cmsmasters-wpclever-wishlist';
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
		return __( 'Wpclever Smart Wishlist', 'cmsmasters-elementor' );
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
		return 'cmsicon-wishlist-list';
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
			'wishlist',
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

		$woosw = new \WPCleverWoosw();

		$this->start_controls_section(
			'section_wpclever_wishlist_general',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$product = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td';
		$product_items = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td:not(.woosw-item--actions)';
		$share_section = '#cmsmasters_body {{WRAPPER}} .woosw-list .woosw-actions';

		$this->add_control(
			'wl_separator_color',
			array(
				'label' => __( 'Separator Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$product => "border-color: {{VALUE}};",
				),
			)
		);

		$this->add_responsive_control(
			'wl_product_separator_size',
			array(
				'label' => esc_html__( 'Separator Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$product => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wl_product_gap',
			array(
				'label' => esc_html__( 'Product Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$product => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wl_product_item_gap',
			array(
				'label' => esc_html__( 'Product Items Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$product_items => 'padding-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wl_share_section_gap',
			array(
				'label' => esc_html__( 'Share & Copy Link Section Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$share_section => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_wishlist_wrapper',
			array(
				'label' => __( 'Wrapper', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$wrapper = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items';

		$this->add_control(
			'wl_wrapper_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$wrapper => "background-color: {{VALUE}};",
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => "wl_wrapper_shadow",
				'selector' => $wrapper,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'wl_border_wrapper',
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
				'separator' => 'before',
				'selector' => $wrapper,
			)
		);

		$this->add_responsive_control(
			'wl_padding_wrapper',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					$wrapper => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wl_bdr_wrapper',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$wrapper => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_wishlist_remove',
			array(
				'label' => __( 'Remove', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$remove = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td.woosw-item--remove span';

		$this->add_responsive_control(
			'wl_product_remove_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$remove => 'width: {{SIZE}}{{UNIT}};',
					$remove => 'height: {{SIZE}}{{UNIT}};',
					$remove => 'line-height: {{SIZE}}{{UNIT}};',
					$remove . ':before' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'wl_product_remove_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$remove => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wl_product_remove_color_hover',
			array(
				'label' => esc_html__( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$remove . ':hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_wishlist_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$image = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td.woosw-item--image img';

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'wl_border_image',
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
				'selector' => $image,
			)
		);

		$this->add_responsive_control(
			'wl_product_image_bdr',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'rem' ),
				'selectors' => array(
					$image => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_wishlist_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$title = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td.woosw-item--info div.woosw-item--name';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_title_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $title,
			)
		);

		$this->add_control(
			'wl_product_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$title . ' a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wl_product_title_color_hover',
			array(
				'label' => esc_html__( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$title . ' a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wl_product_title_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$title => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_wishlist_price',
			array(
				'label' => __( 'Price', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$price = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td.woosw-item--info div.woosw-item--price';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_price_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $price,
			)
		);

		$this->add_control(
			'wl_product_sale_color',
			array(
				'label' => esc_html__( 'Sale Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$price . ' ins' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wl_product_price_color',
			array(
				'label' => esc_html__( 'Price Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$price . ' del' => 'color: {{VALUE}};',
					$price => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wl_product_price_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$price => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_wishlist_date',
			array(
				'label' => __( 'Date', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$date = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td.woosw-item--info div.woosw-item--time';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_date_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $date,
			)
		);

		$this->add_control(
			'wl_product_date_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$date => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wpclever_wishlist_stock',
			array(
				'label' => __( 'Stock', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$stock = '#cmsmasters_body {{WRAPPER}} .woosw-list table.woosw-items .woosw-item td.woosw-item--actions div .stock';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_stock_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $stock,
			)
		);

		$this->add_control(
			'wl_product_out_stock_color',
			array(
				'label' => esc_html__( 'Out Of Stock Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$stock . '.out-of-stock' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wl_product_in_stock_color',
			array(
				'label' => esc_html__( 'In Stock Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'dynamic' => array(),
				'selectors' => array(
					$stock . '.in-stock' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'wl_product_stock_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$stock => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wl_button_style_section',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$selector_button = "#cmsmasters_body {{WRAPPER}} .woosw-item--add a.button, #cmsmasters_body {{WRAPPER}} .woosw-copy-btn .button";

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wl_button_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $selector_button,
			)
		);

		$this->start_controls_tabs( 'wl_button_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$this->start_controls_tab(
				"wl_button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';
			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_control(
				"wl_button_text_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_button . $state => "color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wl_button_bg_{$key}_background",
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
				"wl_button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector_button . $element => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"wl_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"wl_button_bg_{$key}_color_stop",
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
						"wl_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wl_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"wl_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wl_button_bg_{$key}_color_b_stop",
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
						"wl_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wl_button_bg_{$key}_gradient_type",
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
						"wl_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wl_button_bg_{$key}_gradient_angle",
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
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{wl_button_bg_{$key}_color_stop.SIZE}}{{wl_button_bg_{$key}_color_stop.UNIT}}, {{wl_button_bg_{$key}_color_b.VALUE}} {{wl_button_bg_{$key}_color_b_stop.SIZE}}{{wl_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"wl_button_bg_{$key}_background" => array( 'gradient' ),
						"wl_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wl_button_bg_{$key}_gradient_position",
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
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{wl_button_bg_{$key}_color_stop.SIZE}}{{wl_button_bg_{$key}_color_stop.UNIT}}, {{wl_button_bg_{$key}_color_b.VALUE}} {{wl_button_bg_{$key}_color_b_stop.SIZE}}{{wl_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"wl_button_bg_{$key}_background" => array( 'gradient' ),
						"wl_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wl_button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_button . $state => "border-color: {{VALUE}};",
					),
				)
			);

			$this->add_responsive_control(
				"wl_button_border_radius_{$key}",
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
					'name' => "wl_button_shadow_{$key}",
					'selector' => $selector_button . $state,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'wl_border_button',
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
			'wl_button_padding',
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

		if ( 'yes' === $woosw->get_setting( 'page_copy' ) ) {
			$this->start_controls_section(
				'wl_section_copy_link_style',
				array(
					'label' => __( 'Copy Link Fields', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);

			$title_selector = '{{WRAPPER}} .woosw-copy .woosw-copy-label';

			$this->add_control(
				'wl_cpl_title_heading',
				array(
					'type' => Controls_Manager::HEADING,
					'label' => __( 'Label', 'cmsmasters-elementor' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'wl_cpl_typography_title',
					'label' => __( 'Typography', 'cmsmasters-elementor' ),
					'selector' => "{$title_selector}",
					'separator' => 'after',
				)
			);

			$this->add_control(
				"wl_cpl_color_title",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$title_selector}" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'wl_cpl_title_gap',
				array(
					'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 40,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						'{{WRAPPER}} .woosw-list .woosw-copy .woosw-copy-url' => 'margin-left: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'wl_input_heading',
				array(
					'type' => Controls_Manager::HEADING,
					'label' => __( 'Input', 'cmsmasters-elementor' ),
					'separator' => 'before',
				)
			);

			$selector_input = '{{WRAPPER}} .woosw-copy #woosw_copy_url';

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'wl_typography_fields',
					'label' => __( 'Typography', 'cmsmasters-elementor' ),
					'selector' => "{$selector_input}",
					'separator' => 'after',
				)
			);

			$this->start_controls_tabs( 'fields_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'focus' => __( 'Focus', 'cmsmasters-elementor' ),
			);

			$state = '';

			foreach ( $colors as $key => $label ) {

				if ( 'focus' === $key ) {
					$state = ':focus';
				}

				$state_input = $selector_input . $state;

				$this->start_controls_tab(
					"wl_cpl_fields_{$key}",
					array(
						'label' => $label,
					)
				);

				$this->add_control(
					"wl_cpl_color_fields_{$key}",
					array(
						'label' => __( 'Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"{$state_input}" => 'color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"wl_cpl_background_color_{$key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"{$state_input}" => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"wl_cpl_border_color_{$key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							"{$state_input}" => 'border-color: {{VALUE}};',
						),
						'condition' => array(
							'wl_cpl_border_fields_border!' => array(
								'none',
							),
						),
					)
				);

				$this->add_responsive_control(
					"wl_cpl_field_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
							'%',
						),
						'selectors' => array(
							"{$state_input}" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					array(
						'name' => "wl_cpl_fields_form_box_shadow_{$key}",
						'selector' => "{$state_input}",
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name' => 'wl_cpl_border_fields',
					'label' => __( 'Border', 'cmsmasters-elementor' ),
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
					'selector' => "{$selector_input}",
					'exclude' => array( 'color' ),
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'wl_cpl_fields_form_padding',
				array(
					'label' => __( 'Padding', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'separator' => 'before',
					'selectors' => array(
						"{$selector_input}" => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'wl_cpl_button_style_section',
				array(
					'label' => __( 'Copy Link Button', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);

			$selector_button = "{{WRAPPER}} .woosw-copy .woosw-copy-btn #woosw_copy_btn";

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'wl_cpl_button_typography',
					'label' => __( 'Typography', 'cmsmasters-elementor' ),
					'selector' => $selector_button,
				)
			);

			$this->add_responsive_control(
				'wl_cpl_button_gap',
				array(
					'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 40,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						'#cmsmasters_body {{WRAPPER}} .woosw-copy .woosw-copy-btn' => 'margin-left: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->start_controls_tabs( 'wl_cpl_button_tabs' );

			$colors = array(
				'normal' => __( 'Normal', 'cmsmasters-elementor' ),
				'hover' => __( 'Hover', 'cmsmasters-elementor' ),
			);

			foreach ( $colors as $key => $label ) {

				$this->start_controls_tab(
					"wl_cpl_button_tab_{$key}",
					array(
						'label' => $label,
					)
				);

				$state = ( 'hover' === $key ) ? ':hover' : '';

				$this->add_control(
					"wl_cpl_button_background_color_{$key}",
					array(
						'label' => __( 'Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector_button . $state => 'background-color: {{VALUE}};',
						),
					)
				);

				$this->add_control(
					"wl_cpl_button_text_color_{$key}",
					array(
						'label' => __( 'Text Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector_button . $state => "color: {{VALUE}};",
						),
					)
				);

				$this->add_control(
					"wl_cpl_button_border_color_{$key}",
					array(
						'label' => __( 'Border Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector_button . $state => "border-color: {{VALUE}};",
						),
					)
				);

				$this->add_responsive_control(
					"wl_cpl_button_border_radius_{$key}",
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
						'name' => "wl_cpl_button_shadow_{$key}",
						'selector' => $selector_button . $state,
					)
				);

				$this->end_controls_tab();
			}

			$this->end_controls_tabs();

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name' => 'wl_cpl_border_button',
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
				'wl_cpl_button_padding',
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

		if ( 'yes' === $woosw->get_setting( 'page_share' ) ) {
			$this->start_controls_section(
				'wl_section_share_style',
				array(
					'label' => __( 'Share', 'cmsmasters-elementor' ),
					'tab' => Controls_Manager::TAB_STYLE,
				)
			);

			$title_selector = '{{WRAPPER}} .woosw-share .woosw-share-label';

			$this->add_control(
				'wl_share_title_heading',
				array(
					'type' => Controls_Manager::HEADING,
					'label' => __( 'Label', 'cmsmasters-elementor' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name' => 'wl_share_typography_title',
					'label' => __( 'Typography', 'cmsmasters-elementor' ),
					'selector' => "{$title_selector}",
					'separator' => 'after',
				)
			);

			$this->add_control(
				"wl_share_color_title",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						"{$title_selector}" => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'wl_share_title_gap',
				array(
					'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 40,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						"{$title_selector}" => 'margin-right: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'wl_share_link_heading',
				array(
					'type' => Controls_Manager::HEADING,
					'label' => __( 'Link', 'cmsmasters-elementor' ),
					'separator' => 'before',
				)
			);

			$selector_link = '{{WRAPPER}} .woosw-list .woosw-share a';

			if ( 'yes' === $woosw->get_setting( 'page_icon' ) ) {
				$this->add_responsive_control(
					'wl_share_link_size',
					array(
						'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 40,
							),
						),
						'size_units' => array(
							'px',
						),
						'selectors' => array(
							"{$selector_link}" => 'font-size: {{SIZE}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_group_control(
					Group_Control_Typography::get_type(),
					array(
						'name' => 'wl_typography_share_link',
						'label' => __( 'Typography', 'cmsmasters-elementor' ),
						'selector' => "{$selector_link}",
						'separator' => 'after',
					)
				);
			}

			$this->add_responsive_control(
				'wl_share_link_gap',
				array(
					'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'min' => 0,
							'max' => 40,
						),
					),
					'size_units' => array(
						'px',
					),
					'selectors' => array(
						"{$selector_link}" => 'margin-right: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				"wl_share_color_link",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_link => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"wl_share_color_link_hover",
				array(
					'label' => __( 'Color Hover', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_link . ':hover' => 'color: {{VALUE}};',
					),
				)
			);

			$this->end_controls_section();
		}
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

		echo do_shortcode( shortcode_unautop( "[woosw_list]" ) );
	}
}
