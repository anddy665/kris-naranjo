<?php

namespace CmsmastersElementor\Modules\Gallery\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Gallery extends Base_Widget {

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
		return __( 'Gallery', 'cmsmasters-elementor' );
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
		return 'cmsicon-gallery';
	}

	/**
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array( 'elementor-gallery' );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'elementor-gallery',
			'widget-cmsmasters-gallery',
		);
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
			'gallery',
			'image',
			'img',
			'photo',
		);
	}

	/**
	 * Specifying caching of the widget by default.
	 *
	 * @since 1.14.0
	 * @since 1.15.5 Added `Vertical Spacing` control for gallery item.
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
	 * @since 1.18.0
	 */
	public function get_separate_animation_selector() {
		return '.elementor-widget-cmsmasters-gallery__item';
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.18.0
	 */
	public function get_text_animation_class() {
		return array( 'elementor-widget-cmsmasters-gallery__item' => 'sequental, random' );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.18.0 Added `Entrance Animation` and `Entrance Animation Text` hidden control for entrance animation.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'settings',
			array(
				'label' => __( 'Settings', 'cmsmasters-elementor' ),
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
				'default' => '',
			)
		);

		$this->add_control(
			'gallery_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'single' => __( 'Single', 'cmsmasters-elementor' ),
					'multiple' => __( 'Multiple', 'cmsmasters-elementor' ),
				),
				'default' => 'single',
				'label_block' => false,
			)
		);

		$this->add_control(
			'gallery',
			array(
				'type' => Controls_Manager::GALLERY,
				'dynamic' => array(
					'active' => true,
				),
				'condition' => array(
					'gallery_type' => 'single',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'gallery_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'New Gallery', 'cmsmasters-elementor' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'multiple_gallery',
			array(
				'type' => Controls_Manager::GALLERY,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'link_to_multiple',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'file' => __( 'Lightbox', 'cmsmasters-elementor' ),
					'custom' => __( 'URL', 'cmsmasters-elementor' ),
				),
				'default' => 'file',
				'label_block' => false,
				'frontend_available' => true,
			)
		);

		$repeater->add_control(
			'url_multiple',
			array(
				'label' => __( 'URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'default' => array(
					'url' => '#',
				),
				'dynamic' => array(
					'active' => true,
				),
				'frontend_available' => true,
				'condition' => array(
					'link_to_multiple' => 'custom',
				),
			)
		);

		$this->add_control(
			'galleries',
			array(
				'label' => __( 'Galleries', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ gallery_title }}}',
				'default' => array(
					array(
						'gallery_title' => __( 'New Gallery', 'cmsmasters-elementor' ),
					),
				),
				'condition' => array(
					'gallery_type' => 'multiple',
				),
			)
		);

		$this->add_control(
			'link_to',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'file' => __( 'Lightbox', 'cmsmasters-elementor' ),
					'custom' => __( 'URL', 'cmsmasters-elementor' ),
				),
				'default' => 'file',
				'label_block' => false,
				'frontend_available' => true,
				'condition' => array(
					'gallery_type' => 'single',
				),
			)
		);

		$this->add_control(
			'url',
			array(
				'label' => __( 'URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'default' => array(
					'url' => '#',
				),
				'dynamic' => array(
					'active' => true,
				),
				'frontend_available' => true,
				'condition' => array(
					'gallery_type' => 'single',
					'link_to' => 'custom',
				),
			)
		);

		$this->add_control(
			'gallery_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'justified' => __( 'Justified', 'cmsmasters-elementor' ),
					'grid' => __( 'Grid', 'cmsmasters-elementor' ),
					'masonry' => __( 'Masonry', 'cmsmasters-elementor' ),
				),
				'default' => 'justified',
				'label_block' => false,
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'tablet_default' => 2,
				'mobile_default' => 1,
				'min' => 1,
				'max' => 12,
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array(
					'gallery_layout!' => 'justified',
				),
			)
		);

		$this->add_responsive_control(
			'ideal_row_height',
			array(
				'label' => __( 'Row Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
				),
				'default' => array(
					'size' => 200,
				),
				'tablet_default' => array(
					'size' => 150,
				),
				'mobile_default' => array(
					'size' => 150,
				),
				'required' => true,
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array(
					'gallery_layout' => 'justified',
				),
			)
		);

		$this->add_responsive_control(
			'gap',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 10,
				),
				'tablet_default' => array(
					'size' => 10,
				),
				'mobile_default' => array(
					'size' => 10,
				),
				'required' => true,
				'render_type' => 'none',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'vertical_gap',
			array(
				'label' => __( 'Vertical Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'thumbnail_image',
				'default' => 'full',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label' => __( 'Aspect Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'1:1' => '1:1',
					'3:2' => '3:2',
					'4:3' => '4:3',
					'9:16' => '9:16',
					'16:9' => '16:9',
					'21:9' => '21:9',
				),
				'default' => '3:2',
				'render_type' => 'none',
				'frontend_available' => true,
				'condition' => array(
					'gallery_layout' => 'grid',
				),
			)
		);

		$this->end_controls_section(); // settings

		$this->start_controls_section(
			'section_filter_bar_content',
			array(
				'label' => __( 'Filter Bar', 'cmsmasters-elementor' ),
				'condition' => array(
					'gallery_type' => 'multiple',
				),
			)
		);

		$this->add_control(
			'hide_filter_bar',
			array(
				'label' => __( 'Hide Filter Bar', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'show_all_galleries',
			array(
				'label' => __( '"All" Filter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'hide_filter_bar' => '',
				),
			)
		);

		$this->add_control(
			'show_all_galleries_label',
			array(
				'label' => __( '"All" Filter Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'All', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_all_galleries' => 'yes',
					'hide_filter_bar' => '',
				),
			)
		);

		$this->add_control(
			'animation_text',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => 'None',
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sink' => 'Sink',
					'float' => 'Float',
					'skew' => 'Skew',
					'rotate' => 'Rotate',
				),
				'default' => 'grow',
				'condition' => array(
					'hide_filter_bar' => '',
				),
			)
		);

		$this->end_controls_section(); // settings

		$this->start_controls_section(
			'overlay',
			array(
				'label' => __( 'Additional', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'overlay_content',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'text' => __( 'Text', 'cmsmasters-elementor' ),
				),
				'label_block' => false,
				'default' => 'none',
				'toggle' => false,
			)
		);

		$this->add_control(
			'overlay_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'caption' => __( 'Caption', 'cmsmasters-elementor' ),
					'description' => __( 'Description', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'frontend_available' => true,
				'condition' => array(
					'gallery_type' => 'single',
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'overlay_description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'caption' => __( 'Caption', 'cmsmasters-elementor' ),
					'description' => __( 'Description', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'frontend_available' => true,
				'condition' => array(
					'gallery_type' => 'single',
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'overlay_title_multiple',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'caption' => __( 'Caption', 'cmsmasters-elementor' ),
					'description' => __( 'Description', 'cmsmasters-elementor' ),
					'gallery_name' => __( 'Gallery Name', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'frontend_available' => true,
				'condition' => array(
					'gallery_type' => 'multiple',
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'overlay_description_multiple',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'None', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'caption' => __( 'Caption', 'cmsmasters-elementor' ),
					'description' => __( 'Description', 'cmsmasters-elementor' ),
					'gallery_name' => __( 'Gallery Name', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'frontend_available' => true,
				'condition' => array(
					'gallery_type' => 'multiple',
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-link',
					'library' => 'solid',
				),
				'recommended' => array(
					'fa-solid' => array(
						'link',
						'lightbulb',
						'smile',
					),
				),
				'condition' => array(
					'overlay_content' => 'icon',
				),
			)
		);

		$this->add_control(
			'overlay_background',
			array(
				'label' => __( 'Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'lazyload',
			array(
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Lazy Load', 'cmsmasters-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'separator' => 'before',
			)
		);

		$this->end_controls_section(); // additional

		$this->start_controls_section(
			'image_style',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'image_tabs' );

		$this->start_controls_tab(
			'image_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'image_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'box_shadow_normal',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'image_css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-image',
			)
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->start_controls_tab(
			'image_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'image_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'image_border_radius_hover',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'image_css_filters_hover',
				'selector' => '{{WRAPPER}} .e-gallery-item:hover .e-gallery-image',
			)
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->end_controls_tabs();// overlay_background tabs

		$this->add_control(
			'image_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_hover_animation',
			array(
				'label' => __( 'Hover Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => 'None',
					'grow' => 'Zoom In',
					'shrink-contained' => 'Zoom Out',
					'move-contained-left' => 'Move Left',
					'move-contained-right' => 'Move Right',
					'move-contained-top' => 'Move Up',
					'move-contained-bottom' => 'Move Down',
				),
				'default' => '',
				'separator' => 'before',
				'frontend_available' => true,
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'image_animation_duration',
			array(
				'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 550,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 3000,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .e-gallery-image' => 'transition-duration: {{SIZE}}ms',
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item' => 'transition-duration: {{SIZE}}ms',
				),
			)
		);

		$this->end_controls_section(); // overlay_background

		$this->start_controls_section(
			'overlay_style',
			array(
				'label' => __( 'Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'overlay_background' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'overlay_background_tabs' );

		$this->start_controls_tab(
			'overlay_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'overlay_background',
				'types' => array( 'classic', 'gradient' ),
				'exclude' => array( 'image' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-overlay',
				'fields_options' => array(
					'background' => array(
						'label' => __( 'Overlay', 'cmsmasters-elementor' ),
					),
				),
			)
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->start_controls_tab(
			'overlay_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'overlay_background_hover',
				'types' => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .e-gallery-item:hover .elementor-widget-cmsmasters-gallery__item-overlay',
				'exclude' => array( 'image' ),
				'fields_options' => array(
					'background' => array(
						'default' => 'classic',
					),
					'color' => array(
						'default' => 'rgba(0,0,0,0.5)',
					),
				),
			)
		);

		$this->end_controls_tab(); // overlay_background normal

		$this->end_controls_tabs();// overlay_background tabs

		$this->add_control(
			'image_blend_mode',
			array(
				'label' => __( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-overlay' => 'mix-blend-mode: {{VALUE}}',
				),
				'separator' => 'before',
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'background_overlay_hover_animation',
			array(
				'label' => __( 'Hover Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => array(
					array(
						'label' => __( 'None', 'cmsmasters-elementor' ),
						'options' => array(
							'' => __( 'None', 'cmsmasters-elementor' ),
						),
					),
					array(
						'label' => __( 'Entrance', 'cmsmasters-elementor' ),
						'options' => array(
							'enter-from-right' => 'Slide In Right',
							'enter-from-left' => 'Slide In Left',
							'enter-from-top' => 'Slide In Up',
							'enter-from-bottom' => 'Slide In Down',
							'enter-zoom-in' => 'Zoom In',
							'enter-zoom-out' => 'Zoom Out',
							'fade-in' => 'Fade In',
						),
					),
					array(
						'label' => __( 'Exit', 'cmsmasters-elementor' ),
						'options' => array(
							'exit-to-right' => 'Slide Out Right',
							'exit-to-left' => 'Slide Out Left',
							'exit-to-top' => 'Slide Out Up',
							'exit-to-bottom' => 'Slide Out Down',
							'exit-zoom-in' => 'Zoom In',
							'exit-zoom-out' => 'Zoom Out',
							'fade-out' => 'Fade Out',
						),
					),
				),
				'separator' => 'before',
				'default' => '',
				'frontend_available' => true,
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'background_overlay_animation_duration',
			array(
				'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 800,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 3000,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-overlay' => 'transition-duration: {{SIZE}}ms',
				),
			)
		);

		$this->end_controls_section(); // overlay_background

		$this->start_controls_section(
			'overlay_content_style',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'overlay_content!' => 'none',
				),
			)
		);

		$this->add_control(
			'content_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'prefix_class' => 'cmsmasters-gallery__item-content-',
				'selectors_dictionary' => array(
					'left' => 'flex-start',
					'center' => 'center',
					'right' => 'flex-end',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-content' => 'align-items: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'content_vertical_position',
			array(
				'label' => __( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'middle' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'selectors_dictionary' => array(
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-content' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default' => array(
					'size' => 20,
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-content' => 'padding: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'heading_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_title',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_title_multiple',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-title' => 'color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_title',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_title_multiple',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-title',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_title',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_title_multiple',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description_multiple',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-description' => 'color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description_multiple',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-description',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description_multiple',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'gap_between_description',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-description' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'overlay_content',
									'operator' => '===',
									'value' => 'text',
								),
								array(
									'name' => 'overlay_description_multiple',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'heading_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'overlay_content' => 'icon',
				),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-icon i' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'overlay_content' => 'icon',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-icon i' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'overlay_content' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-icon svg' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'overlay_content' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-icon i' => 'padding: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'overlay_content' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'icon_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-icon i' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'overlay_content' => 'icon',
				),
			)
		);

		$this->add_control(
			'heading_box_title',
			array(
				'label' => __( 'Text Box', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'text_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__text-box' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'text_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__text-box' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_text',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__text-box',
				'separator' => 'before',
				'exclude' => array( 'color' ),
				'condition' => array(
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'text_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__text-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'text_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__text-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'overlay_content' => 'text',
				),
			)
		);

		$this->add_control(
			'content_hover_animation',
			array(
				'label' => __( 'Hover Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => array(
					array(
						'label' => __( 'None', 'cmsmasters-elementor' ),
						'options' => array(
							'' => __( 'None', 'cmsmasters-elementor' ),
						),
					),
					array(
						'label' => __( 'Entrance', 'cmsmasters-elementor' ),
						'options' => array(
							'enter-from-right' => 'Slide In Right',
							'enter-from-left' => 'Slide In Left',
							'enter-from-top' => 'Slide In Up',
							'enter-from-bottom' => 'Slide In Down',
							'enter-zoom-in' => 'Zoom In',
							'enter-zoom-out' => 'Zoom Out',
							'fade-in' => 'Fade In',
						),
					),
					array(
						'label' => __( 'Reaction', 'cmsmasters-elementor' ),
						'options' => array(
							'grow' => 'Grow',
							'shrink' => 'Shrink',
							'move-right' => 'Move Right',
							'move-left' => 'Move Left',
							'move-up' => 'Move Up',
							'move-down' => 'Move Down',
						),
					),
					array(
						'label' => __( 'Exit', 'cmsmasters-elementor' ),
						'options' => array(
							'exit-to-right' => 'Slide Out Right',
							'exit-to-left' => 'Slide Out Left',
							'exit-to-top' => 'Slide Out Up',
							'exit-to-bottom' => 'Slide Out Down',
							'exit-zoom-in' => 'Zoom In',
							'exit-zoom-out' => 'Zoom Out',
							'fade-out' => 'Fade Out',
						),
					),
				),
				'default' => '',
				'separator' => 'before',
				'render_type' => 'ui',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'content_animation_duration',
			array(
				'label' => __( 'Animation Duration', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 800,
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 3000,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-content > div' => 'transition-duration: {{SIZE}}ms',
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__item-content > span' => 'transition-duration: {{SIZE}}ms',
				),
				'condition' => array(
					'content_hover_animation!' => '',
				),
			)
		);

		$this->add_control(
			'box_justified',
			array(
				'label' => __( 'Full Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'none',
				'render_type' => 'ui',
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-gallery__box-justified-',
				'separator' => 'before',
				'condition' => array(
					'overlay_content' => 'text',
				),
			)
		);

		$this->end_controls_section(); // overlay_content

		$this->start_controls_section(
			'filter_bar_style',
			array(
				'label' => __( 'Filter Bar', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'gallery_type' => 'multiple',
				),
			)
		);

		$this->add_control(
			'align_filter_bar_items',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'selectors_dictionary' => array(
					'left' => 'flex-start',
					'right' => 'flex-end',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-container' => 'justify-content: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs( 'filter_bar_colors' );

		$this->start_controls_tab( 'filter_bar_colors_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'galleries_title_color_normal',
			array(
				'label' => __( 'Item Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_title_bg_color_normal',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_title_bd_color_normal',
			array(
				'label' => __( 'Item Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_box_bg_color_normal',
			array(
				'label' => __( 'Container Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-outer' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_box_bd_color_normal',
			array(
				'label' => __( 'Container Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-outer' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'galleries_titles_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__title',
			)
		);

		$this->end_controls_tab();// filter_bar_colors_normal

		$this->start_controls_tab( 'filter_bar_colors_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'galleries_title_color_hover',
			array(
				'label' => __( 'Item Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item:hover,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.elementor-widget-cmsmasters-gallery__bar-item-active,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.highlighted,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item:focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_title_bg_color_hover',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item:hover,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.elementor-widget-cmsmasters-gallery__bar-item-active,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.highlighted,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item:focus' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_title_bd_color_hover',
			array(
				'label' => __( 'Item Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item:hover,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.elementor-widget-cmsmasters-gallery__bar-item-active,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.highlighted,
					{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item:focus' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();// filter_bar_colors_hover

		$this->start_controls_tab( 'filter_bar_colors_active',
			array(
				'label' => __( 'Active', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'galleries_title_color_active',
			array(
				'label' => __( 'Item Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.elementor-widget-cmsmasters-gallery__bar-item-active' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_title_bg_color_active',
			array(
				'label' => __( 'Item Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.elementor-widget-cmsmasters-gallery__bar-item-active' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'galleries_title_bd_color_active',
			array(
				'label' => __( 'Items Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a.elementor-widget-cmsmasters-gallery__bar-item.elementor-widget-cmsmasters-gallery__bar-item-active' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();// filter_bar_colors_active

		$this->end_controls_tabs(); // filter_bar_colors

		$this->add_control(
			'heading_filter_bar_item',
			array(
				'label' => __( 'Filter Bar Item', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_galleries_titles',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__title',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_control(
			'galleries_titles_bdr',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'galleries_titles_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'galleries_titles_space_between',
			array(
				'label' => __( 'Space Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__title' => 'margin: 0 calc( {{SIZE}}{{UNIT}} / 2 ) {{galleries_titles_gap.SIZE}}{{galleries_titles_gap.UNIT}} calc( {{SIZE}}{{UNIT}} / 2 )',
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-container' => 'margin: 0 calc( -{{SIZE}}{{UNIT}} / 2 ) {{galleries_titles_gap.SIZE}}{{galleries_titles_gap.UNIT}} calc( -{{SIZE}}{{UNIT}} / 2 )',
				),
			)
		);

		$this->add_responsive_control(
			'galleries_titles_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-container .elementor-widget-cmsmasters-gallery__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-container' => 'margin-bottom: -{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'heading_filter_bar_box',
			array(
				'label' => __( 'Filter Bar Container', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_galleries_box',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-outer',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_control(
			'galleries_box_bdr',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-outer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'galleries_titles_box_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'galleries_titles_box_margin',
			array(
				'label' => __( 'Margin', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-gallery__titles-outer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section(); // filter_bar_style
	}

	/**
	 * Render widget.
	 *
	 * Outputs the widget HTML code on the frontend.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed rendering of widget items after removing media,
	 * fixed rendering text for "Gallery Multiple".
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$is_multiple = 'multiple' === $settings['gallery_type'] && ! empty( $settings['galleries'] );
		$is_single = 'single' === $settings['gallery_type'] && ! empty( $settings['gallery'] );

		if ( $is_multiple ) {
			$galleries = $this->get_multiple_galleries();
		} elseif ( $is_single ) {
			$galleries = $this->get_single_gallery();
		} else {
			return false;
		}

		$base_class = $this->get_html_wrapper_class();

		$this->add_render_attribute( 'gallery_container', 'class', "{$base_class}__container" );

		if ( 'multiple' === $settings['gallery_type'] ) {
			$has_title = ! empty( $settings['overlay_title_multiple'] );
			$has_description = ! empty( $settings['overlay_description_multiple'] );
		} else {
			$has_title = ! empty( $settings['overlay_title'] );
			$has_description = ! empty( $settings['overlay_description'] );
		}

		$is_overlay_content_icon = 'icon' === $settings['overlay_content'];

		if (
			$has_title ||
			$has_description ||
			$is_overlay_content_icon
		) {
			$this->add_render_attribute( 'gallery_item_content', 'class', "{$base_class}__item-content" );

			if ( $has_title ) {
				$this->add_render_attribute( 'gallery_item_title', 'class', "{$base_class}__item-title" );
			}

			if ( $has_description ) {
				$this->add_render_attribute( 'gallery_item_description', 'class', "{$base_class}__item-description" );
			}

			if ( $is_overlay_content_icon ) {
				$this->add_render_attribute( 'gallery_item_icon', 'class', "{$base_class}__item-icon" );
			}
		}

		$this->add_render_attribute( 'gallery_item_background_overlay', 'class', "{$base_class}__item-overlay" );

		if ( ! empty( $galleries ) ) {
			$has_animation = ! empty( $settings['image_hover_animation'] ) ||
				! empty( $settings['content_hover_animation'] ) ||
				! empty( $settings['background_overlay_hover_animation'] );

			echo '<div ' . $this->get_render_attribute_string( 'gallery_container' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			foreach ( $galleries as $gallery_index => $gallery ) {
				if ( 'single' === $settings['gallery_type'] ) {
					$gallery_settings = $gallery;
				} elseif ( 'multiple' === $settings['gallery_type'] ) {
					$gallery_settings = $gallery['multiple_gallery'];
				}

				foreach ( $gallery_settings as $index => $item ) {
					$attachment = get_post( $item['id'] );

					if ( ! is_null( $attachment ) ) {
						$unique_index = $gallery_index . '_' . $index;
						$attachment_id = $attachment->ID;
						$image_data['alt'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
						$image_data_alt = ( ! empty( $image_data['alt'] ) ? esc_attr( $image_data['alt'] ) : 'Gallery item image' );
						$image_data['permalink'] = get_permalink( $attachment_id );

						$this->add_render_attribute( 'gallery_item_' . $unique_index, array(
							'class' => array(
								'e-gallery-item',
								"{$base_class}__item",
							),
							'alt' => esc_attr( $image_data_alt ),
						) );

						if ( $has_animation ) {
							$this->add_render_attribute( 'gallery_item_' . $unique_index, array( 'class' => "{$base_class}__animated-content" ) );
						}

						if ( $is_multiple ) {
							$this->add_render_attribute( 'gallery_item_' . $unique_index, array( 'data-e-gallery-tags' => $gallery_index ) );
						}

						$image_src = Group_Control_Image_Size::get_attachment_image_src( $item['id'], 'thumbnail_image', $settings );
						$attachment = get_post( $item['id'] );
						$thumbnail_size = $settings['thumbnail_image_size'];

						if ( 'custom' === $settings['thumbnail_image_size'] ) {
							$width = $settings['thumbnail_image_custom_dimension']['width'];
							$height = $settings['thumbnail_image_custom_dimension']['height'];
						} else {
							$width = wp_get_attachment_image_src( $item['id'], $thumbnail_size )['1'];
							$height = wp_get_attachment_image_src( $item['id'], $thumbnail_size )['2'];
						}

						$image_data = array(
							'media' => wp_get_attachment_image_src( $item['id'], 'full' )['0'],
							'src' => $image_src,
							'width' => $width,
							'height' => $height,
						);

						$gallery_item_tag = ! empty( $settings['link_to'] ) ? 'a' : 'div';

						if ( 'multiple' === $settings['gallery_type'] ) {
							$settings_gallery = $gallery;
							$gallery_item_tag = ! empty( $settings_gallery['link_to_multiple'] ) ? 'a' : 'div';
							$link_type = $settings_gallery['link_to_multiple'];
							$custom_url = $settings_gallery['url_multiple'];

						} elseif ( 'single' === $settings['gallery_type'] ) {
							$settings_gallery = $settings;
							$link_type = $settings_gallery['link_to'];
							$custom_url = $settings_gallery['url'];
						}

						if ( 'a' === $gallery_item_tag ) {
							$this->add_render_attribute( 'gallery_item_' . $unique_index, array( 'aria-label' => esc_attr( $image_data_alt ) ) );

							if ( 'file' === $link_type ) {
								$this->add_render_attribute(
									'gallery_item_' . $unique_index,
									array(
										'data-elementor-lightbox-slideshow' => $this->get_id(),
										'href' => esc_url( $image_data['media'] ),
									)
								);
							} else {
								$this->add_link_attributes( 'gallery_item_' . $unique_index, $custom_url );
							}
						}

						$this->add_render_attribute( 'gallery_item_image_' . $unique_index, array( 'alt' => esc_attr( $image_data_alt ) ) );

						$this->add_render_attribute(
							'gallery_item_image_' . $unique_index,
							array(
								'class' => array(
									'e-gallery-image',
									"{$base_class}__item-image",
								),
								'data-thumbnail' => esc_url( $image_data['src'] ),
								'data-width' => $image_data['width'],
								'data-height' => $image_data['height'],
							)
						);

						echo '<' . $gallery_item_tag . ' ' . $this->get_render_attribute_string( 'gallery_item_' . $unique_index ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo '<div ' . $this->get_render_attribute_string( 'gallery_item_image_' . $unique_index ) . '>';  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						if ( 'yes' === $settings['overlay_background'] ) {
							echo '<div ' . $this->get_render_attribute_string( 'gallery_item_background_overlay' ) . ' ></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}

						echo '</div>';

						if ( 'none' !== $settings['overlay_content'] ) {
							echo '<div ' . $this->get_render_attribute_string( 'gallery_item_content' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

							if ( $has_title || $has_description ) {
								$image_data = $this->get_image_data( $attachment );

								echo "<div class=\"{$base_class}__text-box\">"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								if ( $has_title ) {

									if ( 'multiple' === $settings['gallery_type'] ) {

										if ( 'gallery_name' === $settings['overlay_title_multiple'] ) {
											$title = $gallery['gallery_title'];
										} else {
											$title = $image_data[ $settings['overlay_title_multiple'] ];
										}
									} else {
										$title = $image_data[ $settings['overlay_title'] ];
									}

									if ( ! empty( $title ) ) {
										echo '<div ' . $this->get_render_attribute_string( 'gallery_item_title' ) . '>' . esc_html( $title ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
								}

								if ( $has_description ) {

									if ( 'multiple' === $settings['gallery_type'] ) {

										if ( 'gallery_name' === $settings['overlay_description_multiple'] ) {
											$description = $gallery['gallery_title'];
										} else {
											$description = $image_data[ $settings['overlay_description_multiple'] ];
										}
									} else {
										$description = $image_data[ $settings['overlay_description'] ];
									}

									if ( ! empty( $description ) ) {
										echo '<div ' . $this->get_render_attribute_string( 'gallery_item_description' ) . '>' . wp_kses_post( $description ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
								}

								echo '</div>';
							}

							if ( 'icon' === $settings['overlay_content'] ) {
								echo '<span ' . $this->get_render_attribute_string( 'gallery_item_icon' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

									Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );

								echo '</span>';
							}

							echo '</div>';
						}

						echo '</' . $gallery_item_tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
			}

			echo '</div>';
		}
	}

	protected function get_multiple_galleries() {
		$settings = $this->get_settings_for_display();
		$base_class = $this->get_html_wrapper_class();

		$galleries = array();

		if ( ! $settings['hide_filter_bar'] ) {
			$this->add_render_attribute( 'titles-outer', 'class', array(
				"{$base_class}__titles-outer",
			) );

			$this->add_render_attribute( 'titles-container', 'class', array(
				"{$base_class}__titles-container",
				"{$base_class}__pointer-text",
				"{$base_class}__animation-{$settings['animation_text']}",
			) );

			echo '<div ' . $this->get_render_attribute_string( 'titles-outer' ) . '>
				<div ' . $this->get_render_attribute_string( 'titles-container' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if ( $settings['show_all_galleries'] ) {
				echo "<a href='#' data-gallery-index='all' class='{$base_class}__bar-item {$base_class}__title' tabindex='-1'>" . esc_html( $settings['show_all_galleries_label'] ) . "</a>";
			}

			foreach ( $settings['galleries'] as $index => $gallery ) {
				if ( ! $gallery['multiple_gallery'] ) {
					continue;
				}

				$galleries[] = $gallery;

				echo "<a href='#' data-gallery-index='" . esc_attr( $index ) . "' class='{$base_class}__bar-item {$base_class}__title'>" . esc_html( $gallery['gallery_title'] ) . "</a>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			}

				echo '</div>
			</div>';
		} else {
			foreach ( $settings['galleries'] as $index => $gallery ) {
				if ( ! $gallery['multiple_gallery'] ) {
					continue;
				}

				$galleries[] = $gallery;
			}
		}

		return $galleries;
	}

	protected function get_single_gallery() {
		$settings = $this->get_settings_for_display();

		return array( $settings['gallery'] );
	}

	protected function get_image_data( $attachment ) {
		$image_data = array(
			'caption' => $attachment->post_excerpt,
			'description' => $attachment->post_content,
			'title' => $attachment->post_title,
		);

		return $image_data;
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
