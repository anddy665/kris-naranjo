<?php
namespace CmsmastersElementor\Modules\ImageScroll\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Image_Scroll extends Base_Widget {

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
		return __( 'Image Scroll', 'cmsmasters-elementor' );
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
		return 'cmsicon-image-scroll';
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
			'scroll',
			'image',
			'roll',
			'preview',
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
	 * @since 1.0.0
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget styles dependencies.
	 */
	public function get_style_depends(): array {
		$style_depends = array(
			'widget-cmsmasters-image-scroll',
		);

		if ( Icons_Manager::is_migration_allowed() ) {
			$style_depends = array_merge( $style_depends, array(
				'elementor-icons-fa-solid',
				'elementor-icons-fa-brands',
				'elementor-icons-fa-regular',
			) );
		}

		return $style_depends;
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
		return array_merge( array(
			'perfect-scrollbar-js',
			'imagesloaded',
		), parent::get_script_depends() );
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
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Added image container styling controls.
	 * Fixed height & caption gap controls. Controls refactoring.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_image_scroll',
			array( 'label' => __( 'General', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::MEDIA,
				'description' => __( 'Please choose your image to scroll.', 'cmsmasters-elementor' ),
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'img_size',
				'default' => 'full',
			)
		);

		$this->add_control(
			'scroll_type',
			array(
				'label' => __( 'Scroll On', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'hover' => __( 'Hover', 'cmsmasters-elementor' ),
					'mouse' => __( 'Mouse Scroll', 'cmsmasters-elementor' ),
				),
				'default' => 'hover',
				'separator' => 'before',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-image-scroll__type-',
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'scroll_direction',
			array(
				'label' => __( 'Direction', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'vertical' => array(
						'title' => __( 'Vertical', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					),
					'horizontal' => array(
						'title' => __( 'Horizontal', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'default' => 'vertical',
				'prefix_class' => 'cmsmasters-image-scroll__',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 1200,
						'step' => 10,
					),
					'em' => array(
						'min' => 5,
						'max' => 100,
					),
					'vh' => array(
						'min' => 5,
						'max' => 100,
					),
					'vw' => array(
						'min' => 5,
						'max' => 100,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'em',
					'vh',
					'vw',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__image-wrapper' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-image-scroll__horizontal .elementor-widget-cmsmasters-image-scroll__image-parent img' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'vertical_position',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'label_block' => false,
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
				'prefix_class' => 'cmsmasters-image-scroll__vertical_align__',
				'condition' => array( 'scroll_direction' => 'horizontal' ),
			)
		);

		$this->add_control(
			'caption_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'scroll_type' => 'hover' ),
			)
		);

		$this->add_control(
			'type_animate',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'ease' => __( 'Ease', 'cmsmasters-elementor' ),
					'ease-in' => __( 'Ease In', 'cmsmasters-elementor' ),
					'ease-out' => __( 'Ease Out', 'cmsmasters-elementor' ),
					'ease-in-out' => __( 'Ease In Out', 'cmsmasters-elementor' ),
					'linear' => __( 'Linear', 'cmsmasters-elementor' ),
				),
				'default' => 'ease',
				'render_type' => 'ui',
				'prefix_class' => 'cmsmasters-image-scroll__',
				'condition' => array( 'scroll_type' => 'hover' ),
			)
		);

		$this->add_responsive_control(
			'speed',
			array(
				'label' => __( 'Speed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 1 ),
				'range' => array(
					'px' => array(
						'min' => 0.1,
						'max' => 10,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__wrapper img' => 'transition-duration: {{SIZE}}s',
				),
				'condition' => array( 'scroll_type' => 'hover' ),
			)
		);

		$this->add_control(
			'link_heading',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'link_type',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'disabled' => array(
						'title' => __( 'Disabled', 'cmsmasters-elementor' ),
						'description' => __( 'Link Disabled.', 'cmsmasters-elementor' ),
					),
					'url' => array(
						'title' => __( 'Custom URL', 'cmsmasters-elementor' ),
						'description' => __( 'Link to Custom URL.', 'cmsmasters-elementor' ),
					),
					'lightbox' => array(
						'title' => __( 'Lightbox', 'cmsmasters-elementor' ),
						'description' => __( 'Open Image in Lightbox.', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'disabled',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-image-scroll__',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'image_url',
			array(
				'label' => __( 'Url', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'dynamic' => array( 'active' => true ),
				'condition' => array( 'link_type' => 'url' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'img_size_lightbox',
				'default' => 'full',
				'condition' => array( 'link_type' => 'lightbox' ),
			)
		);

		$this->add_control(
			'caption_heading',
			array(
				'label' => __( 'Caption', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'caption',
			array(
				'label' => __( 'Caption', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
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
				'prefix_class' => 'cmsmasters-image-scroll__align-',
				'condition' => array( 'caption!' => '' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_scroll_icon',
			array( 'label' => __( 'Overlay', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'overlay_color',
			array(
				'label' => __( 'Overlay Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_hover',
			array(
				'label' => __( 'Overlay Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}:hover .elementor-widget-cmsmasters-image-scroll__overlay' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hide_overlay',
			array(
				'label' => __( 'Hide Overlay on Mouseover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-image-scroll__overlay-',
			)
		);

		$this->add_control(
			'label_heading',
			array(
				'label' => __( 'Label', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_label',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'Disabled', 'cmsmasters-elementor' ),
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'text' => __( 'Text', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'toggle' => false,
				'render_type' => 'template',
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
				'condition' => array( 'icon_label' => 'icon' ),
			)
		);

		$this->add_control(
			'text',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'condition' => array( 'icon_label' => 'text' ),
				'default' => 'Label',
			)
		);

		$this->add_control(
			'hide_label',
			array(
				'label' => __( 'Hide Label on Mouseover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-image-scroll__label-',
				'condition' => array(
					'icon_label!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'label_position_h',
			array(
				'label' => __( 'Horizontal Position (%)', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'max' => 100,
						'step' => 0.1,
					),
				),
				'default' => array( 'unit' => '%' ),
				'size_unit' => array( '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__label' => 'left: {{SIZE}}%; transform: translate( -{{SIZE}}%, -{{label_position_v.SIZE}}% );',
				),
				'separator' => 'before',
				'condition' => array(
					'icon_label!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'label_position_v',
			array(
				'label' => __( 'Vertical Position (%)', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'%' => array(
						'max' => 100,
						'step' => 0.1,
					),
				),
				'default' => array( 'unit' => '%' ),
				'size_unit' => array( '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__label' => 'top: {{SIZE}}%; transform: translate( -{{label_position_h.SIZE}}%, -{{SIZE}}%);',
				),
				'condition' => array(
					'icon_label!' => 'none',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_container_style',
			array(
				'label' => __( 'Image Container', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'container_background',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'container_border',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'container_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__image-wrapper' => 'border-radius: calc({{TOP}}{{UNIT}} - {{container_border_width.TOP}}{{container_border_width.UNIT}} - {{container_padding.TOP}}{{container_padding.UNIT}}) calc({{RIGHT}}{{UNIT}} - {{container_border_width.RIGHT}}{{container_border_width.UNIT}} - {{container_padding.RIGHT}}{{container_padding.UNIT}}) calc({{BOTTOM}}{{UNIT}} - {{container_border_width.BOTTOM}}{{container_border_width.UNIT}} - {{container_padding.BOTTOM}}{{container_padding.UNIT}}) calc({{BOTTOM}}{{UNIT}} - {{container_border_width.BOTTOM}}{{container_border_width.UNIT}} - {{container_padding.LEFT}}{{container_padding.UNIT}});',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner',
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'image_border_radius',
			array(
				'label' => __( 'Image Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__image-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'container_border_radius!' => '' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_style',
			array(
				'label' => __( 'Overlay Label', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'icon_label!' => 'none',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_label',
				'label' => __( 'Label Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner .elementor-widget-cmsmasters-image-scroll__label .elementor-widget-cmsmasters-image-scroll__label-text',
				'condition' => array(
					'icon_label' => array( 'text' ),
				),
			)
		);

		$this->add_responsive_control(
			'size_icon',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 14 ),
				'range' => array(
					'px' => array(
						'max' => 100,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner .elementor-widget-cmsmasters-image-scroll__label i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner .elementor-widget-cmsmasters-image-scroll__label svg' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'icon_label' => array( 'icon' ),
				),
			)
		);

		$this->start_controls_tabs( 'label_tabs' );

		$colors = array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		);

		foreach ( $colors as $key => $label ) {
			$state = ( 'hover' === $key ) ? ':hover' : '';
			$selector = "{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner{$state} .elementor-widget-cmsmasters-image-scroll__label";

			$this->start_controls_tab(
				"label_tab_{$key}",
				array(
					'label' => $label,
				)
			);

			$this->add_control(
				"color_label_{$key}",
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'render_type' => 'ui',
					'selectors' => array(
						$selector => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"label_bg_{$key}",
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'render_type' => 'ui',
					'selectors' => array(
						$selector => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				"label_bd_color_{$key}",
				array(
					'label' => __( 'Border Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'render_type' => 'ui',
					'selectors' => array(
						$selector => 'border-color: {{VALUE}};',
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

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_label',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner .elementor-widget-cmsmasters-image-scroll__label',
				'exclude' => array( 'color' ),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'label_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner .elementor-widget-cmsmasters-image-scroll__label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'label_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__inner .elementor-widget-cmsmasters-image-scroll__label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_caption_style',
			array(
				'label' => __( 'Caption', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'caption!' => '',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_caption',
				'label' => __( 'Typography', 'cmsmasters-elementor' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__caption',
			)
		);

		$this->add_control(
			'caption_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'caption_bgc',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__caption' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_caption',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__caption',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'caption_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'caption_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 300,
					),
					'em' => array(
						'max' => 10,
						'step' => 0.1,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'em',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-image-scroll__caption' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render image scroll widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'inner', 'class', 'elementor-widget-cmsmasters-image-scroll__inner' );

		$tag = 'a';
		$attachment_id = $settings['image']['id'];
		$image['alt'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		$image_alt = ( ! empty( $image['alt'] ) ? esc_attr( $image['alt'] ) : 'Image scroll' );

		if ( 'lightbox' === $settings['link_type'] ) {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( $settings['image']['id'], 'img_size_lightbox', $settings );

			$image_lightbox = ( '' === $settings['image']['id'] ) ? $settings['image']['url'] : $image_src;

			$this->add_render_attribute( 'inner', array(
				'href' => esc_url( $image_lightbox ),
				'data-elementor-open-lightbox' => 'yes',
			) );

			$this->add_render_attribute( 'inner', 'aria-label', esc_attr( $image_alt ) );
		} elseif ( 'url' === $settings['link_type'] ) {
			$this->add_link_attributes( 'inner', $settings['image_url'] );

			$this->add_render_attribute( 'inner', 'aria-label', esc_attr( $image_alt ) );
		} else {
			$tag = 'div';
		}

		$image_tag = Group_Control_Image_Size::get_attachment_image_html( $settings, 'img_size', 'image' );

		echo '<figure class="elementor-widget-cmsmasters-image-scroll__wrapper">
			<div class="elementor-widget-cmsmasters-image-scroll__outer">
				<' . $tag . ' ' . $this->get_render_attribute_string( 'inner' ) . '>
					<div class="elementor-widget-cmsmasters-image-scroll__image-wrapper">
						<div class="elementor-widget-cmsmasters-image-scroll__image-parent">' .
						$image_tag;

						if ( '' !== $settings['overlay_color'] ) {
							echo '<span class="elementor-widget-cmsmasters-image-scroll__overlay"></span>';
						}

						echo '</div>
					</div>';

					if ( 'none' !== $settings['icon_label'] ) {
						echo '<span class="elementor-widget-cmsmasters-image-scroll__label">';

						if ( 'icon' === $settings['icon_label'] ) {
							echo '<span class="elementor-widget-cmsmasters-image-scroll__label-icon">';
								Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );
							echo '</span>';
						}

						if ( 'text' === $settings['icon_label'] ) {
							echo '<span class="elementor-widget-cmsmasters-image-scroll__label-text">' . esc_html( $settings['text'] ) . '</span>';
						}

						echo '</span>';
					}

				echo '</' . $tag . '>' .
			'</div>';

			if ( '' !== $settings['caption'] ) {
				echo '<figcaption class="elementor-widget-cmsmasters-image-scroll__caption">' . esc_html( $settings['caption'] ) . '</figcaption>';
			}

		echo '</figure>';
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
			'image_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Image Url', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'caption',
				'type' => esc_html__( 'Caption', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'text',
				'type' => esc_html__( 'text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
