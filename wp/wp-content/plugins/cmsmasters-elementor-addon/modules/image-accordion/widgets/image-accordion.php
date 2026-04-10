<?php
namespace CmsmastersElementor\Modules\ImageAccordion\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Image Accordion widget.
 *
 * @since 1.21.0
 */
class Image_Accordion extends Base_Widget {

	/**
	 * Widget settings for display.
	 *
	 * @since 1.21.0
	 *
	 * @var string Widget settings for display.
	 */
	protected $settings;

	/**
	 * Widget class.
	 *
	 * @since 1.21.0
	 *
	 * @var string widget class.
	 */
	protected $widget_class;

	/**
	 * Horizontal text parts.
	 *
	 * @since 1.21.0
	 */
	protected $h_start;
	protected $h_end;

	/**
	 * Get widget title.
	 *
	 * @since 1.21.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Image Accordion', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.21.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-image-accordion';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * @since 1.21.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'image',
			'accordion',
			'gallery',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.21.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
	}

	/**
	 * Get style dependencies.
	 *
	 * @since 1.21.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-image-accordion',
		);
	}

	/**
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.21.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Get stack on breakpoint options.
	 *
	 * @since 1.21.0
	 *
	 * @return array Breakpoint options.
	 */
	protected function get_stack_on_options() {
		$options = array(
			'' => esc_html__( 'None', 'cmsmasters-elementor' ),
		);

		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();

		foreach ( $active_breakpoints as $breakpoint_key => $breakpoint ) {
			$options[ $breakpoint_key ] = $breakpoint->get_label();
		}

		return $options;
	}

