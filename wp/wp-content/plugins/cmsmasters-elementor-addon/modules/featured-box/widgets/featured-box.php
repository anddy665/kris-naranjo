<?php
namespace CmsmastersElementor\Modules\FeaturedBox\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Featured_Box extends Base_Widget {

	/**
	 * Get widget title.
	 *
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Featured Box', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-featured-box';
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
			'featured',
			'call to action',
			'box',
			'button',
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
			'widget-cmsmasters-featured-box',
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

	/**
	 * Get the selector for the animation type 'Item by item'.
	 *
	 * @since 1.18.0
	 */
	public function get_separate_animation_selector() {
		return implode( ', ', array(
			'.elementor-widget-cmsmasters-featured-box__icon-wrap',
			'.elementor-widget-cmsmasters-featured-box__image-wrap',
			'.elementor-widget-cmsmasters-featured-box__title',
			'.elementor-widget-cmsmasters-featured-box__description',
			'.elementor-widget-cmsmasters-featured-box__button-wrapper',
		) );
	}

	/**
	 * Get the class name ot the block for the text animation types 'Line by Line', 'Word by Word', 'Char by Char', etc.
	 * and names of animation types suitable for this widget.
	 *
	 * @since 1.18.0
	 */
	public function get_text_animation_class() {
		$selectors = implode( ', ', array(
			'elementor-widget-cmsmasters-featured-box__icon-wrap',
			'elementor-widget-cmsmasters-featured-box__image-wrap',
			'elementor-widget-cmsmasters-featured-box__title',
			'elementor-widget-cmsmasters-featured-box__description',
			'elementor-widget-cmsmasters-featured-box__button-wrapper',
		) );

		return array( $selectors => 'sequental, random' );
	}

