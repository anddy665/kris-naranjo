<?php
namespace CmsmastersElementor\Modules\TemplateSections\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplateSections\Traits\Site_Widget;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Site logo widget.
 *
 * Addon widget that display site logo.
 *
 * @since 1.0.0
 */
class Site_Logo extends Base_Widget {

	use Site_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve logo widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Site Logo', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve logo widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-site-logo';
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
			'logo',
			'header',
			'logotype',
			'sitename',
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
			'widget-cmsmasters-site-logo',
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
	 * Register site logo widget controls.
	 *
	 * Adds different input fields to allow the user to change and
	 * customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Content', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'logo_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'description' => __( 'Show only Image', 'cmsmasters-elementor' ),
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => __( 'Show only Text', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => __( 'Show Text and Image', 'cmsmasters-elementor' ),
					),
				),
				'default' => CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' ),
				'label_block' => false,
				'prefix_class' => 'cmsmasters-logo-type-',
				'render_type' => 'template',
			)
		);

		$this->add_responsive_control(
			'logo_alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
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
				'selectors_dictionary' => array(
					'left' => 'flex-start;',
					'center' => 'center;',
					'right' => 'flex-end;',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--logo-alignment: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logo_image_heading',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'logo_type!' => 'text' ),
			)
		);

		$this->add_control(
			'logo_image_source',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => __( 'Website Logo', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom Image', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'label_block' => false,
				'condition' => array( 'logo_type!' => 'text' ),
			)
		);

		$this->add_control(
			'logo_image_type',
			array(
				'label' => __( 'Image Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'image' => __( 'Image', 'cmsmasters-elementor' ),
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
				),
				'default' => 'image',
				'label_block' => false,
				'render_type' => 'template',
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'logo_image_position',
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
				),
				'default' => 'left',
				'toggle' => false,
				'label_block' => false,
				'prefix_class' => 'cmsmasters-logo-image-position-',
				'render_type' => 'template',
				'condition' => array( 'logo_type' => 'both' ),
			)
		);

		$this->add_control(
			'logo_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_type' => 'image',
					'logo_image_source' => 'custom',
				),
			)
		);

		$this->add_control(
			'logo_image_retina',
			array(
				'label' => esc_html__( 'Retina Logo Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_type' => 'image',
					'logo_image_source' => 'custom',
					'logo_image[id]!' => '',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'logo_image_2x',
			array(
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_type' => 'image',
					'logo_image_retina' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'logo_image_second_toggle',
			array(
				'label' => esc_html__( 'Second Logo Image', 'cmsmasters-elementor' ),
				'description' => sprintf(
					'%1$s <a href="https://docs.cmsmasters.net/mode-switcher/" target="_blank">%2$s</a>.',
					__( 'Image that will be applied when using the', 'cmsmasters-elementor' ),
					__( 'Mode Switcher', 'cmsmasters-elementor' )
				),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_type' => 'image',
					'logo_image_source' => 'custom',
					'logo_image[id]!' => '',
				),
			)
		);

		$this->start_popover();

		$this->add_control(
			'logo_image_second',
			array(
				'label' => esc_html__( 'Second Logo Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_type' => 'image',
					'logo_image_second_toggle' => 'yes',
				),
			)
		);

		$this->add_control(
			'logo_image_2x_second',
			array(
				'label' => esc_html__( 'Second Retina Logo Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_type' => 'image',
					'logo_image_second_toggle' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'logo_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-wordpress',
					'library' => 'fa-brands',
				),
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_source' => 'custom',
					'logo_image_type' => 'icon',
				),
			)
		);

		$site_logo_title_text = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_title_text', '' );

		$this->add_control(
			'logo_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => ( '' !== $site_logo_title_text ? $site_logo_title_text : get_bloginfo( 'name' ) ),
				'label_block' => false,
				'separator' => 'before',
				'condition' => array( 'logo_type!' => 'image' ),
			)
		);

		$this->add_control(
			'logo_subtitle_view',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'separator' => 'before',
				'condition' => array( 'logo_type!' => 'image' ),
			)
		);

		$site_logo_subtitle_text = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_subtitle_text', '' );

		$this->add_control(
			'logo_subtitle',
			array(
				'label' => __( 'Custom Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'show_label' => false,
				'label_block' => true,
				'placeholder' => ( '' !== $site_logo_subtitle_text ? $site_logo_subtitle_text : get_bloginfo( 'description' ) ),
				'condition' => array(
					'logo_type!' => 'image',
					'logo_subtitle_view' => 'true',
				),
			)
		);

		$this->add_control(
			'logo_subtitle_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'inside' => __( 'Inside', 'cmsmasters-elementor' ),
					'outside' => __( 'Outside', 'cmsmasters-elementor' ),
				),
				'default' => 'inside',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-logo-subtitle-position-',
				'render_type' => 'template',
				'condition' => array(
					'logo_type' => 'both',
					'logo_image_position!' => 'top',
					'logo_subtitle_view' => 'true',
				),
			)
		);

		$this->add_control(
			'logo_additional_heading',
			array(
				'label' => __( 'Additional Options', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'logo_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
					),
					'home' => array(
						'title' => __( 'Home', 'cmsmasters-elementor' ),
					),
					'custom' => array(
						'title' => __( 'Custom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'home',
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'logo_custom_url',
			array(
				'label' => __( 'Custom Logo Url', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array( 'logo_link' => 'custom' ),
			)
		);

		$this->add_control(
			'open_in_new_window',
			array(
				'label' => __( 'Open in new window', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'condition' => array( 'logo_link' => 'home' ),
			)
		);

		$this->add_control(
			'add_nofollow',
			array(
				'label' => __( 'Add nofollow', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'false',
				'condition' => array( 'logo_link' => 'home' ),
			)
		);

		$this->add_control(
			'remove_link_on_front',
			array(
				'label' => __( 'Remove Link on Front Page', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => __( 'No', 'cmsmasters-elementor' ),
				'return_value' => 'true',
				'default' => 'false',
				'condition' => array( 'logo_link!' => 'none' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logo_image_style',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'logo_type!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'logo_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon > svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_source' => 'custom',
					'logo_image_type' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'logo_image_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
				),
				'range' => array(
					'%' => array(
						'min' => 15,
						'max' => 100,
					),
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img' => 'width: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'logo_image_source',
							'operator' => '=',
							'value' => 'default',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'logo_image_source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'logo_image_type',
									'operator' => '=',
									'value' => 'image',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'logo_image_max_width',
			array(
				'label' => __( 'Max Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
				),
				'range' => array(
					'%' => array(
						'min' => 15,
						'max' => 100,
					),
					'px' => array(
						'min' => 50,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'logo_image_source',
							'operator' => '=',
							'value' => 'default',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'logo_image_source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'logo_image_type',
									'operator' => '=',
									'value' => 'image',
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'logo_image_icon_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--logo-image-icon-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'logo_type' => 'both' ),
			)
		);

		$this->add_control(
			'logo_image_separator',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->start_controls_tabs( 'logo_image_effects_tabs' );

		$this->start_controls_tab(
			'normal_image_tab',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'logo_image_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon > svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_source' => 'custom',
					'logo_image_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'logo_image_bg_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logo_image_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'logo_image_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'image_box_shadow',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'logo_image_source',
							'operator' => '=',
							'value' => 'default',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'logo_image_source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'logo_image_type',
									'operator' => '=',
									'value' => 'image',
								),
							),
						),
					),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_image_tab',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'logo_image_icon_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__icon > svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'logo_type!' => 'text',
					'logo_image_source' => 'custom',
					'logo_image_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'logo_image_bg_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__icon' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'logo_image_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__icon' => 'border-color: {{VALUE}};',
				),
				'condition' => array( 'logo_image_border_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'image_box_shadow_hover',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__icon',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__image-container img',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'logo_image_source',
							'operator' => '=',
							'value' => 'default',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'logo_image_source',
									'operator' => '=',
									'value' => 'custom',
								),
								array(
									'name' => 'logo_image_type',
									'operator' => '=',
									'value' => 'image',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'background_img_hover_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon > svg' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'logo_image_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'logo_image_border',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon',
				'exclude' => array( 'color' ),
			)
		);

		$this->add_responsive_control(
			'logo_image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__image-container img,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_logo_title_subtitle_style',
			array(
				'label' => __( 'Title and Subtitle', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'logo_type!' => 'image' ),
			)
		);

		$this->add_control(
			'logo_title_vertical_alignment',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => __( 'Middle', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'center',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-logo-title-vertical-alignment-',
				'condition' => array(
					'logo_type' => 'both',
					'logo_image_position!' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'logo_title_alignment',
			array(
				'label' => __( 'Title Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'flex-start' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-center',
					),
					'flex-end' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-text-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__text-wrapper' => 'align-items: {{VALUE}}',
				),
				'condition' => array(
					'logo_subtitle_view' => 'true',
					'logo_subtitle_position!' => 'outside',
				),
			)
		);

		$this->add_control(
			'logo_title_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'logo_type',
									'operator' => '=',
									'value' => 'both',
								),
								array(
									'name' => 'logo_image_position',
									'operator' => '!==',
									'value' => 'top',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'logo_subtitle_view',
									'operator' => '=',
									'value' => 'true',
								),
								array(
									'name' => 'logo_subtitle_position',
									'operator' => '!==',
									'value' => 'outside',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'logo_title_style_heading',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'logo_title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__title,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__title > a',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'logo_title_shadow',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__title,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__title > a',
			)
		);

		$this->start_controls_tabs( 'logo_title_colors_tabs' );

		$this->start_controls_tab(
			'logo_title_normal_tab',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'logo_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__title,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__title > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'logo_title_hover_tab',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'logo_title_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__title,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover  .elementor-widget-cmsmasters-site-logo__title > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'logo_title_hover_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__title' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'logo_subtitle_style_heading',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'logo_subtitle_view' => 'true' ),
			)
		);

		$this->add_control(
			'logo_subtitle_vertical_position',
			array(
				'label' => __( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'default' => 'bottom',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-logo-subtitle-vertical-position-',
				'condition' => array(
					'logo_type' => 'both',
					'logo_image_position!' => 'top',
					'logo_subtitle_view' => 'true',
					'logo_subtitle_position' => 'outside',
				),
			)
		);

		$this->add_responsive_control(
			'logo_subtitle_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array( 'size' => 5 ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle' => 'margin-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.cmsmasters-logo-subtitle-vertical-position-top .elementor-widget-cmsmasters-site-logo__subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0;',
				),
				'condition' => array( 'logo_subtitle_view' => 'true' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'logo_subtitle_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle > a',
				'condition' => array( 'logo_subtitle_view' => 'true' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'logo_subtitle_text_shadow',
				'fields_options' => array(
					'text_shadow_type' => array(
						'label' => __( 'Text Shadow', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle > a',
				'condition' => array( 'logo_subtitle_view' => 'true' ),
			)
		);

		$this->start_controls_tabs(
			'logo_subtitle_colors_tabs',
			array(
				'condition' => array( 'logo_subtitle_view' => 'true' ),
			)
		);

		$this->start_controls_tab(
			'logo_subtitle_normal_tab',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'logo_subtitle_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'logo_subtitle_hover_tab',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'logo_subtitle_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__subtitle,
					{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__container:hover .elementor-widget-cmsmasters-site-logo__subtitle > a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'logo_subtitle_hover_transition',
			array(
				'label' => __( 'Transition Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-site-logo__subtitle' => 'transition-duration: {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 *
	 * Render logo widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$logo_type`, `$logo_subtitle_position`,
	 * and `$logo_image_position` variables. Added check on empty.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		echo '<div class="elementor-widget-cmsmasters-site-logo__container">';

		$widget_type = ( isset( $settings['logo_type'] ) ? $settings['logo_type'] : '' );
		$logo_subtitle_position = ( isset( $settings['logo_subtitle_position'] ) ? $settings['logo_subtitle_position'] : '' );
		$logo_image_position = ( isset( $settings['logo_image_position'] ) ? $settings['logo_image_position'] : '' );

		if ( 'both' === $widget_type && 'outside' === $logo_subtitle_position && 'top' !== $logo_image_position ) {
			echo '<div class="elementor-widget-cmsmasters-site-logo__outside_container">';
		}

		if ( ! empty( $this->get_logo_wrapper() ) ) {
			$this->get_logo_wrapper();
		}

		if ( 'image' !== $widget_type ) {
			$this->get_text_wrapper();
		}

		if ( 'both' === $widget_type && 'outside' === $logo_subtitle_position && 'top' !== $logo_image_position ) {
			echo '</div>';

			if ( 'image' !== $widget_type ) {
				$this->get_logo_subtitle();
			}
		}

		echo '</div>';
	}

	/**
	 * Returns logo
	 *
	 * @return string Image logo HTML markup.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$logo_image_source`, `$logo_image_type`,
	 * and `$logo_image` variables. Added check on empty.
	 * @since 1.2.1 Fix: svg logo.
	 */
	public function get_logo_wrapper() {
		$settings = $this->get_settings_for_display();

		$site_logo_type = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' );
		$widget_image_source = ( isset( $settings['logo_image_source'] ) ? $settings['logo_image_source'] : '' );
		$widget_type = ( isset( $settings['logo_type'] ) ? $settings['logo_type'] : '' );

		if ( 'text' === $widget_type || ( 'default' === $widget_image_source && 'text' === $site_logo_type ) ) {
			return;
		}

		$is_linked = $this->get_is_linked();

		echo '<div class="elementor-widget-cmsmasters-site-logo__image-container">' .
			( $is_linked ? $this->is_linked_start() : '' );

		if ( ! empty( $this->get_logo_image() ) ) {
			$this->get_logo_image();
		}

		$widget_image_type = ( isset( $settings['logo_image_type'] ) ? $settings['logo_image_type'] : '' );

		if ( 'icon' === $widget_image_type && ! empty( $settings['logo_icon'] ) ) {
			echo '<span class="elementor-widget-cmsmasters-site-logo__icon">';
				Icons_Manager::render_icon( $settings['logo_icon'], array( 'aria-hidden' => 'true' ) );
			echo '</span>';
		}

			echo ( $is_linked ? '</a>' : '' ) .
		'</div>';
	}

	/**
	 * Get logo
	 *
	 * @return string Get Logo
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$logo_image_source`, `$site_logo_url`,
	 * `$logo_image_type` and `$logo_image` variables. Added check on empty.
	 * @since 1.2.1 Fix: svg logo.
	 * @since 1.3.5 Fixed logo image by default.
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_logo_image() {
		$settings = $this->get_settings_for_display();

		$widget_image_type = ( isset( $settings['logo_image_type'] ) ? $settings['logo_image_type'] : '' );

		if ( 'icon' === $widget_image_type ) {
			return;
		}

		// Render Image or Icon Logo
		$site_logo_title = ( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : esc_html__( 'Site logo', 'cmsmasters-elementor' ) );
		$logo_out = '';

		if ( ! empty( $this->get_img_logo_retina_id( 'second' ) ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-site-logo',
				'id' => $this->get_img_logo_retina_id( 'second' ),
				'title' => $site_logo_title,
				'type' => 'retina',
				'state' => 'second',
			) );
		}

		if ( ! empty( $this->get_img_logo_id( 'second' ) ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-site-logo',
				'id' => $this->get_img_logo_id( 'second' ),
				'title' => $site_logo_title,
				'type' => 'normal',
				'state' => 'second',
			) );
		}

		if ( ! empty( $this->get_img_logo_retina_id() ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-site-logo',
				'id' => $this->get_img_logo_retina_id(),
				'title' => $site_logo_title,
				'type' => 'retina',
			) );
		}

		if ( ! empty( $this->get_img_logo_id() ) ) {
			$logo_out .= $this->get_logo_img( array(
				'parent_class' => 'elementor-widget-cmsmasters-site-logo',
				'id' => $this->get_img_logo_id(),
				'title' => $site_logo_title,
				'type' => 'normal',
			) );
		}

		if ( empty( $logo_out ) ) {
			$logo_out = '<img' .
				' class="elementor-widget-cmsmasters-site-logo__img"' .
				' src="' . get_parent_theme_file_uri( 'theme-config/images/logo.svg' ) . '"' .
				' alt="' . esc_attr( $site_logo_title ) . '" />';
		}

		Utils::print_unescaped_internal_string( $logo_out );
	}

	/**
	 * Get logo image id.
	 *
	 * @param string $state Main/second state.
	 *
	 * @return string Logo image id.
	 *
	 * @since 1.2.1
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_img_logo_id( $state = 'main' ) {
		$settings = $this->get_settings_for_display();

		$suffix = ( 'main' !== $state ? "_{$state}" : '' );

		$site_logo_type = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' );
		$site_logo = CmsmastersUtils::get_kit_option( "cmsmasters_logo_image{$suffix}", array( 'id' => '' ) );

		$widget_image_source = ( isset( $settings['logo_image_source'] ) ? $settings['logo_image_source'] : '' );
		$widget_type = ( isset( $settings['logo_image_type'] ) ? $settings['logo_image_type'] : '' );
		$widget_image = ( isset( $settings[ "logo_image{$suffix}" ] ) ? $settings[ "logo_image{$suffix}" ] : '' );

		$logo = '';

		// Get Logo URL
		if ( 'default' === $widget_image_source && 'image' === $site_logo_type && ! empty( $site_logo['id'] ) ) {
			$logo = $site_logo;
		}

		if ( 'custom' === $widget_image_source && 'image' === $widget_type ) {
			if ( ! empty( $widget_image['id'] ) ) {
				$logo = $widget_image;
			}

			if ( 'main' === $state && empty( $widget_image['id'] ) && 'image' === $site_logo_type && ! empty( $site_logo['id'] ) ) {
				$logo = $site_logo;
			}
		}

		return ( ! empty( $logo ) ? $logo['id'] : '' );
	}

	/**
	 * Get logo image retina id.
	 *
	 * @param string $state Main/second state.
	 *
	 * @return string Logo image retina id.
	 *
	 * @since 1.2.1
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_img_logo_retina_id( $state = 'main' ) {
		$settings = $this->get_settings_for_display();

		$suffix = ( 'main' !== $state ? "_{$state}" : '' );

		$site_logo_type = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_type', 'image' );
		$site_logo = CmsmastersUtils::get_kit_option( "cmsmasters_logo_image{$suffix}", array( 'id' => '' ) );
		$site_logo_retina_toggle = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_retina_toggle' );
		$site_logo_retina_toggle = ( 'main' !== $state ? 'yes' : $site_logo_retina_toggle );
		$site_logo_second_toggle = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_second_toggle' );
		$site_logo_second_toggle = ( 'main' !== $state ? $site_logo_second_toggle : 'yes' );
		$site_logo_retina = CmsmastersUtils::get_kit_option( "cmsmasters_logo_retina_image{$suffix}", array( 'id' => '' ) );

		$widget_image_source = ( isset( $settings['logo_image_source'] ) ? $settings['logo_image_source'] : '' );
		$widget_type = ( isset( $settings['logo_image_type'] ) ? $settings['logo_image_type'] : '' );
		$widget_image = ( isset( $settings['logo_image'] ) ? $settings['logo_image'] : '' );
		$widget_logo_retina_toggle = ( isset( $settings['logo_image_retina'] ) ? $settings['logo_image_retina'] : '' );
		$widget_logo_retina_toggle = ( 'main' !== $state ? 'yes' : $widget_logo_retina_toggle );
		$widget_logo_second_toggle = ( isset( $settings['logo_image_second_toggle'] ) ? $settings['logo_image_second_toggle'] : '' );
		$widget_logo_second_toggle = ( 'main' !== $state ? $widget_logo_second_toggle : 'yes' );
		$widget_logo_retina = ( isset( $settings[ "logo_image_2x{$suffix}" ] ) ? $settings[ "logo_image_2x{$suffix}" ] : '' );

		$logo_retina = '';

		// Get Logo URL
		if (
			'default' === $widget_image_source &&
			'image' === $site_logo_type &&
			! empty( $site_logo['id'] ) &&
			'yes' === $site_logo_retina_toggle &&
			'yes' === $site_logo_second_toggle &&
			! empty( $site_logo_retina['id'] )
		) {
			$logo_retina = $site_logo_retina;
		}

		if ( 'custom' === $widget_image_source && 'image' === $widget_type ) {
			if (
				! empty( $widget_image['id'] ) &&
				'yes' === $widget_logo_retina_toggle &&
				'yes' === $widget_logo_second_toggle &&
				! empty( $widget_logo_retina['id'] )
			) {
				$logo_retina = $widget_logo_retina;
			}

			if (
				empty( $widget_image['id'] ) &&
				'image' === $site_logo_type &&
				! empty( $site_logo['id'] ) &&
				'yes' === $site_logo_retina_toggle &&
				'yes' === $site_logo_second_toggle &&
				! empty( $site_logo_retina )
			) {
				$logo_retina = $site_logo_retina;
			}
		}

		return ( ! empty( $logo_retina ) ? $logo_retina['id'] : '' );
	}

	/**
	 * Get logo image.
	 *
	 * @param array $atts Array of attributes.
	 *
	 * @return string Logo image html.
	 *
	 * @since 1.2.1
	 * @since 1.2.2 Fix for Site Logo image sizes.
	 * @since 1.3.5 Fixed logo image by default.
	 * @since 1.10.0 Add second logo functionality for mode switcher.
	 */
	public function get_logo_img( $atts = array(), $type = 'normal' ) {
		$req_vars = array(
			'parent_class' => '',
			'id' => '',
			'title' => '',
			'type' => $type,
			'state' => 'main',
		);

		foreach ( $req_vars as $var_key => $var_value ) {
			if ( array_key_exists( $var_key, $atts ) ) {
				$$var_key = $atts[ $var_key ];
			} else {
				$$var_key = $var_value;
			}
		}

		if ( empty( $id ) || empty( $parent_class ) ) {
			return '';
		}

		$img_data = wp_get_attachment_image_src( $id, 'full' );

		if ( empty( $img_data ) ) {
			return '';
		}

		$img_atts = array(
			'src="' . $img_data[0] . '"',
			'alt="' . $title . '"',
			'title="' . $title . '"',
		);

		if ( 'retina' === $type ) {
			$img_atts[] = 'width="' . round( intval( $img_data[1] ) / 2 ) . '"';
			$img_atts[] = 'height="' . round( intval( $img_data[2] ) / 2 ) . '"';
			$img_atts[] = 'class="' . esc_attr( "{$parent_class}__retina-img {$parent_class}-{$state}" ) . '"';
		} else {
			$img_atts[] = 'class="' . esc_attr( "{$parent_class}__img {$parent_class}-{$state}" ) . '"';
		}

		return '<img ' . implode( ' ', $img_atts ) . '/>';
	}

	/**
	 * Check if logo is linked.
	 *
	 * @return bool
	 */
	public function get_is_linked() {
		$settings = $this->get_settings_for_display();

		if ( 'none' === $settings['logo_link'] ) {
			return false;
		}

		if ( 'true' === $settings['remove_link_on_front'] && is_front_page() ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if logo is linked.
	 *
	 * @return bool
	 * @since 1.1.0 Added check on empty $logo_custom_url variable for logo.
	 */
	public function is_linked_start() {
		$settings = $this->get_settings_for_display();

		$link = '';
		$logo_link = isset( $settings['logo_link'] ) ? esc_attr( $settings['logo_link'] ) : 'none';

		$widget_type = ( isset( $settings['logo_type'] ) ? $settings['logo_type'] : '' );
		$widget_image_source = ( isset( $settings['logo_image_source'] ) ? $settings['logo_image_source'] : '' );
		$widget_image_type = ( isset( $settings['logo_image_type'] ) ? $settings['logo_image_type'] : '' );
		$aria_label = '';

		if (
			'image' === $widget_type &&
			'custom' === $widget_image_source &&
			'icon' === $widget_image_type &&
			! empty( $settings['logo_icon'] )
		) {
			$aria_label = ' aria-label="Logo"';
		}

		if ( 'home' === $logo_link ) {
			$add_nofollow = isset( $settings['add_nofollow'] ) ? $settings['add_nofollow'] : false;
			$open_in_new_window = isset( $settings['open_in_new_window'] ) ? $settings['open_in_new_window'] : true;

			$link .= '<a' .
				' href="' . home_url() . '"' .
				' class="elementor-widget-cmsmasters-site-logo__link"' .
				( $open_in_new_window ? ' target="_blank"' : '' ) .
				( $add_nofollow ? ' rel="nofollow"' : '' ) .
					$aria_label .
			'>';
		} elseif ( 'custom' === $logo_link ) {
			$logo_custom_url = isset( $settings['logo_custom_url']['url'] ) ? esc_attr( $settings['logo_custom_url']['url'] ) : '';

			if ( '' !== $logo_custom_url ) {
				$link .= '<a' .
					' href="' . $logo_custom_url . '"' .
					' class="elementor-widget-cmsmasters-site-logo__link"' .
					( $settings['logo_custom_url']['is_external'] ? ' target="_blank"' : '' ) .
					( $settings['logo_custom_url']['nofollow'] ? ' rel="nofollow"' : '' ) .
					$aria_label .
				'>';
			}
		}

		return $link;
	}

	/**
	 * Returns logo text
	 *
	 * @return string Text logo HTML markup.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$logo_type` variable. Added check on empty.
	 */
	public function get_text_wrapper() {
		$settings = $this->get_settings_for_display();

		$logo_type = ( isset( $settings['logo_type'] ) ? $settings['logo_type'] : '' );

		echo '<div class="elementor-widget-cmsmasters-site-logo__text-wrapper">';

			$this->get_logo_title();

		if ( 'text' === $logo_type || ( 'both' === $logo_type && 'outside' !== $settings['logo_subtitle_position'] ) ) {
			$this->get_logo_subtitle();
		}

		echo '</div>';
	}

	/**
	 * Returns logo title
	 *
	 * @return string Text logo HTML markup.
	 *
	 * @since 1.2.1 Fix: Changed h1 tag to div.
	 */
	public function get_logo_title() {
		$is_linked = $this->get_is_linked();

		echo '<div class="elementor-widget-cmsmasters-site-logo__title-container">' .
			'<div class="elementor-widget-cmsmasters-site-logo__title">' .
				( $is_linked ? $this->is_linked_start() : '' ) .
					$this->get_logo_title_text() .
				( $is_linked ? '</a>' : '' ) .
			'</div>' .
		'</div>';
	}

	/**
	 * Returns logo text
	 *
	 * @return string Text logo HTML markup.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$logo_type` and `$logo_title` variables. Added check on empty.
	 */
	public function get_logo_title_text() {
		$settings = $this->get_settings_for_display();

		$logo_type = ( isset( $settings['logo_type'] ) ? $settings['logo_type'] : '' );
		$title = get_bloginfo( 'name' );
		$logo_title = ( isset( $settings['logo_title'] ) ? $settings['logo_title'] : '' );
		$site_logo_title_text = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_title_text', '' );

		if ( 'image' !== $logo_type ) {
			if ( 'image' !== $logo_type && '' !== $logo_title ) {
				$title = wp_kses_post( $logo_title );
			}

			if ( '' === $logo_title && '' !== $site_logo_title_text ) {
				$title = wp_kses_post( $site_logo_title_text );
			}
		}

		return $title;
	}

	/**
	 * Returns logo text
	 *
	 * @return string Text logo HTML markup.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$subtitle_from` and `$logo_subtitle` variables,
	 * logo subtitle output. Added check on empty.
	 */
	public function get_logo_subtitle() {
		$settings = $this->get_settings_for_display();

		$subtitle_from = ( isset( $settings['logo_subtitle_view'] ) ? $settings['logo_subtitle_view'] : '' );
		$logo_type = ( isset( $settings['logo_type'] ) ? $settings['logo_type'] : '' );

		if (
			'image' === $logo_type ||
			( 'image' !== $logo_type && 'true' !== $subtitle_from )
		) {
			return;
		}

		$logo_subtitle = ( isset( $settings['logo_subtitle'] ) ? $settings['logo_subtitle'] : '' );
		$site_logo_subtitle_text = CmsmastersUtils::get_kit_option( 'cmsmasters_logo_subtitle_text', '' );
		$subtitle = get_bloginfo( 'description' );

		if ( '' !== $logo_subtitle ) {
			$subtitle = $logo_subtitle;
		}

		if ( '' === $logo_subtitle && '' !== $site_logo_subtitle_text ) {
			$subtitle = $site_logo_subtitle_text;
		}

		$is_linked = $this->get_is_linked();

		echo '<div class="elementor-widget-cmsmasters-site-logo__subtitle-container">' .
			'<div class="elementor-widget-cmsmasters-site-logo__subtitle">' .
				( $is_linked ? $this->is_linked_start() : '' ) .
					wp_kses_post( $subtitle ) .
				( $is_linked ? '</a>' : '' ) .
			'</div>' .
		'</div>';
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
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'logo_title',
				'type' => esc_html__( 'Logo Title', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'logo_subtitle',
				'type' => esc_html__( 'Custom Logo Subtitle', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			'logo_custom_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Custom Logo Url', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
		);
	}
}