	/**
	 * Register widget controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_controls() {
		$this->h_start = is_rtl() ? 'right' : 'left';
		$this->h_end = ! is_rtl() ? 'right' : 'left';

		$this->register_content_items_controls();
		$this->register_content_advanced_settings_controls();

		$this->register_style_items_controls();
		$this->register_style_overlay_controls();
		$this->register_style_content_controls();
		$this->register_style_button_controls();
	}

	/**
	 * Register items content controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_content_items_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			array(
				'label' => esc_html__( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic' => array( 'active' => true ),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Accordion Title', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label' => esc_html__( 'Subtitle', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'description',
			array(
				'label' => esc_html__( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 4,
				'label_block' => true,
				'default' => '',
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label' => esc_html__( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'show_button',
			array(
				'label' => esc_html__( 'Show Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			)
		);

		$repeater->add_control(
			'button_text',
			array(
				'label' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => esc_html__( 'Read More', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_button' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'button_icon',
			array(
				'label' => esc_html__( 'Button Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'condition' => array(
					'show_button' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'button_icon_position',
			array(
				'label' => esc_html__( 'Icon Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array( 'title' => __( 'Before', 'cmsmasters-elementor' ) ),
					'top' => array( 'title' => __( 'Top', 'cmsmasters-elementor' ) ),
					'right' => array( 'title' => __( 'After', 'cmsmasters-elementor' ) ),
				),
				'default' => 'left',
				'toggle' => false,
				'label_block' => false,
				'condition' => array(
					'show_button' => 'yes',
					'button_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default' => array(
					array(
						'title' => esc_html__( 'Item #1', 'cmsmasters-elementor' ),
						'subtitle' => '',
						'description' => '',
						'image' => array( 'url' => Utils::get_placeholder_image_src() ),
					),
					array(
						'title' => esc_html__( 'Item #2', 'cmsmasters-elementor' ),
						'subtitle' => '',
						'description' => '',
						'image' => array( 'url' => Utils::get_placeholder_image_src() ),
					),
					array(
						'title' => esc_html__( 'Item #3', 'cmsmasters-elementor' ),
						'subtitle' => '',
						'description' => '',
						'image' => array( 'url' => Utils::get_placeholder_image_src() ),
					),
				),
			)
		);

		// Content Position
		$this->add_control(
			'layout',
			array(
				'label' => esc_html__( 'Layout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'below-image' => esc_html__( 'Headline Below Image', 'cmsmasters-elementor' ),
					'above-image' => esc_html__( 'Headline Above Image', 'cmsmasters-elementor' ),
					'on-image' => esc_html__( 'Content on Image', 'cmsmasters-elementor' ),
				),
				'default' => 'below-image',
			)
		);

		// Height
		$this->add_responsive_control(
			'accordion_height',
			array(
				'label' => esc_html__( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vh', 'vw', 'custom' ),
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 1000,
						'step' => 10,
					),
					'em' => array(
						'min' => 5,
						'max' => 50,
						'step' => 0.5,
					),
					'vh' => array(
						'min' => 10,
						'max' => 100,
						'step' => 1,
					),
				),
				'default' => array(
					'size' => 400,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--accordion-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// Default Active Item
		$this->add_control(
			'active_item',
			array(
				'label' => esc_html__( 'Default Active Item', 'cmsmasters-elementor' ),
				'description' => esc_html__( 'Set the item number to be active by default. Set to 0 if no item should be active.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 0,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register advanced settings content controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_content_advanced_settings_controls() {
		$this->start_controls_section(
			'section_advanced_settings',
			array(
				'label' => esc_html__( 'Advanced Settings', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'accordion_action',
			array(
				'label' => esc_html__( 'Trigger Action', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => array(
					'hover' => esc_html__( 'On Hover', 'cmsmasters-elementor' ),
					'click' => esc_html__( 'On Click', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'active_item_expand_ratio',
			array(
				'label' => esc_html__( 'Active Item Expand Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'step' => 0.1,
				'default' => 3,
				'selectors' => array(
					'{{WRAPPER}}' => '--active-item-expand-ratio: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'stack_on',
			array(
				'label' => esc_html__( 'Stack On', 'cmsmasters-elementor' ),
				'description' => esc_html__( 'Choose at which breakpoint the accordion items should stack vertically.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_stack_on_options(),
				'prefix_class' => 'cmsmasters-image-accordion--stack-',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => esc_html__( 'Title HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				),
				'default' => 'h3',
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'image',
				'default' => 'full',
			)
		);

		// Transition Duration (moved from styles)
		$this->add_control(
			'transition_duration',
			array(
				'label' => esc_html__( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'ms', 's', 'custom' ),
				'range' => array(
					'ms' => array(
						'min' => 100,
						'max' => 2000,
						'step' => 50,
					),
					's' => array(
						'min' => 0.1,
						'max' => 2,
						'step' => 0.1,
					),
				),
				'default' => array(
					'size' => 500,
					'unit' => 'ms',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--transition-duration: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}



	/**
	 * Register items style controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_style_items_controls() {
		$this->start_controls_section(
			'section_style_items',
			array(
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);
		
		// Gap Between
		$this->add_responsive_control(
			'items_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
						'step' => 1,
					),
				),
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--items-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'items_states_tabs' );

		// Items Normal Tab
		$this->start_controls_tab(
			'items_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'items_normal_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--items-normal-bd-color: {{VALUE}};',
				),
				'condition' => array(
					'items_bd_border!' => 'none',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'items_normal',
			)
		);

		$this->end_controls_tab();

		// Items Hover Tab (for click mode only)
		$this->start_controls_tab(
			'items_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array(
					'accordion_action' => 'click',
				),
			)
		);

		$this->add_control(
			'items_hover_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--items-hover-bd-color: {{VALUE}};',
				),
				'condition' => array(
					'items_bd_border!' => 'none',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'items_hover',
			)
		);

		$this->end_controls_tab();

		// Items Active Tab
		$this->start_controls_tab(
			'items_active_tab',
			array(
				'label' => esc_html__( 'Active', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'items_active_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--items-active-bd-color: {{VALUE}};',
				),
				'condition' => array(
					'items_bd_border!' => 'none',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'items_active',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'items_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
			)
		);

		$this->add_control(
			'items_bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--items-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		// Headline (from Header)
		$this->add_control(
			'headline_heading',
			array(
				'label' => esc_html__( 'Headline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		$this->add_responsive_control(
			'header_alignment',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'start',
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-alignment: {{VALUE}};',
				),
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		$this->start_controls_tabs(
			'header_states_tabs',
			array(
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		// Header Normal Tab
		$this->start_controls_tab(
			'header_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'header_normal_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-normal-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_normal_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-normal-bd-color: {{VALUE}};',
				),
				'condition' => array(
					'header_bd_border!' => 'none',
				),
			)
		);

		$this->end_controls_tab();

		// Header Hover Tab (for click mode only)
		$this->start_controls_tab(
			'header_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array(
					'accordion_action' => 'click',
				),
			)
		);

		$this->add_control(
			'header_hover_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-hover-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_hover_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-hover-bd-color: {{VALUE}};',
				),
				'condition' => array(
					'header_bd_border!' => 'none',
				),
			)
		);

		$this->end_controls_tab();

		// Header Active Tab
		$this->start_controls_tab(
			'header_active_tab',
			array(
				'label' => esc_html__( 'Active', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'header_active_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-active-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_active_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--header-active-bd-color: {{VALUE}};',
				),
				'condition' => array(
					'header_bd_border!' => 'none',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'header_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		$this->add_control(
			'header_bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--header-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--header-padding-top: {{TOP}}{{UNIT}}; --header-padding-right: {{RIGHT}}{{UNIT}}; --header-padding-bottom: {{BOTTOM}}{{UNIT}}; --header-padding-left: {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register overlay style controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_style_overlay_controls() {
		$this->start_controls_section(
			'section_style_overlay',
			array(
				'label' => esc_html__( 'Image Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'overlay_states_tabs' );

		$overlay_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'active' => __( 'Active', 'cmsmasters-elementor' ),
		);

		foreach ( $overlay_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"overlay_states_{$state_key}_tab",
				array(
					'label' => $state_label,
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BACKGROUND_GROUP,
				array(
					'name' => "overlay_{$state_key}_bg",
					'types' => array( 'classic', 'gradient' ),
					'exclude' => array( 'image' ),
				)
			);

			$this->add_control(
				"overlay_{$state_key}_opacity",
				array(
					'label' => __( 'Opacity', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'max' => 1,
							'min' => 0,
							'step' => 0.01,
						),
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--overlay-{$state_key}-opacity: {{SIZE}};",
					),
					'condition' => array(
						"overlay_{$state_key}_bg_background!" => '',
					),
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	/**
	 * Register content style controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_style_content_controls() {
		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => esc_html__( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'content_alignment',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'start',
				'options' => array(
					'start' => array(
						'title' => __( 'Start', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_start}",
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'end' => array(
						'title' => __( 'End', 'cmsmasters-elementor' ),
						'icon' => "eicon-text-align-{$this->h_end}",
					),
				),
				'toggle' => true,
				'selectors_dictionary' => array(
					'start' => '--content-alignment: start; --content-text-align: ' . $this->h_start . ';',
					'center' => '--content-alignment: center; --content-text-align: center;',
					'end' => '--content-alignment: end; --content-text-align: ' . $this->h_end . ';',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'content_vertical_position',
			array(
				'label' => esc_html__( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'toggle' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--content-vertical-position: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--content-padding-top: {{TOP}}{{UNIT}}; --content-padding-right: {{RIGHT}}{{UNIT}}; --content-padding-bottom: {{BOTTOM}}{{UNIT}}; --content-padding-left: {{LEFT}}{{UNIT}}',
				),
			)
		);

		// Title styles
		$this->add_control(
			'title_heading',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title',
				'selector' => '{{WRAPPER}} .' . $this->get_html_wrapper_class() . '__title',
			)
		);

		$this->add_responsive_control(
			'title_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--title-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		// Title color for on-image layout (single color for all states - text not visible when inactive)
		$this->add_control(
			'title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--title-normal-color: {{VALUE}}; --title-hover-color: {{VALUE}}; --title-active-color: {{VALUE}};',
				),
				'condition' => array(
					'layout' => 'on-image',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'title_on_image',
				'selector' => '{{WRAPPER}}',
				'condition' => array(
					'layout' => 'on-image',
				),
			)
		);

		// Title tabs for above/below-image layouts
		$this->start_controls_tabs(
			'title_tabs',
			array(
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		// Title Normal Tab
		$this->start_controls_tab(
			'title_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'title_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--title-normal-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'title_normal',
			)
		);

		$this->end_controls_tab();

		// Title Hover Tab (for click mode only)
		$this->start_controls_tab(
			'title_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array(
					'accordion_action' => 'click',
				),
			)
		);

		$this->add_control(
			'title_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--title-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'title_hover',
			)
		);

		$this->end_controls_tab();

		// Title Active Tab
		$this->start_controls_tab(
			'title_active_tab',
			array(
				'label' => esc_html__( 'Active', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'title_active_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--title-active-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'title_active',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Subtitle styles
		$this->add_control(
			'subtitle_heading',
			array(
				'label' => esc_html__( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'subtitle',
			)
		);

		$this->add_responsive_control(
			'subtitle_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--subtitle-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'layout' => 'on-image',
				),
			)
		);

		// Subtitle color for on-image layout (single color for all states - text not visible when inactive)
		$this->add_control(
			'subtitle_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--subtitle-normal-color: {{VALUE}}; --subtitle-hover-color: {{VALUE}}; --subtitle-active-color: {{VALUE}};',
				),
				'condition' => array(
					'layout' => 'on-image',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'subtitle_on_image',
				'selector' => '{{WRAPPER}}',
				'condition' => array(
					'layout' => 'on-image',
				),
			)
		);

		// Subtitle tabs for above/below-image layouts
		$this->start_controls_tabs(
			'subtitle_tabs',
			array(
				'condition' => array(
					'layout!' => 'on-image',
				),
			)
		);

		// Subtitle Normal Tab
		$this->start_controls_tab(
			'subtitle_normal_tab',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'subtitle_normal_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--subtitle-normal-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'subtitle_normal',
			)
		);

		$this->end_controls_tab();

		// Subtitle Hover Tab (for click mode only)
		$this->start_controls_tab(
			'subtitle_hover_tab',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
				'condition' => array(
					'accordion_action' => 'click',
				),
			)
		);

		$this->add_control(
			'subtitle_hover_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--subtitle-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'subtitle_hover',
			)
		);

		$this->end_controls_tab();

		// Subtitle Active Tab
		$this->start_controls_tab(
			'subtitle_active_tab',
			array(
				'label' => esc_html__( 'Active', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'subtitle_active_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--subtitle-active-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'subtitle_active',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		// Description styles
		$this->add_control(
			'description_heading',
			array(
				'label' => esc_html__( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'description',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--description-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'description',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register button style controls.
	 *
	 * @since 1.21.0
	 */
	protected function register_style_button_controls() {
		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => esc_html__( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array(
				'name' => 'button',
			)
		);

		$this->start_controls_tabs( 'button_states_tabs' );

		$button_states = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $button_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"button_states_{$state_key}_tab",
				array( 'label' => $state_label )
			);

