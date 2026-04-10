<?php
namespace CmsmastersElementor\Modules\Slider\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Media\Module as CmsmastersMedia;
use CmsmastersElementor\Modules\Slider\Classes\Slider;
use CmsmastersElementor\Modules\Settings\Kit_Globals;

use Elementor\Controls_Manager;
use Elementor\Embed;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Media_Carousel extends Base_Widget {

	protected $slider;

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
		return __( 'Media Carousel', 'cmsmasters-elementor' );
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
		return 'cmsicon-media-carousel';
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
			'media',
			'media-carousel',
			'carousel',
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
	 * Get script dependencies.
	 *
	 * Retrieve the list of script dependencies the widget requires.
	 *
	 * @since 1.0.0
	 * @since 1.16.0 Added dependency of connecting swiper script after elementor 3.27 version.
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'swiper',
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
	 * Get image caption.
	 *
	 * Retrieve media carousel widget caption.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget caption.
	 */
	protected function get_image_caption( $slide ) {
		$caption_type = $this->get_settings( 'caption' );

		if ( empty( $caption_type ) ) {
			return '';
		}

		$attachment_post = get_post( $slide['image']['id'] );

		if ( 'caption' === $caption_type ) {
			return $attachment_post->post_excerpt;
		}

		if ( 'title' === $caption_type ) {
			return $attachment_post->post_title;
		}

		return $attachment_post->post_content;
	}

	protected function get_default_slides_count() {
		return 3;
	}

	protected function get_repeater_defaults() {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return array_fill( 0, $this->get_default_slides_count(), array(
			'image' => array(
				'url' => $placeholder_image_src,
			),
		) );
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
		$style_depends = array(
			'e-swiper',
			'widget-cmsmasters-media-carousel',
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
	 *
	 * Initializing the Addon `media carousel` widget class.
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
	 * Register controls.
	 *
	 * Used to add new controls to the widget.
	 *
	 * Should be inherited and register new controls using `add_control()`,
	 * `add_responsive_control()` and `add_group_control()`, inside control
	 * wrappers like `start_controls_section()`, `start_controls_tabs()` and
	 * `start_controls_tab()`.
	 *
	 * @since 1.0.0
	 * @since 1.10.1 Fixed deprecated control attribute `scheme` to `global`.
	 * @since 1.14.4 Fixed the width of the slides in the vertical direction of the slider.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_slides',
			array(
				'label' => __( 'Slides', 'cmsmasters-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'type',
			array(
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'default' => 'image',
				'options' => array(
					'image' => array(
						'title' => __( 'Image', 'cmsmasters-elementor' ),
						'icon' => 'eicon-image-bold',
					),
					'video' => array(
						'title' => __( 'Video', 'cmsmasters-elementor' ),
						'icon' => 'eicon-video-camera',
					),
				),
				'label_block' => false,
				'toggle' => false,
			)
		);

		$repeater->add_control(
			'image',
			array(
				'label' => __( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
			)
		);

		$repeater->add_control(
			'image_link_to_type',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'lightbox' => __( 'Light Box', 'cmsmasters-elementor' ),
					'custom' => __( 'Link', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'render_type' => 'template',
				'label_block' => true,
				'condition' => array(
					'type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'image_link_to',
			array(
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'separator' => 'none',
				'show_label' => false,
				'condition' => array(
					'type' => 'image',
					'image_link_to_type' => array( 'custom' ),
				),
			)
		);

		$external_video_sources = array(
			'youtube' => array(
				'title' => __( 'YouTube', 'cmsmasters-elementor' ),
				'default' => CmsmastersMedia::YOUTUBE_VIDEO_URL,
				'description' => __( 'If you insert the playlist link in the URL, the playlist will appear in the player', 'cmsmasters-elementor' ),
			),
			'vimeo' => array(
				'title' => __( 'Vimeo', 'cmsmasters-elementor' ),
				'default' => CmsmastersMedia::VIMEO_VIDEO_URL,
			),
			'dailymotion' => array(
				'title' => __( 'Dailymotion', 'cmsmasters-elementor' ),
				'default' => CmsmastersMedia::DAILYMOTION_VIDEO_URL,
			),
			'facebook' => array(
				'title' => __( 'Facebook', 'cmsmasters-elementor' ),
				'default' => CmsmastersMedia::FACEBOOK_VIDEO_URL,
			),
			'twitch' => array(
				'title' => __( 'Twitch', 'cmsmasters-elementor' ),
				'default' => CmsmastersMedia::TWITCH_VIDEO_URL,
			),
		);

		$video_sources = array();

		foreach ( $external_video_sources as $source => $args ) {
			$video_sources[ $source ] = $args['title'];
		}

		$video_sources['hosted'] = __( 'Self Hosted', 'cmsmasters-elementor' );

		$repeater->add_control(
			'video_type',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => $video_sources,
				'default' => 'youtube',
				'frontend_available' => true,
				'condition' => array(
					'type' => 'video',
				),
			)
		);

		foreach ( $external_video_sources as $source => $args ) {
			$control_args = array(
				'label' => __( 'Video URL', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::TEXT,
				'default' => $args['default'],
				'placeholder' => sprintf( __( 'Enter your %s URL.', 'cmsmasters-elementor' ), $args['title'] ),
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'condition' => array(
					'type' => 'video',
					'video_type' => $source,
				),
			);

			if ( isset( $args['description'] ) ) {
				$control_args['description'] = $args['description'];
				$control_args['classes'] = 'cmsmasters_description';
			}

			$repeater->add_control( "{$source}_url", $control_args );
		}

		$repeater->add_control(
			'insert_url',
			array(
				'label' => __( 'External URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array(
					'type' => 'video',
					'video_type' => 'hosted',
				),
			)
		);

		$repeater->add_control(
			'hosted_url',
			array(
				'label' => __( 'Choose File', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'video',
				'dynamic' => array(
					'active' => true,
					'categories' => array( TagsModule::MEDIA_CATEGORY ),
				),
				'condition' => array(
					'type' => 'video',
					'video_type' => 'hosted',
					'insert_url' => '',
				),
			)
		);

		$repeater->add_control(
			'external_url',
			array(
				'label' => __( 'URL', 'cmsmasters-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'Enter your external video URL.', 'cmsmasters-elementor' ),
				'media_type' => 'video',
				'autocomplete' => false,
				'show_external' => false,
				'default' => array( 'url' => CmsmastersMedia::SELF_HOSTED_VIDEO_URL ),
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'condition' => array(
					'type' => 'video',
					'video_type' => 'hosted',
					'insert_url' => 'yes',
				),
			)
		);

		$repeater->add_control(
			'start',
			array(
				'label' => __( 'Start Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'description' => __( 'Specify a video start time (in seconds).', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array(
					'type' => 'video',
				),
			)
		);

		$this->add_control(
			'slides',
			array(
				'label' => __( 'Slides', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => $this->get_repeater_defaults(),
				'separator' => 'after',
			)
		);

		$this->slider->register_controls_content_per_view( $this );

		$this->update_control(
			'slider_direction',
			array(
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-slider--slider-direction-',
			),
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			array(
				'label' => __( 'Additional Options', 'cmsmasters-elementor' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'image_size',
				'default' => 'full',
			)
		);

		$this->add_control(
			'image_fit',
			array(
				'label' => __( 'Image Fit', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'contain' => __( 'Contain', 'cmsmasters-elementor' ),
					'cover' => __( 'Cover', 'cmsmasters-elementor' ),
					'fill' => __( 'Fill', 'cmsmasters-elementor' ),
					'scale-down' => __( 'Scale Down', 'cmsmasters-elementor' ),
				),
				'default' => 'cover',
				'prefix_class' => 'cmsmasters-media-carousel__image-fit-',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__inner img' => 'object-fit: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'overlay_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Overlay', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'overlay',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'text' => __( 'Text', 'cmsmasters-elementor' ),
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
			)
		);

		$this->add_control(
			'caption',
			array(
				'label' => __( 'Text', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'title' => __( 'Title', 'cmsmasters-elementor' ),
					'caption' => __( 'Caption', 'cmsmasters-elementor' ),
					'description' => __( 'Description', 'cmsmasters-elementor' ),
				),
				'default' => 'title',
				'condition' => array(
					'overlay' => 'text',
				),
			)
		);

		$this->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'show_label' => false,
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-link',
					'library' => 'solid',
				),
				'condition' => array(
					'overlay' => 'icon',
				),
			)
		);

		$this->add_control(
			'overlay_animation',
			array(
				'label' => __( 'Animation', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'fade' => 'Fade',
					'slide-up' => 'Slide Up',
					'slide-down' => 'Slide Down',
					'slide-right' => 'Slide Right',
					'slide-left' => 'Slide Left',
					'zoom-in' => 'Zoom In',
				),
				'default' => 'fade',
				'condition' => array(
					'overlay!' => 'none',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'video_options',
			array( 'label' => __( 'Video Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'youtube_options_popover',
			array(
				'label' => __( 'YouTube', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'ui',
			)
		);

		$this->start_popover();

		$this->add_control(
			'ytb_controls',
			array(
				'label' => __( 'Player Controls', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'ytb_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'ytb_playsinline',
			array(
				'label' => __( 'Play On Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'ytb_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'ytb_mute',
			array(
				'label' => __( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'ytb_loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			)
		);

		$this->add_control(
			'ytb_fs',
			array(
				'label' => __( 'Fullscreen', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'ytb_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'ytb_modestbranding',
			array(
				'label' => __( 'Logo', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'ytb_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'ytb_rel',
			array(
				'label' => __( 'Suggested Videos', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Current Video Channel', 'cmsmasters-elementor' ),
					'yes' => __( 'Any Video', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'ytb_privacy',
			array(
				'label' => __( 'Privacy Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->end_popover();

		$this->add_control(
			'vimeo_options_popover',
			array(
				'label' => __( 'Vimeo', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'ui',
			)
		);

		$this->start_popover();

		$this->add_control(
			'vimeo_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'vimeo_playsinline',
			array(
				'label' => __( 'Play On Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'vimeo_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'vimeo_mute',
			array(
				'label' => __( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'vimeo_loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'vimeo_portrait',
			array(
				'label' => __( 'Intro Portrait', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'vimeo_title',
			array(
				'label' => __( 'Intro Title', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'vimeo_byline',
			array(
				'label' => __( 'Intro Byline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'vimeo_color',
			array(
				'label' => __( 'Controls Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
			)
		);

		$this->end_popover();

		$this->add_control(
			'dailymotion_options_popover',
			array(
				'label' => __( 'Dailymotion', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'ui',
			)
		);

		$this->start_popover();

		$this->add_control(
			'dailymotion_controls',
			array(
				'label' => __( 'Player Controls', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'dailymotion_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'dailymotion_playsinline',
			array(
				'label' => __( 'Play On Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'dailymotion_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'dailymotion_mute',
			array(
				'label' => __( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'dailymotion_showinfo',
			array(
				'label' => __( 'Video Info', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'dailymotion_logo',
			array(
				'label' => __( 'Logo', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'dailymotion_color',
			array(
				'label' => __( 'Controls Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
			)
		);

		$this->end_popover();

		$this->add_control(
			'facebook_options_popover',
			array(
				'label' => __( 'Facebook', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'ui',
			)
		);

		$this->start_popover();

		$this->add_control(
			'facebook_controls',
			array(
				'label' => __( 'Player Controls', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'facebook_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'facebook_playsinline',
			array(
				'label' => __( 'Play On Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'facebook_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'facebook_mute',
			array(
				'label' => __( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'facebook_fs',
			array(
				'label' => __( 'Fullscreen', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'facebook_controls' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'twitch_options_popover',
			array(
				'label' => __( 'Twitch', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'ui',
			)
		);

		$this->start_popover();

		$this->add_control(
			'twitch_controls',
			array(
				'label' => __( 'Player Controls', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'twitch_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'twitch_playsinline',
			array(
				'label' => __( 'Play On Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'twitch_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'twitch_mute',
			array(
				'label' => __( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'twitch_fs',
			array(
				'label' => __( 'Fullscreen', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'twitch_controls' => 'yes',
				),
			)
		);

		$this->end_popover();

		$this->add_control(
			'hosted_options_popover',
			array(
				'label' => __( 'Self Hosted', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'render_type' => 'ui',
			)
		);

		$this->start_popover();

		$this->add_control(
			'hosted_controls',
			array(
				'label' => __( 'Player Controls', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
			)
		);

		$this->add_control(
			'hosted_autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'hosted_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'hosted_playsinline',
			array(
				'label' => __( 'Play On Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'hosted_autoplay' => 'yes',
				),
			)
		);

		$this->add_control(
			'hosted_loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'hosted_fs',
			array(
				'label' => __( 'Fullscreen', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array(
					'hosted_controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'download_button',
			array(
				'label' => __( 'Download Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'picture_in_picture',
			array(
				'label' => __( 'Picture In Picture', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
			)
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slide_style',
			array(
				'label' => __( 'Slide', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'slides_effects' );

		$this->start_controls_tab(
			'slides_effects_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'background_color',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_slides_color',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper' => 'border-color: {{VALUE}};',
				),
				'global' => array( 'default' => Kit_Globals::COLOR_BORDER ),
				'condition' => array( 'border_slides_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'image_box_shadow_normal',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'slides_effects_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'background_color_hover',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'border_slides_color_hover',
			array(
				'label' => __( 'Border Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper:hover' => 'border-color: {{VALUE}};',
				),
				'global' => array( 'default' => Kit_Globals::COLOR_BORDER ),
				'condition' => array( 'border_slides_border!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'image_box_shadow_hover',
				'exclude' => array( 'box_shadow_position' ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper:hover',
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper:hover',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper' => 'transition: all {{SIZE}}s',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'border_slides',
				'exclude' => array( 'color' ),
				'fields_options' => array(
					'border' => array(
						'separator' => 'before',
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper',
			)
		);

		$this->add_responsive_control(
			'slides_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'image_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'em',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label' => __( 'Overlay', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'overlay!' => 'none',
				),
			)
		);

		$this->add_control(
			'alignment',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
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
				'default' => 'center',
				'toggle' => false,
				'separator' => 'after',
				'prefix_class' => 'cmsmasters-media-carousel__alignment-',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer .elementor-widget-cmsmasters-media-carousel__text' => 'justify-content: {{VALUE}}',
				),
				'condition' => array(
					'overlay' => 'text',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'typography_overlay',
				'label' => __( 'Text Typography', 'cmsmasters-elementor' ),
				'global' => array( 'default' => Kit_Globals::TYPOGRAPHY_TEXT ),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer .elementor-widget-cmsmasters-media-carousel__text',
				'condition' => array(
					'overlay' => 'text',
				),
			)
		);

		$this->add_responsive_control(
			'size_icon',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 26 ),
				'range' => array(
					'px' => array(
						'max' => 200,
						'step' => 1,
					),
				),
				'condition' => array(
					'overlay' => 'icon',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer .elementor-widget-cmsmasters-media-carousel__icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer .elementor-widget-cmsmasters-media-carousel__icon svg' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'color_overlay',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__overlay' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__overlay' => 'background-color: {{VALUE}};',
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
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'video_size',
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
					'11' => '1:1',
					'916' => '9:16',
				),
				'default' => '169',
				'_class_class' => 'elementor-aspect-ratio-',
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'video_icon_title',
			array(
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Video Icon', 'cmsmasters-elementor' ),
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'video_icon_color_tabs' );

		$this->start_controls_tab(
			'video_icon_color_tab',
			array(
				'label' => __( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'color_video_icon',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer .elementor-widget-cmsmasters-media-carousel__icon-video' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'video_icon_hover_tab',
			array(
				'label' => __( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'color_video_icon_hover',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer:hover .elementor-widget-cmsmasters-media-carousel__icon-video' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'icon_video',
			array(
				'label' => __( 'Video Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'recommended' => array(
					'fa-regular' => array( 'play-circle' ),
					'fa-solid' => array(
						'play',
						'play-circle',
						'video',
					),
				),
				'default' => array(
					'value' => 'far fa-play-circle',
					'library' => 'fa-regular',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'size_icon_video',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => 36 ),
				'range' => array(
					'px' => array(
						'max' => 200,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer .elementor-widget-cmsmasters-media-carousel__icon-video i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-media-carousel__outer .elementor-widget-cmsmasters-media-carousel__icon-video svg' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'lightbox_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}}' => 'background-color: {{VALUE}};',
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
					'#elementor-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button, #elementor-lightbox-slideshow-{{ID}} .elementor-swiper-button' => 'color: {{VALUE}};',
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button, #elementor-lightbox-{{ID}} .elementor-swiper-button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_ui_hover_color',
			array(
				'label' => __( 'UI Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'#elementor-lightbox-slideshow-{{ID}} .dialog-lightbox-close-button:hover, #elementor-lightbox-slideshow-{{ID}} .elementor-swiper-button:hover' => 'color: {{VALUE}};',
					'#elementor-lightbox-{{ID}} .dialog-lightbox-close-button:hover, #elementor-lightbox-{{ID}} .elementor-swiper-button:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'lightbox_video_width',
			array(
				'label' => __( 'Video Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'unit' => '%',
				),
				'range' => array(
					'%' => array(
						'min' => 50,
					),
				),
				'selectors' => array(
					'#elementor-lightbox-{{ID}} .elementor-video-container' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->slider->register_section_content( $this );
		$this->slider->register_sections_style( $this );
	}

	/**
	 * Render Slides.
	 *
	 * Retrieve media carousel widget slides.
	 *
	 * @since 1.0.0
	 * @since 1.3.1 Fixed image attributes.
	 */
	protected function render_slides() {
		$settings = $this->get_settings_for_display();

		foreach ( $settings['slides'] as $slide_index => $slide ) {
			$name_attr = "outer_{$slide_index}";
			$is_video = 'video' === $slide['type'];
			$is_image = 'image' === $slide['type'];

			$this->add_render_attribute( $name_attr, 'class', 'elementor-widget-cmsmasters-media-carousel__outer' );

			if ( $is_image ) {
				if ( 'lightbox' === $slide['image_link_to_type'] ) {
					$tag = 'a';
					$url = $slide['image']['url'];

					$this->add_render_attribute( $name_attr, array(
						'href' => $url,
						'data-elementor-open-lightbox' => 'yes',
						'data-elementor-lightbox-slideshow' => $this->get_id(),
					) );

					$this->add_render_attribute( $name_attr, 'aria-label', 'Image lightbox open' );
				} elseif ( 'custom' === $slide['image_link_to_type'] ) {
					$tag = 'a';
					$url = $slide['image_link_to']['url'];

					$this->add_render_attribute( $name_attr, 'href', $url );

					if ( $slide['image_link_to']['is_external'] ) {
						$this->add_render_attribute( $name_attr, 'target', '_blank' );
					}

					if ( $slide['image_link_to']['nofollow'] ) {
						$this->add_render_attribute( $name_attr, 'rel', 'nofollow' );
					}

					$this->add_render_attribute( $name_attr, 'aria-label', 'Image link open' );
				} else {
					$tag = 'div';
				}
			} elseif ( $is_video ) {
				$tag = 'a';
				$url = $slide['image']['url'];

				if ( 'youtube' === $slide['video_type'] ||
					'vimeo' === $slide['video_type'] ||
					'dailymotion' === $slide['video_type']
				) {
					$video_url = $slide[ $slide['video_type'] . '_url' ];
					$embed_params = $this->get_embed_params( $slide, $settings );
					$embed_options = $this->get_embed_options( $slide, $settings );
					$lightbox_url = Embed::get_embed_url( $video_url, $embed_params, $embed_options );

				} elseif ( 'hosted' === $slide['video_type'] ) {
					$lightbox_url = $this->get_hosted_video_url( $slide, $settings );
				} elseif ( 'facebook' === $slide['video_type'] ) {
					$lightbox_url = $this->get_facebook_params( $slide, $settings );
				} elseif ( 'twitch' === $slide['video_type'] ) {
					$lightbox_url = $this->get_twitch_params( $slide, $settings );
				}

				$lightbox_options = array(
					'type' => 'video',
					'videoType' => $slide['video_type'],
					'url' => $lightbox_url,
					'modalOptions' => array(
						'id' => 'elementor-lightbox-' . $this->get_id(),
						'videoAspectRatio' => $settings['aspect_ratio'],
					),
				);

				if ( 'hosted' === $slide['video_type'] ) {
					$lightbox_options['videoParams'] = $this->get_hosted_params( $settings );
				}

				$this->add_render_attribute( $name_attr, array(
					'href' => $url,
					'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
					'data-elementor-lightbox-slideshow' => $this->get_id(),
					'aria-label' => 'Video lightbox open',
				) );
			}

			$this->slider->render_slide_open();

			if ( '' !== $settings['overlay'] ) {
				$this->add_render_attribute( 'image-overlay', 'class', array(
					'elementor-widget-cmsmasters-media-carousel__overlay',
					'elementor-widget-cmsmasters-media-carousel__animation-' . $settings['overlay_animation'],
				) );
			}

			echo '<figure class="elementor-widget-cmsmasters-media-carousel__wrapper ' . esc_attr( 'elementor-repeater-item-' . $slide['_id'] ) . '">
				<' . $tag . ' ' . $this->get_render_attribute_string( $name_attr ) . '>
					<div class="elementor-widget-cmsmasters-media-carousel__inner">' .
						Group_Control_Image_Size::get_attachment_image_html( array(
							'image' => $slide['image'],
							'image_size' => $settings['image_size_size'],
						) );

			if ( 'none' !== $settings['overlay'] ) {
				echo '<div ' . $this->get_render_attribute_string( 'image-overlay' ) . '>';

				if ( 'text' === $settings['overlay'] ) {
					echo '<span class="elementor-widget-cmsmasters-media-carousel__text">' .
					wp_kses_post( $this->get_image_caption( $slide ) ) .
					'</span>';
				}

				if ( 'icon' === $settings['overlay'] ) {
					echo '<span class="elementor-widget-cmsmasters-media-carousel__icon">';

						Icons_Manager::render_icon( $settings['icon'], array( 'aria-hidden' => 'true' ) );

					echo '</span>';
				}

				echo '</div>';
			}

			if ( '' !== $settings['icon_video'] && $is_video ) {
				echo '<span class="elementor-widget-cmsmasters-media-carousel__video-icon">';

				if ( ( '' !== $settings['icon_video'] && $is_video ) ) {
					echo '<span class="elementor-widget-cmsmasters-media-carousel__icon-video">';

						Icons_Manager::render_icon( $settings['icon_video'], array( 'aria-hidden' => 'true' ) );

					echo '</span>';
				}

				echo '</span>';
			}

					echo '</div>
				</' . $tag . '>
			</figure>';

			$this->slider->render_slide_close();
		}
	}

	/**
	 * Render media carousel widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$this->slider->render( function () {
			$this->render_slides();
		} );
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve media carousel widget embed parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params( $slide, $settings ) {
		$params = array();
		$params_dictionary = array();
		$video_url = $slide[ $slide['video_type'] . '_url' ];
		$parse_url = wp_parse_url( $video_url, PHP_URL_QUERY );

		if ( 'youtube' === $slide['video_type'] ) {
			$params_dictionary = array(
				'ytb_controls' => 'controls',
				'ytb_loop' => 'loop',
				'ytb_fs' => 'fs',
				'ytb_rel' => 'rel',
				'ytb_playsinline' => 'playsinline',
			);

			if ( $settings['ytb_loop'] ) {
				$video_properties = Embed::get_video_properties( $slide['youtube_url'] );

				$params['playlist'] = $video_properties['video_id'];
			}

			if ( false !== stristr( $parse_url, 'list=' ) ) {
				$list_id = explode( '&', substr( stristr( $parse_url, 'list=' ), 5 ) );

				$params['listType'] = 'playlist';
				$params['list'] = $list_id[0];
			}

			'' === $settings['ytb_modestbranding'] ? $params['modestbranding'] = '1' : '';

			'' !== $slide['start'] ? $params['start'] = $slide['start'] : '';

			$params['wmode'] = 'opaque';

			'yes' === $settings['ytb_autoplay'] ? $params['autoplay'] = 'true' : $params['autoplay'] = 'false';

			'yes' === $settings['ytb_mute'] ? $params['mute'] = 'true' : $params['mute'] = 'false';

		} elseif ( 'vimeo' === $slide['video_type'] ) {
			$params_dictionary = array(
				'vimeo_loop' => 'loop',
				'vimeo_title' => 'title',
				'vimeo_portrait' => 'portrait',
				'vimeo_byline' => 'byline',
				'vimeo_playsinline' => 'playsinline',
			);

			$params['color'] = str_replace( '#', '', $settings['vimeo_color'] );

			$params['autopause'] = '0';

			'yes' === $settings['vimeo_autoplay'] ? $params['autoplay'] = 'true' : $params['autoplay'] = 'false';

			'yes' === $settings['vimeo_mute'] ? $params['muted'] = 'true' : $params['muted'] = 'false';

		} elseif ( 'dailymotion' === $slide['video_type'] ) {
			$params_dictionary = array(
				'dailymotion_controls' => 'controls',
				'dailymotion_showinfo' => 'ui-start-screen-info',
				'dailymotion_logo' => 'ui-logo',
				'dailymotion_playsinline' => 'playsinline',
			);

			$params['ui-highlight'] = str_replace( '#', '', $settings['dailymotion_color'] );

			$params['start'] = $slide['start'];

			'yes' === $settings['dailymotion_autoplay'] ? $params['autoplay'] = 'true' : $params['autoplay'] = 'false';

			'yes' === $settings['dailymotion_mute'] ? $params['mute'] = 'true' : $params['mute'] = 'false';

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
	 * @since 1.0.0
	 */
	private function get_embed_options( $slide, $settings ) {
		$embed_options = array();

		if ( 'youtube' === $slide['video_type'] ) {
			$embed_options['privacy'] = $settings['ytb_privacy'];
		} elseif ( 'vimeo' === $slide['video_type'] ) {
			$embed_options['start'] = $slide['start'];
		}

		return $embed_options;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param bool $from_media
	 *
	 * @return string
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

		if ( $slide['start'] ) {
			$video_url .= '#t=';
		}

		if ( $slide['start'] ) {
			$video_url .= $slide['start'];
		}

		return $video_url;
	}

	/**
	 * @since 1.0.0
	 */
	private function get_hosted_params( $settings ) {
		$video_params = array();

		if ( $settings['hosted_controls'] ) {
			$video_params['controls'] = '';
		}

		if ( 'yes' !== $settings['hosted_autoplay'] ) {
			$video_params['autoplay'] = false;
		}

		if ( $settings['hosted_playsinline'] ) {
			$video_params['playsinline'] = '';
		}

		if ( $settings['hosted_loop'] ) {
			$video_params['loop'] = '';
		}

		if ( '' === $settings['hosted_controls'] ) {
			$video_params['autoplay'] = '';
		}

		if ( '' === $settings['picture_in_picture'] ) {
			$video_params['disablePictureInPicture'] = '';
		}

		if ( ! $settings['download_button'] || ! $settings['hosted_fs'] ) {
			$video_params['controlsList'] = ( ( ! $settings['hosted_fs'] ? 'nofullscreen ' : '' ) . ( ! $settings['download_button'] ? 'nodownload' : '' ) );
		}

		return $video_params;
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve media carousel widget facebook parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video facebook parameters.
	 */
	public function get_facebook_params( $slide, $settings ) {

		$host = 'https://web.facebook.com/v2.7/plugins/video.php?';
		$url_facebook = 'href=' . esc_url( $slide['facebook_url'] );

		$options = array(
			( 'yes' === $settings['facebook_controls'] ? '&controls=true' : '&controls=false' ),
			( 'yes' === $settings['facebook_autoplay'] ? 'autoplay=true' : 'autoplay=false' ),
			( 'yes' === $settings['facebook_playsinline'] ? 'playsinline=1' : 'playsinline=0' ),
			( 'yes' === $settings['facebook_mute'] ? 'mute=true' : 'mute=false' ),
			( 'yes' === $settings['facebook_fs'] ? 'allowfullscreen=true' : 'allowfullscreen=false' ),
		);

		$facebook_video = $host . $url_facebook . implode( '&', $options );

		return $facebook_video;
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve media carousel widget twitch parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video twitch parameters.
	 */
	public function get_twitch_params( $slide, $settings ) {

		if ( wp_parse_url( $slide['twitch_url'], PHP_URL_QUERY ) ) {
			$type = '' . wp_parse_url( $slide['twitch_url'], PHP_URL_QUERY );
		} else {
			if ( ! is_numeric( basename( $slide['twitch_url'] ) ) ) {
				$type = 'channel=' . basename( $slide['twitch_url'] );
			} else {
				$type = 'video=' . basename( $slide['twitch_url'] );
			}
		}

		$options = array(
			( 'yes' === $settings['twitch_controls'] ? '&controls=true' : '&controls=false' ),
			( 'yes' === $settings['twitch_autoplay'] ? 'autoplay=true' : 'autoplay=false' ),
			( 'yes' === $settings['twitch_mute'] ? 'mute=true' : 'mute=false' ),
			( 'yes' === $settings['twitch_fs'] ? 'allowfullscreen=true' : 'allowfullscreen=false' ),
			( 'yes' === $settings['twitch_playsinline'] ? 'playsinline=true' : 'playsinline=false' ),
		);

		$host = 'https://player.twitch.tv/?';
		$parent = '&parent=' . preg_replace( '%^(htt|ft)ps?://|(www\.)%i', '', get_site_url() );
		$twitch_video = $host . $type . explode( '/', $parent )[0] . implode( '&', $options );

		return $twitch_video;
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
				'image_link_to' => array(
					'field' => 'url',
					'type' => esc_html__( 'Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'youtube_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'YouTube URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'vimeo_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Vimeo URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'dailymotion_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Dailymotion URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'facebook_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Facebook URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'twitch_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Twitch URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'external_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'External URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
		);
	}
}