	/**
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Added button width control & Text Shadow group control,
	 * fixed link type control.
	 * @since 1.1.0 Added group control 'BUTTON_BACKGROUND_GROUP', added gradient for button,
	 * added 'text-decoration' on hover for button, fixed border none for button, added 'border-radius' on hover,
	 * added 'box-shadow' for section 'Content Area', added responsive control for 'Alignment'.
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0, Fixed 'width' control.
	 * @since 1.3.3 Fixed image width, added controls for button icon, fixed title hover, added support custom breakpoints.
	 * @since 1.7.4 Added wrapper settings.
	 * @since 1.10.1 Added background color on hover for overlay.
	 * @since 1.14.0 Fixed background gradient for button elements.
	 * @since 1.14.0 Added `Width` and `Alignment` controls for image in column position.
	 * @since 1.18.0 Added `Entrance Animation` and `Entrance Animation Text` hidden control for entrance animation.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'General', 'cmsmasters-elementor' ),
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

		$this->add_responsive_control(
			'alignment',
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
				'toggle' => false,
				'prefix_class' => 'cmsmasters-featured-box__graphic-align%s-',
				'selectors' => array(
					'{{WRAPPER}}' => '--alignment: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'heading_graphic_element',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Graphic Elements', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'graphic_element',
			array(
				'label' => __( 'Element', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
						'icon' => 'eicon-ban',
					),
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'icon' => 'eicon-image-bold',
					),
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'icon' => 'eicon-star',
					),
				),
				'default' => 'image',
				'toggle' => false,
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-star',
					'library' => 'solid',
				),
				'condition' => array(
					'graphic_element' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__icon-wrap i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__icon-wrap svg' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'graphic_element' => 'icon',
				),
			)
		);

		$this->add_control(
			'graphic_image',
			array(
				'label' => __( 'Choose Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'show_label' => false,
				'condition' => array(
					'graphic_element' => 'image',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'graphic_image', // Actually its `image_size`
				'default' => 'full',
				'condition' => array(
					'graphic_element' => 'image',
					'graphic_image[id]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'image_fit',
			array(
				'label' => __( 'Image Fit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'Default', 'cmsmasters-elementor' ),
					'contain' => __( 'Contain', 'cmsmasters-elementor' ),
					'cover' => __( 'Cover', 'cmsmasters-elementor' ),
					'fill' => __( 'Fill', 'cmsmasters-elementor' ),
					'scale-down' => __( 'Scale Down', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'prefix_class' => 'cmsmasters-featured-box__image_fit%s-',
				'condition' => array(
					'graphic_element' => 'image',
				),
			)
		);

		$devices = CmsmastersUtils::get_devices();

		$this->add_responsive_control(
			'graphic_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'row' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'column' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'row-reverse' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'column',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-featured-box__graphic-position%s-',
				'condition' => array(
					'graphic_element!' => 'none',
				),
				'device_args' => array(
					$devices['tablet'] => array(
						'default' => 'column',
					),
					$devices['mobile'] => array(
						'default' => 'column',
					),
				),
			)
		);

		$graphic_position_v_arg = array(
			'conditions' => array(
				'relation' => 'or',
				'terms' => array(
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'graphic_element',
								'operator' => '===',
								'value' => 'image',
							),
							array(
								'name' => 'graphic_position_{{cmsmasters_device}}',
								'operator' => '!==',
								'value' => 'column',
							),
							array(
								'name' => 'image_fit_{{cmsmasters_device}}',
								'operator' => '===',
								'value' => 'none',
							),
						),
					),
					array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name' => 'graphic_element',
								'operator' => '===',
								'value' => 'icon',
							),
							array(
								'name' => 'graphic_position_{{cmsmasters_device}}',
								'operator' => '!==',
								'value' => 'column',
							),
						),
					),
				),
			),
		);

		$this->add_responsive_control(
			'graphic_position_v',
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
				'default' => 'top',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-featured-box__graphic-position%s-v-',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'graphic_element',
									'operator' => '===',
									'value' => 'image',
								),
								array(
									'name' => 'graphic_position',
									'operator' => '!==',
									'value' => 'column',
								),
								array(
									'name' => 'image_fit',
									'operator' => '===',
									'value' => 'none',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'graphic_element',
									'operator' => '===',
									'value' => 'icon',
								),
								array(
									'name' => 'graphic_position',
									'operator' => '!==',
									'value' => 'column',
								),
							),
						),
					),
				),
				'device_args' => CmsmastersUtils::get_devices_args( $graphic_position_v_arg ),
			)
		);

		$this->add_responsive_control(
			'graphic_width',
			array(
				'label' => __( 'Width(%)', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
				),
				'default' => array(
					'unit' => '%',
					'size' => 50,
				),
				'range' => array(
					'%' => array(
						'max' => 70,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--graphic-width: {{SIZE}}%;',
				),
				'condition' => array(
					'graphic_position!' => 'column',
					'graphic_element' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'graphic_column_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'vw',
					'custom',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--graphic-column-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'graphic_position' => 'column',
					'graphic_element' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'graphic_alignment',
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
				'selectors' => array(
					'{{WRAPPER}}' => '--graphic-alignment: {{VALUE}};',
				),
				'condition' => array(
					'graphic_position' => 'column',
					'graphic_element' => 'image',
				),
			)
		);

		$this->add_responsive_control(
			'graphic_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'vh',
				),
				'range' => array(
					'px' => array(
						'max' => 2000,
						'step' => 1,
					),
					'vh' => array(
						'max' => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--graphic-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'graphic_position' => 'column',
					'graphic_element' => 'image',
				),
			)
		);

		$this->add_control(
			'heading_content',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'This is the heading', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Enter your title', 'cmsmasters-elementor' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label' => __( 'Title HTML Tag', 'cmsmasters-elementor' ),
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
				),
				'default' => 'h2',
				'condition' => array(
					'title!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'title_position',
			array(
				'label' => __( 'Title Before Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-feature-box__title-position%s-',
				'condition' => array(
					'title!' => '',
					'graphic_position' => 'column',
					'graphic_element!' => 'none',
				),
			)
		);

		$this->add_control(
			'description',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Click edit button to change this text. Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'cmsmasters-elementor' ),
				'placeholder' => __( 'Enter your description', 'cmsmasters-elementor' ),
				'rows' => 5,
			)
		);

		$this->add_control(
			'link_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => __( 'URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'default' => array(
					'url' => '',
				),
				'dynamic' => array(
					'active' => true,
				),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label' => __( 'Link Type', 'cmsmasters-elementor' ),
				'description' => __( 'Choose the linked area', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'link-box' => array(
						'title' => __( 'Link the box', 'cmsmasters-elementor' ),
					),
					'link-button' => array(
						'title' => __( 'Link button', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => __( 'Link both', 'cmsmasters-elementor' ),
					),
				),
				'prefix_class' => 'cmsmasters-featured-box__',
				'default' => 'both',
				'toggle' => false,
				'render_type' => 'template',
				'condition' => array(
					'link[url]!' => '',
				),
			)
		);

		$this->add_control(
			'button',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
				),
				'default' => __( 'Click Here', 'cmsmasters-elementor' ),
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
				),
			)
		);

		$this->add_control(
			'heading_icon_buton',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Button Icon', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
				),
			)
		);

		$this->add_control(
			'button_icon',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
				),
			)
		);

		$this->add_control(
			'icon_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
					'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
					'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
				),
				'default' => 'default',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
					'button_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'icon_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'icon_align',
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
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
					'button_icon[value]!' => '',
				),
			)
		);

		$this->add_control(
			'button_icon_reverse',
			array(
				'label' => __( 'Reverse', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
					'button_icon[value]!' => '',
					'icon_align' => 'stretch',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_wrapper_style',
			array(
				'label' => __( 'Wrapper', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'wrapper_state_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__wrapper";

			$this->start_controls_tab(
				"wrapper_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"wrapper_bg_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmaster-wrapper-bg-color-{$key}: {{VALUE}}",
					),
				)
			);

			$this->add_control(
				"wrapper_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						'{{WRAPPER}}' => "--cmsmaster-wrapper-bd-color-{$key}: {{VALUE}}",
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "wrapper_box_shadow_{$key}",
					'selector' => $selector . $state,
				)
			);

			if ( 'hover' === $key ) {
				$this->add_control(
					'wrapper_hover_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array(
							'size' => 0.3,
						),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}}' => '--cmsmasters-wrapper-transition-duration: {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'wrapper_border',
				'selector' => $selector,
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'wrapper_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-wrapper-bdr: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-wrapper-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'graphic_element_style',
			array(
				'label' => __( 'Graphic Element', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'graphic_element!' => 'none',
				),
			)
		);

		$this->start_controls_tabs( 'graphic_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__wrapper{$state} .elementor-widget-cmsmasters-featured-box__graphic-item";

			$this->start_controls_tab(
				"graphic_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"graphic_bg_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector => 'background-color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				"graphic_icon_color_{$key}",
				array(
					'label' => __( 'Icon Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector . ' *' => 'color: {{VALUE}}',
						$selector . ' svg path' => 'fill: currentColor;',
					),
					'condition' => array(
						'graphic_element' => 'icon',
					),
				)
			);

			$this->add_control(
				"graphic_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						$selector => 'border-color: {{VALUE}}',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "box_shadow_{$key}",
					'selector' => $selector,
				)
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				array(
					'name' => "css_filters_{$key}",
					'selector' => $selector . ' img',
				)
			);

			$this->add_control(
				"graphic_opacity_{$key}",
				array(
					'label' => __( 'Opacity', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => array(
						'px' => array(
							'max' => 1,
							'min' => 0.10,
							'step' => 0.01,
						),
					),
					'selectors' => array(
						$selector => 'opacity: {{SIZE}};',
					),
				)
			);

			if ( 'hover' === $key ) {
				$this->add_responsive_control(
					"graphic_border_radius_{$key}",
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							"{$selector} img" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					'graphic_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							"{$selector} img" => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			}

			if ( 'hover' === $key ) {
				$this->add_control(
					'hover_transition',
					array(
						'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SLIDER,
						'default' => array(
							'size' => 0.3,
						),
						'range' => array(
							'px' => array(
								'max' => 3,
								'step' => 0.1,
							),
						),
						'selectors' => array(
							'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__graphic-item' => 'transition-duration: {{SIZE}}s',
							'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__graphic-item *' => 'transition-duration: {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();

		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'graphic_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 200,
						'step' => 1,
					),
					'%' => array(
						'max' => 100,
						'step' => 0.1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--graphic-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_graphic',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__graphic-item',
				'separator' => 'before',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'graphic_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__graphic-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			array(
				'label' => __( 'Content Area', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'text_background',
				'dynamic' => array(
					'active' => true,
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__type-wrap',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'box_shadow_text',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__type-wrap',
			)
		);

		$this->add_responsive_control(
			'text_vertical_position',
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
					'stretch' => array(
						'title' => __( 'Stretch', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					),
				),
				'default' => 'middle',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-featured-box__text-valign%s-',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'content_height',
			array(
				'label' => __( 'Min Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 1000,
						'step' => 1,
					),
					'vh' => array(
						'max' => 100,
						'step' => 1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'vh',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-content-min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_text',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__type-wrap',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'text_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__type-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__type-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'title!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__title',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-featured-box__both .elementor-widget-cmsmasters-featured-box__wrapper:hover .elementor-widget-cmsmasters-featured-box__title' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-featured-box__link-box .elementor-widget-cmsmasters-featured-box__wrapper:hover .elementor-widget-cmsmasters-featured-box__title' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow_title',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '10',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__graphic-wrapper .elementor-widget-cmsmasters-featured-box__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
				'condition' => array(
					'title_position' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_description_style',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'description!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__description',
			)
		);

		$this->add_control(
			'description_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__description' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'description_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-featured-box__both .elementor-widget-cmsmasters-featured-box__wrapper:hover .elementor-widget-cmsmasters-featured-box__description' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-featured-box__link-box .elementor-widget-cmsmasters-featured-box__wrapper:hover .elementor-widget-cmsmasters-featured-box__description' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-button',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'text_shadow_description',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__description',
			)
		);

		$this->add_responsive_control(
			'description_spacing',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => '10',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'button_style',
			array(
				'label' => __( 'Button', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'link[url]!' => '',
					'link_type!' => 'link-box',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'button_typography',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button',
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button{$state}";
			$selector2 = "{{WRAPPER}}.cmsmasters-featured-box__both .elementor-widget-cmsmasters-featured-box__wrapper{$state} .elementor-widget-cmsmasters-featured-box__button";

			$this->start_controls_tab(
				"button_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$element = ( 'hover' === $key ) ? ':after' : ':before';

			$this->add_control(
				"button_text_color_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
						$selector2 => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"button_bg_{$key}_background",
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
				"button_background_color_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => array(
						"{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button{$element}" => '--button-bg-color: {{VALUE}}; ' .
							'background: var( --button-bg-color );',
					),
					'condition' => array(
						"button_bg_{$key}_background" => array(
							'color',
							'gradient',
						),
					),
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_stop",
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
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_b",
				array(
					'label' => __( 'Second Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f2295b',
					'render_type' => 'ui',
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_color_b_stop",
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
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_type",
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
						"button_bg_{$key}_background" => array( 'gradient' ),
					),
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_angle",
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
						"{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button{$element}" => 'background-color: transparent; ' .
							"background-image: linear-gradient({{SIZE}}{{UNIT}}, var( --button-bg-color ) {{button_bg_{$key}_color_stop.SIZE}}{{button_bg_{$key}_color_stop.UNIT}}, {{button_bg_{$key}_color_b.VALUE}} {{button_bg_{$key}_color_b_stop.SIZE}}{{button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
						"button_bg_{$key}_gradient_type" => 'linear',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_bg_{$key}_gradient_position",
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
						"{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button{$element}" => 'background-color: transparent; ' .
							"background-image: radial-gradient(at {{VALUE}}, var( --button-bg-color ) {{button_bg_{$key}_color_stop.SIZE}}{{button_bg_{$key}_color_stop.UNIT}}, {{button_bg_{$key}_color_b.VALUE}} {{button_bg_{$key}_color_b_stop.SIZE}}{{button_bg_{$key}_color_b_stop.UNIT}})",
					),
					'condition' => array(
						"button_bg_{$key}_background" => array( 'gradient' ),
						"button_bg_{$key}_gradient_type" => 'radial',
					),
					'separator' => 'after',
					'of_type' => 'gradient',
				)
			);

			$this->add_control(
				"button_border_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
						$selector2 => 'border-color: {{VALUE}};',
					),
					'condition' => array(
						'border_button_border!' => array(
							'none',
						),
					),
				)
			);

			if ( 'normal' === $key ) {
				$this->add_responsive_control(
					'button_border_radius',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							$selector2 => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);
			} else {
				$this->add_responsive_control(
					'button_border_radius_hover',
					array(
						'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => array(
							'px',
							'%',
						),
						'selectors' => array(
							$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							$selector2 => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$this->add_control(
					'button_text_decoration_hover',
					array(
						'label' => __( 'Text Decoration', 'cmsmasters-elementor' ),
						'type' => Controls_Manager::SELECT,
						'options' => array(
							'' => __( 'Default', 'cmsmasters-elementor' ),
							'none' => _x( 'None', 'Typography Control', 'cmsmasters-elementor' ),
							'underline' => _x( 'Underline', 'Typography Control', 'cmsmasters-elementor' ),
							'overline' => _x( 'Overline', 'Typography Control', 'cmsmasters-elementor' ),
							'line-through' => _x( 'Line Through', 'Typography Control', 'cmsmasters-elementor' ),
						),
						'default' => '',
						'selectors' => array(
							$selector => 'text-decoration: {{VALUE}};',
							$selector2 => 'text-decoration: {{VALUE}};',
						),
					)
				);
			}

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => "button_shadow_text_{$key}",
					'selector' => $selector . ', ' . $selector2,
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name' => "button_shadow_{$key}",
					'selector' => $selector . ', ' . $selector2,
				)
			);

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => 'width:100%; max-width: {{SIZE}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_button',
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
						'prefix_class' => 'cmsmasters-featured-box__button-border-',
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
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button',
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'button_icon_size',
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
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_button_icon_style',
			array( 'condition' => array( 'button_icon[value]!' => '' ) )
		);

		$this->start_controls_tab(
			'tab_button_icon_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_icon_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-color-normal: {{VALUE}};',
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_bg_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-bg-color-normal: {{VALUE}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'button_icon_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-bd-color-normal: {{VALUE}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'button_icon_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-bdr-normal: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'icon_indent',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-indent-normal: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_icon_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'button_icon_hover_color',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-color-hover: {{VALUE}};',
				),
				'condition' => array( 'button_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'button_icon_bg_hover_color',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-bg-color-hover: {{VALUE}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_bd_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-bd-color-hover: {{VALUE}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'button_icon_hover_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-bdr-hover: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_hover_indent',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-indent-hover: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_align!' => 'stretch',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_icon_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_square_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-square-pdd: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
					'icon_shape' => 'square',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_circle_padding',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-circle-pdd: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view!' => 'default',
					'icon_shape' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'button_icon_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__button' => '--button-icon-border-w: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'button_icon[value]!' => '',
					'icon_view' => 'framed',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'overlay_style',
			array(
				'label' => __( 'Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'overlay_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__overlay' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'overlay_bg_color_hover',
			array(
				'label' => __( 'Background Color Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-featured-box__wrapper:hover .elementor-widget-cmsmasters-featured-box__overlay' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'hide_on_hover!' => 'yes',
				),
			)
		);

		$this->add_control(
			'overlay_z_index',
			array(
				'label' => __( 'Z-Index', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => __( 'Specify the value 1 if you want the content to be above the overlay', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-z-index-overlay: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'hide_on_hover',
			array(
				'label' => __( 'Hide On Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'prefix_class' => 'cmsmasters-featured-box__overlay-hover-',
				'condition' => array(
					'overlay_bg_color!' => '',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render feature box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( '_wrapper', array(
			'data-separate-animation-selector' => $this->get_separate_animation_selector(),
			'data-text-animation-class' => $this->get_text_animation_class(),
		) );

		$wrapper_tag = 'div';

		$this->add_render_attribute( 'title', 'class', array(
			'elementor-widget-cmsmasters-featured-box__title',
			'elementor-widget-cmsmasters-featured-box__content-item',
		) );

		$this->add_render_attribute( 'description', 'class', array(
			'elementor-widget-cmsmasters-featured-box__description',
			'elementor-widget-cmsmasters-featured-box__content-item',
		) );

		$this->add_render_attribute( 'button', 'class', array(
			'elementor-widget-cmsmasters-featured-box__button',
			'elementor-widget-cmsmasters-featured-box__content-item',
			'cmsmasters-theme-button',
		) );

		$this->add_render_attribute( 'graphic_element', 'class', array(
			'elementor-widget-cmsmasters-featured-box__graphic-wrapper',
			'elementor-widget-cmsmasters-featured-box__content-item',
		) );

		$this->add_render_attribute( 'type_wrap', 'class', array(
			'elementor-widget-cmsmasters-featured-box__type-wrap',
			'elementor-widget-cmsmasters-featured-box__content-item',
		) );

		if ( 'icon' === $settings['graphic_element'] ) {
			$this->add_render_attribute( 'graphic_element', 'class', 'elementor-widget-cmsmasters-featured-box__icon' );
		} elseif ( 'image' === $settings['graphic_element'] && ! empty( $settings['graphic_image']['url'] ) ) {
			$this->add_render_attribute( 'graphic_element', 'class', 'elementor-widget-cmsmasters-featured-box__image' );
		}

		$link_element = '';

		if ( 'link-button' !== $settings['link_type'] && ! empty( $settings['link']['url'] ) ) {
			$wrapper_tag = 'a';
			$link_element = 'wrapper';

		} else {
			$wrapper_tag = 'div';
			$link_element = 'button';
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( $link_element, $settings['link'] );
		}

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-featured-box__wrapper' );

		$this->add_inline_editing_attributes( 'title' );
		$this->add_inline_editing_attributes( 'description' );
		$this->add_inline_editing_attributes( 'button' );

		echo '<' . $wrapper_tag . ' ' . $this->get_render_attribute_string( 'wrapper' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( '' !== $settings['overlay_bg_color'] ) {
			echo '<span class="elementor-widget-cmsmasters-featured-box__overlay"></span>';
		}

			echo '<div class="elementor-widget-cmsmasters-featured-box__content">
				<div class="elementor-widget-cmsmasters-featured-box__content-vertical-inner">';
					$this->render_graphic();
					echo '<div ' . $this->get_render_attribute_string( 'type_wrap' ) . '>';
						$this->render_title();
						$this->render_description();

		if ( 'link-box' !== $settings['link_type'] ) {
			$this->render_button();
		}

					echo '</div>
				</div>
			</div>
		</' . $wrapper_tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get widgets graphic.
	 *
	 * Retrieve widgets graphic.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_graphic() {
		$settings = $this->get_settings_for_display();

		if ( 'image' === $settings['graphic_element'] && ! empty( $settings['graphic_image']['url'] ) || ( 'icon' === $settings['graphic_element'] && ! empty( $settings['icon'] ) ) ) {
			echo '<div ' . $this->get_render_attribute_string( 'graphic_element' ) . '>';
				$this->render_title();

			if ( 'image' === $settings['graphic_element'] && ! empty( $settings['graphic_image']['url'] ) ) {
				$image = Group_Control_Image_Size::get_attachment_image_html( $settings, 'graphic_image' );

				echo '<figure class="elementor-widget-cmsmasters-featured-box__image-wrap elementor-widget-cmsmasters-featured-box__graphic-item">' .
					$image .
				'</figure>';

			} elseif ( 'icon' === $settings['graphic_element'] && ! empty( $settings['icon'] ) ) {
				echo '<div class="elementor-widget-cmsmasters-featured-box__icon-wrap elementor-widget-cmsmasters-featured-box__graphic-item">';
					Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
				echo '</div>';
			}

			echo '</div>';
		}
	}

	/**
	 * Get widgets title.
	 *
	 * Retrieve widgets title.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_title() {
		$settings = $this->get_settings_for_display();
		$title_tag = $settings['title_tag'];

		if ( ! empty( $settings['title'] ) ) {
			echo '<' . Utils::validate_html_tag( $title_tag ) . ' ' . $this->get_render_attribute_string( 'title' ) . '>' . wp_kses_post( $settings['title'] ) . '</' . Utils::validate_html_tag( $title_tag ) . '>';
		}
	}

	/**
	 * Get widgets description.
	 *
	 * Retrieve widgets description.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_description() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['description'] ) ) {
			echo '<div ' . $this->get_render_attribute_string( 'description' ) . '>' .
				wp_kses_post( $settings['description'] );
			echo '</div>';
		}
	}

	/**
	 * Get widgets button.
	 *
	 * Retrieve widgets button.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function render_button() {
		$settings = $this->get_settings_for_display();
		$link_tag = $this->get_link_tag();
		$button_text = ( isset( $settings['button'] ) ? $settings['button'] : '' );
		$button_icon = $this->get_button_icon();

		if ( ! empty( $settings['link']['url'] ) && ( ! empty( $button_text ) || $button_icon ) ) {
			echo '<div class="elementor-widget-cmsmasters-featured-box__button-wrapper elementor-widget-cmsmasters-featured-box__content-item">
				<' . $link_tag . ' ' . $this->get_render_attribute_string( 'button' ) . '>';

			if ( $button_icon ) {
				echo $button_icon;
			}

			if ( ! empty( $button_text ) ) {
				echo wp_kses_post( $button_text );
			}

				echo '</' . $link_tag . '>
			</div>';
		}
	}

	/**
	 * Get widgets link tag.
	 *
	 * Retrieve widgets link tag.
	 *
	 * @since 1.0.0
	 *
	 */
	protected function get_link_tag() {
		$settings = $this->get_settings_for_display();

		if ( 'link-button' !== $settings['link_type'] ) {
			return 'span';
		} else {
			return 'a';
		}
	}

