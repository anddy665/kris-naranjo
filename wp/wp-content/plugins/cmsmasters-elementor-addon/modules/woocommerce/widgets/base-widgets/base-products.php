<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Controls\Groups\Group_Control_Button_Background;
use CmsmastersElementor\Modules\Woocommerce\Module as WooModule;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon base products widget class.
 *
 * @since 1.0.0
 */
abstract class Base_Products extends Base_Widget {

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
	}

	/**
	 * @since 1.0.0
	 */
	protected function get_html_wrapper_class() {
		$html_classes = parent::get_html_wrapper_class();

		$html_classes .= ' elementor-widget-cmsmasters-woo-products-similar';

		return $html_classes;
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
	 * LazyLoad widget use control.
	 *
	 * @since 1.11.1
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return true;
	}

	/**
	 * @since 1.0.0
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->register_main_content_controls();
		$this->register_sections_style_product();
	}

	/**
	 * Register products controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_main_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'template_layout',
			array(
				'label' => esc_html__( 'Product Template', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'default' => 'default',
				'options' => array(
					'default' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			WooModule::CONTROL_TEMPLATE_NAME,
			array(
				'label' => __( 'Choose Entry Template', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => CmsmastersControls::QUERY,
				'autocomplete' => array(
					'object' => Query_Manager::TEMPLATE_OBJECT,
					'query' => array(
						'meta_query' => array(
							array(
								'key' => Document::TYPE_META_KEY,
								'value' => 'cmsmasters_product_entry',
							),
						),
					),
				),
				'condition' => array(
					'template_layout' => array( 'custom' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			WooModule::CONTROL_TEMPLATE_NAME . '_prefix_class',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => 'entry',
				'prefix_class' => 'elementor-widget-cmsmasters-woo-products-',
				'condition' => array(
					WooModule::CONTROL_TEMPLATE_NAME . '!' => '',
					'template_layout' => array( 'custom' ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register products controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_sections_style_product() {
		$selectors_button_array = array(
			'{{WRAPPER}} ul.products li.product .button',
			'{{WRAPPER}} ul.products li.product .added_to_cart',
		);
		$selector_button = join( ',', $selectors_button_array );
		$selector_title = '{{WRAPPER}} ul.products li.product .woocommerce-loop-product__title, {{WRAPPER}} ul.products li.product .woocommerce-loop-category__title';
		$condition = array(
			'template_layout' => 'default',
		);

		$this->start_controls_section(
			'section_products_style',
			array(
				'label' => __( 'Products', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->add_control(
			'heading_image_style',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .attachment-woocommerce_thumbnail',
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .attachment-woocommerce_thumbnail' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'image_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .attachment-woocommerce_thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'heading_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->start_controls_tabs( 'tabs_title_style' );

		$states = array(
			'normal' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			'hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $states as $state => $label ) {
			$selector_title_state = $selector_title;

			if ( 'hover' === $state ) {
				$selector_title_state = '{{WRAPPER}} ul.products li.product > a:hover .woocommerce-loop-product__title, {{WRAPPER}} ul.products li.product > a:hover .woocommerce-loop-category__title';
			}

			$this->start_controls_tab(
				"tab_title_{$state}",
				array(
					'label' => $label,
					'condition' => $condition,
				)
			);

			$this->add_control(
				"title_color_{$state}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_title_state => 'color: {{VALUE}}',
					),
					'condition' => $condition,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => $selector_title,
				'condition' => $condition,

			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					$selector_title => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'heading_rating_style',
			array(
				'label' => __( 'Rating', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label' => __( 'Star Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product .star-rating' => 'color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'empty_star_color',
			array(
				'label' => __( 'Empty Star Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product .star-rating::before' => 'color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'star_size',
			array(
				'label' => __( 'Star Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'em',
				),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 4,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product .star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'rating_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product .star-rating' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'heading_price_style',
			array(
				'label' => __( 'Sale Price', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product .price' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.products li.product .price ins' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.products li.product .price ins .amount' => 'color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'price_typography',
				'selector' => '{{WRAPPER}} ul.products li.product .price',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'heading_old_price_style',
			array(
				'label' => __( 'Regular Price', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'old_price_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product .price del' => 'color: {{VALUE}}',
					'{{WRAPPER}} ul.products li.product .price del .amount' => 'color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'old_price_typography',
				'selector' => '{{WRAPPER}} ul.products li.product .price del .amount,' .
								'{{WRAPPER}} ul.products li.product .price del',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'heading_button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'heading_button_enable',
			array(
				'label' => __( 'Show Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'selector' => $selector_button,
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-font-style: {{VALUE}};',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--cmsmasters-button-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
				),
				'condition' => array_merge( $condition, array(
					'heading_button_enable!' => '',
				) ),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$states = array(
			'normal' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			'hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $states as $state => $label ) {
			$css_var_old_state_prefix = '';

			if ( 'hover' === $state ) {
				$css_var_old_state_prefix = '-hover';
			}

			$selector_button_state = $selector_button;
			$selector_button_state_bg_array = array_map( function ( $selector_button ) {
				$selector_button .= '::before';

				return $selector_button;
			}, $selectors_button_array );

			if ( 'hover' === $state ) {
				$selector_button_state_array = array_map( function ( $selector_button ) {
					$selector_button .= ':hover';

					return $selector_button;
				}, $selectors_button_array );
				$selector_button_state = join( ',', $selector_button_state_array );

				$selector_button_state_bg_array = array_map( function ( $selector_button ) {
					$selector_button .= ':hover::after';

					return $selector_button;
				}, $selectors_button_array );
			}

			$selector_button_state_bg = join( ',', $selector_button_state_bg_array );

			$this->start_controls_tab(
				"tab_button_{$state}",
				array(
					'label' => $label,
					'condition' => array_merge( $condition, array(
						'heading_button_enable!' => '',
					) ),
				)
			);

			$this->add_group_control(
				Group_Control_Button_Background::get_type(),
				array(
					'name' => "button_bgc_{$state}",
					'selector' => $selector_button_state_bg,
					'exclude' => array( 'color' ),
					'condition' => array_merge( $condition, array(
						'heading_button_enable!' => '',
					) ),
				)
			);

			$this->start_injection( array( 'of' => "button_bgc_{$state}_background" ) );

			$this->add_control(
				"button_bgc_{$state}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_button_state_bg => '--button-bg-color: {{VALUE}};' .
						'background-color: var( --button-bg-color );',
					),
					'condition' => array_merge( $condition, array(
						'heading_button_enable!' => '',
					) ),
				)
			);

			$this->end_injection();

			$this->add_control(
				"button_text_color_{$state}",
				array(
					'label' => __( 'Text Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_button_state => "--cmsmasters-button-{$state}-colors-color: {{VALUE}};",
					),
					'condition' => array_merge( $condition, array(
						'heading_button_enable!' => '',
					) ),
				)
			);

			$this->add_control(
				"button_bd_color_{$state}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector_button_state => "--cmsmasters-button-{$state}-colors-bd: {{VALUE}};",
					),
					'condition' => array_merge( $condition, array(
						'button_border_border!' => array( 'none' ),
						'heading_button_enable!' => '',
					) ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "button_shadow_{$state}",
					'selector' => $selector_button_state,
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--cmsmasters-button-{$state}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "button_text_shadow_{$state}",
					'selector' => $selector_button_state,
					'fields_options' => array(
						'text_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => '--cmsmasters-button-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
							),
						),
					),
					'condition' => array(
						'pagination_show!' => '',
						'pagination_type' => 'load_more',
					),
				)
			);

			$this->add_control(
				"button_text_decoration_{$state}",
				array(
					'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => array(
						'' => __( 'Default', 'cmsmasters-elementor' ),
						'none' => __( 'Disable', 'cmsmasters-elementor' ),
						'underline' => __( 'Underline', 'cmsmasters-elementor' ),
						'overline' => __( 'Overline', 'cmsmasters-elementor' ),
						'line-through' => __( 'Line Through', 'cmsmasters-elementor' ),
					),
					'selectors' => array(
						$selector_button_state => "--cmsmasters-button{$css_var_old_state_prefix}-text-decoration: {{VALUE}};",
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(), array(
				'name' => 'button_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => __( 'Disable', 'cmsmasters-elementor' ),
							'solid' => __( 'Solid', 'cmsmasters-elementor' ),
							'double' => __( 'Double', 'cmsmasters-elementor' ),
							'dotted' => __( 'Dotted', 'cmsmasters-elementor' ),
							'dashed' => __( 'Dashed', 'cmsmasters-elementor' ),
							'groove' => __( 'Groove', 'cmsmasters-elementor' ),
						),
					),
					'width' => array(
						'condition' => array(
							'border!' => array( '', 'none' ),
						),
					),
				),
				'selector' => $selector_button,
				'separator' => 'before',
				'condition' => array_merge( $condition, array(
					'heading_button_enable!' => '',
				) ),
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					$selector_button => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array_merge( $condition, array(
					'heading_button_enable!' => '',
				) ),
			)
		);

		$this->add_control(
			'button_text_padding',
			array(
				'label' => __( 'Text Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors' => array(
					$selector_button => '--cmsmasters-button-padding-top: {{TOP}}{{UNIT}}; --cmsmasters-button-padding-right: {{RIGHT}}{{UNIT}}; --cmsmasters-button-padding-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-button-padding-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array_merge( $condition, array(
					'heading_button_enable!' => '',
				) ),
			)
		);

		$this->add_responsive_control(
			'button_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					$selector_button => 'margin-top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array_merge( $condition, array(
					'heading_button_enable!' => '',
				) ),
			)
		);

		$this->add_control(
			'heading_view_cart_style',
			array(
				'label' => __( 'View Cart', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'view_cart_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .added_to_cart' => 'color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'view_cart_typography',
				'selector' => '{{WRAPPER}} .added_to_cart',
				'condition' => $condition,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_product',
			array(
				'label' => __( 'Product Box', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->start_controls_tabs( 'product_style_tabs' );

		$this->start_controls_tab( 'classic_style_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'product_shadow',
				'selector' => '{{WRAPPER}} ul.products li.product',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'product_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product' => 'background-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'product_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product' => 'border-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'classic_style_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
				'condition' => $condition,
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'product_shadow_hover',
				'selector' => '{{WRAPPER}} ul.products li.product:hover',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'product_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'product_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product' => 'text-align: {{VALUE}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'product_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_control(
			'product_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->add_responsive_control(
			'product_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition' => $condition,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'sale_flash_style',
			array(
				'label' => __( 'Sale Label', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => $condition,
			)
		);

		$this->add_control(
			'show_onsale_flash',
			array(
				'label' => __( 'Sale Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'render_type' => 'template',
				'condition' => $condition,
			)
		);

		$this->add_control(
			'onsale_text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product span.onsale' => 'color: {{VALUE}}',
				),
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->add_control(
			'onsale_text_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product span.onsale' => 'background-color: {{VALUE}}',
				),
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'onsale_typography',
				'selector' => '{{WRAPPER}} ul.products li.product span.onsale',
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->add_control(
			'onsale_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product span.onsale' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->add_control(
			'onsale_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product span.onsale' => 'min-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->add_control(
			'onsale_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product span.onsale' => 'min-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->add_control(
			'onsale_horizontal_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product span.onsale' => '{{VALUE}}',
				),
				'selectors_dictionary' => array(
					'left' => 'right: auto; left: 0',
					'right' => 'left: auto; right: 0',
				),
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->add_control(
			'onsale_distance',
			array(
				'label' => __( 'Distance', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => -20,
						'max' => 20,
					),
					'em' => array(
						'min' => -2,
						'max' => 2,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ul.products li.product span.onsale' => 'margin: {{SIZE}}{{UNIT}};',
				),
				'condition' => $condition + array(
					'show_onsale_flash!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Warning if included product filter.
	 *
	 * @since 1.11.1
	 *
	 * @return array
	 */
	public function warning_product_filter() {
		if ( class_exists( 'FrameWpf' ) ) {
			$this->add_control(
				'woo_warning_section',
				array(
					'raw' => __( 'Disable Sorting on the page with WBW Product Filter, as it has its own sorting functionality.', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type' => 'ui',
				)
			);
		}
	}

	/**
	 * Check if use custom template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_custom_template() {
		return 'custom' === $this->get_settings_for_display( 'template_layout' );
	}

	/**
	 * Get custom template ID.
	 *
	 * @since 1.0.0
	 * @since 1.12.1 Add checking template.
	 *
	 * @return int|false
	 */
	protected function get_template_id() {
		if ( ! $this->is_custom_template() ) {
			return false;
		}

		$template_id = $this->get_settings_for_display( WooModule::CONTROL_TEMPLATE_NAME );

		if ( ! CmsmastersUtils::check_template( $template_id ) ) {
			return false;
		}

		return $template_id;
	}

	/**
	 * Get template IDs.
	 *
	 * @since 1.11.1
	 *
	 * @return array Template IDs.
	 */
	public function get_template_ids() {
		$template_ids = $this->get_template_id();

		if ( empty( $template_ids ) ) {
			return array();
		}

		return array( $template_ids );
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Optimized loading CSS in templates.
	 */
	public function render() {
		if ( $this->is_custom_template() ) {
			$template_ids = $this->get_template_ids();

			if ( empty( $template_ids ) ) {
				if ( is_admin() ) {
					/* translators: Products widgets undefined template warning. %s: Products widget title */
					CmsmastersUtils::render_alert( sprintf( esc_html__( 'Please choose your custom "%s" widget template!', 'cmsmasters-elementor' ), $this->get_title() ) );
				}

				return;
			}

			if ( 'enable' !== $this->lazyload_widget_get_status() ) {
				/** @var Plugin $addon */
				$addon = Plugin::instance();

				$addon->frontend->print_template_css( $template_ids, $this->get_id() );
			}
		}

		$this->render_products_before();

		$this->render_products();

		$this->render_products_after();
	}

	/**
	 * Before rendering products.
	 *
	 * @since 1.0.0
	 */
	protected function render_products_before() {
		echo '<div class="elementor-widget-cmsmasters-woo-products-inner">';

		if ( ! $this->is_custom_template() ) {
			$settings = $this->get_settings_for_display();

			if ( ! $settings['show_onsale_flash'] ) {
				add_filter( 'woocommerce_sale_flash', '__return_empty_string' );
			}

			if ( ! $settings['heading_button_enable'] ) {
				add_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string' );
			}
		}
	}

	/**
	 * Render products.
	 *
	 * @since 1.0.0
	 */
	abstract protected function render_products();

	/**
	 * After rendering products.
	 *
	 * @since 1.0.0
	 */
	protected function render_products_after() {
		if ( ! $this->is_custom_template() ) {
			$settings = $this->get_settings_for_display();

			if ( ! $settings['show_onsale_flash'] ) {
				remove_filter( 'woocommerce_sale_flash', '__return_empty_string' );
			}

			if ( ! $settings['heading_button_enable'] ) {
				remove_filter( 'woocommerce_loop_add_to_cart_link', '__return_empty_string' );
			}
		}

		echo '</div>';
	}
}
