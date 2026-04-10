<?php
namespace CmsmastersElementor\Modules\AdvancedTitle\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls\Groups\Group_Control_Vars_Text_Background;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
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

class Advanced_Title extends Base_Widget {

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
		return __( 'Advanced Title', 'cmsmasters-elementor' );
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
		return 'cmsicon-advanced-title';
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
			'advanced',
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
		return array( 'widget-cmsmasters-advanced-title' );
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
		return '.elementor-widget-cmsmasters-advanced-title__item';
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.20.0
	 */
	public function get_text_animation_class() {
		return array( 'elementor-widget-cmsmasters-advanced-title__item' => 'sequental, random, line, word, char' );
	}

	public function get_widget_class() {
		return 'elementor-widget-cmsmasters-advanced-title';
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
	}

	/**
	 * Advanced Title content controls.
	 *
	 * @since 1.20.0
	 * @since 1.22.0 Added new control 'Display Type' and Vertical Alignment for displayed items.
	 * Added new Type 'Image' for widget items.
	 * Added `Height`, `Border Radius` and `Image Resolution` controls for item Type 'Image'.
	 */
	protected function register_content_controls_general() {
		$this->start_controls_section(
			'section_advanced_title',
			array( 'label' => esc_html__( 'Advanced Title', 'cmsmasters-elementor' ) )
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
			'advanced_title_type',
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
				'render_type' => 'template',
			)
		);

		$repeater->add_control(
			'advanced_title_text',
			array(
				'label' => esc_html__( 'Text', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => esc_html__( 'Enter your text here', 'cmsmasters-elementor' ),
				'default' => esc_html__( 'Enter your text here', 'cmsmasters-elementor' ),
				'condition' => array( 'advanced_title_type' => 'text' ),
			)
		);

		$repeater->add_control(
			'advanced_title_icon',
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
				'condition' => array( 'advanced_title_type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'advanced_title_icon_size',
			array(
				'label' => esc_html__( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-advanced-title-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'advanced_title_type' => 'icon' ),
			)
		);

		$repeater->add_control(
			'advanced_title_icon_position',
			array(
				'label' => esc_html__( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-advanced-title-icon-position: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'advanced_title_type' => 'icon' ),
			)
		);



		$repeater->add_control(
			'advanced_title_image',
			array(
				'label' => esc_html__( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic' => array( 'active' => true ),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array( 'advanced_title_type' => 'image' ),
			)
		);

		$repeater->add_control(
			'advanced_title_image_height',
			array(
				'label' => esc_html__( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
					'em' => array(
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-advanced-title-image-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'advanced_title_type' => 'image' ),
			)
		);

		$repeater->add_control(
			'advanced_title_item_link',
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
				'name' => 'advanced_title_item_typography',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.cmsmasters-advanced-title-item-text',
				'condition' => array( 'advanced_title_type' => 'text' ),
			)
		);

		$repeater->add_control(
			'advanced_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--text-color: {{VALUE}}; --icon-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-color-variation-gradient {{CURRENT_ITEM}}' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-color-variation-background-image {{CURRENT_ITEM}}' => '-webkit-text-fill-color: {{VALUE}};',
				),
				'condition' => array( 'advanced_title_type!' => 'image' ),
			)
		);

		$repeater->add_control(
			'advanced_title_color_hover',
			array(
				'label' => esc_html__( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--text-color-hover: {{VALUE}}; --icon-hover-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-color-variation-gradient {{CURRENT_ITEM}}:hover' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-color-variation-background-image {{CURRENT_ITEM}}:hover' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-color-variation-gradient .cmsmasters-global-link:hover {{CURRENT_ITEM}}' => '-webkit-text-fill-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-color-variation-background-image .cmsmasters-global-link:hover {{CURRENT_ITEM}}' => '-webkit-text-fill-color: {{VALUE}};',
				),
				'condition' => array( 'advanced_title_type!' => 'image' ),
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => "--cmsmasters-advanced-title-item-margin-top: {{TOP}}{{UNIT}}; --cmsmasters-advanced-title-item-margin-right: {{RIGHT}}{{UNIT}}; --cmsmasters-advanced-title-item-margin-bottom: {{BOTTOM}}{{UNIT}}; --cmsmasters-advanced-title-item-margin-left: {{LEFT}}{{UNIT}};",
				),
			)
		);

