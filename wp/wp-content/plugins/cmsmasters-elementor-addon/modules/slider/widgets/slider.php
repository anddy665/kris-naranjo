<?php
namespace CmsmastersElementor\Modules\Slider\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Slider\Classes\Slider as SliderUtils;
use CmsmastersElementor\Modules\Settings\Kit_Globals;

use Elementor\Controls_Manager;
use Elementor\Embed;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon `slider` widget.
 *
 * Addon widget that displays the images/content slides.
 *
 * @since 1.0.0
 */
class Slider extends Base_Widget {

	protected $slider;

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
		return __( 'Slider', 'cmsmasters-elementor' );
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
		return 'cmsicon-slider';
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
			'media',
			'slider',
			'slides',
		);
	}

	/**
	 * Get scripts dependencies.
	 *
	 * Retrieve the list of scripts dependencies the widget requires.
	 *
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array( 'swiper' );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.15.3 Added dependency of connecting swiper styles for widgets with swiper slider after elementor 3.26 version.
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'e-swiper',
			'widget-cmsmasters-slider',
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
	 *
	 * Initializing the Addon `slider` widget class.
	 *
	 * @since 1.0.0
	 *
	 * @throws \Exception If arguments are missing when initializing a
	 * full widget instance.
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		$this->slider = new SliderUtils( $this );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 * @since 1.16.4 Removed slider height type 'auto'.
	 */
	protected function register_controls() {
		$repeater = new Repeater();

		$this->start_controls_section(
			'section_type',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$repeater->start_controls_tabs( 'slide_style' );

		$repeater->start_controls_tab(
			'slide_tab_main',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'slide_bg',
				'label' => __( 'Background', 'cmsmasters-elementor' ),
				'types' => array(
					'classic',
					'gradient',
					'video',
				),
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
				),
				'exclude' => array(
					'attachment',
					'play_on_mobile',
				),
				'frontend_available' => true,
				'render_type' => 'template',
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .elementor-widget-cmsmasters-slider__bg',
			)
		);

		$repeater->add_control(
			'slide_bg_overlay_color',
			array(
				'label' => __( 'Background Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-widget-cmsmasters-slider__bg-overlay' => 'background-color: {{VALUE}}',
				),
			)
		);

		$repeater->add_control(
			'slide_bg_overlay_blend_mode',
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
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-widget-cmsmasters-slider__bg-overlay' => 'mix-blend-mode: {{VALUE}}',
				),
				'condition' => array(
					'slide_bg_overlay_color!' => '',
				),
			)
		);

		$repeater->add_control(
			'slide_bg_ken_burns',
			array(
				'label' => __( 'Ken Burns Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'description' => __( 'Works only with one slide per view', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'ken_burns_direction',
			array(
				'label' => __( 'Zoom Direction', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'in' => array(
						'title' => __( 'In', 'cmsmasters-elementor' ),
					),
					'out' => array(
						'title' => __( 'Out', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'in',
				'render_type' => 'template',
				'condition' => array( 'slide_bg_ken_burns!' => '' ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'slide_tab_text',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'slide_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$repeater->add_control(
			'slide_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array( 'slide_content!' => '' ),
			)
		);

		$repeater->add_control(
			'slide_description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array( 'slide_content!' => '' ),
			)
		);

		$repeater->add_control(
			'slide_button_text',
			array(
				'label' => __( 'Button Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Button Text', 'cmsmasters-elementor' ),
				'condition' => array( 'slide_content!' => '' ),
			)
		);

		$repeater->add_control(
			'slide_button_url',
			array(
				'label' => __( 'Button Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array( 'slide_content!' => '' ),
			)
		);

		$repeater->add_control(
			'slide_type_link',
			array(
				'label' => __( 'Type of link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'button' => __( 'Link on button', 'cmsmasters-elementor' ),
					'slide' => __( 'Link on all slide', 'cmsmasters-elementor' ),
				),
				'default' => 'slide',
				'condition' => array(
					'slide_button_url[url]!' => '',
					'slide_content!' => '',
				),
			)
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'slide_tab_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'custom_position',
			array(
				'label' => __( 'Custom', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$repeater->add_responsive_control(
			'slide_text_alignment',
			array(
				'label' => __( 'Text Alignment', 'cmsmasters-elementor' ),
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
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-widget-cmsmasters-slider__content-inner' => 'text-align: {{VALUE}}',
				),
				'condition' => array( 'custom_position!' => '' ),
			)
		);

		$repeater->add_control(
			'slide_position_vertical',
			array(
				'label' => __( 'Content Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'flex-end',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-widget-cmsmasters-slider__content-container' => 'align-items: {{VALUE}}',
				),
				'condition' => array( 'custom_position!' => '' ),
			)
		);

		$repeater->add_control(
			'slide_position_horizontal',
			array(
				'label' => __( 'Content Horizontal Alignment', 'cmsmasters-elementor' ),
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
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-widget-cmsmasters-slider__content-container' => 'justify-content: {{VALUE}}',
				),
				'condition' => array( 'custom_position!' => '' ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'slides',
			array(
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ slide_title }}}',
				'default' => array(
					array(
						'slide_title' => 'Slide 1',
						'slide_bg_color' => '#2647B1',
						'slide_text_color' => '#ffffff',
					),
					array(
						'slide_title' => 'Slide 2',
						'slide_bg_color' => '#EF4040',
						'slide_text_color' => '#ffffff',
					),
					array(
						'slide_title' => 'Slide 3',
						'slide_bg_color' => '#5CC74E',
						'slide_text_color' => '#ffffff',
					),
				),
				'frontend_available' => true,
			)
		);

		$this->slider->register_controls_content_per_view();

		$this->update_responsive_control(
			'slider_per_view',
			array(
				'default' => '1',
				'tablet_default' => '1',
			)
		);

		$this->end_controls_section();

		$this->slider->register_section_content();
		$this->slider->register_sections_style();

		$this->update_control(
			'slider_effect',
			array(
				'options' => array(
					'slide' => __( 'Slide', 'cmsmasters-elementor' ),
					'fade' => __( 'Fade', 'cmsmasters-elementor' ),
					'cube' => __( 'Cube', 'cmsmasters-elementor' ),
					'coverflow' => __( 'Coverflow', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->start_injection( array( 'of' => 'slider_free_mode' ) );

		$this->add_control(
			'content_animation',
			array(
				'label' => __( 'Content Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'fadeInDown' => __( 'Down', 'cmsmasters-elementor' ),
					'fadeInUp' => __( 'Up', 'cmsmasters-elementor' ),
					'fadeInRight' => __( 'Right', 'cmsmasters-elementor' ),
					'fadeInLeft' => __( 'Left', 'cmsmasters-elementor' ),
					'zoomIn' => __( 'Zoom', 'cmsmasters-elementor' ),
					'rollIn' => __( 'Roll', 'cmsmasters-elementor' ),
				),
				'default' => 'fadeInUp',
			)
		);

		$this->end_injection();

		$this->start_injection( array( 'of' => 'slider_bd_color' ) );

		$this->add_responsive_control(
			'slider_text_alignment',
			array(
				'label' => __( 'Text Alignment', 'cmsmasters-elementor' ),
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
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-inner' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'slider_position_vertical',
			array(
				'label' => __( 'Content Vertical', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'flex-end',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container' => 'align-items: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'slider_position_horizontal',
			array(
				'label' => __( 'Content Horizontal', 'cmsmasters-elementor' ),
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
				'default' => 'center',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'slider_slide_padding',
			array(
				'label' => __( 'Slide Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_injection();

		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => __( 'Slider Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'slider_title_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'title_shadow',
				'label' => __( 'Text Shadow', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-title',
			)
		);

		$this->add_responsive_control(
			'title_bottom',
			array(
				'label' => __( 'Bottom Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'slider_description_heading',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'description_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-description',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-description' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'description_shadow',
				'label' => __( 'Text Shadow', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-description',
			)
		);

		$this->add_responsive_control(
			'description_bottom',
			array(
				'label' => __( 'Bottom Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default' => array(
					'size' => 20,
					'unit' => 'px',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			array(
				'label' => __( 'Slider Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_type',
			array(
				'label' => __( 'Button Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'link' => array(
						'title' => __( 'Link', 'cmsmasters-elementor' ),
					),
					'button' => array(
						'title' => __( 'Button', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'button',
				'toggle' => false,
			)
		);

		$this->add_control(
			'button_width',
			array(
				'label' => __( 'Button Width', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'content' => array(
						'title' => __( 'Content', 'cmsmasters-elementor' ),
					),
					'justify' => array(
						'title' => __( 'Justify', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'content',
				'toggle' => false,
				'condition' => array( 'button_type' => 'button' ),
			)
		);

		$this->add_control(
			'button_style',
			array(
				'label' => __( 'Button Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'small' => __( 'Small', 'cmsmasters-elementor' ),
					'medium' => __( 'Medium', 'cmsmasters-elementor' ),
					'large' => __( 'Large', 'cmsmasters-elementor' ),
				),
				'default' => 'medium',
				'condition' => array( 'button_type' => 'button' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_PRIMARY ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button',
			)
		);

		$this->start_controls_tabs( 'slider_button_style' );

		$this->start_controls_tab(
			'slider_button_style_normal',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'button_type' => 'button' ),
			)
		);

		$this->add_control(
			'button_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'button_border_border!' => '',
					'button_type' => 'button',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slider_button_style_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} div.elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button:hover,
					{{WRAPPER}} a.elementor-widget-cmsmasters-slider__content-container:hover .elementor-widget-cmsmasters-slider__content-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} div.elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button:hover,
					{{WRAPPER}} a.elementor-widget-cmsmasters-slider__content-container:hover .elementor-widget-cmsmasters-slider__content-button' => 'background-color: {{VALUE}};',
				),
				'condition' => array( 'button_type' => 'button' ),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} div.elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button:hover,
					{{WRAPPER}} a.elementor-widget-cmsmasters-slider__content-container:hover .elementor-widget-cmsmasters-slider__content-button' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'button_border_border!' => '',
					'button_type' => 'button',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'button_border',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button',
				'separator' => 'before',
				'condition' => array( 'button_type' => 'button' ),
			)
		);

		$this->add_responsive_control(
			'button_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'button_type' => 'button' ),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-slider__content-container .elementor-widget-cmsmasters-slider__content-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'button_type' => 'button' ),
			)
		);

		$this->end_controls_section();

		$this->update_control( 'slider_height_type', array(
			'type' => Controls_Manager::HIDDEN,
			'options' => array(
				'custom' => __( 'Custom', 'cmsmasters-elementor' ),
			),
			'default' => 'custom',
		) );
	}

	public function get_embed_options( $slide, $video_properties ) {
		$embed_options = array();

		if ( 'youtube' === $video_properties['provider'] ) {
			$embed_options['privacy'] = $slide['slide_bg_video_start'];
		} elseif ( 'vimeo' === $video_properties['provider'] ) {
			if ( $slide['slide_bg_video_start'] ) {
				$embed_options['start'] = $slide['slide_bg_video_start'];
			} else {
				$embed_options['start'] = '0';
			}
		}

		return $embed_options;
	}

	public function get_embed_params( $slide, $video_properties ) {
		$params = array();
		$params_dictionary = array();

		if ( 'youtube' === $video_properties['provider'] ) {
			$loop = ( $slide['slide_bg_play_once'] ) ? '0' : '1';

			$params_dictionary = array(
				'autoplay' => '1',
				'cc_load_policy' => '0',
				'controls' => '0',
				'disablekb' => '1',
				'iv_load_policy' => '3',
				'loop' => $loop,
				'modestbranding' => '1',
				'mute' => '1',
				'rel' => '0',
				'showinfo' => '0',
			);

			if ( $slide['slide_bg_video_start'] ) {
				$params_dictionary['start'] = $slide['slide_bg_video_start'];
			}

			if ( $slide['slide_bg_video_end'] ) {
				$params_dictionary['end'] = $slide['slide_bg_video_end'];
			}

			if ( '1' === $loop ) {
				$params_dictionary['playlist'] = $video_properties['video_id'];
			}
		} else {
			$params_dictionary = array(
				'loop' => '1',
				'muted' => '1',
				'autoplay' => '1',
				'background' => '1',
				'transparent' => '0',
				'autopause' => '0',
			);
		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$params[ $key ] = $param_name;
		}

		return $params;
	}

	public function render_slides() {
		$settings = $this->get_settings_for_display();

		foreach ( $settings['slides'] as $index => $slide ) {
			$container_tag = 'div';
			$container_class = 'cmsmasters-container-type-block';
			$button_style = '';

			if ( isset( $settings['button_type'] ) ) {
				$button_style .= " cmsmasters-slide-button-{$settings['button_type']}";
			}

			if ( isset( $settings['button_width'] ) ) {
				$button_style .= " cmsmasters-slide-button-{$settings['button_width']}";
			}

			if ( isset( $settings['button_style'] ) ) {
				$button_style .= " cmsmasters-slide-button-{$settings['button_style']}";
			}

			if ( ! empty( $slide['slide_button_url']['url'] ) ) {
				if ( $slide['slide_button_url']['url'] ) {
					$this->add_link_attributes( "slide-button-url-{$index}", $slide['slide_button_url'] );
				}

				if ( isset( $slide['slide_type_link'] ) && 'slide' === $slide['slide_type_link'] ) {
					$container_tag = 'a';
					$container_class = 'cmsmasters-container-type-link';
				}
			}

			if ( $slide['slide_bg_ken_burns'] && '1' === $settings['slider_per_view'] ) {
				$this->add_render_attribute(
					"slider-bg-{$index}",
					'class',
					array(
						'cmsmasters-ken-burns',
						'cmsmasters-ken-burns-' . esc_attr( $slide['ken_burns_direction'] ),
					)
				);
			}

			$this->slider->render_slide_open();

			$this->add_render_attribute( "slider-bg-{$index}", 'class', 'elementor-widget-cmsmasters-slider__bg' );

			echo '<div class="elementor-widget-cmsmasters-slider__slide-container elementor-repeater-item-' . esc_attr( $slide['_id'] ) . ' ' . esc_attr( $container_class ) . '">' .
				'<div ' . $this->get_render_attribute_string( "slider-bg-{$index}" ) . '>';

			if ( isset( $slide['slide_bg_background'] ) && 'video' === $slide['slide_bg_background'] ) {
				if ( $slide['slide_bg_video_link'] ) {
					$video_properties = Embed::get_video_properties( $slide['slide_bg_video_link'] );

					$this->add_render_attribute( 'background-video-container', 'class', 'elementor-background-video-container' );

					if ( isset( $slide['slide_bg_play_on_mobile'] ) && ! $slide['slide_bg_play_on_mobile'] ) {
						$this->add_render_attribute( 'background-video-container', 'class', 'elementor-hidden-phone' );
					}

					echo '<div ' . $this->get_render_attribute_string( 'background-video-container' ) . '>';
					if ( $video_properties ) {
						$video_url = $slide['slide_bg_video_link'];
						$embed_params = $this->get_embed_params( $slide, $video_properties );
						$embed_options = $this->get_embed_options( $slide, $video_properties );
						$video_html = Embed::get_embed_html( $video_url, $embed_params, $embed_options );

						Utils::print_unescaped_internal_string( $video_html ); // XSS ok.
					} else {
						$video_tag_attributes = 'autoplay muted playsinline';

						if ( ! $slide['slide_bg_play_once'] ) {
							$video_tag_attributes .= 'loop';
						}

						echo '<video class="elementor-background-video-hosted" ' . esc_attr( $video_tag_attributes ) . '></video>';
					}

					echo '</div>';
				}
			}

			echo '</div>';

			echo '<div class="elementor-widget-cmsmasters-slider__bg-overlay"></div>';

			echo '<' . tag_escape( $container_tag ) . ' class="elementor-widget-cmsmasters-slider__content-container" ' . $this->get_render_attribute_string( "slide-button-url-{$index}" ) . '>' .
				'<div class="elementor-widget-cmsmasters-slider__content-inner">';

			if ( $slide['slide_title'] ) {
				echo '<div class="elementor-widget-cmsmasters-slider__content-title">' . esc_html( $slide['slide_title'] ) . '</div>';
			}

			if ( $slide['slide_description'] ) {
				echo '<div class="elementor-widget-cmsmasters-slider__content-description">' . esc_html( $slide['slide_description'] ) . '</div>';
			}

			if ( $slide['slide_button_text'] && 'button' === $slide['slide_type_link'] ) {
				echo '<a class="elementor-widget-cmsmasters-slider__content-button' . esc_attr( $button_style ) . '" ' . $this->get_render_attribute_string( "slide-button-url-{$index}" ) . '>' .
					esc_html( $slide['slide_button_text'] ) .
				'</a>';
			} elseif ( $slide['slide_button_text'] ) {
				echo '<div class="elementor-widget-cmsmasters-slider__content-button' . esc_attr( $button_style ) . '">' . esc_html( $slide['slide_button_text'] ) . '</div>';
			}

				echo '</div>' .
			'</' . tag_escape( $container_tag ) . '>' .
			'</div>';

			$this->slider->render_slide_close();
		}
	}

	/**
	 * Render menu widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<div class="elementor-widget-cmsmasters-slider__wrapper" data-animation=' . esc_attr( $settings['content_animation'] ) . '>';

		$this->slider->render( array( $this, 'render_slides' ) );

		echo '</div>';
	}

	/**
	 * Get fields_in_item config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields in item config.
	 */
	public static function get_wpml_fields_in_item() {
		return array(
			'slides' => array(
				array(
					'field' => 'slide_title',
					'type' => esc_html__( 'Slide Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'slide_description',
					'type' => esc_html__( 'Slide Description', 'cmsmasters-elementor' ),
					'editor_type' => 'AREA',
				),
				array(
					'field' => 'slide_button_text',
					'type' => esc_html__( 'Slide Button Text', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'slide_button_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Button URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
		);
	}
}
