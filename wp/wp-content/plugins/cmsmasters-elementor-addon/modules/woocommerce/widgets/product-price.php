<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Price extends Base_Widget {

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
		return __( 'Product Price', 'cmsmasters-elementor' );
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
		return 'cmsicon-product-price';
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
			'price',
			'sale',
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
			'section_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$product_base = '.woocommerce div.product{{WRAPPER}}';
		$product_entry = '.woocommerce ul.products li.product {{WRAPPER}}';

		$this->add_control(
			'content_align',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-price__wrap' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'prefix_style',
			array(
				'label' => __( 'Prefix', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'none',
				'label_block' => false,
				'toggle' => false,
			)
		);

		$this->add_control(
			'prefix_icon',
			array(
				'label' => __( 'Prefix Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-tag',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'prefix_style' => 'icon',
				),
			)
		);

		$this->add_control(
			'prefix_label',
			array(
				'label' => __( 'Prefix Text', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Price:', 'cmsmasters-elementor' ),
				'condition' => array(
					'prefix_style' => 'text',
				),
			)
		);

		$this->add_control(
			'sale_heading',
			array(
				'label' => __( 'Price Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'prefix_style!' => 'none',
				),
			)
		);

		$this->add_control(
			'price_row',
			array(
				'label' => __( 'Column View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'cmsmasters-price-row-',
			)
		);

		$this->add_control(
			'sale_first',
			array(
				'label' => __( 'Sale Price First', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'cmsmasters-sale-first-',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_price_style',
			array(
				'label' => __( 'Price', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'price_typography',
				'selector' => $product_base . ' .price,' .
				$product_entry . ' .price',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'price_text_shadow',
				'selector' => $product_base . ' .price,' .
				$product_entry . ' .price',
			)
		);

		$this->start_controls_tabs( 'price_style_tabs' );

		$this->start_controls_tab( 'price_style_regular',
			array(
				'label' => esc_html__( 'Regular', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'price_color_regular',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$product_base . ' .price,' .
					$product_entry . ' .price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'price_style_sale',
			array(
				'label' => esc_html__( 'Sale', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'price_color_sale',
			array(
				'label' => esc_html__( 'Sale Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$product_base . ' .price ins,' .
					$product_entry . ' .price ins' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'price_color_sale_regular',
			array(
				'label' => esc_html__( 'Regular Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$product_base . ' .price del,' .
					$product_entry . ' .price del' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'price_regular_scale',
			array(
				'label' => __( 'Scale', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'em',
					'size' => 0.75,
				),
				'size_units' => array( 'em' ),
				'range' => array(
					'em' => array(
						'min' => 0.5,
						'max' => 1,
						'step' => 0.05,
					),
				),
				'selectors' => array(
					$product_base . ' .price del,' .
					$product_entry . ' .price del' => 'font-size: {{SIZE}}em',
				),
			)
		);

		$this->add_control(
			'price_ver_align',
			array(
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
					),
				),
				'prefix_class' => 'cmsmasters-price-ver-align-',
				'default' => 'center',
				'label_block' => true,
				'toggle' => false,
				'condition' => array( 'price_regular_scale[size]!' => '1' ),
			)
		);

		$this->add_responsive_control(
			'price_gap_between_margin',
			array(
				'label' => __( 'Gap Between Old and New Price', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 20,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-price__wrap' => '--price-margin: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'price_currency',
			array(
				'label' => esc_html__( 'Currency Symbol', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'price_currency_scale',
			array(
				'label' => __( 'Symbol Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'em',
					'size' => 1,
				),
				'size_units' => array( 'em' ),
				'range' => array(
					'em' => array(
						'min' => 0.5,
						'max' => 1,
						'step' => 0.05,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .price .woocommerce-Price-currencySymbol, .woocommerce ul.products li.product {{WRAPPER}} .price .woocommerce-Price-currencySymbol' => 'font-size: {{SIZE}}em',
				),
			)
		);

		$this->add_control(
			'price_currency_vertical_align',
			array(
				'label' => esc_html__( 'Currency Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'baseline' => esc_html__( 'Baseline', 'cmsmasters-elementor' ),
					'top' => esc_html__( 'Top', 'cmsmasters-elementor' ),
					'middle' => esc_html__( 'Middle', 'cmsmasters-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
					'sub' => esc_html__( 'Sub', 'cmsmasters-elementor' ),
					'super' => esc_html__( 'Super', 'cmsmasters-elementor' ),
					'text-top' => esc_html__( 'Text Top', 'cmsmasters-elementor' ),
					'text-bottom' => esc_html__( 'Text Bottom', 'cmsmasters-elementor' ),
				),
				'label_block' => true,
				'default' => 'baseline',
				'selectors' => array(
					'{{WRAPPER}} .price .woocommerce-Price-currencySymbol' => 'vertical-align: {{VALUE}};',
				),
				'condition' => array( 'price_currency_scale!' => '' ),
			)
		);

		$this->add_control(
			'prefix_heading',
			array(
				'label' => __( 'Prefix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'prefix_style!' => 'none',
				),
			)
		);

		$this->add_control(
			'prefix_text_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-price__prefix' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'prefix_style!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'prefix_text_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-price__prefix',
				'condition' => array(
					'prefix_style' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'prefix_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-price__prefix',
				'condition' => array(
					'prefix_style!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'prefix_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-price__prefix' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'prefix_style' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'prefix_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-woo-product-price__wrap' => '--prefix-margin: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'prefix_style!' => 'none',
				),
			)
		);

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

		$settings = $this->get_active_settings();

		$prefix_style = isset( $settings['prefix_style'] ) ? $settings['prefix_style'] : false;

		echo '<div class="elementor-widget-cmsmasters-woo-product-price__wrap cmsmasters-product-' . esc_attr( $product->get_type() ) . '">';

		if ( 'none' !== $prefix_style ) {
			if ( 'text' === $prefix_style ) {
				echo '<div class="elementor-widget-cmsmasters-woo-product-price__prefix">' .
					( isset( $settings['prefix_label'] ) ? esc_html( $settings['prefix_label'] ) : 'Price:' ) .
				'</div>';
			} elseif ( 'icon' === $prefix_style ) {
				echo '<div class="elementor-widget-cmsmasters-woo-product-price__prefix">' .
					$this->render_icon( $settings ) .
				'</div>';
			}
		}

		wc_get_template( '/single-product/price.php' );

		echo '</div>';
	}

	/**
	 * Get icon.
	 *
	 * Return custom or predefined icon.
	 *
	 * @since 1.0.0
	 * @since 1.11.6 Fixed render icons in widget.
	 *
	 * @param array $settings
	 *
	 * @return string Icon
	 */
	public function render_icon( $settings ) {
		return Utils::get_render_icon( $settings['prefix_icon'], $attributes = array( 'aria-hidden' => 'true' ) );
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
		return array(
			array(
				'field' => 'prefix_label',
				'type' => esc_html__( 'Prefix Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
