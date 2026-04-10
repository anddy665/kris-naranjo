<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Stock extends Base_Widget {

	use Woo_Singular_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Product Stock', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-product-stock';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'stock',
			'quantity',
		);
	}

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
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
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_product_stock_style',
			array(
				'label' => __( 'Style', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--stock-align: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'text_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .stock',
			)
		);

		$this->start_controls_tabs( 'available_tabs' );

		$this->start_controls_tab(
			'in_stock',
			array( 'label' => __( 'In Stock', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stock' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'availability_choose',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
					),
					'custom-text' => array(
						'title' => __( 'Custom Text', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'default' => 'default',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-nav-view-',
				'render_type' => 'template',
			)
		);

		if ( 'low_amount' !== get_option( 'woocommerce_stock_format' ) ) {
			$stock_text = __( 'Availability Text', 'cmsmasters-elementor' );

			/* translators: Addon %s: stock amount */
			$stock_placeholder = __( '%s in stock', 'cmsmasters-elementor' );
		} else {
			$this->add_control(
				'in_stock_text',
				array(
					'label' => __( 'In stock', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::TEXT,
					'placeholder' => __( 'In stock', 'cmsmasters-elementor' ),
					'condition' => array( 'availability_choose' => 'custom-text' ),
				)
			);

			$stock_text = __( 'Low Amount Text', 'cmsmasters-elementor' );

			/* translators: Addon %s: stock amount */
			$stock_placeholder = __( 'Only %s left in stock', 'cmsmasters-elementor' );
		}

		$this->add_control(
			'availability_text',
			array(
				'label' => $stock_text,
				/* translators: Addon %s: stock amount */
				'description' => __( 'You can use %s instead of products` stock amount.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => $stock_placeholder,
				'condition' => array( 'availability_choose' => 'custom-text' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'out_of_stock',
			array( 'label' => __( 'Out Of Stock', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'out_of_stock_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .stock.out-of-stock' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		global $product;

		$product = wc_get_product();

		if ( empty( $product ) ) {
			return;
		}

		if ( 'grouped' === $product->get_type() ) {
			return;
		}

		$settings = $this->get_active_settings();

		$availability = $product->get_availability();

		$stock_amount = $product->get_stock_quantity();

		$stock_quantity = $stock_amount <= get_option( 'woocommerce_notify_low_stock_amount' );

		$this->custom_availability_text_condition( $settings, $availability, $stock_quantity );

		echo wc_get_stock_html( $product );
	}

	/**
	 * Custom availability text condition.
	 *
	 * Used for overriding availability text
	 * by filter 'woocommerce_get_availability_text'
	 *
	 * @since 1.0.0
	 */
	public function custom_availability_text_condition( $settings, $availability, $stock_quantity ) {
		$is_stock = ( 'in-stock' === $availability['class'] ) ? true : false;

		if (
			$is_stock &&
			(
				'' !== $settings['availability_text'] ||
				'' !== $settings['in_stock_text']
			)
		) {
			if ( 'default' !== $settings['availability_choose'] ) {
				if ( 'low_amount' !== get_option( 'woocommerce_stock_format' ) ) {
					add_filter( 'woocommerce_get_availability_text', array( $this, 'custom_availability_text' ) );
				} else {
					$availability_text = 'custom_availability_text_stock';

					if ( $stock_quantity && '' !== $settings['availability_text'] ) {
						$availability_text = 'custom_availability_text';
					}

					add_filter( 'woocommerce_get_availability_text', array( $this, $availability_text ) );
				}
			}
		}
	}

	/**
	 * Custom availability text stock.
	 *
	 * @since 1.0.0
	 *
	 * @return string Custom availability text stock.
	 */
	public function custom_availability_text_stock() {
		$settings = $this->get_active_settings();

		return $settings['in_stock_text'];
	}

	/**
	 * Custom availability text.
	 *
	 * @since 1.0.0
	 *
	 * @return string Custom availability text.
	 */
	public function custom_availability_text() {
		$settings = $this->get_active_settings();

		return $settings['availability_text'];
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		if ( 'low_amount' !== get_option( 'woocommerce_stock_format' ) ) {
			$stock_text = esc_html__( 'Availability Text', 'cmsmasters-elementor' );
		} else {
			$stock_text = esc_html__( 'Low Amount Text', 'cmsmasters-elementor' );
		}

		return array(
			array(
				'field' => 'in_stock_text',
				'type' => esc_html__( 'In stock', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'availability_text',
				'type' => $stock_text,
				'editor_type' => 'LINE',
			),
		);
	}
}
