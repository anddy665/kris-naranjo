<?php
namespace CmsmastersElementor\Modules\Woocommerce\Widgets\Wpclever\WpcleverSmartButtonBase;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Woocommerce\Traits\Woo_Singular_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Plugin;

use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


abstract class Wpclever_Smart_Button_Base extends Base_Widget {

	use Woo_Singular_Widget;

	/**
	 * @since 1.24.0
	 */
	protected function is_dynamic_content(): bool {
		return true;
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
	public function get_group_name() {
		return 'cmsmasters-wpclever-smart-button-base';
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
			'compare',
			'button',
			'quick-view',
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

	public function cmsmasters_class_prefix() {
		return 'elementor-widget-cmsmasters-wpclever-button';
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
			'section_general_wpclever_smart_panel',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'wpclever_items_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'button' => __( 'Button', 'cmsmasters-elementor' ),
					'link' => __( 'Link', 'cmsmasters-elementor' ),
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
				),
				'default' => 'button',
				'label_block' => false,
			)
		);

		$admin_url = $this->localization_url();

		$this->add_control(
			'wpclever_text',
			array(
				'raw' => __( 'If you want to change the text go to the ', 'cmsmasters-elementor' ) . '<a href="' . $admin_url . '" target="_blank">' . __( 'page for translations', 'cmsmasters-elementor' ) . '</a>',
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_general_wpclever_smart_button_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
			)
		);

		$icon_type = $this->state_icon_control();

		foreach ( $icon_type as $item => $label ) {
			$default_icon = $this->default_icon_control( $item );
			$condition = ( 'normal' !== $item ) ? array( 'wpclever_normal_icon[value]!' => '' ) : '';

			$this->add_control(
				"wpclever_{$item}_icon",
				array(
					'label' => $label,
					'type' => Controls_Manager::ICONS,
					'skin' => 'inline',
					'label_block' => false,
					'default' => $default_icon,
					'condition' => $condition,
				)
			);
		}

