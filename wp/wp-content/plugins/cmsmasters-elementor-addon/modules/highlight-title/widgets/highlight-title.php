<?php
namespace CmsmastersElementor\Modules\HighlightTitle\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls\Groups\Group_Control_Vars_Text_Background;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Highlight_Title extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.20.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Highlight Title', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.20.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-highlight-title';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.20.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array(
			'highlight',
			'title',
		);
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.20.0
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array( 'widget-cmsmasters-highlight-title' );
	}

	/**
	 * Outputs elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.20.0
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Get the selector for the animation type 'Item by item'.
	 *
	 * @since 1.20.0
	 */
	public function get_separate_animation_selector() {
		return '.elementor-widget-cmsmasters-highlight-title__item';
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.20.0
	 */
	public function get_text_animation_class() {
		return array( 'elementor-widget-cmsmasters-highlight-title__item' => 'sequental, random, line, word, char' );
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-highlight-title';
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
	 * @since 1.20.0
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_controls_general();

		/* Style Tab */
		$this->register_style_controls_general();

		$this->register_style_controls_alternative();

		$this->register_style_controls_highlight();
	}

	/**
	 * Highlight Title content controls.
	 *
	 * @since 1.20.0
	 * @since 1.22.0 Added new control 'Display Type' for displayed items.
	 * @since 1.22.0 Fixed custom colors applying.
	 */
	protected function register_content_controls_general() {
		$this->start_controls_section(
			'section_highlight_title',
			array( 'label' => esc_html__( 'Highlight Title', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'entrance_animation',
			array(
				'label' => esc_html__( 'Entrance Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'entrance_animation_text',
			array(
				'label' => esc_html__( 'Entrance Animation Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'highlight_title_type',
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
				),
				'default' => 'text',
				'toggle' => false,
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$repeater->add_control(
			'highlight_title_text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your text here', 'cmsmasters-elementor' ),
				'default' => esc_html__( 'Enter your text here', 'cmsmasters-elementor' ),
				'condition' => array( 'highlight_title_type' => 'text' ),
			)
		);

		$repeater->add_control(
			'highlight_title_icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid' => array(
						'star',
						'circle',
						'dot-circle',
						'square-full',
					),
					'fa-regular' => array(
						'circle',
						'dot-circle',
						'square-full',
					),
				),
				'condition' => array( 'highlight_title_type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'highlight_title_icon_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-highlight-title-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'highlight_title_type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'highlight_title_icon_position',
			array(
				'label' => esc_html__( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-highlight-title-icon-position: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'highlight_title_type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'highlight_title_item_link',
			array(
				'label' => esc_html__( 'Link', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => esc_html__( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'highlight_title_item_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.cmsmasters-highlight-title-item-text',
				'condition' => array( 'highlight_title_type' => 'text' ),
			)
		);

		$repeater->add_control(
			'highlight_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-widget-cmsmasters-highlight-title__item' => '--text-color: {{VALUE}}; --icon-color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.cmsmasters-color-variation-gradient' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.cmsmasters-color-variation-background-image' => '-webkit-text-fill-color: {{VALUE}};',
				),
				'condition' => array( 'alternative_style!' => 'yes' ),
			)
		);

		$repeater->add_control(
			'highlight_title_color_hover',
			array(
				'label' => esc_html__( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}.elementor-widget-cmsmasters-highlight-title__item' => '--text-color-hover: {{VALUE}}; --icon-hover-color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.cmsmasters-color-variation-gradient:hover' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.cmsmasters-color-variation-background-image:hover' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-global-link:hover {{CURRENT_ITEM}}.cmsmasters-color-variation-gradient' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}} .cmsmasters-global-link:hover {{CURRENT_ITEM}}.cmsmasters-color-variation-background-image' => '-webkit-text-fill-color: {{VALUE}};',
				),
				'condition' => array( 'alternative_style!' => 'yes' ),
			)
		);

		$repeater->add_responsive_control(
			'margin',
			array(
				'label' => esc_html__( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
					'em',
					'rem',
					'vw',
					'vh',
					'custom',
				),
				'allowed_dimensions' => 'horizontal',
				'placeholder' => array(
					'top' => '',
					'right' => 'auto',
					'bottom' => '',
					'left' => 'auto',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => "--cmsmasters-highlight-title-item-margin-top: {{TOP}}{{UNIT}}; --cmsmasters-highlight-title-item-margin-right: {{RIGHT}}{{UNIT}}; --cmsmasters-highlight-title-item-margin-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-highlight-title-item-margin-left: {{LEFT}}{{UNIT}};",
				),
			)
		);

		$repeater->add_control(
			'highlight_title_layout',
			array(
				'label' => esc_html__( 'Start New Line', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'block',
				'render_type' => 'template',
			)
		);

		$repeater->add_control(
			'highlight_style',
			array(
				'label' => esc_html__( 'Highlight Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none' => esc_html__( 'None', 'cmsmasters-elementor' ),
					'stroke1' => esc_html__( 'Stroke 1', 'cmsmasters-elementor' ),
					'stroke2' => esc_html__( 'Stroke 2', 'cmsmasters-elementor' ),
					'stroke3' => esc_html__( 'Stroke 3', 'cmsmasters-elementor' ),
					'stroke4' => esc_html__( 'Stroke 4', 'cmsmasters-elementor' ),
					'stroke5' => esc_html__( 'Stroke 5', 'cmsmasters-elementor' ),
					'stroke6' => esc_html__( 'Stroke 6', 'cmsmasters-elementor' ),
					'stroke7' => esc_html__( 'Stroke 7', 'cmsmasters-elementor' ),
					'stroke8' => esc_html__( 'Stroke 8', 'cmsmasters-elementor' ),
					'stroke9' => esc_html__( 'Stroke 9', 'cmsmasters-elementor' ),
				),
				'render_type' => 'template',
				'condition' => array( 'highlight_title_type' => 'text' ),
			)
		);

		$repeater->add_control(
			'highlight_color',
			array(
				'label' => esc_html__( 'Highlight Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#DF3232',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-highlight-title-highlight-color: {{VALUE}};',
				),
				'condition' => array(
					'highlight_style!' => 'none',
					'highlight_title_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'highlight_width',
			array(
				'label' => esc_html__( 'Highlight Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => '40',
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-highlight-title-stroke-width: {{SIZE}};',
				),
				'condition' => array(
					'highlight_style!' => 'none',
					'highlight_title_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'hightlight_offset',
			array(
				'label' => esc_html__( 'Hightlight Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => '0',
					'unit' => '%',
				),
				'size_units' => array(
					'%',
					'px',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-highlight-title-hightlight-offset: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'highlight_style!' => 'none',
					'highlight_title_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'hightlight_forward',
			array(
				'label' => esc_html__( 'Bring Hightlight Forward', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} svg,
					{{WRAPPER}} {{CURRENT_ITEM}} ' . $this->get_widget_selector() . '__svg-wrapper .sc_item_animated_block' => 'z-index: 1;',
				),
				'condition' => array(
					'highlight_style!' => 'none',
					'highlight_title_type' => 'text',
				),
			)
		);

		$repeater->add_control(
			'alternative_style',
			array(
				'label' => esc_html__( 'Alternative Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => esc_html__( 'Enable Alternative Style for this item. The Alternative Style can be customized in the Style tab.', 'cmsmasters-elementor' ),
				'condition' => array( 'highlight_title_type' => 'text' ),
			)
		);

		$this->add_control(
			'content',
			array(
				'label' => esc_html__( 'Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'highlight_title_text' => esc_html__( 'Enter your text here', 'cmsmasters-elementor' ),
						'highlight_style' => 'none',
					),
				),
				'title_field' => '{{{ "text" === highlight_title_type ? highlight_title_text : highlight_title_type }}}',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => esc_html__( 'Link', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => esc_html__( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'tag',
			array(
				'label' => esc_html__( 'HTML Tag', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				),
				'default' => 'h2',
			)
		);

		$this->add_control(
			'highlight_title_display_type',
			array(
				'label' => esc_html__( 'Display Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inline' => array(
						'title' => esc_html__( 'Inline', 'cmsmasters-elementor' ),
					),
					'flex' => array(
						'title' => esc_html__( 'Flex', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'inline',
				'toggle' => false,
				'label_block' => false,
				'prefix_class' => 'cmsmasters-highlight-display-type-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'highlight_title_blend_mode',
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
					'{{WRAPPER}}' => '--cmsmasters-highlight-title-mix-blend-mode: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Highlight Title controls in Style tab.
	 *
	 * @since 1.20.0
	 * @since 1.23.0 Fixed `Alignment` control in adaptive mode.
	 */
	protected function register_style_controls_general() {
		$this->start_controls_section(
			'section_highlight_title_style',
			array(
				'label' => esc_html__( 'Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'highlight_title_alignment',
			array(
				'label' => esc_html__( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors_dictionary' => array(
					'left' => 'text-align: left; justify-content: flex-start;',
					'center' => 'text-align: center; justify-content: center;',
					'right' => 'text-align: right; justify-content: flex-end;',
				),
				'default' => 'left',
				'prefix_class' => 'cmsmasters-highlight-title-alignment-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-highlight-title__title' => '{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'highlight_title_global_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-highlight-title__title',
			)
		);

		$this->start_controls_tabs( 'highlight_title_style_tabs' );

		$this->start_controls_tab(
			'highlight_title_style_tabs_normal_tab',
			array( 'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_group_control(
			Group_Control_Vars_Text_Background::get_type(),
			array(
				'name' => 'highlight_title_bg',
				'fields_options' => array(
					'text_bg_variation' => array( 'prefix_class' => '' ),
					'text_gradient_type_normal' => array( 'prefix_class' => '' ),
					'text_gradient_animation' => array( 'prefix_class' => '' ),
					'text_background_image_hover' => array( 'prefix_class' => '' ),
					'text_background_image_position' => array( 'prefix_class' => '' ),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-highlight-title-item-text',
			)
		);

		$this->add_control(
			'highlight_title_style_icon_color_normal',
			array(
				'label' => esc_html__( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_title_style_text_stroke_width',
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
					'{{WRAPPER}}' => '--cmsmasters-highlight-title-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'highlight_title_style_text_stroke_color',
			array(
				'label' => esc_html__( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-highlight-title-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'highlight_title_style_text_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array( 'name' => 'highlight_title_style' )
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'highlight_title_style_tabs_hover_tab',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'highlight_title_bg_hover_text_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--text-color-hover: {{VALUE}};',
				),
				'condition' => array( 'highlight_title_bg_text_bg_variation' => 'default' ),
			)
		);

		$this->add_control(
			'highlight_title_style_icon_color_hover',
			array(
				'label' => esc_html__( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'highlight_title_style_text_stroke_width_hover',
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
					'{{WRAPPER}}' => '--cmsmasters-highlight-title-text-stroke-width-hover: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'highlight_title_style_text_stroke_color_hover',
			array(
				'label' => esc_html__( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-highlight-title-text-stroke-color-hover: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'highlight_title_style_text_stroke_width_hover[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'highlight_title_style_highlight_color_hover',
			array(
				'label' => esc_html__( 'Highlight Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-highlight-title-highlight-color-hover: {{VALUE}};',
				),
				'condition' => array(
					'highlight_style!' => 'none',
					'highlight_title_type' => 'text',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array( 'name' => 'highlight_title_style_hover' )
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'highlight_title_style_space_between',
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-highlight-title-space-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Highlight controls in Style tab.
	 *
	 * @since 1.20.0
	 */
	protected function register_style_controls_alternative() {
		$this->start_controls_section(
			'section_highlight_title_alternative',
			array(
				'label' => esc_html__( 'Alternative Style', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'highlight_title_alternative_style_tabs' );

		$this->start_controls_tab(
			'highlight_title_alternative_style_tabs_normal_tab',
			array( 'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_group_control(
			Group_Control_Vars_Text_Background::get_type(),
			array(
				'name' => 'highlight_title_alternative_bg',
				'fields_options' => array(
					'text_bg_variation' => array( 'prefix_class' => '' ),
					'text_gradient_type_normal' => array( 'prefix_class' => '' ),
					'text_gradient_animation' => array( 'prefix_class' => '' ),
					'text_background_image_hover' => array( 'prefix_class' => '' ),
					'text_background_image_position' => array( 'prefix_class' => '' ),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes',
			)
		);

		$this->add_control(
			'highlight_title_alternative_style_text_stroke_width',
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
					'{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes' => '--cmsmasters-highlight-title-alternative-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'highlight_title_alternative_style_text_stroke_color',
			array(
				'label' => esc_html__( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes' => '--cmsmasters-highlight-title-alternative-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'highlight_title_alternative_style_text_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'highlight_title_alternative_style',
				'selector' => '{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'highlight_title_alternative_style_tabs_hover_tab',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'highlight_title_alternative_bg_hover_text_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes' => '--text-color-hover: {{VALUE}};',
				),
				'condition' => array( 'highlight_title_alternative_bg_text_bg_variation' => 'default' ),
			)
		);

		$this->add_control(
			'highlight_title_alternative_style_text_stroke_width_hover',
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
					'{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes' => '--cmsmasters-highlight-title-alternative-text-stroke-width-hover: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'highlight_title_alternative_style_text_stroke_color_hover',
			array(
				'label' => esc_html__( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes' => '--cmsmasters-highlight-title-alternative-text-stroke-color-hover: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'highlight_title_alternative_style_text_stroke_width_hover[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array(
				'name' => 'highlight_title_alternative_style_hover',
				'selector' => '{{WRAPPER}} .cmsmasters-highlight-title-item-text.cmsmasters-alternative-style-yes',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Highlight controls in Style tab.
	 *
	 * @since 1.20.0
	 */
	protected function register_style_controls_highlight() {
		$this->start_controls_section(
			'section_highlight_title_highlight',
			array(
				'label' => esc_html__( 'Highlight Animation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'highlight_title_highlight_animation',
			array(
				'label' => esc_html__( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Off', 'cmsmasters-elementor' ),
				'default' => 'animate',
				'separator' => 'before',
				'return_value' => 'animate',
				'prefix_class' => 'cmsmasters-highlight-title-',
			)
		);

		$this->add_control(
			'highlight_title_highlight_animation_delay',
			array(
				'label' => esc_html__( 'Animation Delay', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'render_type' => 'template',
				'condition' => array( 'highlight_title_highlight_animation' => 'animate' ),
			)
		);

		$this->add_control(
			'highlight_title_highlight_animation_duration',
			array(
				'label' => esc_html__( 'Animation Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 100,
				'render_type' => 'template',
				'default' => 2000,
				'condition' => array( 'highlight_title_highlight_animation' => 'animate' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get get_svg.
	 *
	 * Retrieve get_svg html.
	 *
	 * @since 1.20.0
	 */
	protected function get_svg( $return, $type = '' ) {
		$paths = array(
			'none' => null,
			'stroke1' => "<path d='M15.2,133.3L15.2,133.3c121.9-7.6,244-9.9,366.1-6.8c34.6,0.9,69.1,2.3,103.7,4'/>",
			'stroke2' => "<path d='M479,122c-13.3-1-26.8-1-40.4-2.3c-31.8-1.2-63.7,0.4-95.3,0.6c-38.5,1.5-77,2.3-115.5,5.2c-41.6,1.2-83.3,5-124.9,5.2c-5.4,0.4-11,1-16.4,0.8c-21.9-0.4-44.1,1.9-65.6-3.5'/>",
			'stroke3' => "<path d='M15,133.4c19-12.7,48.1-11.4,69.2-8.2c6.3,1.1,12.9,2.1,19.2,3.4c16.5,3.2,33.5,6.3,50.6,5.5c12.7-0.6,24.9-3.4,36.7-6.1c11-2.5,22.4-5.1,34.2-5.9c24.3-1.9,48.5,3.4,71.9,8.4c27.6,6.1,53.8,11.8,80.4,6.8c9.9-1.9,19.2-5.3,28.3-8.4c8.2-3,16.9-5.9,25.9-8c20.3-4.4,45.8-1.1,53.6,12.2'/>",
			'stroke4' => "<path d='M18,122.6c42.3-4.6,87.4-5.1,130.3-1.6'/>
				<path d='M166.7,121.3c29.6,1.6,60,3.3,90.1,1.8c12.4-0.5,24.8-1.6,36.9-2.7c7.3-0.7,14.8-1.3,22.3-1.8c55.5-4.2,112.6-1.8,166,1.1'/>
				<path d='M57.8,133c30.8-0.7,62,1.1,92.1,2.7c30.5,1.8,62,3.6,93.2,2.7c20.4-0.5,41.1-2.4,61.1-4c37.6-3.1,76.5-6.4,113.7-2'/>",
			'stroke5' => "<path d='M53.4,135.8c-12.8-1.5-25.6-1.3-38.3,0.7'/>
				<path d='M111.2,136c-12.2-0.2-24.4-0.5-36.7-0.8'/>
				<path d='M163.3,135.2c-12.2,0.2-24.4,0.5-36.6,0.8'/>
				<path d='M217.8,134.7c-12.5,0.6-24.9,1.2-37.4,1.8'/>
				<path d='M274.7,135.5c-12.8,0.1-25.5,0.1-38.3,0.2'/>
				<path d='M327.6,135.1c-13.6-0.8-27.2-0.3-40.7,1.4'/>
				<path d='M378.8,134.7c-12.2,0.6-24.4,1.2-36.6,1.8'/>
				<path d='M432.5,136.4c-12.2-0.6-24.4-1.1-36.6-1.7'/>
				<path d='M487.9,136.1c-11.6-1.3-23.3-1.4-35-0.2'/>",
			'stroke6' => "<path d='M14.4,111.6c0,0,202.9-33.7,471.2,0c0,0-194-8.9-397.3,24.7c0,0,141.9-5.9,309.2,0'/>",
			'stroke7' => "<path d='M15.2 133.3H485'/>",
			'stroke8' => '<path d="M1.65186 148.981C1.65186 148.981 73.8781 98.5943 206.859 93.0135C339.841 87.4327 489.874 134.065 489.874 134.065"/>',
			'stroke9' => '<path d="M7 74.5C7 74.5 104 127 252 117C400 107 494.5 49 494.5 49C494.5 49 473.5 59 461.5 74.5C449.5 90 449.5 107 449.5 107"/>
				<path d="M20.5 101.5C20.5 101.5 93 133.5 180.5 142.5C268 151.5 347 127.5 347 127.5"/>',
		);

		$svg = array();

		if ( 'list' === $return ) {
			$svg['none'] = '';

			foreach ( $paths as $name => $path ) {
				if ( 'none' !== $name ) {
					$svg[ $name ] = '<span class="' . $this->get_widget_class() . '__svg-wrapper"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">' .
						$path .
					'</svg></span>';
				}
			}
		}

		if ( 'single' === $return && 'none' !== $type ) {
			$svg = '<span class="' . $this->get_widget_class() . '__svg-wrapper"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">' .
				$paths[ $type ] .
			'</svg></span>';
		}

		return $svg;
	}

	/**
	 * Get text bg classes.
	 *
	 * @since 1.20.0
	 */
	protected function text_bg_classes( $settings, $item, $idx ) {
		$text_alternative_style = ( isset( $item['alternative_style'] ) ? $item['alternative_style'] : '' );
		$alternative_style = '';
		$bg_variation = '';
		$gradient_type_normal = '';
		$gradient_animation = '';
		$background_image_hover = '';
		$bg_image_position = '';

		$text_text_bg_variation = ( isset( $settings['highlight_title_bg_text_bg_variation'] ) ? $settings['highlight_title_bg_text_bg_variation'] : '' );

		if ( 'yes' === $text_alternative_style ) {
			$alternative_style = 'cmsmasters-alternative-style-' . $text_alternative_style;

			$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', $alternative_style );

			$text_text_bg_variation = ( isset( $settings['highlight_title_alternative_bg_text_bg_variation'] ) ? $settings['highlight_title_alternative_bg_text_bg_variation'] : '' );
		}

		if ( ! empty( $text_text_bg_variation ) ) {
			$bg_variation = 'cmsmasters-color-variation-' . $text_text_bg_variation;

			$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', $bg_variation );

			if ( 'yes' === $text_alternative_style ) {
				$text_text_gradient_type_normal = ( isset( $settings['highlight_title_alternative_bg_text_gradient_type_normal'] ) ? $settings['highlight_title_alternative_bg_text_gradient_type_normal'] : '' );
			} else {
				$text_text_gradient_type_normal = ( isset( $settings['highlight_title_bg_text_gradient_type_normal'] ) ? $settings['highlight_title_bg_text_gradient_type_normal'] : '' );
			}

			if ( 'gradient' === $text_text_bg_variation && ! empty( $text_text_gradient_type_normal ) ) {
				$gradient_type_normal = 'cmsmasters-color-gradient-' . $text_text_gradient_type_normal;

				$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', $gradient_type_normal );

				if ( 'yes' === $text_alternative_style ) {
					$text_gradient_animation = ( isset( $settings['highlight_title_alternative_bg_text_gradient_animation'] ) ? $settings['highlight_title_alternative_bg_text_gradient_animation'] : '' );
				} else {
					$text_gradient_animation = ( isset( $settings['highlight_title_bg_text_gradient_animation'] ) ? $settings['highlight_title_bg_text_gradient_animation'] : '' );
				}

				if ( ! empty( $text_gradient_animation ) ) {
					$gradient_animation = 'cmsmasters-color-variation-gradient-animation-' . $text_gradient_animation;

					$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', $gradient_animation );
				}
			}

			if ( 'yes' === $text_alternative_style ) {
				$text_background_image = ( isset( $settings['highlight_title_alternative_bg_text_background_image'] ) ? $settings['highlight_title_alternative_bg_text_background_image'] : '' );
				$text_background_image_hover = ( isset( $settings['highlight_title_alternative_bg_text_background_image_hover'] ) ? $settings['highlight_title_alternative_bg_text_background_image_hover'] : '' );
			} else {
				$text_background_image = ( isset( $settings['highlight_title_bg_text_background_image'] ) ? $settings['highlight_title_bg_text_background_image'] : '' );
				$text_background_image_hover = ( isset( $settings['highlight_title_bg_text_background_image_hover'] ) ? $settings['highlight_title_bg_text_background_image_hover'] : '' );
			}

			if ( 'background-image' === $text_text_bg_variation && $text_background_image && '' !== $text_background_image['url'] && 'yes' === $text_background_image_hover ) {
				$background_image_hover = 'cmsmasters-bg-image-hover-' . $text_background_image_hover;
				$text_background_image_hover_position = ( isset( $settings['highlight_title_alternative_bg_text_background_image_hover_position'] ) ? $settings['highlight_title_alternative_bg_text_background_image_hover_position'] : '' );
				$bg_image_position = 'cmsmasters-bg-image-position-' . $text_background_image_hover_position;

				$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', array(
					$background_image_hover,
					$bg_image_position,
				) );
			}
		}
	}

	/**
	 * Render highlight title widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.20.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$this->add_render_attribute( 'highlight-title', 'class', array( $this->get_widget_class() . '__title' ) );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'highlight-title', 'class', 'cmsmasters-global-link' );
		}

		if ( ! empty( $settings['highlight_title_highlight_animation'] ) ) {
			if ( (int) $settings['highlight_title_highlight_animation_duration'] > 0 ) {
				$this->add_render_attribute( 'highlight-title', array(
					'style' => '--cmsmasters-highlight-title-highlight-animation-duration: ' . $settings['highlight_title_highlight_animation_duration'] . 'ms;',
				) );
			}

			if ( (int) $settings['highlight_title_highlight_animation_delay'] > 0 ) {
				$this->add_render_attribute( 'highlight-title', array(
					'data-delay' => esc_attr( $settings['highlight_title_highlight_animation_delay'] ),
				) );
			}
		}

		echo '<' . esc_html( $settings['tag'] ) . ' ' . $this->get_render_attribute_string( 'highlight-title' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$idx = 0;

		foreach ( $settings['content'] as $item ) {
			$idx++;

			$link = array();
			$link_url = '';

			if ( ! empty( $settings['link']['url'] ) ) {
				$link = $settings['link'];
				$link_url = $settings['link']['url'];
			} elseif ( empty( $settings['link']['url'] ) && ! empty( $item['highlight_title_item_link']['url'] ) ) {
				$link = $item['highlight_title_item_link'];
				$link_url = $item['highlight_title_item_link']['url'];
			}

			$this->add_link_attributes( 'item-link-' . $idx, $link );

			$start_tag = $link_url ? '<a ' . wp_kses_post( $this->get_render_attribute_string( 'item-link-' . $idx ) ) : '<span';
			$separator = ( 'block' === $item['highlight_title_layout'] ? '<br />' : '' );
			$end_tag = $link_url ? '</a>' : '</span>';

			$svg_markup = null;

			$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', array(
				$this->get_widget_class() . '__item',
				'elementor-repeater-item-' . esc_attr( $item['_id'] ),
			) );

			switch ( $item['highlight_title_type'] ) {
				case 'text':
					$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', 'cmsmasters-highlight-title-item-text' );

					$this->text_bg_classes( $settings, $item, $idx );

					if ( 'none' !== $item['highlight_style'] ) {
						$svg_markup = $this->get_svg( 'single', $item['highlight_style'] );

						$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', array(
							'elementor-widget-cmsmasters-highlight-title__item-highlighted',
							'cmsmasters-highlight-title-' . esc_attr( $item['highlight_style'] ),
						) );
					}

					echo $separator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					echo $start_tag . ' ' . $this->get_render_attribute_string( 'highlight-title-item' . $idx ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'<span class="' . $this->get_widget_class() . '__text-wrap">' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'<span class="' . $this->get_widget_class() . '__text">' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								esc_html( $item['highlight_title_text'] ) .
							'</span>';

							if ( 'none' !== $item['highlight_style'] ) {
								echo $svg_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}

						echo '</span>' .
					$end_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					break;
				case 'icon':
					ob_start();

					if ( '' !== $item['highlight_title_icon']['value'] ) {
						Icons_Manager::render_icon( $item['highlight_title_icon'], array(
							'aria-hidden' => 'true',
							'aria-label' => esc_attr__( 'Highlight Title item icon', 'cmsmasters-elementor' ),
							'class' => 'char',
						) );
					} else {
						Icons_Manager::render_icon(
							array(
								'value' => 'fas fa-circle',
								'library' => 'fa-solid',
							),
							array(
								'aria-hidden' => 'true',
								'aria-label' => esc_attr__( 'Highlight Title item icon', 'cmsmasters-elementor' ),
								'class' => 'char',
							)
						);
					}

					$icon = ob_get_clean();

					if ( false !== strpos( $icon, '<svg' ) ) {
						$icon = '<span class="' . $this->get_widget_class() . '__item-icon-svg">' .
							$icon .
						'</span>';
					}

					$this->add_render_attribute( 'highlight-title-item' . $idx, 'class', 'cmsmasters-highlight-title-item-icon' );

					echo $separator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					echo $start_tag . ' ' . $this->get_render_attribute_string( 'highlight-title-item' . $idx ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$icon . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$end_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					break;
				default:
					break;
			}
		}

		echo '</' . esc_html( $settings['tag'] ) . '>';
	}

	/**
	 * Render a widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?><#
		var svgs = <?php echo wp_json_encode( $this->get_svg( 'list' ) ); ?>;
		var globalLink = ( settings.link && settings.link.url ) ? ' cmsmasters-global-link' : '';
		var highlight_animation = settings.highlight_title_highlight_animation;
		var highlight_animation_duration = settings.highlight_title_highlight_animation_duration;
		var highlight_animation_delay = settings.highlight_title_highlight_animation_delay;

		if ( highlight_animation && highlight_animation_duration > 0 ) {
			view.addRenderAttribute( 'highlight-title', 'style', '--cmsmasters-highlight-title-highlight-animation-duration: ' + highlight_animation_duration + 'ms;' );
		}

		#><{{{ settings.tag }}} class="elementor-widget-cmsmasters-highlight-title__title {{{ globalLink }}}"<# print( highlight_animation && highlight_animation_delay > 0 ? ' data-delay="' + highlight_animation_delay + '"' : '' ); #> {{{ view.getRenderAttributeString( 'highlight-title' ) }}}><#

		settings.content.forEach( function( item ) {
			var link_url = '',
				link_target = '',
				link_nofollow = '';

			if ( settings.link.url ) {
				link_url = settings.link.url;
				link_target = settings.link.is_external ? ' target="_blank"' : '';
				link_nofollow = settings.link.nofollow ? ' rel="nofollow"' : '';
			} else if ( ! settings.link.url && item.highlight_title_item_link.url != '' ) {
				link_url = item.highlight_title_item_link.url;
				link_target = item.highlight_title_item_link.is_external ? ' target="_blank"' : '';
				link_nofollow = item.highlight_title_item_link.nofollow ? ' rel="nofollow"' : '';
			}

			var start_tag = link_url ? '<a href="' + link_url + '"' + link_target + link_nofollow : '<span';
			var end_tag = link_url ? '</a>' : '</span>';
			var separator = ( item.highlight_title_layout == 'block' ? '<br />' : '' );

			switch ( item.highlight_title_type ) {
				case 'text':
					var alternative_style = '';
					var bg_variation = '';
					var gradient_type_normal = '';
					var gradient_animation = '';
					var background_image_hover = '';
					var bg_image_position = '';

					if ( 'yes' === item.alternative_style ) {
						alternative_style = ' cmsmasters-alternative-style-' + item.alternative_style;

						var text_bg_variation = settings.highlight_title_alternative_bg_text_bg_variation;
					} else {
						var text_bg_variation = settings.highlight_title_bg_text_bg_variation;
					}

					if ( '' !== text_bg_variation ) {
						bg_variation = ' cmsmasters-color-variation-' + text_bg_variation;

						if ( 'yes' === item.alternative_style ) {
							var text_text_gradient_type_normal = settings.highlight_title_alternative_bg_text_gradient_type_normal;
						} else {
							var text_text_gradient_type_normal = settings.highlight_title_bg_text_gradient_type_normal;
						}

						if ( 'gradient' === text_bg_variation && '' !== text_text_gradient_type_normal ) {
							gradient_type_normal = ' cmsmasters-color-gradient-' + text_text_gradient_type_normal;

							if ( 'yes' === item.alternative_style ) {
								var text_gradient_animation = settings.highlight_title_alternative_bg_text_gradient_animation;
							} else {
								var text_gradient_animation = settings.highlight_title_bg_text_gradient_animation;
							}

							if ( '' !== text_gradient_animation ) {
								gradient_animation = ' cmsmasters-color-variation-gradient-animation-' + text_gradient_animation;
							}
						}

						if ( 'yes' === item.alternative_style ) {
							var text_background_image = settings.highlight_title_alternative_bg_text_background_image;
							var text_background_image_hover = settings.highlight_title_alternative_bg_text_background_image_hover;
						} else {
							var text_background_image = settings.highlight_title_bg_text_background_image;
							var text_background_image_hover = settings.highlight_title_alternative_bg_text_background_image_hover;
						}

						if ( 'background-image' === text_bg_variation && '' !== text_background_image && '' !== text_background_image.url && 'yes' === text_background_image_hover ) {
							background_image_hover = ' cmsmasters-bg-image-hover-' + text_background_image_hover;
							bg_image_position = ' cmsmasters-bg-image-position-' + settings.highlight_title_alternative_bg_text_background_image_hover_position;
						}
					}

					let item_classes = [
						'elementor-widget-cmsmasters-highlight-title__item',
						alternative_style,
						bg_variation,
						gradient_type_normal,
						gradient_animation,
						background_image_hover,
						bg_image_position,
						' elementor-repeater-item-' + item._id,
						' cmsmasters-highlight-title-item-text',
					];

					let extra_svg = '';

					if ( 'none' !== item.highlight_style ) {
						item_classes.push( ' elementor-widget-cmsmasters-highlight-title__item-highlighted' );
						item_classes.push( ' cmsmasters-highlight-title-' + item.highlight_style );
						extra_svg = svgs[ item.highlight_style ];
					}

					#>{{{ separator }}}<#
					#>{{{ start_tag }}} class="{{ item_classes.join('') }}"><#
						#><span class="elementor-widget-cmsmasters-highlight-title__text-wrap"><#
							#><span class="elementor-widget-cmsmasters-highlight-title__text">{{{ item.highlight_title_text }}}</span><#
							#>{{{ extra_svg }}}<#
						#></span><#
					#>{{{ end_tag }}}<#

					break;
				case 'icon':
					var iconHTML = elementor.helpers.renderIcon(
						view,
						item.highlight_title_icon,
						{ 'aria-hidden': true, 'aria-label': 'Highlight Title item icon', 'class': 'char' },
						'i',
						'value'
					);

					if ( '' !== item.highlight_title_icon.value ) {
						if ( 'object' === typeof iconHTML && iconHTML.rendered ) {
							iconHTML = iconHTML.value;
						}

						if ( iconHTML && iconHTML.indexOf( '<svg' ) >= 0 ) {
							iconHTML = '<span class="elementor-widget-cmsmasters-highlight-title__item-icon-svg">' + iconHTML + '</span>';
						}
					} else {
						#><span class="elementor-widget-cmsmasters-highlight-title__item-icon-svg">
							<i aria-hidden="true" aria-label="Highlight Title item icon" class="fas fa-circle"></i>
						</span><#
					}

					#>{{{ separator }}}{{{ start_tag }}} class="elementor-widget-cmsmasters-highlight-title__item cmsmasters-highlight-title-item-icon elementor-repeater-item-{{{ item._id }}}">{{{ iconHTML }}}{{{ end_tag }}}<#

					break;
				default:
					break;
			}
		} );

		#></{{{ settings.tag }}}><?php
	}
}
