<?php
namespace CmsmastersElementor\Modules\Marquee\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls\Groups\Group_Control_Vars_Text_Background;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Marquee extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.17.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Marquee', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.17.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-marquee';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.17.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'marquee',
			'slider',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.17.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'widget-cmsmasters-marquee',
		);
	}

	/**
	 * Outputs elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.17.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-marquee';
	}

	public function get_widget_selector() {
		return '.' . $this->get_widget_class();
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * Should be inherited and register new controls using `add_control()`,
	 * `add_responsive_control()` and `add_group_control()`, inside control
	 * wrappers like `start_controls_section()`, `start_controls_tabs()` and
	 * `start_controls_tab()`.
	 *
	 * @since 1.17.0
	 * @since 1.17.3 Added controls for icon and image.
	 */
	protected function register_controls() {
		$this->register_general_content_controls();

		$this->register_motion_content_controls();

		$this->register_marquee_style_controls();

		$this->register_text_style_controls();

		$this->register_icon_style_controls();

		$this->register_image_style_controls();
	}

	/**
	 * Marquee content controls.
	 *
	 * @since 1.17.0
	 * @since 1.17.3 Added Type Icon & Image in Marquee Item.
	 */
	protected function register_general_content_controls() {
		$this->start_controls_section(
			'section_marquee_content',
			array( 'label' => esc_html__( 'Marquee', 'cmsmasters-elementor' ) )
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			array(
				'label' => esc_html__( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'text' => array(
						'title' => esc_html__( 'Text', 'cmsmasters-elementor' ),
					),
					'icon' => array(
						'title' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
					),
					'image' => array(
						'title' => esc_html__( 'Image', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'text',
				'toggle' => false,
				'label_block' => false,
			)
		);

		$repeater->add_control(
			'text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Marquee Text', 'cmsmasters-elementor' ),
				'default' => esc_html__( 'Marquee Text', 'cmsmasters-elementor' ),
				'condition' => array( 'type' => 'text' ),
			)
		);

		$repeater->add_control(
			'text_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--text-color: {{VALUE}} !important; -webkit-text-fill-color: {{VALUE}} !important;',
				),
				'condition' => array( 'type' => 'text' ),
			)
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition' => array( 'type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-marquee-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-marquee-icon-color: {{VALUE}} !important;',
				),
				'condition' => array( 'type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-marquee-icon-bg-color: {{VALUE}} !important;',
				),
				'condition' => array( 'type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'icon_bd_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-marquee-icon-bd-color: {{VALUE}} !important;',
				),
				'condition' => array( 'type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label' => esc_html__( 'Choose Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'show_label' => false,
				'condition' => array( 'type' => 'image' ),
			)
		);

		$this->add_control(
			'marquee_list',
			array(
				'label' => esc_html__( 'Items', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'text' => esc_html__( 'Marquee Item', 'cmsmasters-elementor' ),
					),
					array(
						'text' => esc_html__( 'Marquee Item', 'cmsmasters-elementor' ),
					),
					array(
						'text' => esc_html__( 'Marquee Item', 'cmsmasters-elementor' ),
					),
				),
				'title_field' => '<span class="cmsmasters-repeat-item-num"></span>. {{{ text }}} {{{ type }}}',
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => esc_html__( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'cmsmasters-elementor' ),
				'show_external' => true,
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label' => esc_html__( 'HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'span' => 'span',
					'p' => 'p',
					'div' => 'div',
				),
				'default' => 'h4',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Marquee motion content controls.
	 *
	 * @since 1.17.3
	 */
	protected function register_motion_content_controls() {
		$this->start_controls_section(
			'section_marquee_motion',
			array( 'label' => esc_html__( 'Motion', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'direction',
			array(
				'label' => esc_html__( 'Direction', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'left',
				'toggle' => false,
				'label_block' => false,
				'frontend_available' => true,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-marquee-direction-',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label' => esc_html__( 'Speed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0.1,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => '2',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'hover_behavior',
			array(
				'label' => esc_html__( 'Hover Behavior', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'disable' => array(
						'title' => esc_html__( 'None', 'cmsmasters-elementor' ),
					),
					'pause' => array(
						'title' => esc_html__( 'Pause', 'cmsmasters-elementor' ),
					),
					'modifier' => array(
						'title' => esc_html__( 'Modifier', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'disable',
				'toggle' => false,
				'label_block' => false,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'hover_speed_modifier',
			array(
				'label' => esc_html__( 'Speed Modifier', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array(
						'min' => -1,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'default' => array(
					'unit' => '%',
					'size' => '0.5',
				),
				'frontend_available' => true,
				'condition' => array( 'hover_behavior' => 'modifier' ),
			)
		);

		$this->add_control(
			'animation_trigger',
			array(
				'label' => esc_html__( 'Animation Trigger', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'on_page_load' => array(
						'title' => esc_html__( 'On Page Load', 'cmsmasters-elementor' ),
					),
					'on_viewport_enter' => array(
						'title' => esc_html__( 'On Viewport Enter', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'on_page_load',
				'toggle' => false,
				'label_block' => true,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Marquee style controls.
	 *
	 * @since 1.17.0
	 */
	protected function register_marquee_style_controls() {
		$this->start_controls_section(
			'section_marquee_style',
			array(
				'label' => esc_html__( 'Marquee', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'horizontal_offset',
			array(
				'label' => esc_html__( 'Horizontal Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'marquee_gap',
			array(
				'label' => esc_html__( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'frontend_available' => true,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'marquee_item_gap',
			array(
				'label' => esc_html__( 'Item Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'frontend_available' => true,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-item-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label' => esc_html__( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}}' => 'mix-blend-mode: {{VALUE}}',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Marquee text style controls.
	 *
	 * @since 1.17.0
	 */
	protected function register_text_style_controls() {
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TYPOGRAPHY_GROUP,
			array( 'name' => 'text_typography' )
		);

		$this->add_group_control(
			Group_Control_Vars_Text_Background::get_type(),
			array(
				'name' => 'text',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array( 'name' => 'text' )
		);

		$this->add_control(
			'text_stroke_width',
			array(
				'label' => esc_html__( 'Stroke Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
					'em' => array(
						'min' => 0,
						'max' => 0.2,
						'step' => 0.01,
					),
				),
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'text_stroke_color_normal',
			array(
				'label' => esc_html__( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'text_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Marquee icon style controls.
	 *
	 * @since 1.17.0
	 */
	protected function register_icon_style_controls() {
		$this->start_controls_section(
			'section_icon_style',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'icon_marker_shape',
			array(
				'label' => esc_html__( 'Shape', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => esc_html__( 'Default', 'cmsmasters-elementor' ),
					'circle' => esc_html__( 'Circle', 'cmsmasters-elementor' ),
					'square' => esc_html__( 'Square', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'prefix_class' => 'cmsmaster-icon-shape-',
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-marquee__item.icon' => '--cmsmasters-marquee-icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-marquee__item.icon' => '--cmsmasters-marquee-icon-bg-color: {{VALUE}};',
				),
				'condition' => array( 'icon_marker_shape!' => 'default' ),
			)
		);

		$this->add_control(
			'icon_border_color',
			array(
				'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-marquee__item.icon' => '--cmsmasters-marquee-icon-bd-color: {{VALUE}}',
				),
				'condition' => array(
					'icon_marker_shape!' => 'default',
					'icon_border_border!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-icon-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'icon_marker_shape!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'icon_border',
				'label' => esc_html__( 'Border', 'cmsmasters-elementor' ),
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'options' => array(
							'none' => _x( 'None', 'Border Control', 'cmsmasters-elementor' ),
							'solid' => _x( 'Solid', 'Border Control', 'cmsmasters-elementor' ),
							'double' => _x( 'Double', 'Border Control', 'cmsmasters-elementor' ),
							'dotted' => _x( 'Dotted', 'Border Control', 'cmsmasters-elementor' ),
							'dashed' => _x( 'Dashed', 'Border Control', 'cmsmasters-elementor' ),
							'groove' => _x( 'Groove', 'Border Control', 'cmsmasters-elementor' ),
						),
						'default' => 'none',
						'selectors' => array( '{{WRAPPER}}' => '--cmsmasters-marquee-icon-border-style: {{VALUE}};' ),
					),
					'width' => array(
						'label' => _x( 'Border Width', 'Border Control', 'cmsmasters-elementor' ),
						'selectors' => array(
							'{{WRAPPER}}' => '--cmsmasters-marquee-icon-border-top-width: {{TOP}}{{UNIT}}; ' .
								'--cmsmasters-marquee-icon-border-right-width: {{RIGHT}}{{UNIT}}; ' .
								'--cmsmasters-marquee-icon-border-bottom-width: {{BOTTOM}}{{UNIT}}; ' .
								'--cmsmasters-marquee-icon-border-left-width: {{LEFT}}{{UNIT}};',
						),
						'condition' => array( 'border!' => 'none' ),
					),
				),
				'condition' => array( 'icon_marker_shape!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'icon_border_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-icon-top-left-border-radius: {{TOP}}{{UNIT}}; ' .
						'--cmsmasters-marquee-icon-top-right-border-radius: {{RIGHT}}{{UNIT}}; ' .
						'--cmsmasters-marquee-icon-bottom-right-border-radius: {{BOTTOM}}{{UNIT}}; ' .
						'--cmsmasters-marquee-icon-bottom-left-border-radius: {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'icon_marker_shape' => 'square' ),
			)
		);

		$this->add_control(
			'icon_vertical_align',
			array(
				'label' => esc_html__( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-icon-vertical-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_rotate',
			array(
				'label' => esc_html__( 'Rotate', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 360,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-icon-rotate: {{SIZE}}deg;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Marquee image style controls.
	 *
	 * @since 1.17.3
	 */
	protected function register_image_style_controls() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => esc_html__( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'image',
				'default' => 'thumbnail',
			)
		);

		$this->add_responsive_control(
			'image_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-image-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-image-bg-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array( 'name' => 'image_bd' )
		);

		$this->add_responsive_control(
			'image_bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'vw',
					'vh',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-image-top-left-border-radius: {{TOP}}{{UNIT}}; ' .
						'--cmsmasters-marquee-image-top-right-border-radius: {{RIGHT}}{{UNIT}}; ' .
						'--cmsmasters-marquee-image-bottom-right-border-radius: {{BOTTOM}}{{UNIT}}; ' .
						'--cmsmasters-marquee-image-bottom-left-border-radius: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array( 'name' => 'image' )
		);

		$this->add_group_control(
			CmsmastersControls::VARS_CSS_FILTER_GROUP,
			array( 'name' => 'image' )
		);

		$this->add_responsive_control(
			'image_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-marquee-image-padding-top: {{TOP}}{{UNIT}}; ' .
						'--cmsmasters-marquee-image-padding-right: {{RIGHT}}{{UNIT}}; ' .
						'--cmsmasters-marquee-image-padding-bottom: {{BOTTOM}}{{UNIT}}; ' .
						'--cmsmasters-marquee-image-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get marquee text item.
	 *
	 * Retrieve marquee text item html.
	 *
	 * @since 1.17.3
	 */
	public function get_marquee_text_item( $item, $index, $type, $attr_key ) {
		$text = ( isset( $item['text'] ) ? $item['text'] : '' );

		if ( empty( $text ) ) {
			return;
		}

		echo '<span ' . $this->get_render_attribute_string( $attr_key ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( $text ) .
		'</span>';
	}

	/**
	 * Get marquee icon item.
	 *
	 * Retrieve marquee icon item html.
	 *
	 * @since 1.17.3 Added render marquee icon item.
	 */
	public function get_marquee_icon_item( $item, $index, $type, $attr_key ) {
		$icon = ( isset( $item['item_icon'] ) && ! empty( $item['item_icon']['value'] ) ? $item['item_icon'] : '' );

		if ( empty( $icon ) ) {
			return;
		}

		echo '<span ' . $this->get_render_attribute_string( $attr_key ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			Icons_Manager::render_icon( $icon, array(
				'aria-hidden' => 'true',
				'aria-label' => esc_attr__( 'Marquee item icon', 'cmsmasters-elementor' ),
			) );

		echo '</span>';
	}

	/**
	 * Get marquee image item.
	 *
	 * Retrieve marquee image item html.
	 *
	 * @since 1.17.3 Added render marquee image item.
	 */
	public function get_marquee_image_item( $item, $index, $type, $attr_key ) {
		$settings = $this->get_settings_for_display();

		if ( ! isset( $item['item_image'] ) && empty( $item['item_image']['url'] ) ) {
			return;
		}

		$fake_settings = array(
			'item_image' => $item['item_image'],
			'item_image_size' => $settings['image_size'],
			'item_image_custom_dimension' => ( isset( $settings['image_custom_dimension'] ) ? $settings['image_custom_dimension'] : array() ),
		);

		$image = Group_Control_Image_Size::get_attachment_image_html(
			$fake_settings,
			'item_image',
			'item_image'
		);

		if ( empty( $image ) ) {
			return '';
		}

		echo '<span ' . $this->get_render_attribute_string( $attr_key ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			wp_kses_post( $image ) .
		'</span>';
	}

	/**
	 * Get marquee items.
	 *
	 * Retrieve marquee items html.
	 *
	 * @since 1.17.3 Added render marquee icon and image items.
	 */
	public function get_marquee_items() {
		$marquee_list = $this->get_settings_for_display( 'marquee_list' );

		foreach ( $marquee_list as $index => $item ) {
			$type = ( isset( $item['type'] ) ? $item['type'] : '' );

			if ( empty( $item ) || empty( $type ) ) {
				continue;
			}

			$attr_key = 'marquee_item_' . $index;

			$this->add_render_attribute( $attr_key, 'class', array(
				'elementor-repeater-item-' . esc_attr( $item['_id'] ),
				$this->get_widget_class() . '__item',
				$type,
			) );

			if ( 'text' === $type ) {
				$this->get_marquee_text_item( $item, $index, $type, $attr_key );
			} elseif ( 'icon' === $type ) {
				$this->get_marquee_icon_item( $item, $index, $type, $attr_key );
			} elseif ( 'image' === $type ) {
				$this->get_marquee_image_item( $item, $index, $type, $attr_key );
			}
		}
	}

	/**
	 * Render marquee widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.17.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['marquee_list'] ) ) {
			return;
		}

		$link = ( isset( $settings['link'] ) ? $settings['link']['url'] : '' );
		$is_link = ! empty( $link );
		$tag = ( isset( $settings['tag'] ) ? $settings['tag'] : '' );
		$inner_tag = ( $is_link ? 'a' : 'div' );

		$this->add_render_attribute( 'marquee_wrapper', 'class', $this->get_widget_class() . '__wrapper' );

		$this->add_render_attribute( 'marquee_inner', 'class', $this->get_widget_class() . '__inner' );

		$this->add_render_attribute( 'marquee_cont', 'class', $this->get_widget_class() . '__cont' );

		if ( $is_link ) {
			$this->add_render_attribute( 'marquee_cont', 'href', esc_url( $link ) );
		}

		echo '<div ' . $this->get_render_attribute_string( 'marquee_wrapper' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<' . Utils::validate_html_tag( $tag ) . ' ' . $this->get_render_attribute_string( 'marquee_inner' ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'<' . $inner_tag . ' ' . $this->get_render_attribute_string( 'marquee_cont' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					$this->get_marquee_items();

				echo '</' . $inner_tag . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'</' . Utils::validate_html_tag( $tag ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'</div>';
	}
}
