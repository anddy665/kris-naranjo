<?php
namespace CmsmastersElementor\Modules\TemplatePages\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplatePages\Traits\Singular_Widget;
use CmsmastersElementor\Modules\Settings\Kit_Globals;
use CmsmastersElementor\Modules\Slider\Classes\Slider;
use CmsmastersElementor\Plugin as CmsmastersPlugin;

use Elementor\Controls_Manager;
use Elementor\Embed;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Elementor Post Media widget.
 *
 * Elementor widget that displays vertical or horizontal test with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Post_Media extends Base_Widget {

	use Singular_Widget;

	protected $slider;

	/**
	 * Get widget title.
	 *
	 * Retrieve test widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Media', 'cmsmasters-elementor' );
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
		return 'cmsicon-post-media';
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
			'image',
			'gallery',
			'video',
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
			'widget-cmsmasters-post-media',
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
	 *
	 * Initializing the `post media` widget class.
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

		$this->slider = new Slider( $this );
	}

	/**
	 * Register Post Media widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Commented '__dynamic__[empty_image_placeholder]' condition that caused error.
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 */
	protected function register_controls() {
		// Standard
		$this->start_controls_section(
			'section_standard_image',
			array( 'label' => __( 'Standard', 'cmsmasters-elementor' ) )
		);

		$this->get_featured_image_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_standard_image',
			array(
				'label' => __( 'Standard', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->get_featured_image_style_controls();

		$this->end_controls_section();

		// Image
		$this->start_controls_section(
			'section_image',
			array( 'label' => __( 'Image', 'cmsmasters-elementor' ) )
		);

		$this->get_featured_image_controls( 'image' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->get_featured_image_style_controls( 'image' );

		$this->end_controls_section();

		// Gallery
		$this->start_controls_section(
			'section_gallery_slider',
			array( 'label' => __( 'Gallery Slider', 'cmsmasters-elementor' ) )
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'slider_image',
				'default' => 'large',
				'separator' => 'none',
			)
		);

		$this->add_control(
			'gallery_link_to',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'file' => __( 'Media File', 'cmsmasters-elementor' ),
					'post' => __( 'Post URL', 'cmsmasters-elementor' ),
				),
			)
		);

		$this->add_control(
			'gallery_open_lightbox',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'yes' => __( 'Yes', 'cmsmasters-elementor' ),
					'no' => __( 'No', 'cmsmasters-elementor' ),
				),
				'condition' => array( 'gallery_link_to' => 'file' ),
			)
		);

		$this->add_control(
			'slider_image_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			)
		);

		$this->slider->register_controls_content_per_view();

		$this->start_injection( array(
			'of' => 'slider_image_size',
		) );

		$slider_type_attr = $this->get_controls_attr( 'slider_type' );

		$this->add_control(
			'slider_type',
			$slider_type_attr
		);

		$slider_per_view_attr = $this->get_controls_attr( 'slider_per_view' );

		$this->add_control(
			'slider_per_view',
			$slider_per_view_attr
		);

		$this->update_responsive_control(
			'slider_per_view',
			array(
				'default' => '1',
				'tablet_default' => '1',
			)
		);

		$this->end_injection();

		$this->end_controls_section();

		$this->slider->register_section_content();

		$this->update_control(
			'slider_arrows',
			array(
				'default' => 'yes',
			)
		);

		$this->slider->register_sections_style();

		// Video
		$this->start_controls_section(
			'section_video',
			array( 'label' => __( 'Video', 'cmsmasters-elementor' ) )
		);

			$this->add_control(
				'empty_image_placeholder',
				array(
					'label' => __( 'Image Placeholder', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => array(
						'active' => true,
					),
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name' => 'image_placeholder',
					'default' => 'full',
					'separator' => 'none',
					'conditions' => array(
						'relation' => 'or',
						'terms' => array(
							array(
								'name' => 'empty_image_placeholder[id]',
								'operator' => '!==',
								'value' => '',
							),
						),
					),
				)
			);

			$this->add_control(
				'show_play_icon',
				array(
					'label' => __( 'Play Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'separator' => 'before',
					'conditions' => array(
						'relation' => 'or',
						'terms' => array(
							array(
								'name' => 'empty_image_placeholder[id]',
								'operator' => '!==',
								'value' => '',
							),
						),
					),
				)
			);

			$this->add_control(
				'play_icon',
				array(
					'type' => Controls_Manager::ICONS,
					'label' => esc_html__( 'Icon for Placeholder', 'cmsmasters-elementor' ),
					'fa4compatibility' => 'icon',
					'default' => array(
						'value' => 'far fa-play-circle',
						'library' => 'fa-regular',
					),
					'recommended' => array(
						'fa-regular' => array( 'play-circle' ),
						'fa-solid' => array(
							'play',
							'play-circle',
							'video',
						),
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => 'empty_image_placeholder[id]',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
							array(
								'name' => 'show_play_icon',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'lightbox',
				array(
					'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'frontend_available' => true,
					'label_off' => __( 'Off', 'cmsmasters-elementor' ),
					'label_on' => __( 'On', 'cmsmasters-elementor' ),
					'separator' => 'before',
					'conditions' => array(
						'relation' => 'or',
						'terms' => array(
							array(
								'name' => 'empty_image_placeholder[id]',
								'operator' => '!==',
								'value' => '',
							),
						),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_style',
			array(
				'label' => __( 'Video', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_control(
				'aspect_ratio',
				array(
					'label' => __( 'Aspect Ratio', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => array(
						'169' => '16:9',
						'219' => '21:9',
						'43' => '4:3',
						'32' => '3:2',
						'31' => '3:1',
						'11' => '1:1',
						'916' => '9:16',
						'custom' => 'Custom',
					),
					'default' => '169',
					'prefix_class' => 'elementor-aspect-ratio-',
					'frontend_available' => true,
				)
			);

			$this->add_responsive_control(
				'aspect_ratio_custom_height',
				array(
					'label' => esc_html__( 'Height', 'cmsmasters-elementor' ),
					'label_block' => true,
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%', 'vw', 'vh' ),
					'range' => array(
						'%' => array(
							'min' => 25,
							'max' => 150,
						),
					),
					'default' => array( 'unit' => '%' ),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__wrap.elementor-fit-aspect-ratio' => 'padding-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition' => array( 'aspect_ratio' => 'custom' ),
				)
			);

			$this->add_responsive_control(
				'video_width',
				array(
					'label' => __( 'Width', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array(
						'unit' => '%',
						'size' => '100',
					),
					'size_units' => array( 'px', '%', 'vw', 'vh' ),
					'range' => array(
						'px' => array(
							'min' => 200,
						),
						'%' => array( 'min' => 30 ),
					),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__container' => 'width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'video_position',
				array(
					'label' => __( 'Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'default' => 'center',
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
					'toggle' => false,
					'selectors_dictionary' => array(
						'left' => 'float: left;',
						'center' => 'margin: 0 auto;',
						'right' => 'float: right;',
					),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__container ' => '{{VALUE}}',
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_placeholder_style',
			array(
				'label' => __( 'Video Placeholder', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'empty_image_placeholder[id]',
							'operator' => '!==',
							'value' => '',
						),
					),
				),
			)
		);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				array(
					'name' => 'css_filters',
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-post-media__image-placeholder',
				)
			);

			$this->add_control(
				'play_icon_title',
				array(
					'label' => __( 'Play Icon', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => 'empty_image_placeholder[id]',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
							array(
								'name' => 'show_play_icon',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'play_icon_effect',
				array(
					'label' => __( 'Disable blinking effect', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => 'empty_image_placeholder[id]',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
							array(
								'name' => 'show_play_icon',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'play_icon_color',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__play-icon i:before' => 'color: {{VALUE}}',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => 'empty_image_placeholder[id]',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
							array(
								'name' => 'show_play_icon',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'play_icon_color_hover',
				array(
					'label' => __( 'Color on Hover', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__inner:hover .elementor-widget-cmsmasters-post-media__play-icon i:before' => 'color: {{VALUE}}',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => 'empty_image_placeholder[id]',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
							array(
								'name' => 'show_play_icon',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'play_icon_size',
				array(
					'label' => __( 'Size', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array( 'size' => '100' ),
					'range' => array(
						'px' => array(
							'min' => 10,
							'max' => 300,
						),
					),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__play-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__wrap .elementor-widget-cmsmasters-post-media__play-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => 'empty_image_placeholder[id]',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
							array(
								'name' => 'show_play_icon',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				)
			);

			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(),
				array(
					'name' => 'play_icon_text_shadow',
					'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-post-media__play-icon i:before',
					'fields_options' => array(
						'text_shadow_type' => array(
							'label' => _x( 'Text Shadow', 'Text Shadow Group Color', 'cmsmasters-elementor' ),
						),
					),
					'conditions' => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'relation' => 'or',
								'terms' => array(
									array(
										'name' => 'empty_image_placeholder[id]',
										'operator' => '!==',
										'value' => '',
									),
								),
							),
							array(
								'name' => 'show_play_icon',
								'operator' => '=',
								'value' => 'yes',
							),
						),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'empty_image_placeholder[id]',
									'operator' => '!==',
									'value' => '',
								),
							),
						),
						array(
							'name' => 'lightbox',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

			$this->add_control(
				'lightbox_color',
				array(
					'label' => __( 'Background Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'#elementor-lightbox-{{ID}}' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'lightbox_ui_color',
				array(
					'label' => __( 'UI Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => array(
						'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'lightbox_ui_color_hover',
				array(
					'label' => __( 'UI Hover Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
					'separator' => 'after',
					'selectors' => array(
						'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button:hover' => 'color: {{VALUE}}',
					),
				)
			);

			$this->add_control(
				'lightbox_video_width',
				array(
					'label' => __( 'Content Width', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => array( 'unit' => '%' ),
					'range' => array( '%' => array( 'min' => 30 ) ),
					'selectors' => array(
						'(desktop+)#elementor-lightbox-{{ID}} .elementor-video-container' => 'width: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'lightbox_content_position',
				array(
					'label' => __( 'Content Position', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => array(
						'' => array(
							'title' => __( 'Center', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-bottom',
						),
						'top' => array(
							'title' => __( 'Top', 'cmsmasters-elementor' ),
							'icon' => 'eicon-v-align-top',
						),
					),
					'frontend_available' => true,
					'default' => '',
					'toggle' => false,
					'selectors' => array(
						'#elementor-lightbox-{{ID}} .elementor-video-container' => '{{VALUE}}; transform: translateX(-50%);',
					),
					'selectors_dictionary' => array( 'top' => 'top: 60px' ),
				)
			);

			$this->add_responsive_control(
				'lightbox_animation_entrance',
				array(
					'label' => __( 'Entrance Animation', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::ANIMATION,
					'frontend_available' => true,
				)
			);

		$this->end_controls_section();

		// Audio
		$this->start_controls_section(
			'section_audio',
			array(
				'label' => __( 'Audio', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'mixcloud_popover',
			array(
				'label' => __( 'Mixcloud', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

			$this->add_control(
				'mx_hide_cover',
				array(
					'label' => __( 'Cover', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __( 'off', 'cmsmasters-elementor' ),
					'label_off' => __( 'on', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				'mx_hide_artwork',
				array(
					'label' => __( 'Artwork', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'condition' => array( 'mx_hide_cover' => 'yes' ),
				)
			);

			$this->add_control(
				'mx_light',
				array(
					'label' => __( 'Skin', 'cmsmasters-elementor' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'dark' => __( 'Dark', 'cmsmasters-elementor' ),
						'light' => __( 'Light', 'cmsmasters-elementor' ),
					),
					'default' => 'dark',
					'toggle' => false,
					'label_block' => false,
					'condition' => array( 'mx_hide_cover' => 'yes' ),
				)
			);

			$this->add_control(
				'mx_mini',
				array(
					'label' => __( 'Mini', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'condition' => array( 'mx_hide_cover' => 'yes' ),
				)
			);

			$this->add_control(
				'mx_hide_tracklist',
				array(
					'label' => __( 'Tracklist', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'condition' => array(
						'mx_hide_cover' => 'yes',
						'mx_mini' => 'yes',
					),
				)
			);

		$this->end_popover();

		$this->add_control(
			'deezer_popover',
			array(
				'label' => __( 'Deezer', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

			$this->add_control(
				'dz_format',
				array(
					'label' => __( 'Format', 'cmsmasters-elementor' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'classic' => __( 'Classic', 'cmsmasters-elementor' ),
						'square' => __( 'Square', 'cmsmasters-elementor' ),
					),
					'default' => 'classic',
					'toggle' => false,
					'label_block' => false,
					'prefix_class' => 'cmsmasters-audio-dz-format-',
					'render_type' => 'template',
				)
			);

			$this->add_control(
				'dz_layout',
				array(
					'label' => __( 'Skin', 'cmsmasters-elementor' ),
					'type' => CmsmastersControls::CHOOSE_TEXT,
					'options' => array(
						'dark' => __( 'Dark', 'cmsmasters-elementor' ),
						'light' => __( 'Light', 'cmsmasters-elementor' ),
					),
					'default' => 'dark',
					'toggle' => false,
					'label_block' => false,
					'condition' => array( 'dz_format' => 'classic' ),
				)
			);

			$this->add_control(
				'dz_playlist',
				array(
					'label' => __( 'Playlist', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'condition' => array( 'dz_format' => 'classic' ),
				)
			);

			$this->add_responsive_control(
				'dz_height',
				array(
					'label' => __( 'Height', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 200,
							'max' => 602,
						),
					),
					'default' => array( 'size' => '350' ),
					'conditions' => array(
						'relation' => 'or',
						'terms' => array(
							array(
								'relation' => 'and',
								'terms' => array(
									array(
										'name' => 'dz_format',
										'operator' => '=',
										'value' => 'classic',
									),
									array(
										'name' => 'dz_playlist',
										'operator' => '=',
										'value' => 'yes',
									),
								),
							),
							array(
								'name' => 'dz_format',
								'operator' => '=',
								'value' => 'square',
							),
						),
					),
					'render_type' => 'template',
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-audio-dz-format-square .elementor-widget-cmsmasters-post-media__iframe' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'dz_square_align',
				array(
					'label' => __( 'Align', 'cmsmasters-elementor' ),
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
						'left' => 'float: left;',
						'center' => 'margin: 0 auto;',
						'right' => 'float: right;',
					),
					'default' => 'center',
					'toggle' => false,
					'selectors' => array(
						'{{WRAPPER}}.cmsmasters-audio-dz-format-square .elementor-widget-cmsmasters-post-media__iframe' => '{{VALUE}}',
					),
					'condition' => array( 'dz_format' => 'square' ),
				)
			);

			$this->add_control(
				'dz_color',
				array(
					'label' => __( 'Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
				)
			);

		$this->end_popover();

		$this->add_control(
			'soundcloud_popover',
			array(
				'label' => __( 'SoundCloud', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

			$this->add_control(
				'sc_visual',
				array(
					'label' => __( 'Visual Player', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'no',
				)
			);

			$this->add_control(
				'sc_show_artwork',
				array(
					'label' => __( 'Artwork', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
					'condition' => array( 'sc_visual' => '' ),
				)
			);

			$this->add_control(
				'sc_show_user',
				array(
					'label' => __( 'Username', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				'sc_show_playcount',
				array(
					'label' => __( 'Play Counts', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				'sc_liking',
				array(
					'label' => __( 'Like Button', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				'sc_sharing',
				array(
					'label' => __( 'Share Button', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				'sc_show_comments',
				array(
					'label' => __( 'Comments', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				'sc_buying',
				array(
					'label' => __( 'Buy Button', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
				)
			);

			$this->add_control(
				'sc_download',
				array(
					'label' => __( 'Download Button', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
					'label_on' => __( 'Show', 'cmsmasters-elementor' ),
					'default' => 'yes',
					'description' => 'When the track has Buy and Download options and both buttons are enabled, then only the Buy button will be displayed',
				)
			);

			$this->add_control(
				'sc_color',
				array(
					'label' => __( 'Controls Color', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::COLOR,
				)
			);

			$this->add_responsive_control(
				'sc_height',
				array(
					'label' => __( 'Height', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 200,
							'max' => 900,
						),
					),
					'description' => 'Important! Height value can be changed for playlist only (not for single tracks)',
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__container iframe' => 'height: {{SIZE}}{{UNIT}};',
					),
				)
			);

		$this->end_popover();

		$this->add_control(
			'spotify_popover',
			array(
				'label' => __( 'Spotify', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			)
		);

		$this->start_popover();

			$this->add_control(
				'sp_visual',
				array(
					'label' => __( 'Visual Player', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'no',
				)
			);

			$this->add_responsive_control(
				'sp_height',
				array(
					'label' => __( 'Height', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range' => array(
						'px' => array(
							'min' => 200,
							'max' => 900,
						),
					),
					'default' => array( 'size' => '350' ),
					'selectors' => array(
						'{{WRAPPER}} .elementor-widget-cmsmasters-post-media__container iframe' => 'height: {{SIZE}}{{UNIT}};',
					),
					'condition' => array( 'audio_type' => 'spotify' ),
				)
			);

		$this->end_popover();

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
		);
	}

	public function get_featured_image_controls( $type = 'standard' ) {
		$dynamic_tags = CmsmastersPlugin::elementor()->dynamic_tags;
		$tag_names = $this->get_tag_names();

		$this->add_control(
			$type . '_id',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['image_id'] ),
				),
			)
		);

		$this->add_control(
			$type . '_url',
			array(
				'type' => Controls_Manager::HIDDEN,
				'dynamic' => array(
					'active' => true,
					'default' => $dynamic_tags->tag_data_to_tag_text( null, $tag_names['image_url'] ),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => $type,
				'default' => 'large',
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			$type . '_align',
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
					'{{WRAPPER}} .cmsmasters-image img' => '{{VALUE}}',
				),
			)
		);

		$this->add_control(
			$type . '_fallback_image_popover',
			array(
				'label' => __( 'Fallback Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'separator' => 'before',
			)
		);

		$this->start_popover();

		$this->add_control(
			$type . '_fallback_image',
			array(
				'label' => __( 'Fallback Image', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => Controls_Manager::MEDIA,
				'condition' => array( $type . '_fallback_image_popover' => 'yes' ),
			)
		);

		$this->end_popover();

		$this->add_control(
			$type . '_link_to',
			array(
				'label' => __( 'Image Link', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array(
						'title' => __( 'None', 'cmsmasters-elementor' ),
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
			$type . '_open_lightbox',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'yes' => __( 'Yes', 'cmsmasters-elementor' ),
					'no' => __( 'No', 'cmsmasters-elementor' ),
				),
				'condition' => array( $type . '_link_to' => 'file' ),
			)
		);

		$this->add_control(
			$type . '_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array( $type . '_link_to' => 'custom' ),
			)
		);

		$this->add_control(
			$type . '_object_fit',
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
			)
		);

		$object_fit_condition = array(
			$type . '_object_fit!' => array( '' ),
		);

		$this->add_responsive_control(
			$type . '_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'unit' => 'px' ),
				'tablet_default' => array( 'unit' => 'px' ),
				'mobile_default' => array( 'unit' => 'px' ),
				'size_units' => array( 'px', '%', 'vh' ),
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
			$type . '_object_vert_position',
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
			$type . '_object_hor_position',
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
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'object-position: {{object-vert-position.VALUE}} {{VALUE}};',
				),
				'condition' => array_merge( $object_fit_condition, array( 'fill' ) ),
			)
		);

		$this->add_control(
			$type . '_image_overlay',
			array(
				'label' => __( 'Image Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			$type . '_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			)
		);
	}

	public function get_featured_image_style_controls( $type = 'standard' ) {
		$this->add_responsive_control(
			$type . '_width',
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
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			$type . '_space',
			array(
				'label' => __( 'Max Width', 'cmsmasters-elementor' ) . ' (%)',
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'tablet_default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'mobile_default' => array(
					'unit' => '%',
					'size' => 100,
				),
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			$type . '_separator_panel_style',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->start_controls_tabs( $type . '_effects' );

		$this->start_controls_tab(
			$type . '_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			$type . '_background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'background-color: {{VALUE}};',
				),
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
			)
		);

		$this->add_control(
			$type . '_border_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'border-color: {{VALUE}};',
				),
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
				'condition' => array( $type . '_border_border!' => '' ),
			)
		);

		$this->add_control(
			$type . '_opacity',
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
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => $type . '_box_shadow_normal',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .cmsmasters-format-' . $type . ' img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => $type . '_img_css_filters',
				'selector' => '{{WRAPPER}} .cmsmasters-format-' . $type . ' img',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$type . '_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			$type . '_background_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-format-' . $type . ':hover img' => 'background-color: {{VALUE}};',
				),
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
			)
		);

		$this->add_control(
			$type . '_border_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-format-' . $type . ':hover img' => 'border-color: {{VALUE}};',
				),
				'global' => array( 'default' => Kit_Globals::COLOR_TEXT ),
				'condition' => array( $type . '_border_border!' => '' ),
			)
		);

		$this->add_control(
			$type . '_opacity_hover',
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
					'{{WRAPPER}} .cmsmasters-format-' . $type . ':hover img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => $type . '_box_shadow_hover',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .cmsmasters-format-' . $type . ':hover img',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => $type . '_css_filters_hover',
				'selector' => '{{WRAPPER}} .cmsmasters-format-' . $type . ':hover img',
			)
		);

		$this->add_control(
			$type . '_background_hover_transition',
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
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->add_control(
			$type . '_hover_animation',
			array(
				'label' => __( 'Hover Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			$type . '_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => $type . '_border',
				'exclude' => array( 'color' ),
				'selector' => '{{WRAPPER}} .cmsmasters-format-' . $type . ' img',
			)
		);

		$this->add_responsive_control(
			$type . '_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-format-' . $type . ' img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->get_featured_image_overlay_controls( $type );
	}

	public function get_featured_image_overlay_controls( $type ) {
		$this->add_control(
			$type . '_background_overlay_heading',
			array(
				'label' => __( 'Background Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( $type . '_image_overlay' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			$type . '_tabs_background_overlay',
			array( 'condition' => array( $type . '_image_overlay' => 'yes' ) )
		);

		$this->start_controls_tab(
			$type . '_tab_background_overlay_normal',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => $type . '_background_overlay',
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
			$type . '_background_overlay_opacity',
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
					$type . '_background_overlay_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$type . '_tab_background_overlay_hover',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => $type . '_background_overlay_hover',
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
			$type . '_background_overlay_hover_opacity',
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
					$type . '_background_overlay_hover_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_control(
			$type . '_background_overlay_hover_transition',
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
				'condition' => array( $type . '_image_overlay' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			$type . '_overlay_blend_mode',
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
				'condition' => array( $type . '_image_overlay' => 'yes' ),
			)
		);
	}

	public function get_controls_attr( $control_name ) {
		$control_attr = $this->get_controls( $control_name );

		unset( $control_attr['section'] );
		unset( $control_attr['tab'] );
		unset( $control_attr['name'] );

		$this->remove_control( $control_name );

		return $control_attr;
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.15.4 Fixed empty provider in post video format.
	 * @since 1.16.4 Fixed render audio player.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$format = get_post_format();

		if ( false === $format || 'image' === $format ) {
			$this->cmsmasters_render_featured_image( $settings, $format );
		} elseif ( 'gallery' === $format ) {
			$gallery_data = $this->get_post_meta( 'gallery_images' );

			if ( ! empty( $gallery_data ) ) {
				if ( ! is_array( $gallery_data ) ) {
					$gallery_id = explode( ',', $gallery_data );
				} else {
					$gallery_id = $gallery_data;
				}

				$this->slider->render( function () use ( $gallery_id, $settings, $format ) {
					foreach ( $gallery_id as $key => $id ) {
						$image_url = Group_Control_Image_Size::get_attachment_image_src( $id, 'slider_image', $settings );
						$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
						$attachment = get_post( $id );

						$link = $this->get_link_url( $settings, $format, $id );

						if ( $link ) {
							if ( Plugin::$instance->editor->is_edit_mode() ) {
								$this->add_render_attribute( 'link', 'class', 'elementor-clickable' );
							}

							if ( ! empty( $link['is_external'] ) ) {
								$this->add_render_attribute( 'link', 'target', '_blank' );
							}

							if ( ! empty( $link['nofollow'] ) ) {
								$this->add_render_attribute( 'link', 'rel', 'nofollow' );
							}
						}

						if ( ! $alt ) {
							$alt = $attachment->post_excerpt;
							if ( ! $alt ) {
								$alt = $attachment->post_title;
							}
						}

						$alt_text = trim( wp_strip_all_tags( $alt ) );

						$this->slider->render_slide_open();

						echo '<figure class="swiper-slide-inner">';

							if ( $link ) {
								$format_open_lightbox = ( isset( $settings[ $format . '_open_lightbox' ] ) ? $settings[ $format . '_open_lightbox' ] : '' );

								echo '<a href="' . esc_url( $link ) . '" data-elementor-open-lightbox="' . esc_attr( $format_open_lightbox ) . '" data-elementor-lightbox-slideshow="' . esc_attr( $this->get_id() ) . '">';
							}

								echo '<img class="swiper-slide-image" src="' . esc_attr( $image_url ) . '" alt="' . esc_attr( $alt_text ) . '" />';

							echo $link ? '</a>' : '';

						echo '</figure>';

						$this->slider->render_slide_close();
					}
				} );
			} else {
				$this->cmsmasters_render_featured_image( $settings, 'slider_image' );
			}
		} elseif ( 'video' === $format ) {
			$post_video_type = $this->get_post_meta( 'video_type' );
			$video_url = $this->get_post_meta( 'video_link_embedded' );
			$allowed_provider = array( 'youtube', 'vimeo', 'dailymotion' );
			$current_provider = Embed::get_video_properties( $video_url );

			if ( is_array( $current_provider ) && isset( $current_provider['provider'] ) ) {
				$provider = in_array( $current_provider['provider'], $allowed_provider, true );
			} else {
				$provider = false;
			}

			$is_embed = false;

			if (
				'' !== $video_url &&
				'embedded' === $post_video_type &&
				( false !== $provider || $this->embed_type( '/\bfacebook\b/i', $video_url ) )
			) {
				$is_embed = true;
			}

			$is_hosted = ( false !== $this->get_hosted_video_url() && 'hosted' === $post_video_type );

			if ( $is_embed || $is_hosted ) {
				$this->cmsmasters_render_video( $settings, $video_url, $post_video_type, $provider );
			} else {
				$this->cmsmasters_render_featured_image( $settings, 'image' );
			}
		} elseif ( 'audio' === $format ) {
			$post_audio_type = $this->get_post_meta( 'audio_type' );
			$audio_hosted_id = $this->get_post_meta( 'audio_link_hosted' );
			$audio_hosted_url = esc_url( wp_get_attachment_url( $audio_hosted_id ) );
			$audio_embed_url = $this->get_post_meta( 'audio_link_embedded' );

			if ( 'hosted' === $post_audio_type && false !== $audio_hosted_url ) {
				Utils::print_unescaped_internal_string( wp_audio_shortcode( $this->hosted_audio_attr( $audio_hosted_url ) ) ); // XSS ok.
			} elseif ( 'embedded' === $post_audio_type && '' !== $audio_embed_url ) {
				$this->_current_instance = $settings;

				$link = trim( $audio_embed_url, '/' );

				echo '<div class="elementor-widget-cmsmasters-post-media__container">';

				if ( $this->embed_type( '/\bdeezer\b/i', $link ) ) {
					$this->filter_deezer_result( $link );
				} elseif ( $this->embed_type( '/\bsoundcloud\b/i', $link ) ) {
					add_filter( 'oembed_result', array( $this, 'filter_soundcloud_result' ), 50, 3 );

					echo wp_oembed_get( $link, wp_embed_defaults() );

					remove_filter( 'oembed_result', array( $this, 'filter_soundcloud_result' ), 50 );
				} elseif ( $this->embed_type( '/\bmixcloud\b/i', $link ) ) {
					add_filter( 'oembed_result', array( $this, 'filter_mixcloud_result' ), 50, 3 );

					echo wp_oembed_get( $link, wp_embed_defaults() );

					remove_filter( 'oembed_result', array( $this, 'filter_mixcloud_result' ), 50 );
				} elseif ( $this->embed_type( '/\bspotify\b/i', $link ) ) {
					add_filter( 'oembed_result', array( $this, 'filter_spotify_result' ), 50, 3 );

					echo wp_oembed_get( $link, wp_embed_defaults() );

					remove_filter( 'oembed_result', array( $this, 'filter_spotify_result' ), 50 );
				}

				echo '</div>';
			} else {
				$this->cmsmasters_render_featured_image( $settings, 'image' );
			}
		}
	}

	/**
	 * Get attachment image.
	 *
	 * Return image width predefined dimensions or custom.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @param string $url
	 * @param string $id
	 */
	private function cmsmasters_get_attachment_image( $settings, $format, $url, $id = '' ) {
		$size = $settings[ $format . '_size' ];
		$image_class = '';
		$image_class .= " attachment-$size size-$size";

		if ( ! empty( $size ) && '' !== $id && in_array( $size, get_intermediate_image_sizes(), true ) ) {
			$post_title = get_the_title( $id );
			$alt = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
			$alt = ( ! empty( $alt ) ? esc_attr( $alt ) : esc_attr( $post_title ) . ' thumbnail' );
			$image_attr = array(
				'class' => trim( $image_class ),
				'alt' => $alt,
			);

			return wp_get_attachment_image( get_post_thumbnail_id(), $size, false, $image_attr );
		} else {
			$image_src = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id(), $format, $settings );

			if ( ! $image_src && isset( $url ) ) {
				$image_src = $url;
			}

			if ( $id ) {
				$attachment = get_post( $id );
				$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );

				if ( ! $alt ) {
					$alt = $attachment->post_excerpt;
					if ( ! $alt ) {
						$alt = $attachment->post_title;
					}
				}

				$alt_text = trim( wp_strip_all_tags( $alt ) );
			}

			isset( $alt_text ) ? $alt_text : $alt_text = esc_attr__( 'Empty Image', 'cmsmasters-elementor' );
			( '' !== $id ) ? $title_text = get_the_title( $id ) : $title_text = esc_attr__( 'Empty Image', 'cmsmasters-elementor' );
			$image_class_html = ! empty( $image_class ) ? ' class="' . $image_class . '"' : '';

			return sprintf( '<img src="%s" title="%s" alt="%s"%s />',
				esc_attr( $image_src ),
				$title_text,
				$alt_text,
				$image_class_html
			);
		}
	}

	/**
	 * Get link URL.
	 *
	 * Retrieve image widget link URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Widget settings.
	 * @param string $format
	 *
	 * @return array|string|false An array/string containing the link URL, or false if no link.
	 */
	private function get_link_url( $settings, $format ) {
		if ( 'none' === $settings[ $format . '_link_to' ] ) {
			return false;
		}

		if ( 'custom' === $settings[ $format . '_link_to' ] ) {
			if ( empty( $settings[ $format . '_link' ]['url'] ) ) {
				return false;
			}

			return $settings[ $format . '_link' ];
		}

		if ( 'file' === $settings[ $format . '_link_to' ] ) {
			return array( 'url' => Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id(), $format, $settings ) );
		}

		return array( 'url' => Utils::get_placeholder_image_src() );
	}

	/**
	 * Get attachment image.
	 *
	 * Return image width predefined dimensions or custom.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings
	 * @param string $format
	 */
	private function cmsmasters_render_featured_image( $settings, $format ) {
		( false === $format ) ? $format = 'standard' : $format;

		if ( 0 < (int) $settings[ $format . '_id' ] && ! empty( $settings[ $format . '_url' ] ) ) {
			$settings[ $format ] = array(
				'id' => $settings[ $format . '_id' ],
				'url' => $settings[ $format . '_url' ],
			);
		}

		if ( ! isset( $settings[ $format ] ) ) {
			if (
				empty( $settings[ $format . '_fallback_image_popover' ] ) ||
				empty( $settings[ $format . '_fallback_image' ] ) ||
				empty( $settings[ $format . '_fallback_image' ]['id'] ) ||
				empty( $settings[ $format . '_fallback_image' ]['url'] )
			) {
				return;
			}

			$settings[ $format ] = $settings[ $format . '_fallback_image' ];
		}

		if ( 'standard' === $format ) {
			$format_class = ' cmsmasters-format-standard';
		} else {
			$format_class = ' cmsmasters-format-image';
		}

		$this->add_render_attribute( 'wrapper', 'class', 'elementor-widget-cmsmasters-post-media__wrap' . $format_class );

		if ( ! empty( $settings[ $format . '_shape' ] ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'cmsmasters-shape-' . $settings['shape'] );
		}

		if ( $settings[ $format . '_hover_animation' ] && $settings[ $format . '_image_overlay' ] ) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-animation-' . $settings[ $format . '_hover_animation' ] );
		}

		$before_image = '';
		$after_image = '';

		if ( $settings[ $format . '_image_overlay' ] ) {
			$this->add_render_attribute( 'overlay', 'class', 'cmsmasters-background-overlay-wrap' );

			$after_image .= $this->get_render_tag( 'div', 'overlay', '' );
		}

		$link = $this->get_link_url( $settings, $format );

		if ( $link ) {
			$this->add_link_attributes( 'link', $link );

			$this->add_render_attribute( 'link', 'data-elementor-open-lightbox', $settings[ $format . '_open_lightbox' ] );

			if ( Plugin::$instance->editor->is_edit_mode() ) {
				$this->add_render_attribute( 'link', 'class', 'elementor-clickable' );
			}

			$before_image .= $this->get_render_tag( 'a', 'link' );
			$after_image .= $this->get_render_close_tag( 'a' );
		}

		$this->print_render_tag( 'div', 'wrapper', $before_image .
			$this->cmsmasters_get_attachment_image( $settings, $format, $settings[ $format ]['url'], $settings[ $format ]['id'] ) .
		$after_image );
	}

	/**
	 * Print play video text output on the frontend.
	 *
	 * Written in PHP and used to generate the play video text.
	 *
	 * @since 1.15.4
	 */
	public function print_a11y_text( $image_placeholder ) {
		if ( empty( $image_placeholder['alt'] ) ) {
			return esc_html__( 'Play Video', 'cmsmasters-elementor' );
		} else {
			return esc_html__( 'Play Video about', 'cmsmasters-elementor' ) . ' ' . esc_attr( $image_placeholder['alt'] );
		}
	}

	/**
	 * Get attachment image.
	 *
	 * Return image width predefined dimensions or custom.
	 *
	 * @since 1.0.0
	 * @since 1.15.4 Replaced elementor-screen-only on aria-label attribute.
	 *
	 * @param array $settings
	 */
	private function cmsmasters_render_video( $settings, $video_url, $post_video_type, $provider ) {
		$lightbox_check = $settings['lightbox'] && '' !== get_post_thumbnail_id();

		if ( 'hosted' === $post_video_type ) {
			$video_url = $this->get_hosted_video_url();
		}

		if ( empty( $video_url ) ) {
			return;
		}

		if ( 'hosted' === $post_video_type ) {
			ob_start();

			$this->render_hosted_video();

			$video_html = ob_get_clean();
		} elseif ( 'hosted' !== $post_video_type ) {
			if ( false === $provider ) {
				$video_html = $this->get_facebook_params( $video_url );
			} else {
				$embed_params = $this->get_embed_params( $provider, $lightbox_check );
				$embed_options = $this->get_embed_options();

				$video_html = Embed::get_embed_html( $video_url, $embed_params, $embed_options );
			}
		}

		if ( empty( $video_html ) ) {
			echo esc_url( $video_url );

			return;
		}

		$this->add_render_attribute( 'video-wrapper', 'class', array(
			'elementor-widget-cmsmasters-post-media__container',
			'elementor-open-' . ( $lightbox_check ? 'lightbox' : 'inline' ),
		) );

		if ( ! $lightbox_check ) {
			$this->add_render_attribute( 'video', 'class', 'elementor-widget-cmsmasters-post-media__inner' );
		}

		echo '<div ' . $this->get_render_attribute_string( 'video-wrapper' ) . '>' .
			'<div class="elementor-widget-cmsmasters-post-media__wrap elementor-fit-aspect-ratio">' .
				'<div ' . $this->get_render_attribute_string( 'video' ) . '>' .
					'<span class="elementor-widget-cmsmasters-post-media__close-button eicon-close"></span>';

		if ( ! $lightbox_check ) {
			Utils::print_unescaped_internal_string( $video_html ); // XSS ok.
		}

		if ( $this->has_image_placeholder() ) {
			$this->add_render_attribute(
				'image-placeholder',
				array(
					'class' => 'elementor-widget-cmsmasters-post-media__image-placeholder',
					'role' => 'button',
					'tabindex' => '0',
					'aria-label' => 'Play video',
				)
			);

			if ( $settings['lightbox'] ) {
				if ( 'hosted' === $post_video_type ) {
					$lightbox_url = $video_url;
				} elseif ( false !== $provider ) {
					$lightbox_url = Embed::get_embed_url( $video_url, $embed_params, $embed_options );
				} else {
					$lightbox_url = $this->get_facebook_lightbox_url();
				}

				$lightbox_options = array(
					'type' => 'video',
					'videoType' => $post_video_type,
					'url' => $lightbox_url,
					'modalOptions' => array(
						'id' => 'elementor-lightbox-' . $this->get_id(),
						'entranceAnimation' => $settings['lightbox_animation_entrance'],
						'entranceAnimation_tablet' => $settings['lightbox_animation_entrance_tablet'],
						'entranceAnimation_mobile' => $settings['lightbox_animation_entrance_mobile'],
						'videoAspectRatio' => $settings['aspect_ratio'],
					),
				);

				if ( 'hosted' === $post_video_type ) {
					$lightbox_options['videoParams'] = $this->get_hosted_params();
				}

				$this->add_render_attribute( 'image-placeholder', array(
					'data-elementor-open-lightbox' => 'yes',
					'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
				) );

				if ( Plugin::$instance->editor->is_edit_mode() ) {
					$this->add_render_attribute( 'image-placeholder', array( 'class' => 'elementor-clickable' ) );
				}
			}

			$image_placeholder = ( '' === $settings['empty_image_placeholder']['id'] ) ? get_post_thumbnail_id() : $settings['empty_image_placeholder']['id'];

			$this->add_render_attribute( 'image-placeholder', 'style', 'background-image: url(' . Group_Control_Image_Size::get_attachment_image_src( $image_placeholder, 'image_placeholder', $settings ) . ');' );

			echo '<div ' . $this->get_render_attribute_string( 'image-placeholder' ) . '>';

			if ( 'yes' === $settings['show_play_icon'] && ! empty( $settings['play_icon']['value'] ) ) {
				echo '<div class="elementor-widget-cmsmasters-post-media__play-icon' . ( ( 'yes' === $settings['play_icon_effect'] ) ? ' disable_effect' : '' ) . '">';

					Icons_Manager::render_icon(
						$settings['play_icon'],
						array(
							'aria-hidden' => 'true',
							'aria-label' => esc_attr( $this->print_a11y_text( $settings['empty_image_placeholder'] ) ),
						)
					);

				echo '</div>';
			}

			echo '</div>';
		}
			echo '</div>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video embed parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params( $provider, $lightbox_check ) {
		$params = array();
		$video_url = $this->get_post_meta( 'video_link_embedded' );
		$parse_url = wp_parse_url( $video_url, PHP_URL_QUERY );

		if ( 'youtube' === $provider ) {
			if ( false !== stristr( $parse_url, 'list=' ) ) {
				$list_id = explode( '&', substr( stristr( $parse_url, 'list=' ), 5 ) );

				$params['listType'] = 'playlist';
				$params['list'] = $list_id[0];
			}

			$params['autoplay'] = '1';
			$params['mute'] = 'true';

			$params['wmode'] = 'opaque';

			$this->lightbox_check( $lightbox_check );
		} elseif ( 'vimeo' === $provider ) {
			$params['autopause'] = '0';

			$this->lightbox_check( $lightbox_check, $provider );
		} elseif ( 'dailymotion' === $provider ) {
			$this->lightbox_check( $lightbox_check );
		} elseif ( 'facebook' === $provider ) {
			$this->lightbox_check( $lightbox_check );
		}

		return $params;
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video embed parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video embed parameters.
	 */
	public function lightbox_check( $lightbox_check, $provider = '' ) {
		if ( $lightbox_check ) {
			$params['autoplay'] = '1';

			( 'vimeo' === $provider ) ? $params['muted'] = 'false' : $params['mute'] = 'false';
		}
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget facebook parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video facebook parameters.
	 */
	public function get_facebook_params( $video_url ) {
		$settings = $this->get_settings_for_display();

		$src = ! empty( $settings['empty_image_placeholder']['id'] ) ? ' data-lazy-load="' : ' src="';
		$host = 'https://web.facebook.com/v2.7/plugins/video.php?';
		$url = 'href=' . esc_url( $video_url );

		$options = array(
			'&controls=true',
			'autoplay=false',
			'mute=false',
			'allowfullscreen=true',
		);

		return '<iframe class="elementor-video-iframe"' . $src . $host . $url . implode( '&', $options ) . '" frameborder="0" scrolling="no"></iframe>';
	}

	/**
	 * Get facebook lightbox url.
	 *
	 * @since 1.0.0
	 */
	protected function get_facebook_lightbox_url() {
		$settings = $this->get_settings_for_display();

		$host = 'https://web.facebook.com/v2.7/plugins/video.php?';
		$url = 'href=' . esc_url( $settings['facebook_url'] );

		$options = array(
			( 'yes' === $settings['controls'] ? '&controls=true' : '&controls=false' ),
			( 'yes' === $settings['autoplay_lightbox'] ? 'autoplay=true' : 'autoplay=false' ),
			( 'yes' === $settings['autoplay_lightbox'] ? 'mute=true' : 'mute=false' ),
			( 'yes' === $settings['fs'] ? 'allowfullscreen=true' : 'allowfullscreen=false' ),
		);

		$lightbox_url = $host . $url . implode( '&', $options );

		return $lightbox_url;
	}

	/**
	 * Whether the video widget has an placeholder image or not.
	 *
	 * Used to determine whether an placeholder image was set for the video.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether an placeholder was set for the video.
	 */
	protected function has_image_placeholder() {
		$settings = $this->get_settings_for_display();
		$has_image = $settings['empty_image_placeholder']['id'];

		return ! empty( $has_image );
	}

	/**
	 * Return options of embed video.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	private function get_embed_options() {
		$settings = $this->get_settings_for_display();

		$has_image = $settings['empty_image_placeholder']['id'];

		$embed_options = array();

		$embed_options['lazy_load'] = ! empty( $has_image );

		return $embed_options;
	}

	/**
	 * Return parameters of hosted video.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	private function get_hosted_params() {
		$video_params = array();

		$video_params['controls'] = '';

		$video_params['loop'] = '';

		$video_params['disablePictureInPicture'] = '';

		$video_params['controlsList'] = '';

		return $video_params;
	}

	/**
	 * Return url of hosted video.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_hosted_video_url() {
		$video_id = $this->get_post_meta( 'video_link_hosted' );
		$video_url = wp_get_attachment_url( $video_id );

		return $video_url;
	}

	/**
	 * Render video tag with parameters.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function render_hosted_video() {
		$video_url = $this->get_hosted_video_url();

		if ( empty( $video_url ) ) {
			return;
		}

		$video_params = $this->get_hosted_params();

		$this->add_render_attribute( 'media-hosted', array(
			'class' => 'elementor-widget-cmsmasters-post-media__hosted',
			'src' => esc_url( $video_url ),
			Utils::render_html_attributes( $video_params ),
		) );

		echo '<video ' . $this->get_render_attribute_string( 'media-hosted' ) . '></video>';
	}

	/**
	 * Return meta from post formats in single post.
	 *
	 * @param string $meta_field
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_post_meta( $meta_field ) {
		$meta_option = get_post_meta( get_the_ID(), 'cmsmasters_post_' . $meta_field, true );

		return $meta_option;
	}

	/**
	 * Return attributes for WordPress audio shortcode.
	 *
	 * @param string $audio_hosted_url
	 *
	 * @return array
	 * @since 1.0.0
	 */
	private function hosted_audio_attr( $audio_hosted_url ) {
		$attr = array(
			'src' => $audio_hosted_url,
			'loop' => '',
			'autoplay' => '',
			'preload' => 'none',
		);

		return $attr;
	}

	/**
	 * Return allowed embed type.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function embed_type( $type, $link ) {
		$is_allowed = preg_match( $type, $link, $matches );

		return $is_allowed;
	}

	/**
	 * Filter audio widget oEmbed results.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html The HTML returned by the oEmbed provider.
	 *
	 * @return string Filtered audio widget oEmbed HTML.
	 */
	public function filter_mixcloud_result( $html ) {
		$param_keys = array(
			'hide_cover',
			'hide_artwork',
			'light',
			'mini',
			'hide_tracklist',
		);

		$params = array();

		foreach ( $param_keys as $param_key ) {
			if ( 'hide_tracklist' === $param_key || 'hide_artwork' === $param_key ) {
				$true = '0';
				$false = '1';
			} else {
				$true = '1';
				$false = '0';
			}

			if ( 'light' === $param_key ) {
				$params['light'] = 'dark' === $this->_current_instance['mx_light'] ? '0' : '1';
			} else {
				$params[ $param_key ] = 'yes' === $this->_current_instance[ 'mx_' . $param_key ] ? $true : $false;
			}
		}

		preg_match( '/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $html, $matches );

		$url = esc_url( add_query_arg( $params, $matches[1] ) );

		$visual = 'yes' === $this->_current_instance['mx_mini'] && '' === $this->_current_instance['mx_hide_tracklist'] ? 'true' : 'false';

		$html = str_replace( array( $matches[1], 'visual=true' ), array( $url, 'visual=' . $visual ), $html );

		if ( 'true' === $visual ) {
			$html = preg_replace( '/height="([^"]*)"/', 'height="60"', $html );
		}

		return $html;
	}

	/**
	 * Filter audio widget oEmbed results.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html The HTML returned by the oEmbed provider.
	 *
	 * @return string Filtered audio widget oEmbed HTML.
	 */
	public function filter_soundcloud_result( $html ) {
		$param_keys = array(
			'buying',
			'liking',
			'download',
			'sharing',
			'show_comments',
			'show_playcount',
			'show_user',
			'show_artwork',
		);

		$params = array();

		foreach ( $param_keys as $param_key ) {
			$params[ $param_key ] = 'yes' === $this->_current_instance[ 'sc_' . $param_key ] ? 'true' : 'false';
		}

		$params['color'] = str_replace( '#', '', $this->_current_instance['sc_color'] );

		preg_match( '/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $html, $matches );

		$url = esc_url( add_query_arg( $params, $matches[1] ) );

		$visual = 'yes' === $this->_current_instance['sc_visual'] ? 'true' : 'false';

		$html = str_replace( array( $matches[1], 'visual=true' ), array( $url, 'visual=' . $visual ), $html );

		if ( 'false' === $visual ) {
			$html = str_replace( 'height="400"', 'height="200"', $html );
		}

		return $html;
	}

	/**
	 * Filter audio widget oEmbed results.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html The HTML returned by the oEmbed provider.
	 *
	 * @return string Filtered audio widget oEmbed HTML.
	 */
	public function filter_deezer_result( $link ) {
		preg_match( '/((?:[^\/]+))\/((?:[^\/]+))$/', wp_parse_url( $link, PHP_URL_PATH ), $matches );

		$explode_link = explode( '/', $matches[0] );
		$type = array_shift( $explode_link );
		$id = array_pop( $explode_link );
		$height_size = $this->_current_instance['dz_height']['size'];
		$player_size = '';
		$width = '';
		$height = '';

		if ( 'classic' === $this->_current_instance['dz_format'] && 'yes' === $this->_current_instance['dz_playlist'] ) {
			$player_size = 'height=' . $height_size;
		} elseif ( 'square' === $this->_current_instance['dz_format'] ) {
			$width = ' width=' . $height_size;
			$height = ' height=' . $height_size;

			$player_size = 'height=' . $height_size . '&width=' . $height_size;
		} else {
			$width = ' height=90';
			$height = ' height=90';

			$player_size = 'height=90';
		}

		$layout = ( 'light' === $this->_current_instance['dz_layout'] ? 'layout=light' : 'layout=dark' );
		$format = 'format=' . $this->_current_instance['dz_format'];
		$playlist = ( 'yes' === $this->_current_instance['dz_playlist'] ? 'playlist=true' : 'playlist=false' );
		$type = 'type=' . $type;
		$id = 'id=' . $id;
		$color = ( '' !== $this->_current_instance['dz_color'] ? '&color=' . str_replace( '#', '', $this->_current_instance['dz_color'] ) : '' );

		$options = array(
			$layout,
			$format,
			$playlist,
			$player_size,
			$type,
			$id,
		);

		$host = 'https://www.deezer.com/plugins/player?';
		$url = $host . implode( '&', $options );

		echo '<iframe class="elementor-widget-cmsmasters-post-media__iframe" 
			src="' . esc_url( $url ) . esc_attr( $color ) . '" ' .
			esc_attr( $width ) .
			esc_attr( $height ) . ' 
			scrolling="no" 
			frameborder="0" 
			allowtransparency="true"
		></iframe>';
	}

	/**
	 * Filter audio widget oEmbed results.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $html The HTML returned by the oEmbed provider.
	 *
	 * @return string Filtered audio widget oEmbed HTML.
	 */
	public function filter_spotify_result( $html ) {
		preg_match( '/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $html, $matches );

		$url = esc_url( $matches[1] );

		$visual = 'yes' === $this->_current_instance['sp_visual'] ? 'true' : 'false';

		$html = str_replace( array( $matches[1], 'visual=true' ), array( $url, 'visual=' . $visual ), $html );

		if ( 'false' === $visual ) {
			$html = str_replace( 'height="380"', 'height="80"', $html );
		}

		return $html;
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
			'standard' => array(
				'field' => 'url',
				'type' => esc_html__( 'Standart URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'image' => array(
				'field' => 'url',
				'type' => esc_html__( 'Image URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
		);
	}
}