	/**
	 * Get button icon.
	 *
	 * Retrieve button icon.
	 *
	 * @since 1.3.2
	 * @since 1.3.5 Fixed empty button_icon.
	 *
	 */
	protected function get_button_icon() {
		$settings = $this->get_settings_for_display();

		$button_icon = ( isset( $settings['button_icon'] ) ? $settings['button_icon'] : '' );

		if ( isset( $button_icon['value'] ) && '' !== $button_icon['value'] ) {
			$icon_view = $settings['icon_view'];
			$icon_shape = $settings['icon_shape'];
			$icon_align = $settings['icon_align'];

			$this->add_render_attribute( 'button_icon', 'class', "elementor-widget-cmsmasters-featured-box__button-icon-{$icon_view}" );

			$this->add_render_attribute( 'button_icon', 'class', "elementor-widget-cmsmasters-featured-box__button-icon-{$icon_shape}" );

			$this->add_render_attribute( 'button_icon', 'class', 'elementor-widget-cmsmasters-featured-box__button-icon' );

			$this->add_render_attribute( 'button', 'class', "elementor-widget-cmsmasters-featured-box__button-icon-{$icon_align}"
			);

			if ( 'yes' === $settings['button_icon_reverse'] ) {
				$this->add_render_attribute( 'button', 'class', 'elementor-widget-cmsmasters-featured-box__button-icon-reverse' );
			}

			$attribute_icon = $this->get_render_attribute_string( 'button_icon' );

			ob_start();

			$button_text = ( isset( $settings['button'] ) ? $settings['button'] : '' );
			$button_icon_att = array( 'aria-hidden' => 'true' );

			if ( ! empty( $settings['link']['url'] ) && empty( $button_text ) ) {
				$button_icon_att = array_merge(
					$button_icon_att,
					array( 'aria-label' => 'Button' ),
				);
			}

			echo "<span {$attribute_icon}>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				Icons_Manager::render_icon( $button_icon, $button_icon_att );

			echo '</span>';

			return ob_get_clean();
		}
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
				'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'description',
				'type' => esc_html__( 'Description', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
			),
			'link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'button',
				'type' => esc_html__( 'Button Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