			$this->add_control(
				"button_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-{$state_key}-color: {{VALUE}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BACKGROUND_GROUP,
				array(
					'name' => "button_{$state_key}_bg",
					'types' => array( 'classic', 'gradient' ),
					'exclude' => array( 'image' ),
				)
			);

			$this->add_control(
				"button_{$state_key}_bd_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-{$state_key}-bd-color: {{VALUE}};",
					),
					'condition' => array(
						'button_bd_border!' => 'none',
					),
				)
			);

			$this->add_control(
				"button_{$state_key}_bd_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--button-{$state_key}-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "button_{$state_key}",
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "button_{$state_key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'button_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--button-padding-top: {{TOP}}{{UNIT}}; --button-padding-right: {{RIGHT}}{{UNIT}}; --button-padding-bottom: {{BOTTOM}}{{UNIT}}; --button-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--button-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_icon_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'button_icon_heading_control',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
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
			)
		);

		$button_icon_style_states = array(
			'normal' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			'hover' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
		);

		$this->start_controls_tabs( 'button_icon_style_states_tabs' );

		foreach ( $button_icon_style_states as $state_key => $state_label ) {
			$this->start_controls_tab(
				"button_icon_style_states_{$state_key}_tab",
				array(
					'label' => $state_label,
				)
			);

			$this->add_control(
				"button_icon_{$state_key}_color",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-icon-{$state_key}-color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"button_icon_{$state_key}_bg_color",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-icon-{$state_key}-bg-color: {{VALUE}};",
					),
				)
			);

			$this->add_control(
				"button_icon_{$state_key}_bd_color",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}}' => "--button-icon-{$state_key}-bd-color: {{VALUE}};",
					),
					'condition' => array(
						'button_icon_bd_border!' => 'none',
					),
				)
			);

			$this->add_control(
				"button_icon_{$state_key}_bd_radius",
				array(
					'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'%',
					),
					'selectors' => array(
						'{{WRAPPER}}' => "--button-icon-{$state_key}-bd-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};",
					),
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_BOX_SHADOW_GROUP,
				array(
					'name' => "button_icon_{$state_key}",
				)
			);

			$this->add_group_control(
				CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
				array(
					'name' => "button_icon_{$state_key}",
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'button_icon_bd',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'width' => array( 'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ) ),
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--button-icon-padding-top: {{TOP}}{{UNIT}}; --button-icon-padding-right: {{RIGHT}}{{UNIT}}; --button-icon-padding-bottom: {{BOTTOM}}{{UNIT}}; --button-icon-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
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
					'em' => array(
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--button-icon-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * @since 1.21.0
	 */
	protected function render() {
		$this->settings = $this->get_settings_for_display();
		$this->widget_class = $this->get_html_wrapper_class();

		if ( empty( $this->settings['items'] ) ) {
			return;
		}

		$active_item = ! empty( $this->settings['active_item'] ) ? intval( $this->settings['active_item'] ) : 0;
		$layout = ! empty( $this->settings['layout'] ) ? $this->settings['layout'] : 'below-image';

		$this->add_render_attribute( 'container', array(
			'class' => array(
				$this->widget_class . '__container',
				$this->widget_class . '__container--action-' . $this->settings['accordion_action'],
				$this->widget_class . '__container--layout-' . $layout,
			),
			'data-action' => $this->settings['accordion_action'],
			'data-active-item' => $active_item,
		) );

		$is_click_mode = ( 'click' === $this->settings['accordion_action'] );

		echo '<div ' . $this->get_render_attribute_string( 'container' ) . '>';

			foreach ( $this->settings['items'] as $index => $item ) {
				$has_link = ! empty( $item['link']['url'] );
				$show_button = 'yes' === $item['show_button'];

				$this->add_render_attribute( "item_{$index}", array(
					'class' => array(
						$this->widget_class . '__item',
						'elementor-repeater-item-' . $item['_id'],
						$is_click_mode && ( $active_item === ( $index + 1 ) ) ? $this->widget_class . '__item--active' : '',
					),
				) );

				echo '<div ' . $this->get_render_attribute_string( "item_{$index}" ) . '>';

					// Link on entire block (only if no button)
					if ( $has_link && ! $show_button ) {
						$this->add_render_attribute( "link_{$index}", array(
							'class' => $this->widget_class . '__link',
						) );
						$this->add_link_attributes( "link_{$index}", $item['link'] );

						echo '<a ' . $this->get_render_attribute_string( "link_{$index}" ) . '></a>';
					}

					// Title and subtitle above image
					if ( 'above-image' === $layout ) {
						echo $this->get_title_subtitle( $item, $layout );
					}

					echo '<div class="' . esc_attr( $this->widget_class ) . '__body">';

						$image = Group_Control_Image_Size::get_attachment_image_html( $item, 'image' );

						if ( ! empty( $image ) ) {
							echo '<div class="' . esc_attr( $this->widget_class ) . '__image">' . $image . '</div>';
						}

						echo '<span class="' . esc_attr( $this->widget_class ) . '__overlay ' . esc_attr( $this->widget_class ) . '__overlay-normal"></span>';
						echo '<span class="' . esc_attr( $this->widget_class ) . '__overlay ' . esc_attr( $this->widget_class ) . '__overlay-active"></span>';

						echo '<div class="' . esc_attr( $this->widget_class ) . '__content">' .
							'<div class="' . esc_attr( $this->widget_class ) . '__content-inner">';

								if ( 'on-image' === $layout ) {
									echo $this->get_title_subtitle( $item, $layout );
								}

								if ( ! empty( $item['description'] ) ) {
									echo '<div class="' . esc_attr( $this->widget_class ) . '__description">' .
										wp_kses_post( $item['description'] ) .
									'</div>';
								}
								
								if ( $show_button ) {
									$icon_position = ! empty( $item['button_icon_position'] ) ? $item['button_icon_position'] : 'left';
									$has_icon = ! empty( $item['button_icon']['value'] );
									$button_tag = $has_link ? 'a' : 'span';

									$this->add_render_attribute( "button_{$index}", array(
										'class' => array(
											$this->widget_class . '__button',
											$this->widget_class . '__button--icon-' . $icon_position,
										),
									) );

									if ( $has_link ) {
										$this->add_link_attributes( "button_{$index}", $item['link'] );
									} else {
										// Accessibility: role="button" for non-link buttons
										$this->add_render_attribute( "button_{$index}", 'role', 'button' );
									}

									echo '<div class="' . esc_attr( $this->widget_class ) . '__button-wrap">' .
										'<' . $button_tag . ' ' . $this->get_render_attribute_string( "button_{$index}" ) . '>';

											if ( $has_icon && 'right' !== $icon_position ) {
												echo '<span class="' . esc_attr( $this->widget_class ) . '__button-icon">' .
													Icons_Manager::try_get_icon_html( $item['button_icon'], array( 'aria-hidden' => 'true' ) ) .
												'</span>';
											}

											if ( ! empty( $item['button_text'] ) ) {
												echo '<span class="' . esc_attr( $this->widget_class ) . '__button-text">' .
													esc_html( $item['button_text'] ) .
												'</span>';
											}

											if ( $has_icon && 'right' === $icon_position ) {
												echo '<span class="' . esc_attr( $this->widget_class ) . '__button-icon">' .
													Icons_Manager::try_get_icon_html( $item['button_icon'], array( 'aria-hidden' => 'true' ) ) .
												'</span>';
											}

										echo '</' . $button_tag . '>' .
									'</div>';
								}

							echo '</div>' .
						'</div>' .
					'</div>';

					// Title and subtitle below image
					if ( 'below-image' === $layout ) {
						echo $this->get_title_subtitle( $item, $layout );
					}
					
				echo '</div>';
			}

		echo '</div>';
	}

	/**
	 * Get title and subtitle block.
	 *
	 * @since 1.21.0
	 *
	 * @param array $item Item data.
	 * @param string $layout Layout.
	 */
	protected function get_title_subtitle( $item, $layout ) {
		$out = '';

		if ( ! empty( $item['title'] ) ) {
			$out .= '<' . Utils::validate_html_tag( $this->settings['title_tag'] ) . ' class="' . esc_attr( $this->widget_class ) . '__title">' .
				esc_html( $item['title'] ) .
			'</' . Utils::validate_html_tag( $this->settings['title_tag'] ) . '>';
		}

		if ( ! empty( $item['subtitle'] ) ) {
			$out .= '<div class="' . esc_attr( $this->widget_class ) . '__subtitle">' .
				esc_html( $item['subtitle'] ) .
			'</div>';
		}

		if ( empty( $out ) ) {
			return '';
		}

		if ( 'on-image' === $layout ) {
			return $out;
		}

		return '<div class="' . esc_attr( $this->widget_class ) . '__header">' . $out . '</div>';
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.21.0
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			'items' => array(
				'field' => 'items',
				'type' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'editor_type' => 'REPEATER',
				'fields' => array(
					array(
						'field' => 'title',
						'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field' => 'subtitle',
						'type' => esc_html__( 'Subtitle', 'cmsmasters-elementor' ),
						'editor_type' => 'LINE',
					),
					array(
						'field' => 'description',
						'type' => esc_html__( 'Description', 'cmsmasters-elementor' ),
						'editor_type' => 'AREA',
					),
					array(
						'field' => 'button_text',
						'type' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
						'editor_type' => 'LINE',
					),
					'link' => array(
						'field' => 'url',
						'type' => esc_html__( 'Link', 'cmsmasters-elementor' ),
						'editor_type' => 'LINK',
					),
				),
			),
		);
	}
}