		$repeater->add_control(
			'advanced_title_image_border_radius',
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
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--cmsmasters-advanced-title-image-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'advanced_title_type' => 'image' ),
			)
		);

		$repeater->add_control(
			'advanced_title_layout',
			array(
				'label' => esc_html__( 'Start New Line', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'return_value' => 'block',
				'render_type' => 'template',
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
						'advanced_title_text' => esc_html__( 'Enter your text here', 'cmsmasters-elementor' ),
					),
				),
				'title_field' => '{{{ "text" === advanced_title_type ? advanced_title_text : advanced_title_type }}}',
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

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'advanced_title_image',
				'default' => 'full',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'advanced_title_display_type',
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
				'prefix_class' => 'cmsmasters-advanced-display-type-',
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'content_vertical_position',
			array(
				'label' => esc_html__( 'Vertical Alignment', 'cmsmasters-elementor' ),
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
				'default' => 'center',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-advanced-title-flex-vertical-align-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-advanced-title-flex-vertical-align: {{VALUE}};',
				),
				'condition' => array( 'advanced_title_display_type' => 'flex' ),
			)
		);

		$this->add_control(
			'advanced_title_blend_mode',
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
					'{{WRAPPER}}' => '--cmsmasters-advanced-title-mix-blend-mode: {{VALUE}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register Advanced Title controls in Style tab.
	 *
	 * @since 1.20.0
	 * @since 1.23.0 Fixed `Alignment` control in adaptive mode.
	 */
	protected function register_style_controls_general() {
		$this->start_controls_section(
			'section_advanced_title_style',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'advanced_title_alignment',
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
				'prefix_class' => 'cmsmasters-advanced-title-alignment-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-advanced-title__title' => '{{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'advanced_title_global_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-advanced-title__title',
			)
		);

		$this->start_controls_tabs( 'advanced_title_style_tabs' );

		$this->start_controls_tab(
			'advanced_title_style_tabs_normal_tab',
			array( 'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_group_control(
			Group_Control_Vars_Text_Background::get_type(),
			array(
				'name' => 'advanced_title_bg',
				'selector' => '{{WRAPPER}}',
			)
		);

		$this->add_control(
			'advanced_title_style_icon_color_normal',
			array(
				'label' => esc_html__( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'advanced_title_style_text_stroke_width',
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
					'{{WRAPPER}}' => '--cmsmasters-advanced-title-text-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'advanced_title_style_text_stroke_color',
			array(
				'label' => esc_html__( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-advanced-title-text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'advanced_title_style_text_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array( 'name' => 'advanced_title_style' )
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'advanced_title_style_tabs_hover_tab',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'advanced_title_bg_hover_text_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--text-color-hover: {{VALUE}};',
				),
				'condition' => array( 'advanced_title_bg_text_bg_variation' => 'default' ),
			)
		);

		$this->add_control(
			'advanced_title_style_icon_color_hover',
			array(
				'label' => esc_html__( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--icon-hover-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'advanced_title_style_text_stroke_width_hover',
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
					'{{WRAPPER}}' => '--cmsmasters-advanced-title-text-stroke-width-hover: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'advanced_title_style_text_stroke_color_hover',
			array(
				'label' => esc_html__( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-advanced-title-text-stroke-color-hover: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'advanced_title_style_text_stroke_width_hover[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_TEXT_SHADOW_GROUP,
			array( 'name' => 'advanced_title_style_hover' )
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'advanced_title_style_space_between',
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
					'{{WRAPPER}}' => '--cmsmasters-advanced-title-space-between: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get the effective typography value for a widget setting, supporting both custom and global typography.
	 *
	 * This function checks if a custom typography array is set in the widget settings.
	 * If not, it will check the '__globals__' array for a global typography ID.
	 * For global typography, it returns the ID string so that frontend JS can apply the styles.
	 *
	 * @since 1.21.2
	 *
	 * @param array $settings Widget settings array from get_settings_for_display().
	 * @param array $globals Global settings array from get_settings('__globals__').
	 * @param string $typography_id The setting key to check for typography (e.g., 'cms_custom_cursor_label_typography').
	 *
	 * @return array|string Returns the typography array if custom, or global ID string if global, or empty string.
	 */
	public function get_global_typography_value( $settings, $globals, $typography_id ) {
		$typography_value = array();

		if ( ! empty( $globals[ $typography_id . '_typography' ] ) ) {
			$id = str_replace( 'globals/typography?id=', '', $globals[ $typography_id . '_typography' ] );

			$typography_value = array(
				'font-family' => "var(--e-global-typography-{$id}-font-family)",
				'font-size' => "var(--e-global-typography-{$id}-font-size)",
				'font-weight' => "var(--e-global-typography-{$id}-font-weight)",
				'line-height' => "var(--e-global-typography-{$id}-line-height)",
				'letter-spacing' => "var(--e-global-typography-{$id}-letter-spacing)",
				'word-spacing' => "var(--e-global-typography-{$id}-word-spacing)",
				'text-transform' => "var(--e-global-typography-{$id}-text-transform)",
				'font-style' => "var(--e-global-typography-{$id}-font-style)",
				'text-decoration' => "var(--e-global-typography-{$id}-text-decoration)",
			);
		}

		return $typography_value;
	}

	/**
	 * Render advanced title widget output on the frontend.
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

		$this->add_render_attribute( 'advanced-title', 'class', array( $this->get_widget_class() . '__title' ) );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'advanced-title', 'class', 'cmsmasters-global-link' );
		}

		echo '<' . esc_html( $settings['tag'] ) . ' ' . $this->get_render_attribute_string( 'advanced-title' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$idx = 0;

		foreach ( $settings['content'] as $item ) {
			$idx++;

			$link = array();
			$link_url = '';

			if ( ! empty( $settings['link']['url'] ) ) {
				$link = $settings['link'];
				$link_url = $settings['link']['url'];
			} elseif ( empty( $settings['link']['url'] ) && ! empty( $item['advanced_title_item_link']['url'] ) ) {
				$link = $item['advanced_title_item_link'];
				$link_url = $item['advanced_title_item_link']['url'];
			}

			$this->add_link_attributes( 'item-link-' . $idx, $link );

			$start_tag = $link_url ? '<a ' . wp_kses_post( $this->get_render_attribute_string( 'item-link-' . $idx ) ) : '<span';
			$separator = ( 'block' === $item['advanced_title_layout'] ? '<br />' : '' );
			$end_tag = $link_url ? '</a>' : '</span>';

			$this->add_render_attribute( 'advanced-title-item' . $idx, 'class', array(
				$this->get_widget_class() . '__item',
				'elementor-repeater-item-' . esc_attr( $item['_id'] ),
			) );

			switch ( $item['advanced_title_type'] ) {
				case 'text':
					$this->add_render_attribute( 'advanced-title-item' . $idx, 'class', 'cmsmasters-advanced-title-item-text' );
					$this->add_render_attribute( 'advanced-title-item-text' . $idx, 'class', $this->get_widget_class() . '__text' );

					$globals = isset( $item['__globals__'] ) ? $item['__globals__'] : array();
					$item_global_typography = $this->get_global_typography_value( $item, $globals, 'advanced_title_item_typography' );
					$styles = '';

					if ( ! empty( $item_global_typography ) && is_array( $item_global_typography ) ) {
						foreach ( $item_global_typography as $prop => $value ) {
							if ( '' !== $value ) {
								$styles .= $prop . ':' . $value . ';';
							}
						}

						if ( '' !== $styles ) {
							$this->add_render_attribute( 'advanced-title-item-text' . $idx, 'style', esc_attr( $styles ) );
						}
					}

					echo $separator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					echo $start_tag . ' ' . $this->get_render_attribute_string( 'advanced-title-item' . $idx ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'<span class="' . $this->get_widget_class() . '__text-wrap">' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'<span ' . $this->get_render_attribute_string( 'advanced-title-item-text' . $idx ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								esc_html( $item['advanced_title_text'] ) .
							'</span>' .
						'</span>' .
					$end_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					break;
				case 'icon':
					ob_start();

					if ( '' !== $item['advanced_title_icon']['value'] ) {
						Icons_Manager::render_icon( $item['advanced_title_icon'], array(
							'aria-hidden' => 'true',
							'aria-label' => esc_attr__( 'Advanced Title item icon', 'cmsmasters-elementor' ),
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
								'aria-label' => esc_attr__( 'Advanced Title item icon', 'cmsmasters-elementor' ),
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

					$this->add_render_attribute( 'advanced-title-item' . $idx, 'class', 'cmsmasters-advanced-title-item-icon' );

					echo $separator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					echo $start_tag . ' ' . $this->get_render_attribute_string( 'advanced-title-item' . $idx ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$icon . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$end_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					break;
				case 'image':
					if ( empty( $item['advanced_title_image']['url'] ) ) {
						break;
					}

					$this->add_render_attribute( 'advanced-title-item' . $idx, 'class', 'cmsmasters-advanced-title-item-image' );

					echo $separator; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					$fake_settings = array(
						'advanced_title_image' => $item['advanced_title_image'],
						'advanced_title_image_size' => $settings['advanced_title_image_size'],
						'advanced_title_image_custom_dimension' => ( isset( $settings['advanced_title_image_custom_dimension'] ) ? $settings['advanced_title_image_custom_dimension'] : array() ),
					);

					$image_html = Group_Control_Image_Size::get_attachment_image_html(
						$fake_settings,
						'advanced_title_image',
						'advanced_title_image'
					);

					if ( empty( $image_html ) ) {
						$image_html = '<img src="' . esc_url( $item['advanced_title_image']['url'] ) . '" alt="" />';
					}

					echo $start_tag . ' ' . $this->get_render_attribute_string( 'advanced-title-item' . $idx ) . '>' . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						wp_kses_post( $image_html ) . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
		?>
		<# var globalLink = ( settings.link && settings.link.url ) ? ' cmsmasters-global-link' : ''; #>

		<{{{ settings.tag }}} class="elementor-widget-cmsmasters-advanced-title__title {{{ globalLink }}}" {{{ view.getRenderAttributeString( 'advanced-title' ) }}}><#

		settings.content.forEach( function( item ) {
			var link_url = '',
				link_target = '',
				link_nofollow = '';

			if ( settings.link.url ) {
				link_url = settings.link.url;
				link_target = settings.link.is_external ? ' target="_blank"' : '';
				link_nofollow = settings.link.nofollow ? ' rel="nofollow"' : '';
			} else if ( ! settings.link.url && item.advanced_title_item_link.url != '' ) {
				link_url = item.advanced_title_item_link.url;
				link_target = item.advanced_title_item_link.is_external ? ' target="_blank"' : '';
				link_nofollow = item.advanced_title_item_link.nofollow ? ' rel="nofollow"' : '';
			}

			var start_tag = link_url ? '<a href="' + link_url + '"' + link_target + link_nofollow : '<span';
			var end_tag = link_url ? '</a>' : '</span>';
			var separator = ( item.advanced_title_layout == 'block' ? '<br />' : '' );

			switch ( item.advanced_title_type ) {
				case 'text':
					var globalStyle = '';
					var hasGlobal = item.__globals__ && item.__globals__.advanced_title_item_typography_typography;

					if ( hasGlobal ) {
						var id = item.__globals__.advanced_title_item_typography_typography.replace( 'globals/typography?id=', '' );

						globalStyle =
							'font-family:var(--e-global-typography-' + id + '-font-family);' +
							'font-size:var(--e-global-typography-' + id + '-font-size);' +
							'font-weight:var(--e-global-typography-' + id + '-font-weight);' +
							'line-height:var(--e-global-typography-' + id + '-line-height);' +
							'letter-spacing:var(--e-global-typography-' + id + '-letter-spacing);' +
							'word-spacing:var(--e-global-typography-' + id + '-word-spacing);' +
							'text-transform:var(--e-global-typography-' + id + '-text-transform);' +
							'font-style:var(--e-global-typography-' + id + '-font-style);' +
							'text-decoration:var(--e-global-typography-' + id + '-text-decoration);';
					}

					#>{{{ separator }}}{{{ start_tag }}} class="elementor-widget-cmsmasters-advanced-title__item cmsmasters-advanced-title-item-text elementor-repeater-item-{{{ item._id }}}"><#
						#><span class="elementor-widget-cmsmasters-advanced-title__text-wrap"><#
							#><span class="elementor-widget-cmsmasters-advanced-title__text"
								<# if (globalStyle) { #>
									style="{{{ globalStyle }}}"
								<# } #>
							>{{{ item.advanced_title_text }}}</span><#
						#></span><#
					#>{{{ end_tag }}}<#
					break;
				case 'icon':
					var iconHTML = elementor.helpers.renderIcon(
						view,
						item.advanced_title_icon,
						{ 'aria-hidden': true, 'aria-label': 'Advanced Title item icon', 'class': 'char' },
						'i',
						'value'
					);

					if ( '' !== item.advanced_title_icon.value ) {
						if ( 'object' === typeof iconHTML && iconHTML.rendered ) {
							iconHTML = iconHTML.value;
						}

						if ( iconHTML && iconHTML.indexOf( '<svg' ) >= 0 ) {
							iconHTML = '<span class="elementor-widget-cmsmasters-advanced-title__item-icon-svg">' + iconHTML + '</span>';
						}
					} else {
						#><span class="elementor-widget-cmsmasters-advanced-title__item-icon-svg">
							<i aria-hidden="true" aria-label="Advanced Title item icon" class="fas fa-circle"></i>
						</span><#
					}

					#>{{{ separator }}}{{{ start_tag }}} class="elementor-widget-cmsmasters-advanced-title__item cmsmasters-advanced-title-item-icon elementor-repeater-item-{{{ item._id }}}">{{{ iconHTML }}}{{{ end_tag }}}<#

					break;
				case 'image':
					if ( item.advanced_title_image && item.advanced_title_image.url ) {
						var imageObj = {
							id: item.advanced_title_image.id,
							url: item.advanced_title_image.url,
							size: settings.advanced_title_image_size,
							dimension: settings.advanced_title_image_custom_dimension,
							model: view.getEditModel()
						};
						var image_url = elementor.imagesManager.getImageUrl( imageObj ) || item.advanced_title_image.url;
						#>{{{ separator }}}{{{ start_tag }}} class="elementor-widget-cmsmasters-advanced-title__item cmsmasters-advanced-title-item-image elementor-repeater-item-{{{ item._id }}}"><img src="{{{ image_url }}}" alt="" />{{{ end_tag }}}<#
					}
					break;
				default:
					break;
			}
		} );

		#></{{{ settings.tag }}}><?php
	}
}
