<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Product_Add_To_Cart_Button extends Base_Widget {

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
		return __( 'Add To Cart Button', 'cmsmasters-elementor' );
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
			'button',
			'add to cart',
		);
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
			Base_Document::SITE_WIDGETS_CATEGORY,
			Base_Document::WOO_WIDGETS_CATEGORY,
			Base_Document::WOO_SINGULAR_WIDGETS_CATEGORY,
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
		return array(
			'wc-add-to-cart',
			'wc-add-to-cart-variation',
			'wc-single-product',
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
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Fixed selectors for loading button.
	 * @since 1.1.0 Added controls & gradient for add to cart button to match global button control settings.
	 * @since 1.3.5 Added controls for icons.
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_atc_button',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'product_id',
			array(
				'label' => __( 'Custom Product (Optional)', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersControls::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::POST_OBJECT,
					'query' => array( 'post_type' => 'product' ),
				),
				'export' => false,
			)
		);

		$this->add_control(
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
				),
				'default' => 'left',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart' => 'text-align: {{VALUE}};',
				),
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
						'min' => 140,
						'max' => 300,
						'step' => 5,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart > a' => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_tabs' );

		foreach ( array(
			'normal' => __( 'Default', 'cmsmasters-elementor' ),
			'loading' => __( 'Loading', 'cmsmasters-elementor' ),
			'added' => __( 'Added', 'cmsmasters-elementor' ),
		) as $button_key => $label ) {
			$this->start_controls_tab(
				"icon_tab_{$button_key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"icon_{$button_key}",
				array(
					'type' => Controls_Manager::ICONS,
					'label' => __( 'Icon', 'cmsmasters-elementor' ),
					'label_block' => false,
					'fa4compatibility' => 'icon',
					'skin' => 'inline',
					'exclude_inline_options' => array( 'svg' ),
					'frontend_available' => true,
				)
			);

			if ( 'normal' === $button_key ) {
				$this->add_control(
					'icon_view',
					array(
						'label' => __( 'View', 'cmsmasters-elementor' ),
						'type' => CmsmastersControls::CHOOSE_TEXT,
						'options' => array(
							'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
							'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
							'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
						),
						'default' => 'default',
						'label_block' => false,
						'render_type' => 'template',
						'condition' => array(
							'icon_normal[value]!' => '',
						),
					)
				);

				$this->add_control(
					'icon_shape',
					array(
						'label' => __( 'Shape', 'cmsmasters-elementor' ),
						'type' => CmsmastersControls::CHOOSE_TEXT,
						'options' => array(
							'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
							'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
						),
						'default' => 'square',
						'label_block' => false,
						'render_type' => 'template',
						'condition' => array(
							'icon_view!' => 'default',
							'icon_normal[value]!' => '',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'icon_align',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'row-reverse' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'row' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'row',
				'toggle' => false,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_atc_button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-add-to-cart > a',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_ACCENT ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_border',
				'selector' => '{{WRAPPER}} .cmsmasters-add-to-cart > a',
				'exclude' => array( 'color' ),
			)
		);

		$this->start_controls_tabs( 'button_style_tabs' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'loading' => __( 'Loading', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $button_key => $label ) {
			$button_selector_base = '{{WRAPPER}} .cmsmasters-add-to-cart > a';

			if ( 'loading' === $button_key ) {
				$button_selector = "{$button_selector_base}.loading";

				$gradient_bg_selector = "{$button_selector}:before, {$button_selector}:after";
			} elseif ( 'hover' === $button_key ) {
				$button_selector = "{$button_selector_base}:hover";

				$gradient_bg_selector = "{$button_selector_base}:after";
			} elseif ( 'normal' === $button_key ) {
				$button_selector = $button_selector_base;

				$gradient_bg_selector = "{$button_selector_base}:before";
			}

			$this->start_controls_tab(
				"button_tab_{$button_key}",
				array( 'label' => $label )
			);

			$this->add_control(
				"button_text_color_{$button_key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$button_selector => "--button-{$button_key}-text-color: {{VALUE}}",
					),
				)
			);

			$this->add_control(
				"button_bg_color_group_{$button_key}_background",
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
				"button_bg_color_{$button_key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$gradient_bg_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"button_bg_color_group_{$button_key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"button_bg_color_group_{$button_key}_color_stop",
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
						"button_bg_color_group_{$button_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_color_group_{$button_key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_color_group_{$button_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_color_group_{$button_key}_color_b_stop",
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
						"button_bg_color_group_{$button_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_color_group_{$button_key}_gradient_type",
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
						"button_bg_color_group_{$button_key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_color_group_{$button_key}_gradient_angle",
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
						$gradient_bg_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_bg_color_group_{$button_key}_color_stop.SIZE}}{{button_bg_color_group_{$button_key}_color_stop.UNIT}}, {{button_bg_color_group_{$button_key}_color_b.VALUE}} {{button_bg_color_group_{$button_key}_color_b_stop.SIZE}}{{button_bg_color_group_{$button_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_color_group_{$button_key}_background" => array( 'gradient' ),
						"button_bg_color_group_{$button_key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_color_group_{$button_key}_gradient_position",
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
						$gradient_bg_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_bg_color_group_{$button_key}_color_stop.SIZE}}{{button_bg_color_group_{$button_key}_color_stop.UNIT}}, {{button_bg_color_group_{$button_key}_color_b.VALUE}} {{button_bg_color_group_{$button_key}_color_b_stop.SIZE}}{{button_bg_color_group_{$button_key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_color_group_{$button_key}_background" => array( 'gradient' ),
						"button_bg_color_group_{$button_key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_border_color_{$button_key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$button_selector => 'border-color: {{VALUE}}',
					),
					'condition' => array( 'button_border_border!' => '' ),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "button_text_shadow_{$button_key}",
					'selector' => $button_selector,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "button_box_shadow_{$button_key}",
					'selector' => $button_selector,
				)
			);

			$border_radius_id = ( 'normal' === $button_key ) ? 'button_border_radius' : "button_border_radius_{$button_key}";

			$this->add_control(
				$border_radius_id,
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						$button_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			if ( 'hover' === $button_key ) {
				$this->add_control(
					'button_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array(
							'size' => 0.3,
						),
						'range' => array(
							'px' => array(
								'max' => 2,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							"{$button_selector_base}, {$button_selector_base}:before, {$button_selector_base}:after" => 'transition: all {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

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
				'default' => array(
					'top' => '10',
					'bottom' => '10',
					'left' => '20',
					'right' => '20',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-add-to-cart' => '--button-padding-right: {{RIGHT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'icon_normal[value]!' => '' ),
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
					'%',
					'vw',
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
					'{{WRAPPER}}' => '--button-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'icon_normal[value]!' => '' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_button_icon_style',
			array( 'condition' => array( 'icon_normal[value]!' => '' ) )
		);

		$this->start_controls_tab(
			'tab_button_icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-color-normal: {{VALUE}};',
				),
				'condition' => array( 'icon_normal[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-bg-color-normal: {{VALUE}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'button_icon_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-bd-color-normal: {{VALUE}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'button_icon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-bdr-normal: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_icon_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_icon_hover_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-color-hover: {{VALUE}};',
				),
				'condition' => array( 'icon_normal[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_bg_hover_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-bg-color-hover: {{VALUE}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-bd-color-hover: {{VALUE}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-bdr-hover: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_icon_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 50,
					),
					'em' => array(
						'min' => 0.1,
						'max' => 1.5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart' => '--icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
					'vw',
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
					'{{WRAPPER}} .cmsmasters-add-to-cart' => '--button-icon-pdd: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart__button-icon' => '--button-icon-border-top-width: {{TOP}}{{UNIT}}; --button-icon-border-right-width: {{RIGHT}}{{UNIT}}; --button-icon-border-bottom-width: {{BOTTOM}}{{UNIT}}; --button-icon-border-left-width: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'icon_normal[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'heading_icon_style',
			array(
				'label' => __( 'Loading & Added Icons', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
					'em' => array(
						'min' => 0.5,
						'max' => 2,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart' => '--loading-added-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-add-to-cart' => '--loading-added-icon-color: {{VALUE}};',
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
	 * @since 1.1.0 Fix for working button with Custom Product ID in non-woocommerce page.
	 * @since 1.3.4 Added control html class for button icons.
	 */
	protected function render() {
		global $product;

		$product = wc_get_product();

		$settings = $this->get_settings_for_display();

		if ( empty( $product ) ) {
			if ( ! empty( $settings['product_id'] ) ) {
				$product = wc_get_product( $settings['product_id'] );
			} else {
				return;
			}
		}

		if ( ! empty( $settings['product_id'] ) ) {
			$product_id = $settings['product_id'];
		} elseif ( wp_doing_ajax() && isset( $_POST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// PHPCS - No nonce is required.
			$product_id = sanitize_text_field( wp_unslash( $_POST['post_id'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			$product_id = get_queried_object_id();
		}

		$this->add_render_attribute( 'cmsmasters_add_to_cart', 'class', "cmsmasters-add-to-cart" );
		$this->add_render_attribute( 'cmsmasters_add_to_cart', 'class', 'cmsmasters-product-' . esc_attr( $product->get_type() ) . '' );

		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'filter_woocommerce_loop_add_to_cart_link' ), 10, 3 );

		echo '<div ' . $this->get_render_attribute_string( 'cmsmasters_add_to_cart' ) . '>';

			woocommerce_template_loop_add_to_cart( $product_id );

		echo '</div>';
	}


	/**
	 * Filter woocommerce_loop_add_to_cart_link.
	 *
	 * @since 1.1.0 Added as replace of filter_woocommerce_loop_add_to_cart_args to add icon classes.
	 * @since 1.3.4 Added icon for button.
	 */
	public function filter_woocommerce_loop_add_to_cart_link( $link, $product, $args ) {
		$settings = $this->get_settings_for_display();

		$icon = 'cmsmasters-add-to-cart__button-icon';
		$icon_normal = '';
		$icon_added = '';
		$icon_loading = '';
		$has_icon_loading = '';

		if ( ! empty( $settings['icon_normal']["value"] ) ) {
			$icon_normal = CmsmastersUtils::get_render_icon(
				$settings['icon_normal'],
				array(
					'class' => "$icon {$icon}-normal {$icon}-{$settings['icon_view']} {$icon}-{$settings['icon_shape']}",
					'aria-hidden' => 'true',
				),
				false
			);
		}

		if ( ! empty( $settings['icon_loading']["value"] ) ) {
			$icon_loading = CmsmastersUtils::get_render_icon(
				$settings['icon_loading'],
				array(
					'class' => "$icon {$icon}-loading {$icon}-{$settings['icon_view']} {$icon}-{$settings['icon_shape']}",
					'aria-hidden' => 'true',
				),
				false
			);

			$has_icon_loading = "{$icon}-has-custom-loading";

			if ( isset( $args['class'] ) ) {
				$args['class'] .= " {$has_icon_loading}";
			}
		}

		if ( ! empty( $settings['icon_added']["value"] ) ) {
			$icon_added = CmsmastersUtils::get_render_icon(
				$settings['icon_added'],
				array(
					'class' => "$icon {$icon}-added {$icon}-{$settings['icon_view']} {$icon}-{$settings['icon_shape']}",
					'aria-hidden' => 'true',
				),
				false
			);
		}

		return sprintf(
			'<a href="%s" data-quantity="%s" class="%s" aria-label="Add to cart button" %s data-view-icon="%s">%s' . $icon_normal . $icon_loading . '</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] : "button {$has_icon_loading}" ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_attr( ( ! empty( $settings['icon_added']["value"] ) ) ? $icon_added : '' ),
			'<span>' . esc_html( $product->add_to_cart_text() ) . '</span>'
		);
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
