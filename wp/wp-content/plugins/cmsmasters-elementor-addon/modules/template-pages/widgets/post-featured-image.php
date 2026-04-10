<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Plugin as CmsmastersPlugin;
use CmsmastersElementor\Traits\Extendable_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Post Featured Image widget.
 *
 * Widget that displays featured image of current post.
 *
 * @since 1.0.0
 */
class Post_Featured_Image extends Base_Widget {

	use Singular_Widget;
	use Extendable_Widget;

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
		return 'cmsmasters-widget-image';
	}

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
		return __( 'Featured Image', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve test widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-featured-image';
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
			'image',
			'featured',
			'thumbnail',
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
			'widget-cmsmasters-post-featured-image',
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
	 * Register test widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Fixed size and alignment for image overlay.
	 * @since 1.6.0 Fixed border radius for background overlay.
	 */
	protected function register_controls() {
		$dynamic_tags = CmsmastersPlugin::elementor()->dynamic_tags;
		$tag_names = $this->get_tag_names();

		$this->start_controls_section(
			'section_image',
			array( 'label' => __( 'Image', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'image_id',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['image_id'] ),
				),
			)
		);

		$this->add_control(
			'image_url',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['image_url'] ),
				),
			)
		);

		$this->add_control(
			'post_url',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['post_url'] ),
				),
				'condition' => array( 'link_to' => 'post' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'image',
				'default' => 'large',
				'separator' => 'none',
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
				),
				'selectors_dictionary' => array(
					'left' => 'text-align:left; margin-left:0; margin-right:auto;',
					'center' => 'text-align:center; margin-left:auto; margin-right:auto;',
					'right' => 'text-align:right; margin-right:0; margin-left:auto;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '{{VALUE}}',
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => '{{VALUE}}',
					'{{WRAPPER}} .cmsmasters-widget-image__wrap .cmsmasters-background-overlay-wrap' => '{{VALUE}}',
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			'fallback_image_popover',
			array(
				'label' => __( 'Fallback Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'separator' => 'before',
			)
		);

		$this->start_popover();

		$this->add_control(
			'fallback_image',
			array(
				'label' => __( 'Fallback Image', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => Controls_Manager::MEDIA,
				'condition' => array( 'fallback_image_popover' => 'yes' ),
			)
		);

		$this->end_popover();

		$this->add_control(
			'link_to',
			array(
				'label' => __( 'Image Link', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'post' => array(
						'title' => __( 'Post', 'cmsmasters-elementor' ),
						'description' => __( 'Open Post', 'cmsmasters-elementor' ),
					),
					'file' => array(
						'title' => __( 'Media', 'cmsmasters-elementor' ),
						'description' => __( 'Media File', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
						'description' => __( 'Custom URL', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'none',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'open_lightbox',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'yes' => __( 'Yes', 'cmsmasters-elementor' ),
					'no' => __( 'No', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'condition' => array( 'link_to' => 'file' ),
			)
		);

		$this->add_control(
			'link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array( 'link_to' => 'custom' ),
			)
		);

		$this->add_control(
			'caption_source',
			array(
				'label' => __( 'Caption', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'attachment' => __( 'Attachment Caption', 'cmsmasters-elementor' ),
					'description' => __( 'Image Description', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
				'default' => 'none',
			)
		);

		$this->add_control(
			'caption',
			array(
				'label' => __( 'Custom Caption', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'Enter your image caption', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'caption_source' => 'custom' ),
			)
		);

		$this->add_control(
			'caption_display',
			array(
				'label' => __( 'Display Inline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-caption-inline-',
				'condition' => array( 'caption_source!' => 'none' ),
			)
		);

		$this->add_control(
			'object-fit',
			array(
				'label' => __( 'Object Fit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'description' => __( 'The image will be resized to fit its parent container.', 'cmsmasters-elementor' ),
				'options' => array(
					'' => __( 'Disabled', 'cmsmasters-elementor' ),
					'fill' => __( 'Fill', 'cmsmasters-elementor' ),
					'cover' => __( 'Cover', 'cmsmasters-elementor' ),
					'contain' => __( 'Contain', 'cmsmasters-elementor' ),
					'scale-down' => __( 'Scale Down', 'cmsmasters-elementor' ),
					'none' => __( 'None', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-object-fit cmsmasters-object-fit-',
				'condition' => array( 'caption_source' => 'none' ),
			)
		);

		$object_fit_condition = array(
			'caption_source' => 'none',
			'object-fit!' => array( '' ),
		);

		$this->add_responsive_control(
			'height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array( 'px', 'vh' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vh' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition' => $object_fit_condition,
			)
		);

		$this->add_control(
			'object-vert-position',
			array(
				'label' => __( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'condition' => array_merge( $object_fit_condition, array( 'fill' ) ),
			)
		);

		$this->add_control(
			'object-hor-position',
			array(
				'label' => __( 'Horizontal Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => 'object-position: {{object-vert-position.VALUE}} {{VALUE}};',
				),
				'condition' => array_merge( $object_fit_condition, array( 'fill' ) ),
			)
		);

		$this->add_control(
			'image_overlay',
			array(
				'label' => __( 'Image Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
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
			'section_style_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'unit' => '%' ),
				'tablet_default' => array( 'unit' => '%' ),
				'mobile_default' => array( 'unit' => '%' ),
				'size_units' => array( '%', 'px', 'vw' ),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
					'px' => array(
						'min' => 1,
						'max' => 1000,
					),
					'vw' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'width_full',
			array(
				'type' => Controls_Manager::HIDDEN,
				'default' => '100%',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap a, ' .
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => 'width: {{VALUE}};',
				),
				'condition' => array(
					'width[size]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'max_width',
			array(
				'label' => __( 'Max Width', 'cmsmasters-elementor' ) . ' (%)',
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'unit' => '%' ),
				'tablet_default' => array( 'unit' => '%' ),
				'mobile_default' => array( 'unit' => '%' ),
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'separator_panel_style',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_BACKGROUND ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_BORDER ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'image_border_border!' => '' ),
			)
		);

		$this->add_control(
			'opacity',
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
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'image_box_shadow_normal',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__wrap img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__wrap img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'background_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap:hover img' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap:hover img' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'image_border_border!' => '' ),
			)
		);

		$this->add_control(
			'opacity_hover',
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
					'{{WRAPPER}} .cmsmasters-widget-image__wrap:hover img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'image_box_shadow_hover',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__wrap:hover img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__wrap:hover img',
			)
		);

		$this->add_control(
			'background_hover_transition',
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
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->add_control(
			'hover_animation',
			array(
				'label' => __( 'Hover Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'image_border',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'separator' => 'before',
					),
				),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__wrap img',
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img,
					{{WRAPPER}} .cmsmasters-widget-image__wrap .cmsmasters-background-overlay-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_caption',
			array(
				'label' => __( 'Caption', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'caption_source!' => 'none' ),
			)
		);

		$this->add_control(
			'caption_align',
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
				),
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => 'text-align: {{VALUE}};',
				),
				'condition' => array( 'caption_display!' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'caption_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__caption',
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_TEXT ),
			)
		);

		$this->add_control(
			'text_color',
			array(
				'label' => __( 'Text Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'caption_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'caption_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'caption_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'caption_border',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__caption',
			)
		);

		$this->add_control(
			'caption_border_radius', array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'after',
				'size_units' => array( '%', 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'caption_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__caption',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'caption_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__caption',
			)
		);

		$this->add_responsive_control(
			'caption_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'caption_space',
			array(
				'label' => __( 'Spacing', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__caption' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_overlay',
			array(
				'label' => __( 'Image Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'image_overlay' => 'yes' ),
			)
		);

		$this->start_controls_tabs( 'tabs_background_overlay' );

		$this->start_controls_tab(
			'tab_background_overlay_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'background_overlay',
				'exclude' => array(
					'image',
					'position',
					'attachment',
					'repeat',
					'size',
				),
				'selector' => '{{WRAPPER}} .cmsmasters-background-overlay-wrap',
			)
		);

		$this->add_control(
			'background_overlay_opacity',
			array(
				'label' => __( 'Opacity', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => .5,
				),
				'range' => array(
					'px' => array(
						'max' => 1,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-background-overlay-wrap' => 'opacity: {{SIZE}};',
				),
				'condition' => array(
					'background_overlay_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_background_overlay_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'background_overlay_hover',
				'exclude' => array(
					'image',
					'position',
					'attachment',
					'repeat',
					'size',
				),
				'selector' => '{{WRAPPER}} .cmsmasters-widget-image__wrap:hover .cmsmasters-background-overlay-wrap',
			)
		);

		$this->add_control(
			'background_overlay_hover_opacity',
			array(
				'label' => __( 'Opacity', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => .5,
				),
				'range' => array(
					'px' => array(
						'max' => 1,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-widget-image__wrap:hover .cmsmasters-background-overlay-wrap' => 'opacity: {{SIZE}};',
				),
				'condition' => array(
					'background_overlay_hover_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_control(
			'background_overlay_hover_transition',
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
					'{{WRAPPER}} .cmsmasters-background-overlay-wrap' => 'transition: all {{SIZE}}s',
				),
				'render_type' => 'ui',
				'condition' => array( 'image_overlay' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'overlay_blend_mode',
			array(
				'label' => __( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Normal', 'cmsmasters-elementor' ),
					'multiply' => __( 'Multiply', 'cmsmasters-elementor' ),
					'screen' => __( 'Screen', 'cmsmasters-elementor' ),
					'overlay' => __( 'Overlay', 'cmsmasters-elementor' ),
					'darken' => __( 'Darken', 'cmsmasters-elementor' ),
					'lighten' => __( 'Lighten', 'cmsmasters-elementor' ),
					'color-dodge' => __( 'Color Dodge', 'cmsmasters-elementor' ),
					'saturation' => __( 'Saturation', 'cmsmasters-elementor' ),
					'color' => __( 'Color', 'cmsmasters-elementor' ),
					'luminosity' => __( 'Luminosity', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-background-overlay-wrap' => 'mix-blend-mode: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get tag names.
	 *
	 * Retrieve widget dynamic controls tag names.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget dynamic controls tag names.
	 */
	protected function get_tag_names() {
		return array(
			'image_id' => 'cmsmasters-post-featured-image-id',
			'image_url' => 'cmsmasters-post-featured-image-url',
			'post_url' => 'cmsmasters-post-url',
		);
	}

	/**
	 * Render Post Featured Image widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( 0 < (int) $settings['image_id'] && ! empty( $settings['image_url'] ) ) {
			$settings['image'] = array(
				'id' => $settings['image_id'],
				'url' => $settings['image_url'],
			);
		}

		if ( ! isset( $settings['image'] ) ) {
			if (
				empty( $settings['fallback_image_popover'] ) ||
				empty( $settings['fallback_image'] ) ||
				empty( $settings['fallback_image']['id'] ) ||
				empty( $settings['fallback_image']['url'] )
			) {
				return;
			}

			$settings['image'] = $settings['fallback_image'];
		}

		$widget_class = $this->get_extendable_widget_class();

		$this->add_render_attribute( 'wrapper', 'class', "{$widget_class}__wrap" );

		if ( ! empty( $settings['shape'] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'cmsmasters-shape-' . $settings['shape'] );
		}

		if ( $settings['hover_animation'] && $settings['image_overlay'] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-animation-' . $settings['hover_animation'] );
		}

		$has_caption = $this->has_caption( $settings );
		$before_image = '';
		$after_image = '';

		if ( $has_caption ) {
			$this->add_render_attribute( 'figure', 'class', 'wp-caption' );
			$this->add_render_attribute( 'figcaption', 'class', array(
				'wp-caption-text',
				"{$widget_class}__caption",
			) );

			$before_image .= $this->get_render_tag( 'figure', 'figure' );
		}

		if ( $settings['image_overlay'] ) {
			$this->add_render_attribute( 'overlay', 'class', 'cmsmasters-background-overlay-wrap' );

			$after_image .= $this->get_render_tag( 'div', 'overlay', '' );
		}

		$link = $this->get_link_url( $settings );

		if ( $link ) {
			$this->add_link_attributes( 'link', $link );

			$this->add_render_attribute( 'link', 'data-elementor-open-lightbox', $settings['open_lightbox'] );

			$this->add_render_attribute( 'link', 'aria-label', 'Featured Image' );

			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$this->add_render_attribute( 'link', 'class', 'elementor-clickable' );
			}

			$before_image .= $this->get_render_tag( 'a', 'link' );
			$after_image .= $this->get_render_close_tag( 'a' );
		}

		if ( $has_caption ) {
			$after_image .= $this->get_render_tag( 'figcaption', 'figcaption', $this->get_caption( $settings ) ) .
			$this->get_render_close_tag( 'figure' );
		}

		$this->print_render_tag( 'div', 'wrapper', $before_image .
			Group_Control_Image_Size::get_attachment_image_html( $settings ) .
		$after_image );
	}

	/**
	 * Get link URL.
	 *
	 * Retrieve image widget link URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_link_url( $settings ) {
		if ( 'none' === $settings['link_to'] ) {
			return false;
		}

		if ( 'custom' === $settings['link_to'] ) {
			if ( empty( $settings['link']['url'] ) ) {
				return false;
			}

			return $settings['link'];
		}

		if ( 'post' === $settings['link_to'] ) {
			return array( 'url' => $settings['post_url'] );
		}

		if ( 'file' === $settings['link_to'] ) {
			return array( 'url' => $settings['image_url'] );
		}

		return array( 'url' => Utils::get_placeholder_image_src() );
	}

	/**
	 * Check image caption.
	 *
	 * Check if the current image has caption.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return bool
	 */
	private function has_caption( $settings ) {
		return ( ! empty( $settings['caption_source'] ) && 'none' !== $settings['caption_source'] );
	}

	/**
	 * Get image caption.
	 *
	 * Get the caption for current image.
	 *
	 * @since 1.0.0
	 *
	 * @param $settings Widget settings.
	 *
	 * @return string Caption string.
	 */
	private function get_caption( $settings ) {
		$caption = '';

		if ( ! empty( $settings['caption_source'] ) ) {
			switch ( $settings['caption_source'] ) {
				case 'attachment':
					if ( $settings['image_id'] ) {
						$caption = wp_get_attachment_caption( $settings['image_id'] );
					}

					break;
				case 'description':
					if ( $settings['image_id'] ) {
						$caption = get_post( $settings['image_id'] )->post_content;
					}

					break;
				case 'custom':
					if ( ! empty( $settings['caption'] ) ) {
						$caption = $settings['caption'];
					}

					break;
			}
		}

		return $caption;
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}

	/**
	 * Render image widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		?>
		<#
		if (
			_.isEmpty( settings.image_id ) ||
			'0' === settings.image_id ||
			_.isEmpty( settings.image_url ) ||
			'null' === settings.image_url
		) {
			if (
				! settings.fallback_image_popover ||
				! settings.fallback_image.id ||
				! settings.fallback_image.url
			) {
				return false;
			}

			settings.image_id = settings.fallback_image.id;
			settings.image_url = settings.fallback_image.url;
		}

		var image = {
			id: settings.image_id,
			url: settings.image_url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel(),
		};

		elementor.imagesManager.registerItem( image );

		if ( ! elementor.imagesManager.getItem( image ) ) {
			clearInterval( document.featuredImageWidgetInterval );

			document.featuredImageWidgetInterval = setInterval( function() {
				clearInterval( document.featuredImageWidgetInterval );

				view.render();
			}, 2000);

			return false;
		}

		var imageUrl = elementor.imagesManager.getImageUrl( image );

		if ( ! imageUrl ) {
			return;
		}

		var hasCaption = function() {
			if ( ! settings.caption_source || 'none' === settings.caption_source ) {
				return false;
			}

			return true;
		};

		var ensureAttachmentData = function( id ) {
			if ( 'undefined' === typeof wp.media.attachment( id ).get( 'caption' ) ) {
				wp.media.attachment( id ).fetch().then( function( data ) {
					view.render();
				} );
			}
		};

		var getAttachmentAttribute = function( id, attr ) {
			if ( ! id ) {
				return '';
			}

			ensureAttachmentData( id );

			var attachmentAttr = wp.media.attachment( id ).get( attr );

			if ( ! attachmentAttr ) {
				return '';
			}

			return attachmentAttr;
		};

		var getCaption = function() {
			if ( ! hasCaption() ) {
				return '';
			}

			if ( 'custom' === settings.caption_source ) {
				return settings.caption;
			}

			if ( 'description' === settings.caption_source ) {
				return getAttachmentAttribute( settings.image_id, 'description' );
			}

			return getAttachmentAttribute( settings.image_id, 'caption' );
		};

		var widgetClass = 'cmsmasters-widget-image',
			imgBefore = '',
			imgAfter = '';

		view.addRenderAttribute( 'wrapper', 'class', widgetClass + '__wrap' );

		if ( settings.shape ) {
			view.addRenderAttribute( 'wrapper', 'class', 'cmsmasters-shape-' + settings.shape );
		}

		view.addRenderAttribute( 'img', 'src', imageUrl );

		if ( '' !== settings.hover_animation ) {
			view.addRenderAttribute(
				( settings.image_overlay ) ? 'wrapper' : 'img',
				'class',
				'elementor-animation-' + settings.hover_animation
			);
		}

		if ( hasCaption() ) {
			view.addRenderAttribute( 'figure', 'class', 'wp-caption' );

			view.addRenderAttribute( 'figcaption', 'class', [ 'wp-caption-text', widgetClass + '__caption' ] );

			imgBefore += '<figure ' + view.getRenderAttributeString( 'figure' ) + '>';
		}

		if ( settings.image_overlay ) {
			view.addRenderAttribute( 'overlay', 'class', 'cmsmasters-background-overlay-wrap' );

			imgAfter += '<div ' + view.getRenderAttributeString( 'overlay' ) + '></div>';
		}

		var linkUrl = '';

		if ( 'custom' === settings.link_to ) {
			linkUrl = settings.link.url;
		} else if ( 'post' === settings.link_to && settings.post_url ) {
			linkUrl = settings.post_url;
		} else if ( 'file' === settings.link_to ) {
			linkUrl = settings.image_url;
		}

		if ( linkUrl ) {
			view.addRenderAttribute( 'link', 'href', linkUrl );
			view.addRenderAttribute( 'link', 'class', 'elementor-clickable' );
			view.addRenderAttribute( 'link', 'data-elementor-open-lightbox', settings.open_lightbox );
			view.addRenderAttribute( 'link', 'aria-label', 'Featured Image' );

			imgBefore += '<a ' + view.getRenderAttributeString( 'link' ) + '>';
			imgAfter += '</a>';
		}

		if ( hasCaption() ) {
			imgAfter += '<figcaption ' + view.getRenderAttributeString( 'figcaption' ) + '>' + getCaption() + '</figcaption>' +
			'</figure>';
		}
		#>
		<div {{{ view.getRenderAttributeString( 'wrapper' ) }}}>
			{{{ imgBefore }}}
				<img {{{ view.getRenderAttributeString( 'img' ) }}} />
			{{{ imgAfter }}}
		</div>
		<?php
	}
}