		$this->add_control(
			'wpclever_items_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
					'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
					'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
				),
				'default' => 'framed',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'wpclever_normal_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'wpclever_items_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'circle',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'wpclever_items_view!' => 'default',
					'wpclever_normal_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'wpclever_icon_align',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'stretch' => array(
						'title' => __( 'Justified ', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'render_type' => 'template',
				'condition' => array(
					'wpclever_normal_icon[value]!' => '',
					'wpclever_items_type!' => 'icon',
				),
			)
		);

		$this->add_control(
			'wpclever_icon_reverse',
			array(
				'label' => __( 'Reverse', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'render_type' => 'template',
				'condition' => array(
					'wpclever_normal_icon[value]!' => '',
					'wpclever_items_type!' => 'icon',
					'wpclever_icon_align' => 'stretch',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wpclever_button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wpclever_items_type!' => 'icon',
				),
			)
		);

		$prefix = $this->cmsmasters_class_prefix();

		$selector = "{{WRAPPER}} .{$prefix}__general";
		$selector_button = "{{WRAPPER}} .{$prefix}__button";

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'wpclever_button_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => $selector,
			)
		);

		$this->start_controls_tabs( 'wpclever_button_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {

			$this->start_controls_tab(
				"wpclever_button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';
			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_control(
				"wpclever_button_text_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-wpcl-text-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wpclever_button_bg_{$key}_background",
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
					'condition' => array( 'wpclever_items_type' => 'button' ),
				)
			);

			$this->add_control(
				"wpclever_button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector_button . $element => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						'wpclever_items_type' => 'button',
						"wpclever_button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"wpclever_button_bg_{$key}_color_stop",
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
						'wpclever_items_type' => 'button',
						"wpclever_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wpclever_button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						'wpclever_items_type' => 'button',
						"wpclever_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wpclever_button_bg_{$key}_color_b_stop",
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
						'wpclever_items_type' => 'button',
						"wpclever_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wpclever_button_bg_{$key}_gradient_type",
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
						'wpclever_items_type' => 'button',
						"wpclever_button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wpclever_button_bg_{$key}_gradient_angle",
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
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{wpclever_button_bg_{$key}_color_stop.SIZE}}{{wpclever_button_bg_{$key}_color_stop.UNIT}}, {{wpclever_button_bg_{$key}_color_b.VALUE}} {{wpclever_button_bg_{$key}_color_b_stop.SIZE}}{{wpclever_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						'wpclever_items_type' => 'button',
						"wpclever_button_bg_{$key}_background" => array( 'gradient' ),
						"wpclever_button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wpclever_button_bg_{$key}_gradient_position",
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
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{wpclever_button_bg_{$key}_color_stop.SIZE}}{{wpclever_button_bg_{$key}_color_stop.UNIT}}, {{wpclever_button_bg_{$key}_color_b.VALUE}} {{wpclever_button_bg_{$key}_color_b_stop.SIZE}}{{wpclever_button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						'wpclever_items_type' => 'button',
						"wpclever_button_bg_{$key}_background" => array( 'gradient' ),
						"wpclever_button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"wpclever_button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-wpcl-bd-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wpclever_items_type' => 'button',
					),
				)
			);

			$this->add_responsive_control(
				"wpclever_button_border_radius_{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmasters-wpcl-bdr-color-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
					'condition' => array(
						'wpclever_items_type' => 'button',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "wpclever_button_shadow_text_{$key}",
					'selector' => $selector . $state,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "wpclever_button_shadow_{$key}",
					'selector' => $selector_button . $state,
					'condition' => array(
						'wpclever_items_type' => 'button',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'wpclever_button_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'separator' => 'before',
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
					'stretch' => array(
						'title' => __( 'Stretch', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'wpclever_border_button',
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
				'condition' => array(
					'wpclever_items_type' => 'button',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_padding',
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
				'condition' => array(
					'wpclever_items_type' => 'button',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'wpclever_icon_style',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'wpclever_normal_icon[value]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_size',
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'wpclever_tabs_button_icon_style' );

		$colors_icon = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors_icon as $key => $label ) {
			$this->start_controls_tab(
				"wpclever_tab_button_icon_{$key}",
				array( 'label' => $label )
			);

			$this->add_control(
				"wpclever_button_icon_color_{$key}",
				array(
					'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-color-{$key}: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"wpclever_button_icon_bg_color_{$key}",
				array(
					'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-bg-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wpclever_items_view!' => 'default',
					),
				)
			);

			$this->add_control(
				"wpclever_button_icon_bd_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-bd-color-{$key}: {{VALUE}};",
					),
					'condition' => array(
						'wpclever_items_view' => 'framed',
					),
				)
			);

			$state = ( 'hover' === $key ) ? ':hover' : '';

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "wpclever_button_icon_box_shadow_{$key}",
					'selector' => "{{WRAPPER}} .elementor-widget-cmsmasters-wpclever-button__general{$state} .elementor-widget-cmsmasters-wpclever-button__icon-wrapper",
				)
			);

			$this->add_control(
				"wpclever_button_icon_border_radius__{$key}",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--wpclever-button-icon-bdr-{$key}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
					'condition' => array(
						'wpclever_items_view!' => 'default',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_control(
			'wpclever_button_icon_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'wpclever_items_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_icon_indent',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}}' => '--wpclever-button-icon-indent-normal: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'wpclever_items_type!' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_square_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-square-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'wpclever_items_view!' => 'default',
					'wpclever_items_shape' => 'square',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_circle_padding',
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
					'{{WRAPPER}}' => '--wpclever-button-icon-circle-pdd: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'wpclever_items_view!' => 'default',
					'wpclever_items_shape' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'wpclever_button_icon_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--wpclever-button-icon-border-w: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'wpclever_items_view' => 'framed',
				),
			)
		);

		$this->end_controls_section();
	}

	public function is_editor() {
		return Plugin::elementor()->editor->is_edit_mode();
	}

	public function get_product_attrs() {
		$attrs = array();

		$product = wc_get_product();

		if ( is_object( $product ) ) {
			$product_image_id = $product->get_image_id();
			$attrs['product_id'] = $product->get_id();
			$attrs['product_name'] = $product->get_name();
			$attrs['product_image'] = wp_get_attachment_image_url( $product_image_id );
		} else {
			$product_image_id = '1';
			$attrs['product_id'] = '1';
			$attrs['product_name'] = 'name';
			$attrs['product_image'] = '#';
		}

		return $attrs;
	}

	public function render_icon( $normal = true, $active = true, $load = true ) {
		$settings = $this->get_settings_for_display();
		$prefix_class = $this->cmsmasters_class_prefix();

		if ( isset( $settings["wpclever_normal_icon"] ) && ! empty( $settings["wpclever_normal_icon"]['value'] ) ) {
			$icon_view = $settings['wpclever_items_view'];
			$icon_shape = $settings['wpclever_items_shape'];

			$this->add_render_attribute( 'icon-normal', 'class', array(
				"{$prefix_class}__icon",
				"{$prefix_class}__icon-normal",
			) );

			$this->add_render_attribute( 'icon-active', 'class', array(
				"{$prefix_class}__icon",
				"{$prefix_class}__icon-active",
			) );

			$this->add_render_attribute( 'icon-load', 'class', array(
				"{$prefix_class}__icon",
				"{$prefix_class}__icon-load",
			) );

			$this->add_render_attribute( 'icon-wrapper', 'class', array(
				"{$prefix_class}__icon-wrapper",
				"{$prefix_class}__icon-{$icon_view}",
				"{$prefix_class}__icon-{$icon_shape}",
			) );

			ob_start();

			echo "<div {$this->get_render_attribute_string( 'icon-wrapper' )}>";

			$wpclever_icon_att = array( 'aria-hidden' => 'true' );

			if ( 'icon' === $settings["wpclever_items_type"] ) {
				$wpclever_icon_att = array_merge(
					$wpclever_icon_att,
					array( 'aria-label' => 'Button' ),
				);
			}

			if ( $normal && isset( $settings["wpclever_normal_icon"] ) && ! empty( $settings["wpclever_normal_icon"]['value'] ) ) {
				echo "<span {$this->get_render_attribute_string( 'icon-normal' )}>";
					Icons_Manager::render_icon( $settings["wpclever_normal_icon"], $wpclever_icon_att );
				echo '</span>';
			}

			if ( $active && isset( $settings["wpclever_active_icon"] ) && ! empty( $settings["wpclever_active_icon"]['value'] ) ) {
				echo "<span {$this->get_render_attribute_string( 'icon-active' )}>";
					Icons_Manager::render_icon( $settings["wpclever_active_icon"], $wpclever_icon_att );
				echo '</span>';
			}

			if ( $load && isset( $settings["wpclever_load_icon"] ) && ! empty( $settings["wpclever_load_icon"]['value'] ) ) {
				echo "<span {$this->get_render_attribute_string( 'icon-load' )}>";
					Icons_Manager::render_icon( $settings["wpclever_load_icon"], $wpclever_icon_att );
				echo '</span>';
			}

			echo "</div>";

			return ob_get_clean();
		}
	}

	abstract public function localization_url();
	abstract public function state_icon_control();
	abstract public function default_icon_control( $item );
	abstract public function render_button();

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.11.0
	 */
	public function render() {
		$this->render_button();
	}
}
