<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Notices extends Base_Widget {

	use Woo_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'WooCommerce Notices', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.8.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-woocommerce-notices';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'woocommerce',
			'notices',
			'notifications',
		);
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.8.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( Base_Document::WOO_WIDGETS_CATEGORY );
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

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-woo-notices';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.8.0
	 * @since 1.11.8 Fixed display of notice after WooCommerce update.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 */
	protected function register_controls() {
		$this->register_woocommerce_notices_controls_content();

		$this->register_woocommerce_error_notices_controls_style();

		$this->register_woocommerce_message_notices_controls_style();

		$this->register_woocommerce_info_notices_controls_style();
	}

	/**
	 * Register woocommerce notices controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.8.0
	 * @since 1.11.11 Added `Space Between` control for notices.
	 */
	protected function register_woocommerce_notices_controls_content() {
		$this->start_controls_section(
			'section',
			array( 'label' => esc_html__( 'WooCommerce Notices', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'one_per_page_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf(
					esc_html__( '%1$sNote:%2$s For optimal functionality, this widget can only be added once per page.', 'cmsmasters-elementor' ),
					'<strong>',
					'</strong>'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'woocommerce_notices_elements',
			array(
				'label' => esc_html__( 'Notice Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => array(
					'wc_error' => esc_html__( 'Error Notices', 'cmsmasters-elementor' ),
					'wc_message' => esc_html__( 'Message Notices', 'cmsmasters-elementor' ),
					'wc_info' => esc_html__( 'Info Notices', 'cmsmasters-elementor' ),
				),
				'render_type' => 'ui',
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'woocommerce_notices_space_between',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--woocommerce-notices-space-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function add_notice_box_controls( $prefix ) {
		$this->add_control(
			$prefix . '_notice_box_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Notice Box', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => $prefix . '_notice_box_bg',
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'color' => array(
						'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .woocommerce-' . $prefix,
			)
		);

		$this->add_responsive_control(
			$prefix . '_notice_box_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $prefix . '-box-top-padding: {{TOP}}{{UNIT}}; --' . $prefix . '-box-right-padding: {{RIGHT}}{{UNIT}}; --' . $prefix . '-box-bottom-padding: {{BOTTOM}}{{UNIT}}; --' . $prefix . '-box-left-padding: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$prefix . '_notice_box_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $prefix . '-box-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => $prefix . '_notice_box_border',
				'fields_options' => array(
					'border' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-border-style: {{VALUE}};',
						),
					),
					'width' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-border-top-width: {{TOP}}{{UNIT}}; --' . $prefix . '-border-right-width: {{RIGHT}}{{UNIT}}; --' . $prefix . '-border-bottom-width: {{BOTTOM}}{{UNIT}}; --' . $prefix . '-border-left-width: {{LEFT}}{{UNIT}};',
						),
					),
					'color' => array(
						'label' => esc_html_x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-border-color: {{VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => $prefix . '_notice_box_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'box_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);
	}

	private function add_notice_text_controls( $prefix ) {
		$this->add_control(
			$prefix . '_message_text_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Notice Text', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => $prefix . '_message_text_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			$prefix . '_message_text_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $prefix . '-message-text-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => $prefix . '_message_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'text_shadow' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-message-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			$prefix . '_icon_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			$prefix . '_icon_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $prefix . '-icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			$prefix . '_icon_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $prefix . '-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$prefix . '_icon_gap',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--' . $prefix . '-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);
	}

	private function add_notice_button_controls( $prefix ) {
		$this->add_control(
			$prefix . '_button_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Button', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => $prefix . '_button_typography',
				'fields_options' => array(
					'font_family' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-font-family: {{VALUE}};',
						),
					),
					'font_size' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-font-size: {{SIZE}}{{UNIT}};',
						),
					),
					'font_weight' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-font-weight: {{VALUE}};',
						),
					),
					'text_transform' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-text-transform: {{VALUE}};',
						),
					),
					'font_style' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-font-style: {{VALUE}};',
						),
					),
					'text_decoration' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-text-decoration: {{VALUE}}',
						),
					),
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-line-height: {{SIZE}}{{UNIT}};',
						),
					),
					'letter_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-letter-spacing: {{SIZE}}{{UNIT}};',
						),
					),
					'word_spacing' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--' . $prefix . '-button-word-spacing: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->start_controls_tabs( $prefix . '_button_styles' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $main_key => $label ) {
			$state = ( 'normal' === $main_key ? ':before' : ':after' );
			$buttons_bg_selector = "{{WRAPPER}} .woocommerce-{$prefix} .button{$state}";

			if ( 'info' === $prefix ) {
				$buttons_bg_selector .= ", {{WRAPPER}} .woocommerce-{$prefix} .woocommerce-Button{$state}";
			}

			$this->start_controls_tab(
				"{$prefix}_button_{$main_key}_tab",
				array( 'label' => $label )
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_color",
				array(
					'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--{$prefix}-button-{$main_key}-color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_bg_group_background",
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
				"{$prefix}_button_{$main_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$buttons_bg_selector => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"{$prefix}_button_{$main_key}_bg_group_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_bg_group_color_stop",
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
						"{$prefix}_button_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_bg_group_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"{$prefix}_button_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_bg_group_color_b_stop",
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
						"{$prefix}_button_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_bg_group_gradient_type",
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
						"{$prefix}_button_{$main_key}_bg_group_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_bg_group_gradient_angle",
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
						$buttons_bg_selector => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{{$prefix}_button_{$main_key}_bg_group_color_stop.SIZE}}{{{$prefix}_button_{$main_key}_bg_group_color_stop.UNIT}}, {{{$prefix}_button_{$main_key}_bg_group_color_b.VALUE}} {{{$prefix}_button_{$main_key}_bg_group_color_b_stop.SIZE}}{{{$prefix}_button_{$main_key}_bg_group_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"{$prefix}_button_{$main_key}_bg_group_background" => array( 'gradient' ),
						"{$prefix}_button_{$main_key}_bg_group_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_bg_group_gradient_position",
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
						$buttons_bg_selector => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{{$prefix}_button_{$main_key}_bg_group_color_stop.SIZE}}{{{$prefix}_button_{$main_key}_bg_group_color_stop.UNIT}}, {{{$prefix}_button_{$main_key}_bg_group_color_b.VALUE}} {{{$prefix}_button_{$main_key}_bg_group_color_b_stop.SIZE}}{{{$prefix}_button_{$main_key}_bg_group_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"{$prefix}_button_{$main_key}_bg_group_background" => array( 'gradient' ),
						"{$prefix}_button_{$main_key}_bg_group_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"{$prefix}_button_{$main_key}_border_color",
				array(
					'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--{$prefix}-button-{$main_key}-border-color: {{VALUE}};",
					),
					'condition' => array( "{$prefix}_button_border_type!" => 'none' ),
				)
			);

			$this->add_responsive_control(
				"{$prefix}_button_{$main_key}_border_radius",
				array(
					'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--{$prefix}-button-{$main_key}-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "{$prefix}_button_{$main_key}_text_shadow",
					'label' => esc_html__( 'Text Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'text_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--{$prefix}-button-{$main_key}-text-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{COLOR}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "{$prefix}_button_{$main_key}_box_shadow",
					'label' => esc_html__( 'Box Shadow', 'cmsmasters-elementor' ),
					'fields_options' => array(
						'box_shadow' => array(
							'selectors' => array(
								'{{SELECTOR}}' => "--{$prefix}-button-{$main_key}-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};",
							),
						),
					),
					'selector' => '{{WRAPPER}}',
				)
			);

			if ( 'hover' === $main_key ) {
				$this->add_control(
					"{$prefix}_button_{$main_key}_transition_duration",
					array(
						'label' => esc_html__( 'Transition Duration', 'cmsmasters-elementor' ) . ' (ms)',
						'type' => Controls_Manager::SLIDER,
						'range' => array(
							'px' => array(
								'min' => 0,
								'max' => 3000,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => "--{$prefix}-button-{$main_key}-transition-duration: {{SIZE}}ms;",
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			"{$prefix}_button_gap",
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => "--{$prefix}-button-margin-top: {{TOP}}{{UNIT}}; --{$prefix}-button-margin-right: {{RIGHT}}{{UNIT}}; --{$prefix}-button-margin-bottom: {{BOTTOM}}{{UNIT}}; --{$prefix}-button-margin-left: {{LEFT}}{{UNIT}};",
				),
			)
		);

		$this->add_responsive_control(
			"{$prefix}_button_padding",
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => "--{$prefix}-button-padding-top: {{TOP}}{{UNIT}}; --{$prefix}-button-padding-right: {{RIGHT}}{{UNIT}}; --{$prefix}-button-padding-bottom: {{BOTTOM}}{{UNIT}}; --{$prefix}-button-padding-left: {{LEFT}}{{UNIT}};",
				),
			)
		);

		$this->add_control(
			"{$prefix}_button_border_type",
			array(
				'label' => esc_html__( 'Border Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_custom_border_type_options(),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => "--{$prefix}-button-border-style: {{VALUE}};",
				),
			)
		);

		$this->add_responsive_control(
			"{$prefix}_button_border_width",
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => "--{$prefix}-button-border-top-width: {{TOP}}{{UNIT}}; --{$prefix}-button-border-right-width: {{RIGHT}}{{UNIT}}; --{$prefix}-button-border-bottom-width: {{BOTTOM}}{{UNIT}}; --{$prefix}-button-border-left-width: {{LEFT}}{{UNIT}};",
				),
				'condition' => array(
					"{$prefix}_button_border_type!" => array(
						'',
						'none',
					),
				),
			)
		);
	}

	protected function register_woocommerce_error_notices_controls_style() {
		$this->start_controls_section(
			'woocommerce_error_notices',
			array(
				'label' => esc_html__( 'Error Notices', 'cmsmasters-elementor' ),
				'condition' => array( 'woocommerce_notices_elements' => 'wc_error' ),
			)
		);

		$this->add_notice_box_controls( 'error' );

		$this->add_notice_text_controls( 'error' );

		$this->add_control(
			'error_message_link_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link Text', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'error_message_link_typography',
				'selector' => 'body.e-wc-error-notice .woocommerce-error a.wc-backward',
			)
		);

		$this->start_controls_tabs( 'error_message_links' );

		$this->start_controls_tab(
			'error_message_normal_links',
			array( 'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'error_message_normal_links_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.e-wc-error-notice .woocommerce-error' => '--error-message-normal-links-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'error_message_hover_links',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'error_message_hover_links_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.e-wc-error-notice .woocommerce-error' => '--error-message-hover-links-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_notice_button_controls( 'error' );

		$this->end_controls_section();
	}

	protected function register_woocommerce_message_notices_controls_style() {
		$this->start_controls_section(
			'woocommerce_message_notices',
			array(
				'label' => esc_html__( 'Message Notices', 'cmsmasters-elementor' ),
				'condition' => array( 'woocommerce_notices_elements' => 'wc_message' ),
			)
		);

		$this->add_notice_box_controls( 'message' );

		$this->add_notice_text_controls( 'message' );

		$this->add_control(
			'notice_message_link_heading',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Link Text', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'notice_message_link_typography',
				'selector' => 'body.e-wc-message-notice .woocommerce-message .restore-item, body.e-wc-message-notice .woocommerce-message a:not([class])',
			)
		);

		$this->start_controls_tabs( 'notice_message_links' );

		$this->start_controls_tab(
			'notice_message_normal_links',
			array( 'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'notice_message_normal_links_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.e-wc-message-notice .woocommerce-message .restore-item,
					.e-wc-message-notice .woocommerce-message a:not([class])' => '--message-normal-links-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'notice_message_hover_links',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'notice_message_hover_links_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'.e-wc-message-notice .woocommerce-message .restore-item:hover,
					.e-wc-message-notice .woocommerce-message a:not([class]):hover' => '--message-hover-links-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_notice_button_controls( 'message' );

		$this->end_controls_section();
	}

	protected function register_woocommerce_info_notices_controls_style() {
		$this->start_controls_section(
			'woocommerce_info_notices',
			array(
				'label' => esc_html__( 'Info Notices', 'cmsmasters-elementor' ),
				'condition' => array( 'woocommerce_notices_elements' => 'wc_info' ),
			)
		);

		$this->add_notice_box_controls( 'info' );

		$this->add_notice_text_controls( 'info' );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_notice_button_controls( 'info' );

		$this->end_controls_section();
	}

	/**
	 * Get Custom Border Type Options
	 *
	 * Return a set of border options to be used in different WooCommerce widgets.
	 *
	 * This will be used in cases where the Group Border Control could not be used.
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public static function get_custom_border_type_options() {
		return array(
			'' => esc_html__( 'Default', 'cmsmasters-elementor' ),
			'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
			'solid' => esc_html__( 'Solid', 'cmsmasters-elementor' ),
			'double' => esc_html__( 'Double', 'cmsmasters-elementor' ),
			'dotted' => esc_html__( 'Dotted', 'cmsmasters-elementor' ),
			'dashed' => esc_html__( 'Dashed', 'cmsmasters-elementor' ),
			'groove' => esc_html__( 'Groove', 'cmsmasters-elementor' ),
		);
	}

	private function hide_woocommerce_notices() {
		?>
		<style>
			.woocommerce-notices-wrapper,
			.woocommerce-message,
			.woocommerce-error,
			.woocommerce-info {
				display: none;
			}
		</style>
		<?php
	}

	protected function render_demo_notice() {
		?>
		<div class="elementor-widget-cmsmasters-woo-notices__wrapper">
		<?php
		wc_get_template( 'notices/notice.php', array(
			'notices' => array(
				'0' => array(
					'notice' => '<div class="cmsmasters-wc-add-to-cart-message">' . esc_html__( 'This is an example of a WooCommerce notice. (You won\'t see this while previewing your site.)', 'cmsmasters-elementor' ) . '</div>',
					'data' => array(),
				),
			),
		) );
		?>
		</div>
		<?php
	}

	protected function render() {
		if ( Plugin::$instance->editor->is_edit_mode() || Plugin::$instance->preview->is_preview_mode() ) {
			$this->render_demo_notice();
		} elseif ( function_exists( 'woocommerce_output_all_notices' ) && function_exists( 'wc_print_notices' ) ) {
			$this->hide_woocommerce_notices();

			$this->add_render_attribute( 'notices-wrapper', 'class', array(
				$this->get_widget_class() . '__wrapper',
				$this->get_widget_class() . '__loading',
			) );

			echo '<div ' . $this->get_render_attribute_string( 'notices-wrapper' ) . '>' .
				woocommerce_output_all_notices() .
			'</div>';
		}
	}
}
