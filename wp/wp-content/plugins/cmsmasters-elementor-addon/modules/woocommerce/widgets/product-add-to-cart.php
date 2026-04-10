<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Add_To_Cart extends Base_Widget {

	use Woo_Singular_Widget;

	private $product_base = '.woocommerce div.product{{WRAPPER}} ';

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
		return __( 'Add To Cart', 'cmsmasters-elementor' );
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
		return 'cmsicon-add-to-cart';
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
			'cart',
			'add to cart',
		);
	}

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * Get script depends.
	 *
	 * @since 1.0.0
	 *
	 * @return array Get script depends.
	 */
	public function get_script_depends() {
		return array( 'wc-single-product', 'wc-add-to-cart-variation' );
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
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not.
	 *
	 * @since 1.19.3 Added widget hiding in the admin panel.
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		return false;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Added button `Text Shadow`, variations select `Options` styles and `Price Gap`.
	 * @since 1.1.0 Added controls & gradient for add to cart button to match global button control settings.
	 * @since 1.2.0 Added `Border` control for button.
	 * @since 1.4.0 Added settings stock.
	 * @since 1.10.1 Added `Alignment`control for stock. Fixed the application of typography, colors and spacing for stock.
	 * @since 1.11.0 Added controls for WPC Variation Swatches.
	 * @since 1.11.8 Added `Show`control for Reset.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->register_atc_button_controls_style();

		$this->register_atc_quantity_controls_style();

		$wpcvs_settings = ( null !== get_option( 'wpcvs_settings' ) ? get_option( 'wpcvs_settings' ) : '' );

		if ( class_exists( 'WPCleverWpcvs' ) && $wpcvs_settings && 'yes' === $wpcvs_settings['button_default'] ) {
			$this->register_wpcvs_atc_variation_section_style();

			$this->register_wpcvs_atc_variation_attribute_type_section_style();

			if ( 'none' !== $wpcvs_settings['tooltip_library'] ) {
				$this->register_wpcvs_atc_variation_tooltip_section_style();
			}
		} else {
			$this->register_atc_group_variations_controls_style();

			$this->register_atc_variations_controls_style();

			$this->register_group_controls_style();
		}

		$this->register_price_controls_style();

		$this->register_description_controls_style();

		$this->register_stock_controls_style();
	}

	protected function register_atc_button_controls_style() {
		$this->start_controls_section(
			'section_atc_button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'alignment',
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
					'justify' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'prefix_class' => 'cmsmasters-add-to-cart%s-align-',
			)
		);

		$this->add_responsive_control(
			'button_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 300,
						'step' => 5,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					$this->product_base . '.cart .button' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'alignment!' => 'justify' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'selector' => $this->product_base . '.cart .button',
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label' => __( 'Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Add to Cart',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			)
		);

		$this->add_control(
			'button_icon_align',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array( 'title' => __( 'After', 'cmsmasters-elementor' ) ),
					'row-reverse' => array( 'title' => __( 'Before', 'cmsmasters-elementor' ) ),
				),
				'default' => 'row',
				'toggle' => false,
				'label_block' => false,
				'selectors' => array(
					$this->product_base . '' => '--cmsmasters-button-icon-align: {{VALUE}}',
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_arrangement',
			array(
				'label' => __( 'Arrangement', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'description' => __( 'Applies only for Justified Alignment.', 'cmsmasters-elementor' ),
				'options' => array(
					'center' => array( 'title' => __( 'Together', 'cmsmasters-elementor' ) ),
					'space-between' => array( 'title' => __( 'Side', 'cmsmasters-elementor' ) ),
				),
				'default' => 'center',
				'label_block' => false,
				'selectors' => array(
					$this->product_base . '' => '--cmsmasters-button-icon-arrangement: {{VALUE}}',
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		$this->start_controls_tab( 'button_style_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart .button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_bg_color_group_background',
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
			'button_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$this->product_base . 'form.cart .button:before' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'button_bg_color_group_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'button_bg_color_group_color_stop',
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
					'button_bg_color_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'button_bg_color_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_color_b_stop',
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
					'button_bg_color_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_gradient_type',
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
					'button_bg_color_group_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_gradient_angle',
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
					$this->product_base . 'form.cart .button:before' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_bg_color_group_color_stop.SIZE}}{{button_bg_color_group_color_stop.UNIT}}, {{button_bg_color_group_color_b.VALUE}} {{button_bg_color_group_color_b_stop.SIZE}}{{button_bg_color_group_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_bg_color_group_background' => array( 'gradient' ),
					'button_bg_color_group_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_gradient_position',
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
					$this->product_base . 'form.cart .button:before' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_bg_color_group_color_stop.SIZE}}{{button_bg_color_group_color_stop.UNIT}}, {{button_bg_color_group_color_b.VALUE}} {{button_bg_color_group_color_b_stop.SIZE}}{{button_bg_color_group_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_bg_color_group_background' => array( 'gradient' ),
					'button_bg_color_group_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart .button' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'button_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'button_text_shadow_normal',
				'selector' => $this->product_base . 'form.cart .button',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'button_box_shadow_normal',
				'selector' => $this->product_base . 'form.cart .button',
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					$this->product_base . '.cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'button_style_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart .button:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_bg_color_group_hover_background',
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
			'button_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$this->product_base . 'form.cart .button:after' => '--button-bg-color: {{VALUE}}; ' .
						'background: var( --button-bg-color );',
				),
				'condition' => array(
					'button_bg_color_group_hover_background' => array(
						'color',
						'gradient',
					),
				),
			)
		);

		$this->add_control(
			'button_bg_color_group_hover_color_stop',
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
					'button_bg_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_hover_color_b',
			array(
				'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f2295b',
				'render_type' => 'ui',
				'condition' => array(
					'button_bg_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_hover_color_b_stop',
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
					'button_bg_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_hover_gradient_type',
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
					'button_bg_color_group_hover_background' => array( 'gradient' ),
				),
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_hover_gradient_angle',
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
					$this->product_base . 'form.cart .button:after' => 'background-color: transparent; ' .
						'background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_bg_color_group_hover_color_stop.SIZE}}{{button_bg_color_group_hover_color_stop.UNIT}}, {{button_bg_color_group_hover_color_b.VALUE}} {{button_bg_color_group_hover_color_b_stop.SIZE}}{{button_bg_color_group_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_bg_color_group_hover_background' => array( 'gradient' ),
					'button_bg_color_group_hover_gradient_type' => 'linear',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_bg_color_group_hover_gradient_position',
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
					$this->product_base . 'form.cart .button:after' => 'background-color: transparent; ' .
						'background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_bg_color_group_hover_color_stop.SIZE}}{{button_bg_color_group_hover_color_stop.UNIT}}, {{button_bg_color_group_hover_color_b.VALUE}} {{button_bg_color_group_hover_color_b_stop.SIZE}}{{button_bg_color_group_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'button_bg_color_group_hover_background' => array( 'gradient' ),
					'button_bg_color_group_hover_gradient_type' => 'radial',
				),
				'separator' => 'after',
				'of_type' => 'gradient',
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart .button:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'button_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'button_text_shadow_hover',
				'selector' => $this->product_base . 'form.cart .button:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'button_box_shadow_hover',
				'selector' => $this->product_base . 'form.cart .button:hover',
			)
		);

		$this->add_control(
			'button_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					$this->product_base . '.cart .button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0.2,
				),
				'range' => array(
					'px' => array(
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$this->product_base . '.cart .button,' .
					$this->product_base . '.cart .button:before,' .
					$this->product_base . '.cart .button:after' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'divider_after_button_style_tabs',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					$this->product_base . '.cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_border',
				'exclude' => array( 'color' ),
				'selector' => $this->product_base . '.cart .button',
			)
		);

		$this->add_control(
			'button_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					$this->product_base => '--cmsmasters-button-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					$this->product_base => "--cmsmasters-button-icon-gap: {{SIZE}}{{UNIT}};",
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->start_controls_tabs(
			'button_icon_tabs',
			array( 'condition' => array( 'button_icon[value]!' => '' ) )
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"button_icon_{$main_key}_tab",
				array(
					'label' => $label,
					'condition' => array( 'button_icon[value]!' => '' ),
				)
			);

			$this->add_control(
				"button_icon_{$main_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$this->product_base => "--cmsmasters-button-icon-{$main_key}-color: {{VALUE}};",
					),
					'condition' => array( 'button_icon[value]!' => '' ),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_atc_quantity_controls_style() {
		$this->start_controls_section(
			'section_atc_quantity_style',
			array(
				'label' => __( 'Quantity', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					$this->product_base . '.quantity + .button + .cmsmasters-add-to-cart-button' => 'margin-left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'quantity_label_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default' => array(
					'unit' => '%',
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 10,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 20,
				),
				'range' => array(
					'%' => array(
						'min' => 5,
						'max' => 100,
						'step' => 1,
					),
					'px' => array(
						'min' => 80,
						'max' => 200,
						'step' => 5,
					),
				),
				'selectors' => array(
					$this->product_base . '.quantity' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->start_controls_tabs( 'quantity_style_tabs' );

		$this->start_controls_tab( 'quantity_style_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'quantity_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.quantity .qty' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.quantity .qty' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.quantity .qty' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'quantity_border_border!' => '' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'quantity_style_focus',
			array(
				'label' => __( 'Focus', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'quantity_text_color_focus',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.quantity .qty:focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_bg_color_focus',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.quantity .qty:focus' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'quantity_border_color_focus',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.quantity .qty:focus' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'quantity_border_border!' => '' ),
			)
		);

		$this->add_control(
			'quantity_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0.2,
				),
				'range' => array(
					'px' => array(
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$this->product_base . '.quantity .qty' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'quantity_typography',
				'selector' => $this->product_base . '.quantity .qty',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'quantity_border',
				'selector' => $this->product_base . '.quantity .qty',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_control(
			'quantity_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					$this->product_base . '.quantity .qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'quantity_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					$this->product_base . '.quantity .qty' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_atc_group_variations_controls_style() {
		$this->start_controls_section(
			'section_atc_group_variations_table',
			array(
				'label' => __( 'Group & Variations Table', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$wpcvs_settings = ( null !== get_option( 'wpcvs_settings' ) ? get_option( 'wpcvs_settings' ) : '' );

		if ( class_exists( 'WPCleverWpcvs' ) && $wpcvs_settings && 'yes' !== $wpcvs_settings['button_default'] ) {
			$this->add_control(
				'atc_group_variations_description',
				array(
					'raw' => __( 'To enable the settings for WPC Variation Swatches proceed to the ', 'cmsmasters-elementor' ) . '<a href="admin.php?page=wpclever-wpcvs" target="_blank">' . __( 'plugins settings page', 'cmsmasters-elementor' ) . '</a>' . __( ' and set "Button swatch by default" to "Yes".', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'render_type' => 'ui',
				)
			);
		}

		$this->add_responsive_control(
			'group_variations_alignment',
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
					$this->product_base . 'table th,' .
					$this->product_base . 'table td' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'group_variations_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$this->product_base . 'form.cart .group_table,' .
					$this->product_base . 'form.cart table.variations' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'group_variations_lines_background_odd',
			array(
				'label' => __( 'Odd Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.group_table tr:nth-child(odd) td,' .
					$this->product_base . 'table.variations tr:nth-child(odd) td' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'group_variations_lines_background_even',
			array(
				'label' => __( 'Even Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.group_table tr:nth-child(even) td,' .
					$this->product_base . 'table.variations tr:nth-child(even) td' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'group_variations_border',
				'selector' => $this->product_base . '.group_table td,' .
				$this->product_base . 'table.variations td',
			)
		);

		$this->add_control(
			'group_variations_border_choose',
			array(
				'label' => __( 'Display Borders', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'all' => array(
						'title' => __( 'All', 'cmsmasters-elementor' ),
					),
					'inn-hor' => array(
						'title' => __( 'Inner Hor', 'cmsmasters-elementor' ),
						'description' => __( 'Borders in parent, horizontal borders in table cells', 'cmsmasters-elementor' ),
					),
					'only-hor' => array(
						'title' => __( 'Only Hor', 'cmsmasters-elementor' ),
						'description' => __( 'Only horizontal borders in table cells', 'cmsmasters-elementor' ),
					),
				),
				'prefix_class' => 'cmsmasters-group-border-',
				'default' => 'all',
				'label_block' => true,
				'toggle' => false,
				'condition' => array( 'group_variations_border_border!' => '' ),
			)
		);

		$this->add_control(
			'group_variations_padding',
			array(
				'label' => __( 'Table Cell Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					$this->product_base . '.group_table td,' .
					$this->product_base . 'table.variations td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_variations_group_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'group_variations_label_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
					'size' => 25,
				),
				'size_units' => array( '%', 'px' ),
				'range' => array(
					'%' => array(
						'max' => 100,
						'step' => 1,
					),
					'px' => array(
						'min' => 100,
						'max' => 400,
						'step' => 10,
					),
				),
				'selectors' => array(
					$this->product_base . '.woocommerce-grouped-product-list-item__label,' .
					$this->product_base . 'table.variations td.label' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'group_variations_label_typography',
				'selector' => $this->product_base . '.woocommerce-grouped-product-list-item__label a,' .
				$this->product_base . 'table.variations td',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register variations controls.
	 *
	 * @since 1.18.4 Added `Label` control for variation reset button.
	 */
	protected function register_atc_variations_controls_style() {
		$this->start_controls_section(
			'section_atc_variations_style',
			array(
				'label' => __( 'Variations', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$wpcvs_settings = ( null !== get_option( 'wpcvs_settings' ) ? get_option( 'wpcvs_settings' ) : '' );

		if ( class_exists( 'WPCleverWpcvs' ) && $wpcvs_settings && 'yes' !== $wpcvs_settings['button_default'] ) {
			$this->add_control(
				'atc_variations_description',
				array(
					'raw' => __( 'To enable the settings for WPC Variation Swatches proceed to the ', 'cmsmasters-elementor' ) . '<a href="admin.php?page=wpclever-wpcvs" target="_blank">' . __( 'plugins settings page', 'cmsmasters-elementor' ) . '</a>' . __( ' and set "Button swatch by default" to "Yes".', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'render_type' => 'ui',
				)
			);
		}

		$this->add_responsive_control(
			'variations_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'heading_variations_label',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'variations_spacing_label',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations label' => 'margin-right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'variations_label_typography',
				'selector' => $this->product_base . 'form.cart table.variations label',
			)
		);

		$this->add_control(
			'variations_label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'heading_variations_select_field',
			array(
				'label' => __( 'Select Field', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'variations_select_typography',
				'selector' => $this->product_base . 'form.cart table.variations td.value select',
			)
		);

		$this->add_control(
			'heading_variations_select',
			array(
				'label' => __( 'Select', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'variations_select_border',
				'selector' => $this->product_base . 'form.cart table.variations td.value select',
				'exclude' => array( 'color' ),
			)
		);

		$this->start_controls_tabs( 'variations_select_tabs' );

		$this->start_controls_tab( 'variations_select_tab_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'variations_select_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'variations_select_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'variations_select_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'variations_select_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'variations_select_box_shadow',
				'selector' => $this->product_base . 'form.cart table.variations td.value select',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'variations_select_tab_focus',
			array(
				'label' => __( 'Focus', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'variations_select_color_focus',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select:focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'variations_select_bg_color_focus',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select:focus' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'variations_select_border_color_focus',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select:focus' => 'border-color: {{VALUE}}',
				),
				'condition' => array( 'variations_select_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'variations_select_box_shadow_focus',
				'selector' => $this->product_base . 'form.cart table.variations td.value select:focus',
			)
		);

		$this->add_control(
			'variations_select_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0.2,
				),
				'range' => array(
					'px' => array(
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'divider_after_variations_select_tabs',
			array(
				'type' => Controls_Manager::DIVIDER,
			)
		);

		$this->add_control(
			'variations_select_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'variations_select_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value select,' .
					$this->product_base . 'form.cart table.variations td.value:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_variations_options',
			array(
				'label' => __( 'Options', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'variations_options_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value option' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'variations_options_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . 'form.cart table.variations td.value option' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'heading_variations_reset',
			array(
				'label' => __( 'Reset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'variations_reset_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors_dictionary' => array(
					'yes' => 'block',
					'' => 'none',
				),
				'default' => 'yes',
				'selectors' => array(
					$this->product_base . '.reset_variations' => "--cmsmasters-variations-reset-show: {{VALUE}}",
				),
			)
		);

		$this->add_control(
			'variations_reset_text',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => 'Clear',
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'variations_spacing_reset',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$this->product_base . 'form.cart .reset_variations' => 'margin-left: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'variations_reset_typography',
				'selector' => $this->product_base . '.reset_variations',
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			'variations_reset',
			array( 'condition' => array( 'variations_reset_show' => 'yes' ) )
		);

		$this->start_controls_tab( 'variations_reset_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->add_control(
			'variations_reset_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.reset_variations' => 'color: {{VALUE}}',
				),
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'variations_reset_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->add_control(
			'variations_reset_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.reset_variations:hover' => 'color: {{VALUE}}',
				),
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->add_control(
			'variations_reset_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0.2,
				),
				'range' => array(
					'px' => array(
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$this->product_base . '.reset_variations' => 'transition: all {{SIZE}}s',
				),
				'condition' => array( 'variations_reset_show' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_wpcvs_atc_variation_section_style() {
		$this->start_controls_section(
			'wpcvs_atc_variation_section_style',
			array(
				'label' => __( 'Variations', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$selector = $this->product_base . '.cmsmasters_wpcvs_variation';

		$this->add_responsive_control(
			'variations_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'row' => array( 'title' => __( 'Horizontal', 'cmsmasters-elementor' ) ),
					'column' => array( 'title' => __( 'Vertical', 'cmsmasters-elementor' ) ),
				),
				'default' => 'row',
				'label_block' => false,
				'toggle' => false,
				'selectors_dictionary' => array(
					'row' => '--cmsmasters-variations-position: row; --cmsmasters-variations-justify-align: flex-start; --cmsmasters-variations-text-align: left; --cmsmasters-variations-horizontal-width: 100%;',
					'column' => '--cmsmasters-variations-position: column;',
				),
				'selectors' => array(
					$selector => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'variations_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'flex-start',
				'toggle' => false,
				'selectors_dictionary' => array(
					'flex-start' => '--cmsmasters-variations-align: flex-start; --cmsmasters-variations-justify-align: flex-start; --cmsmasters-variations-text-align: left;',
					'center' => '--cmsmasters-variations-align: center; --cmsmasters-variations-justify-align: center; --cmsmasters-variations-text-align: center;',
					'flex-end' => '--cmsmasters-variations-align: flex-end; --cmsmasters-variations-justify-align: flex-end; --cmsmasters-variations-text-align: right;',
				),
				'selectors' => array(
					$selector => '{{VALUE}}',
				),
				'condition' => array( 'variations_type' => 'column' ),
			)
		);

		$this->add_responsive_control(
			'variations_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$selector => '--cmsmasters-variations-spacing: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'variations_item_heading',
			array(
				'label' => __( 'Item', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'variations_item_spacing',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$selector => '--cmsmasters-variations-item-spacing: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'variations_item_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					$selector => '--cmsmasters-variations-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'variations_item_bd',
				'fields_options' => array(
					'width' => array( 'label' => __( 'Border Width', 'cmsmasters-elementor' ) ),
					'color' => array( 'label' => __( 'Border Color', 'cmsmasters-elementor' ) ),
				),
			)
		);

		$this->add_control(
			'variations_label_heading',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'variations_label_hide',
			array(
				'label' => __( 'Hide', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors_dictionary' => array(
					'yes' => 'none',
					'' => 'block',
				),
				'selectors' => array(
					$selector => '--cmsmasters-variations-label-hide: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'variations_label_typography',
				'condition' => array( 'variations_label_hide' => '' ),
			)
		);

		$this->add_control(
			'variations_label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-variations-label-color: {{VALUE}}',
				),
				'condition' => array( 'variations_label_hide' => '' ),
			)
		);

		$this->add_responsive_control(
			'variations_label_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'%' => array( 'max' => 50 ),
				),
				'selectors' => array(
					$selector => '--cmsmasters-variations-label-width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'variations_type' => 'row',
					'variations_label_hide' => '',
				),
			)
		);

		$this->add_responsive_control(
			'variations_label_spacing',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$selector => '--cmsmasters-variations-label-spacing: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'variations_label_hide' => '' ),
			)
		);

		$this->add_control(
			'variations_reset_heading',
			array(
				'label' => __( 'Reset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'wpcvs_variations_reset_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors_dictionary' => array(
					'yes' => 'block',
					'' => 'none',
				),
				'default' => 'yes',
				'selectors' => array(
					$this->product_base . '.reset_variations' => "--cmsmasters-variations-reset-show: {{VALUE}}",
				),
			)
		);

		$this->add_responsive_control(
			'variations_reset_alignment',
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
					$this->product_base . '.reset_variations' => '--cmsmasters-variations-reset-alignment: {{VALUE}}',
				),
				'condition' => array( 'wpcvs_variations_reset_show' => 'yes' ),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'variations_reset_typography',
				'condition' => array( 'wpcvs_variations_reset_show' => 'yes' ),
			)
		);

		$this->add_control(
			'variations_reset_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-variations-reset-color: {{VALUE}}',
				),
				'condition' => array( 'wpcvs_variations_reset_show' => 'yes' ),
			)
		);

		$this->add_control(
			'variations_reset_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-variations-reset-hover-color: {{VALUE}}',
				),
				'condition' => array( 'wpcvs_variations_reset_show' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'variations_reset_spacing',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$selector => '--cmsmasters-variations-reset-spacing: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'wpcvs_variations_reset_show' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_wpcvs_atc_variation_attribute_type_section_style() {
		$selector = $this->product_base . '.cmsmasters_wpcvs_variation';

		$this->start_controls_section(
			'wpcvs_atc_variation_attribute_type_section_style',
			array(
				'label' => __( 'Attribute Type', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'variation_attribute_type_tabs' );

		foreach ( array(
			'button' => __( 'Button', 'cmsmasters-elementor' ),
			'color' => __( 'Color', 'cmsmasters-elementor' ),
			'image' => __( 'Image', 'cmsmasters-elementor' ),
			'radio' => __( 'Radio', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$this->start_controls_tab(
				"variation_attribute_type_{$main_key}_tab",
				array( 'label' => $label )
			);

			if ( 'button' === $main_key || 'radio' === $main_key ) {
				$this->add_responsive_control(
					"variation_attribute_type_{$main_key}_type",
					array(
						'label' => __( 'Type', 'cmsmasters-elementor' ),
						'type' => CmsmastersControls::CHOOSE_TEXT,
						'options' => array(
							'row' => array( 'title' => __( 'Horizontal', 'cmsmasters-elementor' ) ),
							'column' => array( 'title' => __( 'Vertical', 'cmsmasters-elementor' ) ),
						),
						'label_block' => false,
						'toggle' => false,
						'selectors_dictionary' => array(
							'row' => "--cmsmasters-variations-attribute-type-{$main_key}-type: row; --cmsmasters-variations-attribute-type-{$main_key}-max-width: max-content;",
							'column' => "--cmsmasters-variations-attribute-type-{$main_key}-type: column; --cmsmasters-variations-attribute-type-{$main_key}-max-width: 100%;",
						),
						'selectors' => array(
							$selector => '{{VALUE}}',
						),
					)
				);
			}

			$this->add_control(
				"variation_attribute_type_{$main_key}_selected_icon",
				array(
					'label' => __( 'Selected Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'selectors_dictionary' => array(
						'yes' => 'block',
						'' => 'none',
					),
					'default' => 'yes',
					'selectors' => array(
						$selector => "--cmsmasters-variations-attribute-type-{$main_key}-selected-icon: {{VALUE}}",
					),
				)
			);

			if ( 'color' === $main_key || 'image' === $main_key ) {
				$this->add_responsive_control(
					"variation_attribute_type_{$main_key}_size",
					array(
						'label' => __( 'Size', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => array(
							'px',
							'em',
							'vw',
						),
						'range' => array(
							'px' => array(
								'min' => 20,
								'max' => 100,
							),
						),
						'selectors' => array(
							$selector => "--cmsmasters-variations-attribute-type-{$main_key}-size: {{SIZE}}{{UNIT}}",
						),
					)
				);
			}

			$this->add_responsive_control(
				"variation_attribute_type_{$main_key}_spacing",
				array(
					'label' => __( 'Space Between', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array( 'max' => 50 ),
					),
					'selectors' => array(
						$selector => "--cmsmasters-variations-attribute-type-{$main_key}-spacing: {{SIZE}}{{UNIT}}",
					),
				)
			);

			if ( 'button' === $main_key ) {
				$this->register_wpcvs_atc_variation_attribute_type_button_section_style( $selector );
			}

			if ( 'color' === $main_key || 'image' === $main_key ) {
				$this->register_wpcvs_atc_variation_attribute_type_color_image_section_style( $main_key, $selector );
			}

			if ( 'radio' === $main_key ) {
				$this->register_wpcvs_atc_variation_attribute_type_radio_section_style( $selector );
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_wpcvs_atc_variation_attribute_type_button_section_style( $selector ) {
		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover/Active', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			if ( 'normal' !== $main_key ) {
				$this->add_control(
					"variation_attribute_type_button_{$main_key}_divider",
					array( 'type' => Controls_Manager::DIVIDER )
				);
			}

			$this->add_control(
				"variation_attribute_type_button_{$main_key}_heading",
				array(
					'label' => $label,
					'type' => Controls_Manager::HEADING,
				)
			);

			if ( 'normal' === $main_key ) {
				$this->add_group_control(
					CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
					array(
						'name' => 'variation_attribute_type_button_typography',
					)
				);
			}

			$this->add_control(
				"variation_attribute_type_button_{$main_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-button-{$main_key}-color: {{VALUE}};",
					),
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"variation_attribute_type_button_{$main_key}_selected_icon_color",
					array(
						'label' => __( 'Selected Icon Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--cmsmasters-variations-button-{$main_key}-selected-icon-color: {{VALUE}};",
						),
						'condition' => array( 'variation_attribute_type_button_selected_icon' => 'yes' ),
					)
				);
			}

			$this->add_control(
				"variation_attribute_type_button_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-button-{$main_key}-bg-color: {{VALUE}};",
					),
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"variation_attribute_type_button_{$main_key}_selected_icon_bg_color",
					array(
						'label' => __( 'Selected Icon Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--cmsmasters-variations-button-{$main_key}-selected-icon-bg-color: {{VALUE}};",
						),
						'condition' => array( 'variation_attribute_type_button_selected_icon' => 'yes' ),
					)
				);
			}

			$this->add_control(
				"variation_attribute_type_button_{$main_key}_border_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-button-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( 'variation_attribute_type_button_border!' => 'none' ),
				)
			);

			if ( 'normal' === $main_key ) {
				$this->add_responsive_control(
					'variation_attribute_type_button_padding',
					array(
						'label' => __( 'Padding', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
						),
						'selectors' => array(
							$selector => '--cmsmasters-variations-button-padding-top: {{TOP}}{{UNIT}}; --cmsmasters-variations-button-padding-right: {{RIGHT}}{{UNIT}}; --cmsmasters-variations-button-padding-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-variations-button-padding-left: {{LEFT}}{{UNIT}};',
						),
					)
				);
			}

			$this->add_responsive_control(
				"variation_attribute_type_button_{$main_key}_border_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						$selector => "--cmsmasters-variations-button-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "variation_attribute_type_button_{$main_key}",
					'label' => _x( 'Text Shadow', 'Text Shadow Control', 'cmsmasters-elementor' ),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "variation_attribute_type_button_{$main_key}",
				)
			);

			if ( 'normal' === $main_key ) {
				$this->add_group_control(
					CmsmastersControls::VARS_BORDER_GROUP,
					array(
						'name' => 'variation_attribute_type_button',
						'exclude' => array( 'color' ),
						'fields_options' => array(
							'width' => array( 'label' => __( 'Border Width', 'cmsmasters-elementor' ) ),
						),
					)
				);
			}
		}
	}

	protected function register_wpcvs_atc_variation_attribute_type_color_image_section_style( $type, $selector ) {
		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover/Active', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			if ( 'normal' !== $main_key ) {
				$this->add_control(
					"variation_attribute_type_{$type}_{$main_key}_divider",
					array( 'type' => Controls_Manager::DIVIDER )
				);
			}

			$this->add_control(
				"variation_attribute_type_{$type}_{$main_key}_heading",
				array(
					'label' => $label,
					'type' => Controls_Manager::HEADING,
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"variation_attribute_type_{$type}_{$main_key}_selected_icon_color",
					array(
						'label' => __( 'Selected Icon Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--cmsmasters-variations-{$type}-{$main_key}-selected-icon-color: {{VALUE}};",
						),
						'condition' => array( "variation_attribute_type_{$type}_selected_icon" => 'yes' ),
					)
				);
			}

			$this->add_control(
				"variation_attribute_type_{$type}_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-{$type}-{$main_key}-bg-color: {{VALUE}};",
					),
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"variation_attribute_type_{$type}_{$main_key}_selected_icon_bg_color",
					array(
						'label' => __( 'Selected Icon Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--cmsmasters-variations-{$type}-{$main_key}-selected-icon-bg-color: {{VALUE}};",
						),
						'condition' => array( "variation_attribute_type_{$type}_selected_icon" => 'yes' ),
					)
				);
			}

			$this->add_control(
				"variation_attribute_type_{$type}_{$main_key}_border_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-{$type}-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( "variation_attribute_type_{$type}_border!" => 'none' ),
				)
			);

			if ( 'normal' === $main_key ) {
				$this->add_responsive_control(
					"variation_attribute_type_{$type}_padding",
					array(
						'label' => __( 'Padding', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
						),
						'selectors' => array(
							$selector => "--cmsmasters-variations-{$type}-padding-top: {{TOP}}{{UNIT}}; --cmsmasters-variations-{$type}-padding-right: {{RIGHT}}{{UNIT}}; --cmsmasters-variations-{$type}-padding-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-variations-{$type}-padding-left: {{LEFT}}{{UNIT}};",
						),
					)
				);
			}

			$this->add_responsive_control(
				"variation_attribute_type_{$type}_{$main_key}_border_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						$selector => "--cmsmasters-variations-{$type}-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array( 'name' => "variation_attribute_type_{$type}_{$main_key}" )
			);

			if ( 'normal' === $main_key ) {
				$this->add_group_control(
					CmsmastersControls::VARS_BORDER_GROUP,
					array(
						'name' => "variation_attribute_type_{$type}",
						'exclude' => array( 'color' ),
						'fields_options' => array(
							'width' => array( 'label' => __( 'Border Width', 'cmsmasters-elementor' ) ),
						),
					)
				);
			}
		}
	}

	protected function register_wpcvs_atc_variation_attribute_type_radio_section_style( $selector ) {
		$this->add_responsive_control(
			'variation_attribute_type_radio_label_spacing',
			array(
				'label' => __( 'Label Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array( 'max' => 50 ),
				),
				'selectors' => array(
					$selector => '--cmsmasters-variations-attribute-type-radio-label-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover/Active', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			if ( 'normal' !== $main_key ) {
				$this->add_control(
					"variation_attribute_type_radio_{$main_key}_divider",
					array( 'type' => Controls_Manager::DIVIDER )
				);
			}

			$this->add_control(
				"variation_attribute_type_radio_{$main_key}_heading",
				array(
					'label' => $label,
					'type' => Controls_Manager::HEADING,
				)
			);

			if ( 'normal' === $main_key ) {
				$this->add_group_control(
					CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
					array( 'name' => 'variation_attribute_type_radio_typography' )
				);
			}

			$this->add_control(
				"variation_attribute_type_radio_{$main_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-radio-{$main_key}-color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"variation_attribute_type_radio_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-radio-{$main_key}-bg-color: {{VALUE}};",
					),
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"variation_attribute_type_radio_{$main_key}_selected_icon_bg_color",
					array(
						'label' => __( 'Selected Icon Background Color', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::COLOR,
						'selectors' => array(
							$selector => "--cmsmasters-variations-radio-{$main_key}-selected-icon-bg-color: {{VALUE}};",
						),
						'condition' => array( 'variation_attribute_type_radio_selected_icon' => 'yes' ),
					)
				);
			}

			$this->add_control(
				"variation_attribute_type_radio_{$main_key}_border_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => "--cmsmasters-variations-radio-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( 'variation_attribute_type_radio_border!' => 'none' ),
				)
			);

			if ( 'normal' === $main_key ) {
				$this->add_responsive_control(
					'variation_attribute_type_radio_padding',
					array(
						'label' => __( 'Padding', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'em',
						),
						'selectors' => array(
							$selector => '--cmsmasters-variations-radio-padding-top: {{TOP}}{{UNIT}}; --cmsmasters-variations-radio-padding-right: {{RIGHT}}{{UNIT}}; --cmsmasters-variations-radio-padding-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-variations-radio-padding-left: {{LEFT}}{{UNIT}};',
						),
					)
				);
			}

			$this->add_responsive_control(
				"variation_attribute_type_radio_{$main_key}_border_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						$selector => "--cmsmasters-variations-radio-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array( 'name' => "variation_attribute_type_radio_{$main_key}" )
			);

			if ( 'normal' === $main_key ) {
				$this->add_group_control(
					CmsmastersControls::VARS_BORDER_GROUP,
					array(
						'name' => 'variation_attribute_type_radio',
						'exclude' => array( 'color' ),
						'fields_options' => array(
							'width' => array( 'label' => __( 'Border Width', 'cmsmasters-elementor' ) ),
						),
					)
				);
			}
		}
	}

	protected function register_wpcvs_atc_variation_tooltip_section_style() {
		$selector = $this->product_base . '.cmsmasters_wpcvs_variation';

		$this->start_controls_section(
			'wpcvs_atc_variation_tooltip_section_style',
			array(
				'label' => __( 'Tooltip', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'variation_tooltip_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--variation-tooltip-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
			)
		);

		$this->add_control(
			'variation_tooltip_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-variations-tooltip-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'variation_tooltip_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-variations-tooltip-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'variation_tooltip_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-variations-tooltip-border-color: {{VALUE}};',
				),
				'condition' => array( 'variation_tooltip_bd_border!' => 'none' ),
			)
		);

		$wpcvs_settings = ( null !== get_option( 'wpcvs_settings' ) ? get_option( 'wpcvs_settings' ) : '' );

		if ( 'tippy' === $wpcvs_settings['tooltip_library'] ) {
			$this->add_responsive_control(
				'variation_tooltip_width',
				array(
					'label' => __( 'Width', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'selectors' => array(
						$selector => '--cmsmasters-variations-tooltip-width: {{SIZE}}{{UNIT}}',
					),
				)
			);
		}

		$this->add_responsive_control(
			'variation_tooltip_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					$selector => '--cmsmasters-variations-tooltip-padding-top: {{TOP}}{{UNIT}}; --cmsmasters-variations-tooltip-padding-right: {{RIGHT}}{{UNIT}}; --cmsmasters-variations-tooltip-padding-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-variations-tooltip-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'variation_tooltip_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					$selector => '--cmsmasters-variations-tooltip-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array( 'name' => 'variation_tooltip' )
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'variation_tooltip_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => __( 'Border Width', 'cmsmasters-elementor' ) ),
				),
			)
		);

		if ( 'tippy' === $wpcvs_settings['tooltip_library'] ) {
			$this->add_control(
				'variation_tooltip_name_heading',
				array(
					'label' => __( 'Name', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'variation_tooltip_name_gap',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'selectors' => array(
						$selector => '--cmsmasters-variations-tooltip-name-gap: {{SIZE}}{{UNIT}}',
					),
				)
			);

			$this->add_control(
				'variation_tooltip_description_heading',
				array(
					'label' => __( 'Description', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
				array(
					'name' => 'variation_tooltip_description_typography',
					'fields_options' => array(
						'font_family' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-font-family: {{VALUE}};',
							),
						),
						'font_size' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-font-size: {{SIZE}}{{UNIT}};',
							),
						),
						'font_weight' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-font-weight: {{VALUE}};',
							),
						),
						'text_transform' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-text-transform: {{VALUE}};',
							),
						),
						'font_style' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-font-style: {{VALUE}};',
							),
						),
						'text_decoration' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-text-decoration: {{VALUE}}',
							),
						),
						'line_height' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-line-height: {{SIZE}}{{UNIT}};',
							),
						),
						'letter_spacing' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-letter-spacing: {{SIZE}}{{UNIT}};',
							),
						),
						'word_spacing' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--variation-tooltip-description-word-spacing: {{SIZE}}{{UNIT}}',
							),
						),
					),
				)
			);

			$this->add_control(
				'variation_tooltip_description_color',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => '--cmsmasters-variations-tooltip-desc-color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'variation_tooltip_description_gap',
				array(
					'label' => __( 'Gap', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array( 'max' => 300 ),
					),
					'selectors' => array(
						$selector => '--cmsmasters-variations-tooltip-desc-gap: {{SIZE}}{{UNIT}}',
					),
				)
			);
		}

		$this->add_control(
			'variation_tooltip_arrow_heading',
			array(
				'label' => __( 'Arrow', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'variation_tooltip_arrow_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$selector => '--cmsmasters-variations-tooltip-arrow-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_group_controls_style() {
		$this->start_controls_section(
			'section_group_style',
			array(
				'label' => __( 'Group', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$wpcvs_settings = ( null !== get_option( 'wpcvs_settings' ) ? get_option( 'wpcvs_settings' ) : '' );

		if ( class_exists( 'WPCleverWpcvs' ) && $wpcvs_settings && 'yes' !== $wpcvs_settings['button_default'] ) {
			$this->add_control(
				'group_description',
				array(
					'raw' => __( 'To enable the settings for WPC Variation Swatches proceed to the ', 'cmsmasters-elementor' ) . '<a href="admin.php?page=wpclever-wpcvs" target="_blank">' . __( 'plugins settings page', 'cmsmasters-elementor' ) . '</a>' . __( ' and set "Button swatch by default" to "Yes".', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'render_type' => 'ui',
				)
			);
		}

		$this->start_controls_tabs( 'group_style_tabs' );

		$this->start_controls_tab( 'group_style_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'group_label_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.woocommerce-grouped-product-list-item__label a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'group_style_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'group_label_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . '.woocommerce-grouped-product-list-item__label a:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'group_label_transition',
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
					$this->product_base . '.woocommerce-grouped-product-list-item__label a' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_price_controls_style() {
		$this->start_controls_section(
			'section_price_style',
			array(
				'label' => __( 'Price', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'price_alignment',
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
					$this->product_base => '--cmsmasters-price-alignment: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'price_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 60,
					),
				),
				'selectors' => array(
					$this->product_base . 'form.cart .woocommerce-variation .woocommerce-variation-price' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'price_typography',
				'selector' => $this->product_base . '.woocommerce-variation-price .price,' . $this->product_base . '.woocommerce-grouped-product-list-item__price',
			)
		);

		$this->start_controls_tabs( 'price_style_tabs' );

		$this->start_controls_tab( 'price_style_regular',
			array(
				'label' => __( 'Regular', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'price_color_regular',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$this->product_base . '.woocommerce-variation-price .price,' . $this->product_base . '.woocommerce-grouped-product-list-item__price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'price_style_sale',
			array(
				'label' => __( 'Sale', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'price_sale_row',
			array(
				'label' => __( 'Row View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'cmsmasters-price-row-',
			)
		);

		$this->add_control(
			'price_sale_first',
			array(
				'label' => __( 'Sale Price First', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'prefix_class' => 'cmsmasters-sale-first-',
			)
		);

		$this->add_control(
			'price_color_sale',
			array(
				'label' => __( 'Sale Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$this->product_base . '.woocommerce-variation-price .price ins,' . $this->product_base . '.woocommerce-grouped-product-list-item__price ins' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'price_color_sale_regular',
			array(
				'label' => __( 'Regular Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					$this->product_base . '.woocommerce-variation-price .price del,' . $this->product_base . '.woocommerce-grouped-product-list-item__price del' => 'color: {{VALUE}};',
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
					$this->product_base . '.woocommerce-variation-price .price del,' . $this->product_base . '.woocommerce-grouped-product-list-item__price del' => 'font-size: {{SIZE}}em',
				),
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
					$this->product_base . 'del + ins' => '--price-margin: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'price_currency',
			array(
				'label' => __( 'Currency Symbol', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'price_currency_scale',
			array(
				'label' => __( 'Currency Scale', 'cmsmasters-elementor' ),
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
					$this->product_base . '.woocommerce-variation-price .price .woocommerce-Price-currencySymbol,' . $this->product_base . '.woocommerce-grouped-product-list-item__price .woocommerce-Price-currencySymbol' => 'font-size: {{SIZE}}em',
				),
			)
		);

		$this->add_control(
			'price_currency_vertical_align',
			array(
				'label' => __( 'Currency Vertical Alignment', 'cmsmasters-elementor' ),
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
					$this->product_base . '.woocommerce-variation-price .price .woocommerce-Price-currencySymbol,' . $this->product_base . '.woocommerce-grouped-product-list-item__price .woocommerce-Price-currencySymbol' => 'vertical-align: {{VALUE}};',
				),
				'condition' => array( 'price_currency_scale!' => '' ),
			)
		);

		$this->end_controls_section();
	}

	protected function register_description_controls_style() {
		$this->start_controls_section(
			'section_description_style',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$selector = '.woocommerce-variation-description';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'description_typography',
				'selector' => $this->product_base . $selector,
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base . $selector => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'description_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$this->product_base . $selector => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_stock_controls_style() {
		$this->start_controls_section(
			'section_stock_style',
			array(
				'label' => __( 'Stock', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'hide_stock',
			array(
				'label' => __( 'Hide Stock', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'stock_alignment',
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
					$this->product_base => '--cmsmasters-stock-alignment: {{VALUE}};',
				),
				'condition' => array( 'hide_stock!' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'stock_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-stock-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => $this->product_base,
				'condition' => array( 'hide_stock!' => 'yes' ),
			)
		);

		$this->add_control(
			'stock_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$this->product_base => '--cmsmasters-stock-color: {{VALUE}};',
				),
				'condition' => array( 'hide_stock!' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'stock_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => array(
					$this->product_base => '--cmsmasters-stock-spacing: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'hide_stock!' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render variations reset button on the frontend.
	 *
	 * @since 1.0.0
	 * @since 1.11.0 Added render variations reset button.
	 * @since 1.18.4 Added render custom variations reset button label.
	 */
	public function get_variations_reset() {
		$settings = $this->get_settings();

		$reset_text = ( isset( $settings['variations_reset_text'] ) && ! empty( $settings['variations_reset_text'] ) ? esc_html( $settings['variations_reset_text'] ) : esc_html__( 'Clear', 'cmsmasters-elementor' ) );

		echo '<a class="reset_variations" href="#">' .
			$reset_text . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'</a>';
	}

	/**
	 * Render custom add to cart button on the frontend.
	 *
	 * @since 1.11.8
	 */
	public function custom_add_to_cart_button() {
		$settings = $this->get_active_settings();

		$product_id = get_the_ID();
		$product = wc_get_product( $product_id );

		$product_type = $product->get_type();

		echo '<button type="submit" class="cmsmasters-add-to-cart-button single_add_to_cart_button button alt ' . esc_attr( $product_type ) . esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ) . '"' . ( 'external' !== $product_type ? ' data-product_id="' . esc_attr( $product_id ) . '"' : '' ) . '>' .
			esc_html( $product->single_add_to_cart_text() );

			echo '<span class="cmsmasters-add-to-cart-button__icon">';

				if ( ! empty( $settings['button_icon']['value'] ) ) {
					Icons_Manager::render_icon(
						$settings['button_icon'],
						array(
							'class' => 'cmsmasters-add-to-cart-button-icon',
							'aria-hidden' => 'true',
						)
					);
				}

			echo '</span>';

		echo '</button>';
	}

	/**
	 * Render custom add to cart button text on the frontend.
	 *
	 * @since 1.11.8
	 */
	public function custom_add_to_cart_text() {
		$settings = $this->get_active_settings();

		$product = wc_get_product( get_the_ID() );

		$purchasable = $product->is_purchasable();
		$stock = $product->is_in_stock();
		$button_text = ( isset( $settings['button_text'] ) && ! empty( $settings['button_text'] ) ? esc_html( $settings['button_text'] ) : __( 'Add to Cart', 'cmsmasters-elementor' ) );
		$product_type = $product->get_type();
		$button_more_text = ( 'external' === $product_type ? $product->add_to_cart_text() : __( 'Read more', 'cmsmasters-elementor' ) );

		$new_text = $purchasable && $stock ? $button_text : $button_more_text;

		return $new_text;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.11.0 Added render WPC Variation Swatches.
	 */
	protected function render() {
		global $product;

		$product = wc_get_product();
		$settings = $this->get_settings_for_display();

		if ( empty( $product ) ) {
			return;
		}

		echo '<div class="cmsmasters-add-to-cart cmsmasters-product-' . esc_attr( $product->get_type() ) . ( class_exists( 'WPCleverWpcvs' ) ? ' cmsmasters_wpcvs_variation' : '' ) . '">';

		if ( $settings['hide_stock'] ) {
			add_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
		}

		add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'custom_add_to_cart_text' ) );

		add_filter( 'woocommerce_after_add_to_cart_button', array( $this, 'custom_add_to_cart_button' ) );

		add_filter( 'woocommerce_reset_variations_link', '__return_empty_string' );

		add_action( 'woocommerce_after_variations_table', array( $this, 'get_variations_reset' ) );

		woocommerce_template_single_add_to_cart();

		if ( $settings['hide_stock'] ) {
			remove_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
		}

		echo '</div>';
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}
}
