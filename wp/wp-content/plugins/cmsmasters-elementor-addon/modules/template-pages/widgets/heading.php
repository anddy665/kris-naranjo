<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Animation\Classes\Animation as AnimationModule;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Traits\Extendable_Widget;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * CMSMasters Heading widget.
 *
 * CMSMasters widget that displays the heading tag.
 *
 * @since 1.0.0
 */
class Heading extends Base_Widget {

	use Extendable_Widget;

	protected $link_active = false;

	/**
	 * Get extendable widget class.
	 *
	 * Retrieve the extendable widget container class.
	 *
	 * @since 1.0.0
	 *
	 * @return string Extendable widget container class.
	 */
	public function get_extendable_widget_class() {
		return 'cmsmasters-widget-title';
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-title';
	}

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
		return __( 'Title', 'cmsmasters-elementor' );
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
		return 'cmsicon-title';
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
			'title',
			'heading',
			'headline',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 */
	protected function is_dynamic_content(): bool {
		return false;
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
	 * Get the selector for the animation type 'Item by item'.
	 *
	 * @since 1.19.3
	 */
	public function get_separate_animation_selector() {
		return '.cmsmasters-widget-title__heading';
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.19.3
	 */
	public function get_text_animation_class() {
		return array( 'cmsmasters-widget-title__heading' => 'sequental, random, line, word, char' );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.2.3 Fix for line-clamp css property.
	 * @since 1.19.3 Added `Entrance Animation` and `Entrance Animation Text` hidden control for entrance animation.
	 */
	protected function register_controls() {
		$this->set_condition_sets();

		$this->start_controls_section(
			'section_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'entrance_animation',
			array(
				'label' => __( 'Entrance Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'entrance_animation_text',
			array(
				'label' => __( 'Entrance Animation Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'Enter your title', 'cmsmasters-elementor' ),
				'default' => __( 'Add Your Heading Text Here', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => '',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => __( 'HTML Tag', 'cmsmasters-elementor' ),
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
					'justify' => array(
						'title' => __( 'Justified', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-justify',
					),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'line_clamp',
			array(
				'label' => __( 'Truncate Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-line-clamp-',
			)
		);

		$this->add_control(
			'line_clamp_count',
			array(
				'label' => __( 'Number of Lines', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 2,
				'min' => 1,
				'max' => 5,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-color-variation-default .cmsmasters-widget-title__heading, ' .
					'{{WRAPPER}}.cmsmasters-color-variation-gradient .cmsmasters-widget-title__heading span.title-inner-element, ' .
					'{{WRAPPER}}.cmsmasters-color-variation-background-image .cmsmasters-widget-title__heading span.title-inner-element' => '-webkit-line-clamp: {{SIZE}};',
				),
				'condition' => array( 'line_clamp' => 'yes' ),
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color_variation',
			array(
				'label' => __( 'Text Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'default' => array(
						'title' => __( 'Default', 'cmsmasters-elementor' ),
						'icon' => 'eicon-paint-brush',
					),
					'gradient' => array(
						'title' => __( 'Gradient', 'cmsmasters-elementor' ),
						'icon' => 'eicon-barcode',
					),
					'background-image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'icon' => 'eicon-image',
					),
				),
				'default' => 'default',
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-color-variation-',
			)
		);

		$this->register_gradient_controls();

		$this->register_image_controls();

		$this->add_control(
			'title_typography_before_divider',
			array( 'type' => Controls_Manager::DIVIDER )
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-title__heading, ' .
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow_normal',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-title__heading',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'title_box_shadow_normal',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-title__heading',
				'condition' => array( '_background_color!' => '' ),
			)
		);

		$this->add_control(
			'title_link_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading' => '--title-color-hover: {{VALUE}};',
				),
				'condition' => array( 'title_color_variation' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_text_shadow_hover',
				'fields_options' => array(
					'text_shadow' => array(
						'label' => _x( 'Text Shadow Hover', 'Text Shadow Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-title__heading:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'title_box_shadow_hover',
				'fields_options' => array(
					'box_shadow' => array(
						'label' => _x( 'Box Shadow Hover', 'Box Shadow Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-title__heading:hover',
				'condition' => array( '_background_color!' => '' ),
			)
		);

		$this->add_control(
			'title_transition',
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
					'{{WRAPPER}} .cmsmasters-widget-title__heading' => 'transition: all {{SIZE}}s',
				),
				'condition' => array( 'title_color_variation!' => 'background-image' ),
			)
		);

		$this->add_control(
			'title_background_image_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 0.5 ),
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading, ' .
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'transition: all {{SIZE}}s cubic-bezier(0.99, 0.01, 0.01, 0.99)',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'title_color_variation',
							'operator' => '=',
							'value' => 'background-image',
						),
						array(
							'name' => 'title_background_image[url]',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

		$this->add_control(
			'blend_mode',
			array(
				'label' => __( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Normal', 'cmsmasters-elementor' ),
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

		$this->add_control(
			'title_stroke_width',
			array(
				'label' => __( 'Stroke Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => 'px',
					'size' => '',
				),
				'size_units' => array( 'px', 'em' ),
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
				'selectors' => array(
					'{{WRAPPER}}' => '--text-stroke-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_stroke_color_normal',
			array(
				'label' => __( 'Stroke Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--text-stroke-color: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->add_control(
			'title_stroke_color_hover',
			array(
				'label' => __( 'Stroke Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading' => '--text-stroke-color-hover: {{VALUE}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'title_stroke_width[size]',
							'operator' => '>',
							'value' => '0',
						),
					),
				),
			)
		);

		$this->end_controls_section();

		$condition = array(
			'relation' => 'and',
			'terms' => array(
				array(
					'name' => 'title_color_variation',
					'operator' => '!==',
					'value' => 'gradient',
				),
				array(
					'name' => 'line_clamp',
					'operator' => '=',
					'value' => '',
				),
			),
		);

		AnimationModule::register_sections_controls( $this, false, $condition );
	}

	protected function register_gradient_controls() {
		$this->add_control(
			'title_color_normal',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--title-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_color_stop_normal',
			array(
				'label' => __( 'Location', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => 0,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--title-color-stop: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'title_color_variation' => 'gradient' ),
			)
		);

		$this->add_control(
			'title_second_color_normal',
			array(
				'label' => __( 'Second Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'title' => __( 'Background Color', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--title-second-color: {{VALUE}};',
				),
				'condition' => array( 'title_color_variation' => 'gradient' ),
			)
		);

		$this->add_control(
			'title_second_color_stop_normal',
			array(
				'label' => __( 'Location', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--title-second-color-stop: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'title_color_variation' => 'gradient' ),
			)
		);

		$this->add_control(
			'title_gradient_type_normal',
			array(
				'label' => _x( 'Type', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'linear' => _x( 'Linear', 'Background Control', 'cmsmasters-elementor' ),
					'radial' => _x( 'Radial', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => 'linear',
				'prefix_class' => 'cmsmasters-color-gradient-',
				'render_type' => 'template',
				'condition' => array( 'title_color_variation' => 'gradient' ),
			)
		);

		$this->add_control(
			'title_gradient_angle_normal',
			array(
				'label' => _x( 'Angle', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default' => array(
					'unit' => 'deg',
					'size' => 90,
				),
				'range' => array(
					'deg' => array( 'step' => 10 ),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--title-gradient-angle: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'title_color_variation' => 'gradient',
					'title_gradient_type_normal' => 'linear',
				),
			)
		);

		$this->add_control(
			'title_gradient_position_normal',
			array(
				'label' => _x( 'Position', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'center center' => _x( 'Center Center', 'Background Control', 'cmsmasters-elementor' ),
					'center left' => _x( 'Center Left', 'Background Control', 'cmsmasters-elementor' ),
					'center right' => _x( 'Center Right', 'Background Control', 'cmsmasters-elementor' ),
					'top center' => _x( 'Top Center', 'Background Control', 'cmsmasters-elementor' ),
					'top left' => _x( 'Top Left', 'Background Control', 'cmsmasters-elementor' ),
					'top right' => _x( 'Top Right', 'Background Control', 'cmsmasters-elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'cmsmasters-elementor' ),
					'bottom left' => _x( 'Bottom Left', 'Background Control', 'cmsmasters-elementor' ),
					'bottom right' => _x( 'Bottom Right', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => 'center center',
				'selectors' => array(
					'{{WRAPPER}}' => '--title-gradient-radial: at {{VALUE}};',
				),
				'condition' => array(
					'title_color_variation' => 'gradient',
					'title_gradient_type_normal' => 'radial',
				),
			)
		);

		$this->add_control(
			'title_gradient_animation',
			array(
				'label' => _x( 'Animate Gradient', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => false,
				'return_value' => 'enable',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-color-variation-gradient-animation-',
				'condition' => array(
					'title_color_variation' => 'gradient',
				),
			)
		);

		$this->add_control(
			'title_gradient_animation_type',
			array(
				'label' => _x( 'Animation Type', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'cmsmasters_text_gradient_animation_shift' => __( 'Shift', 'cmsmasters-elementor' ),
					'cmsmasters_text_gradient_animation_pulse' => __( 'Pulse', 'cmsmasters-elementor' ),
					'cmsmasters_text_gradient_animation_wave' => __( 'Wave', 'cmsmasters-elementor' ),
				),
				'default' => 'cmsmasters_text_gradient_animation_shift',
				'selectors' => array(
					'{{WRAPPER}}' => '--title-gradient-animation-type: {{VALUE}};',
				),
				'condition' => array(
					'title_color_variation' => 'gradient',
					'title_gradient_animation' => 'enable',
				),
			)
		);

		$this->add_control(
			'title_gradient_animation_duration',
			array(
				'label' => _x( 'Animation Duration', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 5 ),
				'range' => array(
					'px' => array(
						'max' => 10,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--title-gradient-animation-duration: {{SIZE}}s;',
				),
				'condition' => array(
					'title_color_variation' => 'gradient',
					'title_gradient_animation' => 'enable',
				),
			)
		);
	}

	/**
	 * Register image controls.
	 *
	 * @since 1.0.0
	 * @since 1.3.3 Fixed on breakpoints.
	 */
	protected function register_image_controls() {
		$this->add_responsive_control(
			'title_background_image',
			array(
				'label' => _x( 'Image', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'title' => _x( 'Background Image', 'Background Control', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-image: url("{{URL}}");',
					'{{WRAPPER}}.cmsmasters-color-variation-background-image' => '--background-image-url: url("{{URL}}");',
				),
				'render_type' => 'template',
				'condition' => array( 'title_color_variation' => 'background-image' ),
			)
		);

		$bg_img_condition = array(
			'title_color_variation' => 'background-image',
			'title_background_image[url]!' => '',
		);

		$this->add_control(
			'title_background_image_hover',
			array(
				'label' => __( 'Image Visibility:', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'no' => array(
						'title' => __( 'Always', 'cmsmasters-elementor' ),
						'description' => __( 'Always show background image.', 'cmsmasters-elementor' ),
					),
					'yes' => array(
						'title' => __( 'On Hover', 'cmsmasters-elementor' ),
						'description' => __( 'Show background image only on text hover.', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'no',
				'prefix_class' => 'cmsmasters-bg-image-hover-',
				'condition' => $bg_img_condition,
			)
		);

		$bg_img_condition_hover = $bg_img_condition;

		$bg_img_condition_hover['title_background_image_hover'] = 'yes';

		$this->add_responsive_control(
			'title_background_image_hover_position',
			array(
				'label' => __( 'Hover Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'top -40em left 0' => __( 'Top', 'cmsmasters-elementor' ),
					'top -40em right -40em' => __( 'Top Right', 'cmsmasters-elementor' ),
					'top 0 right -40em' => __( 'Right', 'cmsmasters-elementor' ),
					'bottom -40em right -40em' => __( 'Bottom Right', 'cmsmasters-elementor' ),
					'bottom -40em left 0' => __( 'Bottom', 'cmsmasters-elementor' ),
					'bottom -40em left -40em' => __( 'Bottom Left', 'cmsmasters-elementor' ),
					'top 0 left -40em' => __( 'Left', 'cmsmasters-elementor' ),
					'top -40em left -40em' => __( 'Top Left', 'cmsmasters-elementor' ),
				),
				'default' => 'top -40em left 0',
				'prefix_class' => 'cmsmasters-bg-image-position-',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-bg-image-hover-yes .cmsmasters-widget-title__heading span.title-inner-element' => 'background: url("{{title_background_image.URL}}") no-repeat {{VALUE}}, {{title_color_normal.VALUE}} center;',
					'{{WRAPPER}}.cmsmasters-bg-image-hover-yes .cmsmasters-widget-title__heading:hover span.title-inner-element' => 'background-position: center;',
					'{{WRAPPER}}.cmsmasters-color-variation-background-image' => '--background-position: {{VALUE}};',
				),
				'condition' => $bg_img_condition_hover,
			)
		);

		$bg_img_condition_not_hover = $bg_img_condition;

		$bg_img_condition_not_hover['title_background_image_hover'] = 'no';

		$this->add_responsive_control(
			'title_background_image_position',
			array(
				'label' => _x( 'Position', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
					'top left' => _x( 'Top Left', 'Background Control', 'cmsmasters-elementor' ),
					'top center' => _x( 'Top Center', 'Background Control', 'cmsmasters-elementor' ),
					'top right' => _x( 'Top Right', 'Background Control', 'cmsmasters-elementor' ),
					'center left' => _x( 'Center Left', 'Background Control', 'cmsmasters-elementor' ),
					'center center' => _x( 'Center Center', 'Background Control', 'cmsmasters-elementor' ),
					'center right' => _x( 'Center Right', 'Background Control', 'cmsmasters-elementor' ),
					'bottom left' => _x( 'Bottom Left', 'Background Control', 'cmsmasters-elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'cmsmasters-elementor' ),
					'bottom right' => _x( 'Bottom Right', 'Background Control', 'cmsmasters-elementor' ),
					'initial' => _x( 'Custom', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-position: {{VALUE}};',
				),
				'condition' => $bg_img_condition_not_hover,
			)
		);

		$bg_img_condition_not_hover_initial = $bg_img_condition_not_hover;

		$bg_img_condition_not_hover_initial['title_background_image_position'] = 'initial';

		$this->add_responsive_control(
			'title_background_image_position_x',
			array(
				'label' => _x( 'X Position', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vw' ),
				'default' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'range' => array(
					'px' => array(
						'min' => -800,
						'max' => 800,
					),
					'em' => array(
						'min' => -100,
						'max' => 100,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'vw' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-position: {{SIZE}}{{UNIT}} {{title_background_image_position_y.SIZE}}{{title_background_image_position_y.UNIT}}',
				),
				'required' => true,
				'device_args' => CmsmastersUtils::get_devices_args( array(
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-position: {{SIZE}}{{UNIT}} {{title_background_image_position_y_{{cmsmasters_device}}.SIZE}}{{title_background_image_position_y_{{cmsmasters_device}}.UNIT}}',
					),
				) ),
				'condition' => $bg_img_condition_not_hover_initial,
			)
		);

		$this->add_responsive_control(
			'title_background_image_position_y',
			array(
				'label' => _x( 'Y Position', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vh' ),
				'default' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'tablet_default' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'mobile_default' => array(
					'unit' => 'px',
					'size' => 0,
				),
				'range' => array(
					'px' => array(
						'min' => -800,
						'max' => 800,
					),
					'em' => array(
						'min' => -100,
						'max' => 100,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'vh' => array(
						'min' => -100,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-position: {{title_background_image_position_x.SIZE}}{{title_background_image_position_x.UNIT}} {{SIZE}}{{UNIT}}',
				),
				'required' => true,
				'device_args' => CmsmastersUtils::get_devices_args( array(
					'selectors' => array(
						'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-position: {{title_background_image_position_x_{{cmsmasters_device}}.SIZE}}{{title_background_image_position_x_{{cmsmasters_device}}.UNIT}} {{SIZE}}{{UNIT}}',
					),
				) ),
				'condition' => $bg_img_condition_not_hover_initial,
			)
		);

		$this->add_control(
			'title_background_image_attachment',
			array(
				'label' => _x( 'Attachment', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
					'scroll' => _x( 'Scroll', 'Background Control', 'cmsmasters-elementor' ),
					'fixed' => _x( 'Fixed', 'Background Control', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'selectors' => array(
					'(desktop+){{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-attachment: {{VALUE}};',
				),
				'condition' => $bg_img_condition_not_hover,
			)
		);

		$bg_img_condition_not_hover_fixed = $bg_img_condition_not_hover;

		$bg_img_condition_not_hover_fixed['title_background_image_attachment'] = 'fixed';

		$this->add_control(
			'title_background_image_attachment_alert',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-control-field-description',
				'raw' => __( 'Note: Attachment Fixed works only on desktop.', 'cmsmasters-elementor' ),
				'separator' => 'none',
				'condition' => $bg_img_condition_not_hover_fixed,
			)
		);

		$this->add_responsive_control(
			'title_background_image_repeat',
			array(
				'label' => _x( 'Repeat', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
					'no-repeat' => _x( 'No-repeat', 'Background Control', 'cmsmasters-elementor' ),
					'repeat' => _x( 'Repeat', 'Background Control', 'cmsmasters-elementor' ),
					'repeat-x' => _x( 'Repeat-x', 'Background Control', 'cmsmasters-elementor' ),
					'repeat-y' => _x( 'Repeat-y', 'Background Control', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-repeat: {{VALUE}};',
				),
				'condition' => $bg_img_condition_not_hover,
			)
		);

		$this->add_responsive_control(
			'title_background_image_size',
			array(
				'label' => _x( 'Size', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					'' => _x( 'Default', 'Background Control', 'cmsmasters-elementor' ),
					'auto' => _x( 'Auto', 'Background Control', 'cmsmasters-elementor' ),
					'cover' => _x( 'Cover', 'Background Control', 'cmsmasters-elementor' ),
					'contain' => _x( 'Contain', 'Background Control', 'cmsmasters-elementor' ),
					'initial' => _x( 'Custom', 'Background Control', 'cmsmasters-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-size: {{VALUE}};',
				),
				'condition' => $bg_img_condition_not_hover,
			)
		);

		$this->add_responsive_control(
			'title_background_image_bg_width',
			array(
				'label' => _x( 'Width', 'Background Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%', 'vw' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'vw' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'required' => true,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-title__heading span.title-inner-element' => 'background-size: {{SIZE}}{{UNIT}} auto',

				),
				'condition' => $bg_img_condition_not_hover_initial,
			)
		);
	}

	/**
	 * Set condition sets.
	 *
	 * Declare extendable widget controls condition sets.
	 *
	 * @since 1.0.0
	 */
	protected function set_condition_sets() {
		$this->add_conditions_set( 'link_visible', array( 'link[url]!' => '' ) );
		$this->add_conditions_set( 'link_hidden', array( 'link[url]' => '' ) );

		$this->add_conditions_set( 'link_visible_term', array(
			'name' => 'link[url]',
			'operator' => '!==',
			'value' => '',
		) );
		$this->add_conditions_set( 'link_hidden_term', array(
			'name' => 'link[url]',
			'value' => '',
		) );
	}

	/**
	 * Stop inline editing.
	 *
	 * Whether to enable inline editing of the widget or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether to enable inline editing of the widget or not.
	 */
	public function stop_editing() {
		return false;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$title = $this->get_title_render_text();

		if ( empty( $title ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();

		if ( 'default' !== $settings['title_color_variation'] && $this->stop_editing() ) {
			$title = sprintf(
				'<span %2$s>%1$s</span>',
				$title,
				'class="title-inner-element"'
			);
		}

		if ( ! $this->link_active ) {
			if ( is_array( $settings['link'] ) ) {
				if ( ! empty( $settings['link']['url'] ) ) {
					$this->add_link_attributes( 'link', $settings['link'] );

					$this->link_active = true;
				}
			} elseif ( ! empty( $settings['link'] ) ) {
				$this->add_render_attribute( 'link', 'href', $settings['link'] );

				$this->link_active = true;
			}
		}

		$is_animation = 'gradient' !== $settings['title_color_variation'] && 'none' !== $settings['pointer'];

		$animation_class = ( $is_animation ) ? AnimationModule::get_animation_class() : '';

		$animation_class_attr = ( $is_animation ) ? 'class="' . $animation_class . '"' : '';

		if ( $this->link_active ) {
			$get_inline_link = $this->add_render_attribute( 'title', 'class', array(
				$animation_class,
				'title-inner-element',
			) );

			if ( ! $this->stop_editing() ) {
				$this->add_inline_editing_attributes( 'title' );

				if ( 'default' === $settings['title_color_variation'] ) {
					$this->remove_render_attribute( 'title', 'class', 'title-inner-element' );

					$get_inline_link = $this->get_render_attribute_string( 'title' );

					$title = sprintf(
						'<a %1$s %2$s>%3$s</a>',
						$this->get_render_attribute_string( 'link' ),
						$get_inline_link,
						$title
					);

					$this->remove_render_attribute( 'title', 'class', array( $animation_class ) );
				} else {
					$this->remove_render_attribute( 'title', 'class', array( $animation_class ) );

					$get_inline_link = $this->get_render_attribute_string( 'title' );

					$title = sprintf(
						'<span %2$s>%1$s</span>',
						$title,
						$get_inline_link
					);

					$title = sprintf( '<a %1$s %2$s>%3$s</a>',
						$this->get_render_attribute_string( 'link' ),
						$animation_class_attr,
						$title
					);

					$this->remove_render_attribute( 'title', 'class', 'title-inner-element' );
				}
			} else {
				$title = sprintf( '<a %1$s %2$s>%3$s</a>',
					$this->get_render_attribute_string( 'link' ),
					$animation_class_attr,
					$title
				);
			}
		} elseif ( 'default' !== $settings['title_color_variation'] ) {
			$this->add_render_attribute( 'title', 'class', 'title-inner-element' );

			if ( ! $this->stop_editing() ) {
				$this->add_inline_editing_attributes( 'title' );
			}

			$title = sprintf(
				'<span %2$s>%1$s</span>',
				$title,
				$this->get_render_attribute_string( 'title' )
			);

			$this->remove_render_attribute( 'title', 'class', 'title-inner-element' );

			if ( ! $this->stop_editing() ) {
				$this->remove_render_attribute( 'title', 'class', 'elementor-inline-editing' );
				$this->remove_render_attribute( 'title', 'data-elementor-setting-key' );
			}
		}

		if ( 'yes' === $settings['line_clamp'] ) {
			$this->add_render_attribute( 'title', 'title', wp_strip_all_tags( $title ) );
		}

		$this->add_render_attribute( 'title', 'class', array( 'cmsmasters-widget-title__heading' ) );

		if ( ! empty( $settings['title_hover_animation'] ) ) {
			$this->add_render_attribute( 'title', 'class', 'elementor-animation-' . esc_attr( $settings['title_hover_animation'] ) );
		}

		if ( ! $this->link_active ) {
			$this->add_render_attribute( 'title', 'class', array( $animation_class ) );
		}

		if ( ! $this->stop_editing() ) {
			if ( ! $this->link_active ) {
				$this->add_inline_editing_attributes( 'title' );

				if ( 'default' !== $settings['title_color_variation'] ) {
					$this->remove_render_attribute( 'title', 'class', 'elementor-inline-editing' );
					$this->remove_render_attribute( 'title', 'data-elementor-setting-key' );
				}
			} else {
				$this->remove_render_attribute( 'title', 'class', 'elementor-inline-editing' );
				$this->remove_render_attribute( 'title', 'data-elementor-setting-key' );
			}
		} elseif ( $this->stop_editing() ) {
			if ( $this->link_active ) {
				$this->remove_render_attribute( 'title', 'class', array( $animation_class, 'title-inner-element' ) );
			}
		}

		$title_html = sprintf(
			'<%1$s %2$s>%3$s</%1$s>',
			Utils::validate_html_tag( $settings['title_tag'] ),
			$this->get_render_attribute_string( 'title' ),
			wp_kses_post( $title )
		);

		Utils::print_unescaped_internal_string( $title_html ); // XSS ok.
	}

	/**
	 * Get title text.
	 *
	 * Getting method that renders title text.
	 *
	 * @since 1.0.0
	 *
	 * @return string Title text.
	 */
	protected function get_title_render_text() {
		return $this->get_title_text();
	}

	/**
	 * Get title text.
	 *
	 * Getting title text from settings.
	 *
	 * @since 1.0.0
	 *
	 * @return string Title text.
	 */
	protected function get_title_text() {
		return $this->get_settings_for_display( 'title' );
	}

	/**
	 * Render widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to
	 * generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		?>
		<#
		var title = ( 'undefined' !== typeof modifiedTitle ) ? modifiedTitle : settings.title;

		if ( '' === title ) {
			return false;
		}

		if ( 'undefined' === typeof titleUrl ) {
			var titleUrl = ( 'object' === typeof settings.link ) ? settings.link.url : settings.link;
		}

		var animationClass = ( 'gradient' !== settings.title_color_variation && 'none' !== settings.pointer )
			? '<?php echo AnimationModule::get_animation_class(); ?>' : '';

		var innerClass = 'title-inner-element';

		if ( '' !== titleUrl ) {
			if ( 'undefined' === typeof stopEditing ) {
				view.addInlineEditingAttributes( 'title' );

				var titleAttr = obj.view.renderAttributes.title;

				if ( 'default' === settings.title_color_variation ) {
					view.addRenderAttribute( 'title', 'class', animationClass );

					title = '<a href="' + titleUrl + '"' + view.getRenderAttributeString( 'title' ) + '>' + title + '</a>';

					titleAttr.class.splice( titleAttr.class.indexOf( animationClass ), 1 );
				} else {
					view.addRenderAttribute( 'title', 'class', innerClass );

					var animationClassAttr = ( '' !== animationClass ) ? 'class="' + animationClass + '"' : '';

					title = '<a href="' + titleUrl + '" ' + animationClassAttr + '>' +
						'<span ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</span>' +
					'</a>';

					titleAttr.class.splice( titleAttr.class.indexOf( innerClass ), 1 );
				}
			} else {
				var animationAttr = ( 'gradient' !== settings.title_color_variation ) ? ' class="' + animationClass + '"' : '';

				if ( 'default' !== settings.title_color_variation ) {
					title = '<span class="title-inner-element">' + title + '</span>';
				}

				title = '<a href="' + titleUrl + '"' + animationAttr + '>' + title + '</a>';
			}
		}

		if ( 'yes' === settings.line_clamp ) {
			view.addRenderAttribute( 'title', 'title', title.replace( /<\/?[^>]+>/gi, '' ) );
		}

		view.addRenderAttribute( 'title', 'class', 'cmsmasters-widget-title__heading' );

		if ( settings.title_hover_animation ) {
			view.addRenderAttribute( 'title', 'class', 'elementor-animation-' + settings.title_hover_animation );
		}

		if ( 'undefined' === typeof stopEditing ) {
			if ( '' === titleUrl ) {
				view.addInlineEditingAttributes( 'title' );

				if ( 'default' !== settings.title_color_variation ) {
					var titleAttr = obj.view.renderAttributes.title;

					view.addRenderAttribute( 'title', 'class', 'title-inner-element' );

					titleAttr.class.splice( titleAttr.class.indexOf( 'cmsmasters-widget-title__heading' ), 1 );

					title = '<span ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</span>';

					titleAttr.class.splice( titleAttr.class.indexOf( 'elementor-inline-editing' ), 1 );

					delete titleAttr[ 'data-elementor-setting-key' ];

					view.addRenderAttribute( 'title', 'class', 'cmsmasters-widget-title__heading' );
				}

				var titleAttr = obj.view.renderAttributes.title;

				titleAttr.class.splice( titleAttr.class.indexOf( innerClass ), 1 );

				view.addRenderAttribute( 'title', 'class', animationClass );
			} else {
				var titleAttr = obj.view.renderAttributes.title;

				titleAttr.class.splice( titleAttr.class.indexOf( 'elementor-inline-editing' ), 1 );

				delete titleAttr[ 'data-elementor-setting-key' ];
			}
		} else {
			if ( '' === titleUrl ) {
				if ( 'default' !== settings.title_color_variation ) {
					title = '<span class="title-inner-element">' + title + '</span>';

					var titleAttr = obj.view.renderAttributes.title;
				}

				view.addRenderAttribute( 'title', 'class', animationClass );
			}
		}

		#>
		<{{{ settings.title_tag }}} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ title }}}</{{{ settings.title_tag }}}>
		<?php
	}

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
				'field' => 'title',
				'type' => esc_html__( 'Heading Text', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			'link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Heading Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
		);
	}
}
