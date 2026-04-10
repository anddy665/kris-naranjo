<?php
namespace CmsmastersElementor\Modules\Media\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Media\Module as MediaModule;
use CmsmastersElementor\Modules\Media\Traits\Video_Widget;

use Elementor\Controls_Manager;
use Elementor\Embed;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addon video slider widget.
 *
 * Addon widget that displays video slider.
 *
 * @since 1.0.0
 */
class Video_Slider extends Base_Widget {

	use Video_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve video slider widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Video Slider', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve video slider widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-video-slider';
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
			'slider',
			'vimeo',
			'dailymotion',
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
	 * Retrieve the list of scripts the video slider widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'swiper',
			'jquery-ui-draggable',
		), parent::get_script_depends() );
	}

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.15.4 Added dependency of connecting swiper styles for widgets with swiper slider after elementor 3.26 version.
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array(
			'e-swiper',
			'widget-cmsmasters-video-slider',
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

	private $slide_prints_count = 0;

	/**
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Disabled options for 'URL' control.
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_video_slider',
			array(
				'label' => __( 'Slides', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'video_type',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'youtube' => __( 'YouTube', 'cmsmasters-elementor' ),
					'vimeo' => __( 'Vimeo', 'cmsmasters-elementor' ),
					'dailymotion' => __( 'Dailymotion', 'cmsmasters-elementor' ),
					'hosted' => __( 'Self Hosted', 'cmsmasters-elementor' ),
				),
				'default' => 'youtube',
				'frontend_available' => true,
			)
		);

		$repeater->add_control(
			'youtube_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => MediaModule::YOUTUBE_VIDEO_URL,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (YouTube)',
				'label_block' => true,
				'condition' => array( 'video_type' => 'youtube' ),
			)
		);

		$repeater->add_control(
			'cc_load_policy',
			array(
				'label' => __( 'Captions', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => array( 'video_type' => 'youtube' ),
			)
		);

		$repeater->add_control(
			'vimeo_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => MediaModule::VIMEO_VIDEO_URL,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (Vimeo)',
				'label_block' => true,
				'condition' => array( 'video_type' => 'vimeo' ),
			)
		);

		$repeater->add_control(
			'dailymotion_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => MediaModule::DAILYMOTION_VIDEO_URL,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (Dailymotion)',
				'label_block' => true,
				'condition' => array( 'video_type' => 'dailymotion' ),
			)
		);

		$repeater->add_control(
			'insert_url',
			array(
				'label' => __( 'External URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array( 'video_type' => 'hosted' ),
			)
		);

		$repeater->add_control(
			'hosted_url',
			array(
				'label' => __( 'Choose File', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
					'categories' => array( TagsModule::MEDIA_CATEGORY ),
				),
				'media_type' => 'video',
				'condition' => array(
					'video_type' => 'hosted',
					'insert_url' => '',
				),
			)
		);

		$repeater->add_control(
			'external_url',
			array(
				'label' => __( 'URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'autocomplete' => false,
				'show_external' => false,
				'options' => false,
				'label_block' => true,
				'show_label' => false,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'media_type' => 'video',
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ),
				'default' => array( 'url' => MediaModule::SELF_HOSTED_VIDEO_URL ),
				'condition' => array(
					'video_type' => 'hosted',
					'insert_url' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'cover',
			array(
				'label' => __( 'Choose Cover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic' => array( 'active' => true ),
				'frontend_available' => true,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'item_content_title',
			array(
				'label' => __( 'Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array( 'active' => true ),
				'label_block' => true,
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'item_content_subtitle',
			array(
				'label' => __( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array( 'active' => true ),
				'label_block' => true,
				'condition' => array( 'item_content_title!' => '' ),
			)
		);

		$repeater->add_control(
			'item_content_author',
			array(
				'label' => __( 'Author', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array( 'active' => true ),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'item_content_author_link',
			array(
				'label' => __( 'Link for Author', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array( 'item_content_author!' => '' ),
			)
		);

		$this->add_control(
			'videos_list',
			array(
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'youtube_url' => MediaModule::YOUTUBE_VIDEO_URL,
						'item_content_title' => __( 'Video Slide 1', 'cmsmasters-elementor' ),
						'item_content_subtitle' => __( 'Video 1 subtitle text', 'cmsmasters-elementor' ),
					),
					array(
						'youtube_url' => MediaModule::YOUTUBE_ALTERNATE_VIDEO_URL,
						'item_content_title' => __( 'Video Slide 2', 'cmsmasters-elementor' ),
						'item_content_subtitle' => __( 'Video 2 subtitle text', 'cmsmasters-elementor' ),
					),
				),
				'title_field' => '<# if ( \'\' === item_content_title ) { #>Video slide <span class="cmsmasters-repeat-item-num"></span> <# } else { #> {{{ item_content_title }}} <span class="cmsmasters-repeat-item-num hidden"></span><# } #>',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'video_ratio',
			array(
				'label' => __( 'Video', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
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
					'11' => '1:1',
					'916' => '9:16',
				),
				'selectors_dictionary' => array(
					'169' => '1.77777',
					'219' => '2.33333',
					'43' => '1.33333',
					'32' => '1.5',
					'11' => '1',
					'916' => '0.5625',
				),
				'default' => '169',
				'selectors' => array(
					'{{WRAPPER}}' => '--video-aspect-ratio: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__image-overlay,
					{{WRAPPER}} iframe',
			)
		);

		$this->add_control(
			'slides_per_view',
			array(
				'label' => __( 'Slides to Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => '1',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'slides_to_scroll',
			array(
				'label' => __( 'Slides to Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => '1',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'effect',
			array(
				'label' => __( 'Effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'slide' => 'Slide',
					'fade' => 'Fade',
					'cube' => 'Cube',
					'coverflow' => 'Coverflow',
					'flip' => 'Flip',
				),
				'default' => 'slide',
				'render_type' => 'template',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-slider-effect-',
				'separator' => 'before',
				'condition' => array( 'videos_list!' => '1' ),
			)
		);

		$this->end_controls_section();

		// Start Additional Options
		$this->start_controls_section(
			'section_additional_options',
			array( 'label' => __( 'Additional Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'duration',
			array(
				'label' => __( 'Sliding Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 500,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label' => __( 'Auto Slide', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label' => __( 'Auto Slide Timeout', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 5000,
				'frontend_available' => true,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label' => __( 'Pause On Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array( 'autoplay!' => '' ),
			)
		);

		$this->add_control(
			'autoplay_reverse',
			array(
				'label' => __( 'Auto Slide Reverse', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'frontend_available' => true,
				'condition' => array( 'autoplay!' => '' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'slide_index',
			array(
				'label' => __( 'Active Slide', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'placeholder' => '1',
				'min' => 1,
				'step' => 1,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'arrows_nav_enable',
			array(
				'label' => __( 'Navigation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'arrows_nav_type',
			array(
				'label' => esc_html__( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'icon' => array(
						'title' => __( 'Icon', 'cmsmasters-elementor' ),
						'description' => 'Close button has only icon',
					),
					'text' => array(
						'title' => __( 'Text', 'cmsmasters-elementor' ),
						'description' => 'Close button has only text',
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => 'Close button has icon and text',
					),
				),
				'default' => 'icon',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-arrows-type-',
				'condition' => array( 'arrows_nav_enable' => 'yes' ),
			)
		);

		$this->add_control(
			'arrows_nav_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'video' => __( 'Video', 'cmsmasters-elementor' ),
					'info-box' => __( 'Info Box', 'cmsmasters-elementor' ),
				),
				'default' => 'video',
				'label_block' => false,
				'render_type' => 'template',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-arrows-position-',
				'condition' => array( 'arrows_nav_enable' => 'yes' ),
			)
		);

		$this->add_control(
			'arrows_nav_view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'default' => array( 'title' => __( 'Default', 'cmsmasters-elementor' ) ),
					'stacked' => array( 'title' => __( 'Stacked', 'cmsmasters-elementor' ) ),
					'framed' => array( 'title' => __( 'Framed', 'cmsmasters-elementor' ) ),
				),
				'default' => 'stacked',
				'label_block' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-arrows-view-',
				'condition' => array( 'arrows_nav_enable' => 'yes' ),
			)
		);

		$this->add_control(
			'arrows_nav_full_height',
			array(
				'label' => __( 'Full Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-arrows-fixed-button-size-',
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_position' => 'video',
					'arrows_nav_view!' => 'default',
				),
			)
		);

		$this->add_control(
			'arrows_nav_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-arrows-shape-',
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_type' => 'icon',
					'arrows_nav_view!' => 'default',
					'arrows_nav_full_height!' => 'yes',
				),
			)
		);

		$this->add_control(
			'arrows_nav_icon_position',
			array(
				'label' => esc_html__( 'Icon Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'outside' => __( 'Outside', 'cmsmasters-elementor' ),
					'inside' => __( 'Inside', 'cmsmasters-elementor' ),
				),
				'default' => 'outside',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-arrows-nav-text-position-',
				'condition' => array( 'arrows_nav_type' => 'both' ),
			)
		);

		$this->add_control(
			'arrows_nav_vertical_alignment',
			array(
				'label' => __( 'Vertical Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
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
				'default' => 'middle',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-arrows-vertical-alignment-',
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_position' => 'video',
					'arrows_nav_full_height' => '',
				),
			)
		);

		/* Start Navigation Icon */
		$this->add_control(
			'arrows_nav_icon_heading',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_type!' => 'text',
				),
			)
		);

		$this->start_controls_tabs(
			'arrows_nav_icon_tabs',
			array(
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'arrows_nav_enable',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'name' => 'arrows_nav_type',
							'operator' => '!==',
							'value' => 'text',
						),
					),
				),
			)
		);

		$this->start_controls_tab(
			'arrows_nav_icon_prev_tab',
			array( 'label' => __( 'Previous', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'arrows_nav_icon_prev',
			array(
				'label' => esc_html__( 'Previous Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-angle-left',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid' => array(
						'angle-left',
						'chevron-left',
						'arrow-left',
						'caret-left',
						'long-arrow-alt-left',
					),
				),
				'show_label' => false,
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_nav_icon_next_tab',
			array( 'label' => __( 'Next', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'arrows_nav_icon_next',
			array(
				'label' => esc_html__( 'Next Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-angle-right',
					'library' => 'fa-solid',
				),
				'recommended' => array(
					'fa-solid' => array(
						'angle-right',
						'chevron-right',
						'arrow-right',
						'caret-right',
						'long-arrow-alt-right',
					),
				),
				'show_label' => false,
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'arrows_nav_icon_devices',
			array(
				'label' => esc_html__( 'Devices', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => array(
					'desktop' => esc_html__( 'Desktop', 'cmsmasters-elementor' ),
					'tablet' => esc_html__( 'Tablet', 'cmsmasters-elementor' ),
					'mobile' => esc_html__( 'Mobile', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'desktop',
					'tablet',
					'mobile',
				),
				'label_block' => true,
				'show_label' => false,
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_type!' => 'text',
				),
			)
		);

		/* Start Navigation Text */
		$this->add_control(
			'arrows_nav_text_heading',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_type!' => 'icon',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_text_prev',
			array(
				'label' => __( 'Previous', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Previous', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters_arrow_button.cmsmasters_arrow_button_prev .cmsmasters_arrow_button_text:after' => 'content: "{{VALUE}}"',
				),
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_type!' => 'icon',
					'arrows_nav_text_devices!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_text_next',
			array(
				'label' => __( 'Next', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Next', 'cmsmasters-elementor' ),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters_arrow_button.cmsmasters_arrow_button_next .cmsmasters_arrow_button_text:after' => 'content: "{{VALUE}}"',
				),
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_type!' => 'icon',
					'arrows_nav_text_devices!' => '',
				),
			)
		);

		$this->add_control(
			'arrows_nav_text_devices',
			array(
				'label' => esc_html__( 'Devices', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => array(
					'desktop' => esc_html__( 'Desktop', 'cmsmasters-elementor' ),
					'tablet' => esc_html__( 'Tablet', 'cmsmasters-elementor' ),
					'mobile' => esc_html__( 'Mobile', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'desktop',
					'tablet',
					'mobile',
				),
				'label_block' => true,
				'show_label' => false,
				'render_type' => 'template',
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_type!' => 'icon',
				),
			)
		);

		$this->end_controls_section();
		// Finished Additional Options

		// Started Info Box Box Style
		$this->start_controls_section(
			'section_info_box_style',
			array(
				'label' => __( 'Info Box', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'info_box_style',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'under' => __( 'Below', 'cmsmasters-elementor' ),
					'inside' => __( 'Inside', 'cmsmasters-elementor' ),
				),
				'default' => 'under',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-info-box-style-',
			)
		);

		$this->add_responsive_control(
			'info_box_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 300,
						'max' => 1200,
					),
					'%' => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__info-box' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'info_box_style' => 'inside' ),
			)
		);

		$this->add_control(
			'info_horizontal_position',
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
				'selectors_dictionary' => array(
					'left' => 'float: left;',
					'center' => 'margin: 0 auto;',
					'right' => 'float: right;',
				),
				'default' => 'left',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__info-box' => '{{VALUE}};',
				),
				'condition' => array(
					'info_box_width[size]!' => '',
					'info_box_style' => 'inside',
				),
			)
		);

		$this->add_control(
			'info_box_vertical_align',
			array(
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
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
				'frontend_available' => true,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-info-box-vertical-align-',
				'condition' => array( 'info_box_style' => 'inside' ),
			)
		);

		$this->add_control(
			'info_box_text_align',
			array(
				'label' => __( 'Text Align', 'cmsmasters-elementor' ),
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
				'default' => 'left',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__info-box-inner > div' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'info_box_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => -200,
						'max' => 200,
						'step' => 5,
					),
					'%' => array(
						'min' => -30,
						'max' => 30,
						'step' => 5,
					),
				),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-info-box-vertical-align-bottom .elementor-widget-cmsmasters-video-slider__content' => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-info-box-vertical-align-top .elementor-widget-cmsmasters-video-slider__content' => 'top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'info_box_style' => 'inside' ),
			)
		);

		$this->add_control(
			'info_box_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__info-box' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'info_box_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__info-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'info_box_responsive',
			array(
				'label' => esc_html__( 'Responsive', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'separator' => 'before',
				'condition' => array( 'info_box_style' => 'inside' ),
			)
		);

		$this->start_popover();

		$this->add_control(
			'info_box_responsive_devices',
			array(
				'label' => esc_html__( 'Below position on:', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => true,
				'options' => array(
					'tablet' => esc_html__( 'Tablet', 'cmsmasters-elementor' ),
					'mobile' => esc_html__( 'Mobile', 'cmsmasters-elementor' ),
				),
				'default' => array(
					'mobile',
				),
				'render_type' => 'template',
				'condition' => array( 'info_box_style' => 'inside' ),
			)
		);

		$this->add_control(
			'info_box_responsive_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-info-box-responsive-bg: {{VALUE}};',
				),
				'condition' => array(
					'info_box_style' => 'inside',
					'info_box_responsive!' => '',
					'info_box_responsive_devices!' => '',
				),
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_info_style',
			array(
				'label' => __( 'Info', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'info_variations',
			array(
				'label' => __( 'Variations', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::SELECTIZE,
				'label_block' => true,
				'options' => array(
					'subtitle' => __( 'Subtitle', 'cmsmasters-elementor' ),
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'author' => __( 'Author', 'cmsmasters-elementor' ),
				),
				'multiple' => true,
				'control_options' => array(
					'plugins' => array(
						'remove_button',
						'drag_drop',
					),
				),
				'default' => array(
					'subtitle',
					'title',
					'author',
				),
			)
		);

		$this->start_controls_tabs(
			'info_tabs',
			array( 'separator' => 'before' )
		);

		$this->start_controls_tab(
			'info_title_tab',
			array( 'label' => __( 'Title', 'cmsmasters-elementor' ) )
		);

		$this->add_responsive_control(
			'info_title_gap',
			array(
				'label' => __( 'Bottom Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__title_wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'info_variations' => 'title' ),
			)
		);

		$this->add_control(
			'info_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'info_title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__title',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'info_subtitle_tab',
			array( 'label' => __( 'Subtitle', 'cmsmasters-elementor' ) )
		);

		$this->add_responsive_control(
			'info_subtitle_gap',
			array(
				'label' => __( 'Bottom Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__subtitle_wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'info_variations' => 'subtitle' ),
			)
		);

		$this->add_control(
			'info_subtitle_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'info_subtitle_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__subtitle',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'info_author_tab',
			array( 'label' => __( 'Author', 'cmsmasters-elementor' ) )
		);

		$this->add_responsive_control(
			'info_author_gap',
			array(
				'label' => __( 'Bottom Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array( 'max' => 100 ),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__author_wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'info_variations' => 'author' ),
			)
		);

		$this->add_control(
			'info_author_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__author' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'info_author_color_hover',
			array(
				'label' => __( 'Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__author:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'info_author_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__author',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cover_buttons_style',
			array(
				'label' => __( 'Cover & Buttons', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'cover_buttons_description',
			array(
				'raw' => __( 'Settings in the section can be used only when Cover Image is added on a slide’s settings.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'cover_heading',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'cover',
				'default' => 'full',
				'separator' => 'none',
			)
		);

		$this->add_control(
			'play_button',
			array(
				'label' => __( 'Play Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'play_button_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'label' => esc_html__( 'Icon for Play Button', 'cmsmasters-elementor' ),
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
			)
		);

		$this->add_control(
			'play_button_position',
			array(
				'label' => esc_html__( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'center' => esc_html__( 'Center', 'cmsmasters-elementor' ),
					'auto-center' => array(
						'title' => esc_html__( 'Auto Center', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Vertically aligned to the center of the larger area', 'cmsmasters-elementor' ),
					),
					'custom' => esc_html__( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'center',
				'toggle' => false,
				'render_type' => 'template',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-play-button-position-',
				'condition' => array(
					'info_box_style' => 'inside',
					'play_button_icon[value]!' => '',
				),
			)
		);

		$this->start_controls_tabs(
			'play_button_tabs_color',
			array( 'condition' => array( 'play_button_icon[value]!' => '' ) )
		);

		/* Start Arrow Normal Tab */
		$this->start_controls_tab(
			'play_button_tab_color_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'play_button_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__play-button i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__play-button svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		/* Start Arrow Normal Tab */
		$this->start_controls_tab(
			'play_button_tab_color_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'play_button_color_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__slide-item:hover .elementor-widget-cmsmasters-video-slider__play-button i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__slide-item:hover .elementor-widget-cmsmasters-video-slider__play-button svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'play_button_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 300,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__play-button i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__play-button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__play-button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'play_button_icon[value]!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'play_button_text_shadow',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__play-button i:before',
				'condition' => array( 'play_button_icon[value]!' => '' ),
			)
		);

		$this->add_control(
			'play_button_blinking',
			array(
				'label' => __( 'Blinking effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'play_button_icon[value]!' => '' ),
			)
		);

		$start = is_rtl() ? __( 'Right', 'cmsmasters-elementor' ) : __( 'Left', 'cmsmasters-elementor' );
		$end = ! is_rtl() ? __( 'Right', 'cmsmasters-elementor' ) : __( 'Left', 'cmsmasters-elementor' );

		$this->add_control(
			'offset_orientation_h',
			array(
				'label' => __( 'Horizontal Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => array(
					'start' => array(
						'title' => $start,
						'icon' => 'eicon-h-align-left',
					),
					'end' => array(
						'title' => $end,
						'icon' => 'eicon-h-align-right',
					),
				),
				'render_type' => 'ui',
				'separator' => 'before',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-offset-orientation-h-',
				'condition' => array( 'play_button_position' => 'custom' ),
			)
		);

		$this->add_responsive_control(
			'offset_x',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
					'custom',
				),
				'range' => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					),
					'%' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'default' => array(
					'size' => '0',
					'unit' => '%',
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'body:not(.rtl) .cmsmasters-play-button-position-custom.cmsmasters-offset-orientation-h-start .elementor-widget-cmsmasters-video-slider__play-button' => 'left: {{SIZE}}{{UNIT}}',
					'body.rtl .cmsmasters-play-button-position-custom.cmsmasters-offset-orientation-h-start .elementor-widget-cmsmasters-video-slider__play-button' => 'right: {{SIZE}}{{UNIT}}',

					'body:not(.rtl) .cmsmasters-play-button-position-custom.cmsmasters-offset-orientation-h-end .elementor-widget-cmsmasters-video-slider__play-button' => 'right: {{SIZE}}{{UNIT}}',
					'body.rtl .cmsmasters-play-button-position-custom.cmsmasters-offset-orientation-h-end .elementor-widget-cmsmasters-video-slider__play-button' => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'play_button_position' => 'custom' ),
			)
		);

		$this->add_control(
			'offset_orientation_v',
			array(
				'label' => __( 'Vertical Orientation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => array(
					'start' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'end' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
				),
				'render_type' => 'ui',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-offset-orientation-v-',
				'condition' => array( 'play_button_position' => 'custom' ),
			)
		);

		$this->add_responsive_control(
			'offset_y',
			array(
				'label' => __( 'Offset', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					),
					'%' => array(
						'min' => -200,
						'max' => 200,
					),
				),
				'size_units' => array(
					'px',
					'%',
					'custom',
				),
				'default' => array(
					'size' => '0',
					'unit' => '%',
				),
				'frontend_available' => true,
				'render_type' => 'ui',
				'selectors' => array(
					'.cmsmasters-play-button-position-custom.cmsmasters-offset-orientation-v-start .elementor-widget-cmsmasters-video-slider__play-button' => 'top: {{SIZE}}{{UNIT}}',
					'.cmsmasters-play-button-position-custom.cmsmasters-offset-orientation-v-end .elementor-widget-cmsmasters-video-slider__play-button' => 'bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'play_button_position' => 'custom' ),
			)
		);

		$this->add_control(
			'stop_button',
			array(
				'label' => __( 'Close Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'stop_button_top_gap',
			array(
				'label' => __( 'Gap from Top', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1,
					),
					'%' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-slider__stop-video' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'stop_button' => 'yes' ),
			)
		);

		$this->end_controls_section();

		/* Start Arrows Navigation */
		$this->start_controls_section(
			'section_arrows_nav',
			array(
				'label' => __( 'Arrows Navigation', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'arrows_nav_enable' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_ver_gap',
			array(
				'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vh' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-arrows-vertical-alignment-top .cmsmasters_arrow_button' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.cmsmasters-arrows-vertical-alignment-bottom .cmsmasters_arrow_button' => 'bottom: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'arrows_nav_enable' => 'yes',
					'arrows_nav_position' => 'video',
					'arrows_nav_full_height' => '',
					'arrows_nav_vertical_alignment!' => 'middle',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_side_gap',
			array(
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Side Gap', 'cmsmasters-elementor' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-arrows-position-video .cmsmasters_arrow_button_prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-arrows-position-video .cmsmasters_arrow_button_next' => 'right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'arrows_nav_position' => 'video' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_gap_between',
			array(
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-arrows-position-info-box .cmsmasters_arrow_button + .cmsmasters_arrow_button' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'arrows_nav_position' => 'info-box' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_right_gap',
			array(
				'type' => Controls_Manager::SLIDER,
				'label' => __( 'Right Gap', 'cmsmasters-elementor' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-arrows-position-info-box .cmsmasters_arrow_buttons' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'arrows_nav_position' => 'info-box' ),
			)
		);

		$this->start_controls_tabs( 'tabs_arrows_nav_colors' );

		$this->start_controls_tab(
			'tab_arrows_nav_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'arrows_nav_primary',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--arrows-nav-primary-normal: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrows_nav_secondary',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--arrows-nav-secondary-normal: {{VALUE}};',
				),
				'condition' => array( 'arrows_nav_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_opacity',
			array(
				'label' => __( 'Opacity', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters_arrow_button' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_tab();
		/* Finished Arrow Tab */

		/* Start Arrow Hover Tab */
		$this->start_controls_tab(
			'tab_arrows_nav_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'arrows_nav_primary_hover',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--arrows-nav-primary-hover: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-arrows-view-framed .cmsmasters_arrow_button:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'arrows_nav_secondary_hover',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}' => '--arrows-nav-secondary-hover: {{VALUE}};',
				),
				'condition' => array( 'arrows_nav_view!' => 'default' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_opacity_hover',
			array(
				'label' => __( 'Opacity', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'max' => 1,
						'min' => 0,
						'step' => 0.01,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}:hover .cmsmasters_arrow_button' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'arrows_nav_hr',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'arrows_nav_text_typography',
				'fields_options' => array(
					'typography' => array( 'label' => __( 'Text Typography', 'cmsmasters-elementor' ) ),
				),
				'selector' => '{{WRAPPER}} .cmsmasters_arrow_button_text',
				'condition' => array( 'arrows_nav_type!' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_icon_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters_arrow_button i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .cmsmasters_arrow_button svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}' => '--cmsmasters-icon-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array( 'arrows_nav_type!' => 'text' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_icon_gap',
			array(
				'label' => __( 'Icon Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-left-gap: {{SIZE}}{{UNIT}}; --cmsmasters-icon-right-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'arrows_nav_type' => 'both' ),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-square-padding-top: {{TOP}}{{UNIT}}; 
						--cmsmasters-icon-square-padding-right: {{RIGHT}}{{UNIT}}; 
						--cmsmasters-icon-square-padding-bottom: {{BOTTOM}}{{UNIT}}; 
						--cmsmasters-icon-square-padding-left: {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'arrows_nav_view',
							'operator' => '!==',
							'value' => 'default',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'arrows_nav_type',
									'operator' => '!==',
									'value' => 'icon',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'arrows_nav_type',
											'operator' => '=',
											'value' => 'icon',
										),
										array(
											'name' => 'arrows_nav_shape',
											'operator' => '=',
											'value' => 'square',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_icon_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-circle-padding: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'arrows_nav_view!' => 'default',
					'arrows_nav_type' => 'icon',
					'arrows_nav_shape' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-border-top: {{TOP}}{{UNIT}};
						--cmsmasters-icon-border-right: {{RIGHT}}{{UNIT}};
						--cmsmasters-icon-border-bottom: {{BOTTOM}}{{UNIT}};
						--cmsmasters-icon-border-left: {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'arrows_nav_view',
							'operator' => '=',
							'value' => 'framed',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'arrows_nav_type',
									'operator' => '!==',
									'value' => 'icon',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'arrows_nav_type',
											'operator' => '=',
											'value' => 'icon',
										),
										array(
											'name' => 'arrows_nav_shape',
											'operator' => '=',
											'value' => 'square',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_border_icon_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-icon-circle-border: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'arrows_nav_view' => 'framed',
					'arrows_nav_type' => 'icon',
					'arrows_nav_shape' => 'circle',
				),
			)
		);

		$this->add_responsive_control(
			'arrows_nav_border_radius',
			array(
				'label' => _x( 'Border Radius', 'Border Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array( 'min' => 0 ),
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters_arrow_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'arrows_nav_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'arrows_nav_box_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters_arrow_button',
				'condition' => array( 'arrows_nav_view!' => 'default' ),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'arrows_nav_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters_arrow_button',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render video slider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$this->print_video_slider();
	}

	/**
	 * Render video slider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the slider HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_video_slider() {
		$settings = $this->get_settings_for_display();

		$playlist = $settings['videos_list'];

		$arrows_nav_icon_devices = '';
		$arrows_nav_text_devices = '';

		if ( 'yes' === $settings['arrows_nav_enable'] && 'text' !== $settings['arrows_nav_type'] ) {
			$arrows_nav_icon_devices = ' cmsmasters-icon-devices-' . implode( ' cmsmasters-icon-devices-', $settings['arrows_nav_icon_devices'] );
		}

		if ( 'yes' === $settings['arrows_nav_enable'] && 'icon' !== $settings['arrows_nav_type'] ) {
			$arrows_nav_text_devices = ' cmsmasters-text-devices-' . implode( ' cmsmasters-text-devices-', $settings['arrows_nav_text_devices'] );
		}

		echo '<div class="elementor-widget-cmsmasters-video-slider__container' . esc_attr( $arrows_nav_icon_devices ) . esc_attr( $arrows_nav_text_devices ) . '">' .
			'<div class="elementor-widget-cmsmasters-video-slider__wrap cmsmasters_swiper_content">';

				$this->print_video_slide( $settings, $playlist );

		if ( count( $playlist ) > 1 && 'video' === $settings['arrows_nav_position'] ) {
			Utils::print_unescaped_internal_string( $this->print_arrows() );
		}

			echo '</div>';

			$this->print_video_info_box( $settings, $playlist );

		echo '</div>';
	}

	/**
	 * Print play video text output on the frontend.
	 *
	 * Written in PHP and used to generate the play video text.
	 *
	 * @since 1.15.4
	 */
	public function print_a11y_text( $cover ) {
		if ( empty( $cover['alt'] ) ) {
			return esc_html__( 'Play Video', 'cmsmasters-elementor' );
		} else {
			return esc_html__( 'Play Video about', 'cmsmasters-elementor' ) . ' ' . esc_attr( $cover['alt'] );
		}
	}

	/**
	 * Render video slider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the slide HTML.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 * @since 1.15.4 Replaced elementor-screen-only on aria-label attribute.
	 */
	protected function print_video_slide( $settings, $playlist ) {
		$settings = $this->get_settings_for_display();

		echo '<div class="swiper-wrapper">';

		foreach ( $playlist as $index => $slide ) {
			++$this->slide_prints_count;

			echo '<div class="elementor-repeater-item elementor-repeater-item-' . esc_attr( $slide['_id'] ) . ' swiper-slide" data-repeater-id="' . esc_attr( $slide['_id'] ) . '">';

				$element_key = 'slide-' . $index . '-' . $this->slide_prints_count;
				$video_url = $slide[ $slide['video_type'] . '_url' ];

				if ( 'hosted' === $slide['video_type'] ) {
					$video_url = $this->get_hosted_video_url( $slide );
				}

				if ( empty( $video_url ) ) {
					return;
				}

				if ( 'hosted' === $slide['video_type'] ) {
					ob_start();

					$this->render_hosted_video( $slide );

					$video_html = ob_get_clean();
				} else {
					$embed_params = $this->get_embed_params( $slide );
					$embed_options = $this->get_embed_options( $slide );

					$video_html = Embed::get_embed_html( $video_url, $embed_params, $embed_options );
				}

				$this->add_render_attribute( $element_key . '-slide', array( 'class' => 'elementor-widget-cmsmasters-video-slider__slide-item' ) );

				$this->add_render_attribute(
					'image-overlay',
					array(
						'class' => 'elementor-widget-cmsmasters-video-slider__image-overlay',
						'role' => 'button',
						'tabindex' => '0',
						'aria-label' => 'Play video',
					)
				);

				echo '<div ' . $this->get_render_attribute_string( $element_key . '-slide' ) . '>' .
					'<div class="elementor-widget-cmsmasters-video-slider__image-overlay">';

				if ( ! empty( $slide['cover']['id'] ) ) {
					echo $this->get_attachment_image_html( $slide, esc_html( $settings['cover_size'] ), 'cover' );

					$play_button = $settings['play_button_icon'];

					if ( ! empty( $play_button['value'] ) ) {
						echo '<div class="elementor-widget-cmsmasters-video-slider__play-button' . ( 'yes' === $settings['play_button_blinking'] ? '' : ' cmsmasters-disable-effect' ) . '">';

							Icons_Manager::render_icon(
								$play_button,
								array(
									'aria-hidden' => 'true',
									'aria-label' => esc_attr( $this->print_a11y_text( $slide['cover'] ) ),
								)
							);

						echo '</div>';
					}
				}

					echo '</div>';

				if ( $settings['stop_button'] ) {
					echo '<span class="elementor-widget-cmsmasters-video-slider__stop-video eicon-close"></span>';
				}

					Utils::print_unescaped_internal_string( $video_html );

				echo '</div>' .
			'</div>';
		}

		echo '</div>';
	}

	/**
	 * @param bool $from_media
	 *
	 * @return string
	 * @since 1.0.0
	 */
	private function get_hosted_video_url( $slide ) {
		if ( ! empty( $slide['insert_url'] ) ) {
			$video_url = $slide['external_url']['url'];
		} else {
			$video_url = $slide['hosted_url']['url'];
		}

		if ( empty( $video_url ) ) {
			return '';
		}

		return $video_url;
	}

	/**
	 * Render hosted video.
	 *
	 * @since 1.0.0
	 */
	private function render_hosted_video( $slide ) {
		$video_url = $this->get_hosted_video_url( $slide );

		if ( empty( $video_url ) ) {
			return;
		}

		$this->add_render_attribute( 'elementor-video', array(
			'class' => array(
				'elementor-video',
				'cmsmasters-hosted-video',
			),
			( $slide['cover']['id'] ? 'data-lazy-load' : 'src' ) => esc_url( $video_url ),
			Utils::render_html_attributes( array() ),
			'controls' => 'controls',
		) );

		echo '<video ' . $this->get_render_attribute_string( 'elementor-video' ) . '></video>';
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video slider widget embed parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video slider embed parameters.
	 */
	public function get_embed_params( $slide ) {
		$settings = $this->get_settings_for_display();

		$params = array();
		$params_dictionary = array();
		$video_type = $slide['video_type'];

		if ( 'youtube' === $video_type ) {
			$params['wmode'] = 'opaque';

			$cc_load_policy = ( 'yes' === $slide['cc_load_policy'] ? 1 : 0 );

			$params['cc_load_policy'] = $cc_load_policy;
		}

		if ( 'vimeo' === $video_type ) {
			$params['autopause'] = '0';
		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name = $param_name;

			if ( is_string( $key ) ) {
				$setting_name = $key;
			}

			$setting_value = $settings[ $setting_name ] ? '1' : '0';

			$params[ $param_name ] = $setting_value;
		}

		return $params;
	}

	/**
	 * Get embed options.
	 *
	 * Retrieve video slider widget embed options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video slider embed options.
	 */
	private function get_embed_options( $slide ) {
		if ( $slide['cover']['id'] ) {
			$lazy_load = true;
		} else {
			$lazy_load = false;
		}

		$embed_options['lazy_load'] = $lazy_load;

		return $embed_options;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Video slider arrows.
	 */
	private function print_arrows() {
		$settings = $this->get_active_settings();

		if ( $settings['arrows_nav_enable'] ) {
			$arrow_button_align = array(
				'prev',
				'next',
			);

			echo '<div class="cmsmasters_arrow_buttons">';

			foreach ( $arrow_button_align as $align ) {
				echo '<div class="cmsmasters_arrow_button_' . esc_attr( $align ) . ' cmsmasters_arrow_button">';

				$arrows_nav_type = ( isset( $settings['arrows_nav_type'] ) ? $settings['arrows_nav_type'] : '' );

				if ( 'text' !== $arrows_nav_type ) {
					echo '<span class="cmsmasters_arrow_button_icon">';

					$arrow_buttons_icon = ( isset( $settings[ 'arrows_nav_icon_' . $align ] ) ? $settings[ 'arrows_nav_icon_' . $align ] : '' );
					$arrow_buttons_icon_att = array( 'aria-hidden' => 'true' );

					if ( 'icon' === $arrows_nav_type ) {
						$arrow_buttons_icon_att = array_merge(
							$arrow_buttons_icon_att,
							array( 'aria-label' => 'Submit Button' ),
						);
					}

					if ( '' !== $arrow_buttons_icon['value'] ) {
						Icons_Manager::render_icon( $arrow_buttons_icon, $arrow_buttons_icon_att );
					} else {
						Icons_Manager::render_icon(
							array(
								'value' => 'fas fa-angle-' . ( 'prev' === $align ? 'left' : 'right' ),
								'library' => 'fa-solid',
							),
							$arrow_buttons_icon_att
						);
					}

					echo '</span>';
				}

				if ( 'icon' !== $arrows_nav_type ) {
					echo '<span class="cmsmasters_arrow_button_text"></span>';
				}

				echo '</div>';
			}

			echo '</div>';
		}
	}

	/**
	 * Render video slider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the slide HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_video_info_box( $settings, $playlist ) {
		$info_box_responsive_devices = '';

		if ( ! empty( $settings['info_box_responsive_devices'][0] ) ) {
			$info_box_responsive_devices = ' cmsmasters-info-box-position-' . implode( ' cmsmasters-info-box-position-', $settings['info_box_responsive_devices'] );
		}

		echo '<div class="elementor-widget-cmsmasters-video-slider__content' . esc_attr( $info_box_responsive_devices ) . '">' .
			'<div class="elementor-widget-cmsmasters-video-slider__content-inner cmsmasters_swiper_gallery">' .
				'<div class="swiper-wrapper">';

		foreach ( $playlist as $index => $slide ) {
			++$this->slide_prints_count;

			echo '<div class="elementor-repeater-item elementor-repeater-item-' . esc_attr( $slide['_id'] ) . ' swiper-slide" data-repeater-id="' . esc_attr( $slide['_id'] ) . '">';

				$this->get_video_info_box( $slide, $settings, 'slide-' . $index . '-' . $this->slide_prints_count );

			echo '</div>';
		}

				echo '</div>' .
			'</div>' .
		'</div>';
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Video slide info box.
	 */
	private function get_video_info_box( $slide ) {
		$settings = $this->get_settings_for_display();

		$content_title = $slide['item_content_title'];
		$content_subtitle = $slide['item_content_subtitle'];
		$content_author = $slide['item_content_author'];

		if ( $content_title || $content_subtitle || $content_author ) {
			echo '<div class="elementor-widget-cmsmasters-video-slider__info-box">' .
				'<div class="elementor-widget-cmsmasters-video-slider__info-box-inner">';

			foreach ( $settings['info_variations'] as $item ) {
				switch ( $item ) {
					case 'author':
						Utils::print_unescaped_internal_string( $this->print_video_author( $slide ) );

						break;
					case 'subtitle':
						Utils::print_unescaped_internal_string( $this->print_video_subtitle( $slide ) );

						break;
					case 'title':
						Utils::print_unescaped_internal_string( $this->print_video_title( $slide ) );

						break;
				}
			}

				echo '</div>';

			if (
				count( $settings['videos_list'] ) > 1 &&
				'info-box' === $settings['arrows_nav_position']
			) {
				Utils::print_unescaped_internal_string( $this->print_arrows() );
			}

			echo '</div>';
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Video slide author.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `content_author_link` variable. Added check on empty.
	 */
	private function print_video_author( $slide ) {
		$html = '';
		$content_author = $slide['item_content_author'];
		$content_author_link = '';

		if ( isset( $slide['item_content_author_link'] ) ) {
			$content_author_link = $slide['item_content_author_link']['url'];
		}

		if ( '' !== $content_author ) {
			$tag = '' !== $content_author_link ? 'a' : 'h6';
			$link = '' !== $content_author_link ? ' href="' . $content_author_link . '"' : '';

			$html .= '<div class="elementor-widget-cmsmasters-video-slider__author_wrap">' .
				'<' . $tag . ' class="elementor-widget-cmsmasters-video-slider__author"' . $link . '>' .
					esc_html( $content_author ) .
				'</' . $tag . '>' .
			'</div>';
		}

		return $html;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Video slide subtitle.
	 */
	private function print_video_subtitle( $slide ) {
		$html = '';
		$content_subtitle = $slide['item_content_subtitle'];

		if ( '' !== $content_subtitle ) {
			$html .= '<div class="elementor-widget-cmsmasters-video-slider__subtitle_wrap">' .
				'<h6 class="elementor-widget-cmsmasters-video-slider__subtitle">' .
					esc_html( $content_subtitle ) .
				'</h6>' .
			'</div>';
		}

		return $html;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return Video slide title.
	 */
	private function print_video_title( $slide ) {
		$html = '';
		$content_title = $slide['item_content_title'];

		if ( '' !== $content_title ) {
			$html .= '<div class="elementor-widget-cmsmasters-video-slider__title_wrap">' .
				'<h3 class="elementor-widget-cmsmasters-video-slider__title">' .
					esc_html( $content_title ) .
				'</h3>' .
			'</div>';
		}

		return $html;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return string Image HTML.
	 */
	public function get_attachment_image_html( $settings, $image_size_key, $image_key = null ) {
		if ( ! $image_key ) {
			$image_key = $image_size_key;
		}

		// Old version of image settings.
		if ( ! isset( $image_size_key ) ) {
			$image_size_key = '';
		}

		// If is the new version - with image size.
		$image_sizes = get_intermediate_image_sizes();

		$image_sizes[] = 'full';

		$image = $settings[ $image_key ];

		if ( ! empty( $image['id'] ) && ! wp_attachment_is_image( $image['id'] ) ) {
			$image['id'] = '';
		}

		$is_static_render_mode = Plugin::$instance->frontend->is_static_render_mode();

		// On static mode don't use WP responsive images.
		$html = '';

		if ( ! empty( $image['id'] ) && in_array( $image_size_key, $image_sizes ) && ! $is_static_render_mode ) {
			$image_class = ! empty( $settings['hover_animation'] ) ? 'elementor-animation-' . $settings['hover_animation'] : '';
			$image_class .= " attachment-$image_size_key size-$image_size_key";
			$image_attr = array( 'class' => trim( $image_class ) );

			$html .= wp_get_attachment_image( $image['id'], $image_size_key, false, $image_attr );
		}

		return apply_filters( 'elementor/image_size/get_attachment_image_html', $html, $settings, $image_size_key, $image_key );
	}

	/**
	 * Render video slider widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {
		$this->print_video_slider_template();
	}

	/**
	 * Render video slider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the slider HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_video_slider_template() {
		?>
		<#
		var arrows_nav_icon_devices = '';
		var arrows_nav_text_devices = '';

		if ( 'yes' === settings.arrows_nav_enable && 'text' !== settings.arrows_nav_type ) {
			arrows_nav_icon_devices = ' cmsmasters-icon-devices-' + settings.arrows_nav_icon_devices.join( ' cmsmasters-icon-devices-' );
		}

		if ( 'yes' === settings.arrows_nav_enable && 'icon' !== settings.arrows_nav_type ) {
			arrows_nav_text_devices = ' cmsmasters-text-devices-' + settings.arrows_nav_text_devices.join( ' cmsmasters-text-devices-' );
		}

		#><div class="elementor-widget-cmsmasters-video-slider__container{{{arrows_nav_icon_devices}}}{{{arrows_nav_text_devices}}}">
			<div class="elementor-widget-cmsmasters-video-slider__wrap cmsmasters_swiper_content">
				<?php $this->print_video_slide_template(); ?><#

				if ( 'video' === settings.arrows_nav_position ) {
					#><?php $this->print_arrows_template(); ?><#
				}

			#></div><#

			var info_box_responsive_devices = '';

			if ( settings.info_box_responsive_devices.length ) {
				info_box_responsive_devices = ' cmsmasters-info-box-position-' + settings.info_box_responsive_devices.join( ' cmsmasters-info-box-position-' );
			}

			#><div class="elementor-widget-cmsmasters-video-slider__content{{{info_box_responsive_devices}}}">
				<div class="elementor-widget-cmsmasters-video-slider__content-inner cmsmasters_swiper_gallery">
					<div class="swiper-wrapper"><#
						_.each( settings.videos_list, function( item ) {
							#><?php $this->slide_prints_count++; ?>
							<div class="elementor-repeater-item elementor-repeater-item-{{{item['_id']}}} swiper-slide" data-repeater-id="{{{item['_id']}}}">
								<#
								var contentTitle = item.item_content_title;
								var contentSubtitle = item.item_content_subtitle;
								var contentAuthor = item.item_content_author;

								if ( contentTitle || contentSubtitle || contentAuthor ) {
									#><div class="elementor-widget-cmsmasters-video-slider__info-box">
										<div class="elementor-widget-cmsmasters-video-slider__info-box-inner"><#
											_.each( settings.info_variations, function( info ) {
												switch ( info ) {
													case 'author':
														var contentAuthor = item.item_content_author;
														var contentAuthorLink = item.item_content_author_link.url;

														if ( '' !== contentAuthor ) {
															var tag = ( '' !== contentAuthorLink ? 'a' : 'h6' );
															var link = ( '' !== contentAuthorLink ? ' href="' + contentAuthorLink + '"' : '' );

															#><div class="elementor-widget-cmsmasters-video-slider__author_wrap">
																<{{{ tag }}} class="elementor-widget-cmsmasters-video-slider__author"' . link . '>{{{ contentAuthor }}}</{{{ tag }}}>
															</div><#
														}

														break;
													case 'subtitle':
														var contentSubtitle = item.item_content_subtitle;

														if ( '' !== contentSubtitle ) {
															#><div class="elementor-widget-cmsmasters-video-slider__subtitle_wrap">
																<h6 class="elementor-widget-cmsmasters-video-slider__subtitle">{{{ contentSubtitle }}}</h6>
															</div><#
														}

														break;
													case 'title':
														var contentTitle = item.item_content_title;

														if ( '' !== contentTitle ) {
															#><div class="elementor-widget-cmsmasters-video-slider__title_wrap">
																<h3 class="elementor-widget-cmsmasters-video-slider__title">{{{ contentTitle }}}</h3>
															</div><#
														}

														break;
												}
											} );
										#></div><#

										if ( 'info-box' === settings.arrows_nav_position ) {
											#><?php $this->print_arrows_template(); ?><#
										}
									#></div><#
								}
							#></div><#
						} );
					#></div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render video slider widget output on the frontend.
	 *
	 * Written in PHP and used to generate the slide HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_video_slide_template() {
		?>
		<div class="swiper-wrapper"><#

		_.each( settings.videos_list, function( slide, index ) {
			#><?php $this->slide_prints_count++; ?><#

			var count = #><?php $this->slide_prints_count; ?><#
			var element_key = 'slide-' + index + '-' + count;

			#><div class="elementor-repeater-item elementor-repeater-item-{{{slide['_id']}}} swiper-slide" data-repeater-id="{{{slide['_id']}}}"><#
				view.addRenderAttribute( element_key + '-slide', 'class', 'elementor-widget-cmsmasters-video-slider__slide-item' );
				view.addRenderAttribute( 'search-form-container', 'class', 'cmsmasters-buttons-type-' + settings.submit_button_type );

				#><div {{{ view.getRenderAttributeString( element_key + '-slide' ) }}}>
					<div class="elementor-widget-cmsmasters-video-slider__image-overlay"><#
						var cover = {
							id: slide.cover.id,
							url: slide.cover.url,
							size: settings.cover_size,
							dimension: slide.cover_custom_dimension,
							model: view.getEditModel()
						};

						var cover_url = elementor.imagesManager.getImageUrl( cover );

						#><img src="{{{ cover_url }}}" /><#

						if ( '' !== slide.cover.id ) {

							var playButton = settings.play_button_icon;
							var play_button_blinking = ( 'yes' === settings.play_button_blinking ? '' : ' cmsmasters-disable-effect' );
							var play_video_text = ( '' === slide.cover.alt ? 'Play Video' : 'Play Video about' );

							if ( '' !== playButton.value ) {
								#><div class="elementor-widget-cmsmasters-video-slider__play-button {{{play_button_blinking}}}" role="button" aria-label="{{{play_video_text}}}" tabindex="0"><#

									iconHTML = elementor.helpers.renderIcon( view, playButton );

									if ( '' !== playButton.value ) {
										if ( 'svg' !== playButton.library ) {
											#><i class="{{{playButton.value}}}"></i><#
										} else {
											#>{{{ iconHTML.value }}}<#
										}
									} else {
										#><i class="far fa-play-circle"></i><#
									}

									#>
								</div><#
							}
						}

					#></div><#

					if ( settings.stop_button ) {
						#><span class="elementor-widget-cmsmasters-video-slider__stop-video eicon-close"></span><#
					}

					let videoUrl = '';

					if ( 'hosted' === slide.video_type ) {
						videoUrl = ( '' !== slide.insert_url ? slide.external_url.url : slide.hosted_url.url );
					} else {
						videoUrl = slide[slide.video_type + '_url'];

						videoUrl = videoUrl
							.replace( /^.*(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^\?&\"\'>]+)/, 'https://www.youtube.com/embed/$1?feature=oembed&wmode=opaque' )
							.replace( /^.*vimeo\.com\/(?:[a-z]*\/)*([‌​0-9]{6,11})[?]?.*/, 'https://player.vimeo.com/video/$1?autopause=0#t=' )
							.replace( /^.*dailymotion.com\/(?:video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/, 'https://dailymotion.com/embed/video/$1' );
					}

					if ( '' === videoUrl ) {
						return;
					}

					var src = ( slide.cover.id ? 'data-lazy-load=' : 'src=' );

					if ( 'hosted' === slide.video_type ) {
						#><video class="elementor-video cmsmasters-hosted-video" {{{src}}}{{{videoUrl}}} title="{{{slide.video_type}}} video player"></video><#
					} else {
						#><iframe class="elementor-video-iframe" allowfullscreen="" title="{{{slide.video_type}}} video player" {{{src}}}"{{{videoUrl}}}"></iframe><#
					}
					#>
				</div>
			</div><#
		} );

		#></div>
		<?php
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video slider widget embed parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video slider embed parameters.
	 */
	public function print_arrows_template() {
		?>
		<#
		if ( settings.videos_list.length > 1 && settings.arrows_nav_enable ) {
			var arrowsNavType = settings.arrows_nav_type;

			#><div class="cmsmasters_arrow_buttons">
				<div class="cmsmasters_arrow_button_prev cmsmasters_arrow_button"><#

					if ( 'text' !== arrowsNavType ) {
						var arrowButtonsIcon = settings.arrows_nav_icon_prev;

						#><span class="cmsmasters_arrow_button_icon"><#

						iconHTML = elementor.helpers.renderIcon( view, arrowButtonsIcon );

						if ( '' !== arrowButtonsIcon.value ) {
							if ( 'svg' !== arrowButtonsIcon.library ) {
								#><i class="{{{arrowButtonsIcon.value}}}"></i><#
							} else {
								#>{{{ iconHTML.value }}}<#
							}
						} else {
							#><i class="fas fa-angle-left"></i><#
						}

						#></span><#
					}

					if ( 'icon' !== arrowsNavType ) {
						#><span class="cmsmasters_arrow_button_text"></span><#
					}

				#></div>
				<div class="cmsmasters_arrow_button_next cmsmasters_arrow_button"><#

					if ( 'text' !== arrowsNavType ) {
						var arrowButtonsIcon = settings.arrows_nav_icon_next;

						#><span class="cmsmasters_arrow_button_icon"><#

						iconHTML = elementor.helpers.renderIcon( view, arrowButtonsIcon );

						if ( '' !== arrowButtonsIcon.value ) {
							if ( 'svg' !== arrowButtonsIcon.library ) {
								#><i class="{{{arrowButtonsIcon.value}}}"></i><#
							} else {
								#>{{{ iconHTML.value }}}<#
							}
						} else {
							#><i class="fas fa-angle-right"></i><#
						}

						#></span><#
					}

					if ( 'icon' !== arrowsNavType ) {
						#><span class="cmsmasters_arrow_button_text"></span><#
					}

				#></div>
			</div><#
		}
		#>
		<?php
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
			'videos_list' => array(
				array(
					'field' => 'youtube_url',
					'type' => esc_html__( 'YouTube URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'vimeo_url',
					'type' => esc_html__( 'Vimeo URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'dailymotion_url',
					'type' => esc_html__( 'Dailymotion URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'external_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Self Hosted URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				array(
					'field' => 'item_content_title',
					'type' => esc_html__( 'Video Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'item_content_subtitle',
					'type' => esc_html__( 'Video Subtitle', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'item_content_author',
					'type' => esc_html__( 'Video Author', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'item_content_author_link' => array(
					'field' => 'url',
					'type' => esc_html__( 'Link for Author', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
		);
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
				'field' => 'arrows_nav_text_prev',
				'type' => esc_html__( 'Arrow Previous Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'arrows_nav_text_next',
				'type' => esc_html__( 'Arrow Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
