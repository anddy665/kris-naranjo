<?php
namespace CmsmastersElementor\Modules\Media\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Media\Module as MediaModule;
use CmsmastersElementor\Modules\Media\Traits\Video_Widget;
use CmsmastersElementor\Utils as CmsmastersUtils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Video stream widget.
 *
 * Widget that displays video stream.
 *
 * @since 1.0.0
 */
class Video_Stream extends Base_Widget {

	use Video_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve video stream widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Video Stream', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve video stream widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-video-stream';
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
			'stream',
			'twitch',
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
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Update for new Elementor responsive mode breakpoints.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_video',
			array( 'label' => __( 'Stream Source', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'video_type',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'youtube' => __( 'YouTube', 'cmsmasters-elementor' ),
					'twitch' => __( 'Twitch', 'cmsmasters-elementor' ),
				),
				'default' => 'youtube',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'youtube_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (YouTube)',
				'default' => MediaModule::YOUTUBE_VIDEO_URL,
				'label_block' => true,
				'condition' => array( 'video_type' => 'youtube' ),
			)
		);

		$this->add_control(
			'twitch_url',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'placeholder' => __( 'Enter your channel URL', 'cmsmasters-elementor' ) . ' (Twitch Channel)',
				'default' => MediaModule::TWITCH_VIDEO_URL,
				'label_block' => true,
				'frontend_available' => true,
				'condition' => array( 'video_type' => 'twitch' ),
			)
		);

		$this->add_control(
			'show_video_chat',
			array(
				'label' => __( 'Content', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'video' => array(
						'title' => __( 'Video', 'cmsmasters-elementor' ),
						'description' => 'Show only video',
					),
					'chat' => array(
						'title' => __( 'Chat', 'cmsmasters-elementor' ),
						'description' => 'Show only chat',
					),
					'both' => array(
						'title' => __( 'Both', 'cmsmasters-elementor' ),
						'description' => 'Show video and chat',
					),
				),
				'default' => 'video',
				'toggle' => false,
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$this->end_controls_section();

		// Started Video Options Controls
		$this->start_controls_section(
			'section_video_options',
			array(
				'label' => __( 'Video Options', 'cmsmasters-elementor' ),
				'condition' => array( 'show_video_chat!' => 'chat' ),
			)
		);

		$this->add_control(
			'controls',
			array(
				'label' => __( 'Player Controls', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'frontend_available' => true,
				'description' => 'In Twitch when controls are turned off autoplay will work automatically',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => 'Autoplay only works when mute mode is turned on',
				'frontend_available' => true,
				'condition' => array(
					'cover_image[id]' => '',
					'controls!' => '',
				),
			)
		);

		$this->add_control(
			'playsinline',
			array(
				'label' => __( 'Autoplay on Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'autoplay' => 'yes',
					'video_type' => array(
						'youtube',
						'twitch',
					),
				),
			)
		);

		$this->add_control(
			'mute',
			array(
				'label' => __( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => array(
					'controls' => 'yes',
					'autoplay' => '',
					'cover_image[id]' => '',
				),
			)
		);

		$this->add_control(
			'fs',
			array(
				'label' => __( 'Fullscreen', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
				'condition' => array( 'controls' => 'yes' ),
			)
		);

		// YouTube.
		$this->add_control(
			'cc_load_policy',
			array(
				'label' => __( 'Captions', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => array(
					'video_type' => 'youtube',
					'controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'modestbranding',
			array(
				'label' => __( 'Logo', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'video_type' => 'youtube',
					'controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'youtube',
			)
		);

		// Global.
		$this->add_control(
			'video_minimize',
			array(
				'label' => __( 'Minimize on Scroll', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'separator' => 'before',
				'description' => 'When Minimize option is turned on without choosing Video Cover Image, video is always minimized',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'show_video_chat',
									'operator' => '!==',
									'value' => 'chat',
								),
								array(
									'name' => 'lightbox',
									'operator' => '!==',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'show_video_chat',
									'operator' => '!==',
									'value' => 'chat',
								),
								array(
									'name' => 'cover_image[id]',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'lightbox',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'video_minimize_overlay',
			array(
				'label' => __( 'Minimize only after click on Cover Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'description' => 'When Cover Image is set, minimize video only after clicking on Cover Image',
				'condition' => array(
					'lightbox!' => 'yes',
					'video_minimize' => 'yes',
					'show_video_chat!' => 'chat',
					'cover_image[id]!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cover_image_play_icon',
			array(
				'label' => __( 'Cover Image / Play Icon', 'cmsmasters-elementor' ),
				'condition' => array( 'autoplay!' => 'yes' ),
			)
		);

		$this->add_control(
			'cover_image',
			array(
				'label' => __( 'Choose Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic' => array( 'active' => true ),
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'cover_image',
				'default' => 'full',
				'separator' => 'none',
				'condition' => array( 'cover_image[id]!' => '' ),
			)
		);

		$this->add_control(
			'play_icon_note',
			array(
				'raw' => '<strong>' . __( 'Please note!', 'cmsmasters-elementor' ) . '</strong> ' . __( 'Choose Cover Image first to see the Play Icon settings.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
				'condition' => array( 'cover_image[id]' => '' ),
			)
		);

		$this->add_control(
			'show_play_icon',
			array(
				'label' => __( 'Play Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
				'condition' => array( 'cover_image[id]!' => '' ),
			)
		);

		$this->add_control(
			'play_icon',
			array(
				'type' => Controls_Manager::ICONS,
				'label' => esc_html__( 'Icon for Overlay', 'cmsmasters-elementor' ),
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
				'condition' => array(
					'cover_image[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'lightbox',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Off', 'cmsmasters-elementor' ),
				'label_on' => __( 'On', 'cmsmasters-elementor' ),
				'frontend_available' => true,
				'separator' => 'before',
				'condition' => array(
					'cover_image[id]!' => '',
					'show_video_chat' => 'video',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_container_style',
			array(
				'label' => __( 'Container', 'cmsmasters-elementor' ),
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
				'prefix_class' => 'elementor-aspect-ratio-',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'container_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 1500,
					),
					'%' => array(
						'min' => 30,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__container' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'container_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__container ' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_cover_style',
			array(
				'label' => __( 'Cover Image / Play Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'cover_image[id]!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__cover-image',
			)
		);

		$this->add_control(
			'play_icon_title',
			array(
				'label' => __( 'Play Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cover_image[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'play_icon_effect',
			array(
				'label' => __( 'Blinking effect', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'On', 'cmsmasters-elementor' ),
				'label_on' => __( 'Off', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array(
					'cover_image[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'play_icon_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__play-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__play-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'cover_image[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'play_icon_color_hover',
			array(
				'label' => __( 'Color on Hover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__wrap:hover .elementor-widget-cmsmasters-video-stream__play-icon i:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__wrap:hover .elementor-widget-cmsmasters-video-stream__play-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'cover_image[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'play_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 300,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__play-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__play-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__play-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cover_image[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'play_icon_text_shadow',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => __( 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__play-icon i:before',
				'condition' => array(
					'cover_image[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_chat_style',
			array(
				'label' => __( 'Chat', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_video_chat!' => 'video',
				),
			)
		);

		$this->add_control(
			'video_chat_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'right',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-video-chat-position-',
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-video-chat-position-left .elementor-widget-cmsmasters-video-stream__inner > .elementor-widget-cmsmasters-video-stream__video-chat' => 'left:0;',
					'{{WRAPPER}}.cmsmasters-video-chat-position-left .elementor-widget-cmsmasters-video-stream__container .elementor-widget-cmsmasters-video-stream__wrap .elementor-widget-cmsmasters-video-stream__inner iframe' => 'right:0;',
					'{{WRAPPER}}.cmsmasters-video-chat-position-right .elementor-widget-cmsmasters-video-stream__inner > .elementor-widget-cmsmasters-video-stream__video-chat' => 'right:0;',
					'{{WRAPPER}}.cmsmasters-video-chat-position-right .elementor-widget-cmsmasters-video-stream__container .elementor-widget-cmsmasters-video-stream__wrap .elementor-widget-cmsmasters-video-stream__inner iframe' => 'left:0;',
				),
				'condition' => array( 'show_video_chat' => 'both' ),
			)
		);

		$breakpoints = CmsmastersUtils::get_breakpoints();

		$this->add_control(
			'video_chat_breakpoints',
			array(
				'label' => __( 'Breakpoint', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					/* translators: Tablet breakpoint %d: number in pixels. */
					'tablet' => sprintf( __( 'Tablet (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['tablet'] + 1 ),
					/* translators: Mobile breakpoint %d: number in pixels. */
					'mobile' => sprintf( __( 'Mobile (< %dpx)', 'cmsmasters-elementor' ), $breakpoints['mobile'] + 1 ),
				),
				'default' => 'tablet',
				'description' => 'Hide on resolutions below chosen',
				'prefix_class' => 'cmsmasters-video-chat-disable-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'video_chat_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 200,
						'max' => 1500,
					),
					'%' => array(
						'min' => 20,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__inner > .elementor-widget-cmsmasters-video-stream__video-chat' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__inner > iframe' => 'width: calc( 100% - {{SIZE}}{{UNIT}} );',
				),
				'condition' => array(
					'video_chat_position' => array(
						'right',
						'left',
					),
				),
			)
		);

		$this->add_control(
			'video_chat_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 350,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__video-chat' => 'height: {{VALUE}}px',
				),
				'condition' => array( 'video_chat_position' => 'bottom' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'cover_image[id]!' => '',
					'lightbox' => 'yes',
					'show_video_chat' => 'video',
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
				'size_units' => array( '%' ),
				'range' => array(
					'%' => array( 'min' => 30 ),
				),
				'default' => array( 'unit' => '%' ),
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
					'middle' => array(
						'title' => __( 'Center', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-middle',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
				),
				'selectors_dictionary' => array( 'top' => 'top: 60px' ),
				'default' => 'middle',
				'frontend_available' => true,
				'toggle' => false,
				'selectors' => array(
					'#elementor-lightbox-{{ID}} .elementor-video-container' => '{{VALUE}}; transform: translateX(-50%);',
				),
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

		$this->start_controls_section(
			'section_minimize_style',
			array(
				'label' => __( 'Minimize', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'lightbox',
									'operator' => '!==',
									'value' => 'yes',
								),
								array(
									'name' => 'video_minimize',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'cover_image[id]',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'lightbox',
									'operator' => '=',
									'value' => 'yes',
								),
								array(
									'name' => 'video_minimize',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		$video_aspect_ratio_selectors = array(
			'{{WRAPPER}} .minimize .elementor-widget-cmsmasters-video-stream__inner' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
		);

		$video_aspect_ratio = array(
			'219' => '0.428571',
			'169' => '0.5625',
			'43' => '0.75',
			'32' => '0.666666',
			'11' => '1',
			'916' => '1.778',
		);

		foreach ( $video_aspect_ratio as $ratio => $index ) {
			$ratio_class = "{{WRAPPER}}.elementor-aspect-ratio-{$ratio} .minimize .elementor-widget-cmsmasters-video-stream__inner";
			$selector = "height: calc( {{SIZE}}{{UNIT}} * {$index} ); max-height: calc( {{SIZE}}{{UNIT}} * {$index} );";

			$video_aspect_ratio_selectors[ $ratio_class ] = $selector;
		}

		$this->add_responsive_control(
			'video_minimize_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 280,
						'max' => 600,
					),
				),
				'default' => array( 'size' => '280' ),
				'selectors' => $video_aspect_ratio_selectors,
			)
		);

		$this->add_control(
			'video_on_scroll_ver_position',
			array(
				'label' => __( 'Vertical Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
				),
				'default' => 'bottom',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-ver-position-on-scroll-',
			)
		);

		$this->add_control(
			'video_on_scroll_hor_position',
			array(
				'label' => __( 'Horizontal Position', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
				),
				'default' => 'right',
				'prefix_class' => 'cmsmasters-hor-position-on-scroll-',
				'toggle' => false,
			)
		);

		$this->add_control(
			'video_on_scroll_animation',
			array(
				'label' => __( 'Animation Direction', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'up' => array(
						'title' => __( 'Up', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-top',
					),
					'down' => array(
						'title' => __( 'Down', 'cmsmasters-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
				),
				'default' => 'up',
				'prefix_class' => 'cmsmasters-animation-on-scroll-',
				'toggle' => false,
			)
		);

		$this->add_responsive_control(
			'video_minimize_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-ver-position-on-scroll-top .minimize .elementor-widget-cmsmasters-video-stream__inner' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					'{{WRAPPER}}.cmsmasters-ver-position-on-scroll-bottom .minimize .elementor-widget-cmsmasters-video-stream__inner' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					'{{WRAPPER}}.cmsmasters-hor-position-on-scroll-right .minimize .elementor-widget-cmsmasters-video-stream__inner' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					'{{WRAPPER}}.cmsmasters-hor-position-on-scroll-left .minimize .elementor-widget-cmsmasters-video-stream__inner' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			)
		);

		$this->add_control(
			'video_minimize_close_but_heading',
			array(
				'label' => __( 'Close Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'video_minimize_close_but',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-show-button-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'video_minimize_close_but_view',
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
				'prefix_class' => 'cmsmasters-close-but-view-',
				'condition' => array( 'video_minimize_close_but' => 'yes' ),
			)
		);

		$this->add_control(
			'video_minimize_close_but_shape',
			array(
				'label' => __( 'Shape', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'square' => array( 'title' => __( 'Square', 'cmsmasters-elementor' ) ),
					'circle' => array( 'title' => __( 'Circle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'square',
				'label_block' => false,
				'prefix_class' => 'cmsmasters-close-but-shape-',
				'condition' => array(
					'video_minimize_close_but' => 'yes',
					'video_minimize_close_but_view!' => 'default',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_video_minimize_close_but_style',
			array( 'condition' => array( 'video_minimize_close_but' => 'yes' ) )
		);

		$this->start_controls_tab(
			'tab_video_minimize_close_but_normal',
			array( 'label' => __( 'Normal', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'video_minimize_close_but_primary',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-but-view-framed .elementor-widget-cmsmasters-video-stream__close-button' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'video_minimize_close_but_secondary',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'video_minimize_close_but_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_video_minimize_close_but_hover',
			array( 'label' => __( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'video_minimize_close_but_primary_hover',
			array(
				'label' => __( 'Primary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-but-view-framed .elementor-widget-cmsmasters-video-stream__close-button:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'video_minimize_close_but_secondary_hover',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button:hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'video_minimize_close_but_view!' => 'default' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'video_minimize_close_but_size',
			array(
				'label' => __( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 30,
					),
				),
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'video_minimize_close_but' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'video_minimize_square_close_but_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'video_minimize_close_but' => 'yes',
					'video_minimize_close_but_view!' => 'default',
					'video_minimize_close_but_shape' => 'square',
				),
			)
		);

		$this->add_responsive_control(
			'video_minimize_circle_close_but_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'padding: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'video_minimize_close_but' => 'yes',
					'video_minimize_close_but_view!' => 'default',
					'video_minimize_close_but_shape' => 'circle',
				),
			)
		);

		$this->add_control(
			'video_minimize_circle_close_but_border_width',
			array(
				'label' => __( 'Border Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'video_minimize_close_but' => 'yes',
					'video_minimize_close_but_view' => 'framed',
				),
			)
		);

		$this->add_control(
			'video_minimize_circle_close_but_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'video_minimize_close_but' => 'yes',
					'video_minimize_close_but_view!' => 'default',
				),
			)
		);

		$this->add_responsive_control(
			'video_minimize_close_but_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'video_minimize_close_but' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'video_minimize_circle_close_but_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-stream__close-button',
				'condition' => array(
					'video_minimize_close_but' => 'yes',
					'video_minimize_close_but_view!' => 'default',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render video stream widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$video_type = $settings['video_type'];

		if ( empty( $settings[ $video_type . '_url' ] ) ) {
			return;
		}

		$lightbox = $settings['lightbox'];
		$chat_position = $settings['video_chat_position'];
		$show_video_chat = $settings['show_video_chat'];

		if ( ! $lightbox ) {
			$this->add_render_attribute( 'video', 'id', $video_type );
			$this->add_render_attribute( 'video', 'class', 'elementor-widget-cmsmasters-video-stream__inner' );
		}

		$this->add_render_attribute( 'video-stream-container', array(
			'class' => array(
				'elementor-widget-cmsmasters-video-stream__container',
				'elementor-open-' . ( $lightbox ? 'lightbox' : 'inline' ),
				( '' === $settings['video_minimize_overlay'] ? 'minimize_always' : '' ),
				( 'both' === $show_video_chat ? 'show_video_chat' : '' ),
			),
		) );

		echo '<div ' . $this->get_render_attribute_string( 'video-stream-container' ) . '">' .
			'<div class="elementor-widget-cmsmasters-video-stream__wrap elementor-fit-aspect-ratio">' .
				'<div ' . $this->get_render_attribute_string( 'video' ) . '>' .
					'<span class="elementor-widget-cmsmasters-video-stream__close-button eicon-close"></span>';

		if ( ! $lightbox ) {
			echo '<iframe 
				class="elementor-video-iframe" ' .
				( ! empty( $settings['cover_image']['id'] ) ? 'data-lazy-load' : 'src' ) . '="' . esc_url( $this->get_video() ) . '" 
				allowfullscreen 
				frameborder="0" 
				scrolling="no" 
				title="' . esc_attr( $settings['video_type'] ) . ' video player"></iframe>';

			if ( ( 'bottom' !== $chat_position ) && 'video' !== $show_video_chat ) {
				$this->get_video_chat_params();
			}
		}

		if ( 'bottom' !== $chat_position ) {
			$this->cover_image();
		}

				echo '</div>' .
			'</div>';

		if ( 'bottom' === $chat_position ) {
			if ( ! $lightbox && 'video' !== $show_video_chat ) {
				$this->get_video_chat_params();
			}

			$this->cover_image();
		}

		echo '</div>';
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video stream widget parameters.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Fixed getting YouTube video id for short link.
	 *
	 * @return array Video stream parameters.
	 */
	public function get_video() {
		$settings = $this->get_settings_for_display();

		$video_type = $settings['video_type'];
		$youtube_id = '';
		$protocol = PHP_URL_QUERY;

		if ( 'youtube' === $video_type ) {
			$youtube_id = wp_parse_url( $settings[ $video_type . '_url' ], $protocol );
		}

		$protocol = ( ( 'youtube' === $video_type && $youtube_id ) ? $protocol : PHP_URL_PATH );

		$video_id = wp_parse_url( $settings[ $video_type . '_url' ], $protocol );

		if ( 'youtube' === $video_type ) {
			$video_id = str_replace( 'v=', '', $video_id );

			if ( ! $youtube_id ) {
				$video_id = str_replace( '/', '', $video_id );
			}
		}

		$get_video_params = 'get_' . $video_type . '_video_params';

		return $this->$get_video_params( $video_id );
	}

	/**
	 * Get youtube chat params.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video stream youtube chat parameters.
	 */
	public function get_youtube_video_params( $video_id ) {
		$settings = $this->get_settings_for_display();

		$host = 'https://www.youtube.com/embed/';

		$autoplay_setting = $settings['autoplay'];

		$options = array(
			( $settings['controls'] ? '?controls=1' : '?controls=0' ),
			( ! $settings['lightbox'] ? ( $autoplay_setting ? 'autoplay=1' : 'autoplay=0' ) : 'autoplay=0' ),
			( ! $settings['lightbox'] ? ( ( ( $autoplay_setting ) || ( ! $autoplay_setting && $settings['mute'] ) ) ? 'mute=true' : 'mute=false' ) : 'mute=true' ),
			( $settings['fs'] ? 'fs=1' : 'fs=0' ),
			( $autoplay_setting && $settings['playsinline'] ? 'playsinline=1' : 'playsinline=0' ),
			( $settings['modestbranding'] ? 'modestbranding=0' : 'modestbranding=1' ),
			( $settings['cc_load_policy'] ? 'cc_load_policy=1' : 'cc_load_policy=0' ),
			'wmode=opaque',
		);

		$url = $host . $video_id . implode( '&', $options ) . '"';

		return $url;
	}

	/**
	 * Get twitch chat params.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video stream twitch chat parameters.
	 */
	public function get_twitch_video_params( $video_id ) {
		$settings = $this->get_settings_for_display();

		$parent = '&parent=' . preg_replace( '%^(htt|ft)ps?://|(www\.)%i', '', get_site_url() );
		$controls_setting = $settings['controls'];
		$autoplay_setting = $settings['autoplay'];

		$controls = ( $controls_setting ? 'controls=true' : 'controls=false' );

		if ( ! $controls_setting || $settings['lightbox'] ) {
			$autoplay = 'autoplay=true';
			$muted = 'muted=true';
		} else {
			if ( $autoplay_setting ) {
				$autoplay = 'autoplay=true';
			} else {
				$autoplay = 'autoplay=false';
			}

			if ( $autoplay_setting || ( ! $autoplay_setting && $settings['mute'] ) ) {
				$muted = 'muted=true';
			} else {
				$muted = 'muted=false';
			}
		}

		if ( $autoplay_setting && $settings['playsinline'] ) {
			$playsinline = 'playsinline=1';
		} else {
			$playsinline = 'playsinline=0';
		}

		if ( $settings['fs'] ) {
			$allowfullscreen = 'allowfullscreen=true';
		} else {
			$allowfullscreen = 'allowfullscreen=false';
		}

		$params = array(
			explode( '/', $parent )[0],
			$controls,
			$autoplay,
			$playsinline,
			$muted,
			$allowfullscreen,
			'layout=video',
		);

		$host = 'https://player.twitch.tv/?channel=';

		$url = $host . str_replace( '/', '', $video_id ) . implode( '&', $params );

		return $url;
	}

	/**
	 * Get chat params.
	 *
	 * Retrieve video stream widget chat parameters.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Fixed getting YouTube video id for short link.
	 *
	 * @return array Video stream chat parameters.
	 */
	public function get_video_chat_params() {
		$settings = $this->get_settings_for_display();

		$video_type = $settings['video_type'];
		$youtube_id = '';
		$protocol = PHP_URL_PATH;

		if ( 'youtube' === $video_type ) {
			$youtube_id = wp_parse_url( $settings['youtube_url'], PHP_URL_QUERY );

			if ( $youtube_id ) {
				$protocol = PHP_URL_QUERY;
			}
		}

		$video_id = wp_parse_url( $settings[ $video_type . '_url' ], $protocol );

		if ( 'youtube' === $video_type ) {
			$video_id = str_replace( '/', '', $video_id );

			if ( ! $youtube_id ) {
				$video_id = 'v=' . $video_id;
			}
		}

		$get_video_params = 'get_' . $video_type . '_chat_params';

		$lazy_load = ( ! empty( $settings['cover_image']['id'] ) ? 'data-lazy-load=' : 'src=' );
		$src = $lazy_load . $this->$get_video_params( $video_id );

		$this->add_render_attribute( 'stream-video-chat', array(
			'class' => 'elementor-widget-cmsmasters-video-stream__live-chat',
			( ! empty( $settings['cover_image']['id'] ) ? 'data-lazy-load' : 'src' ) => $this->$get_video_params( $video_id ),
			'title' => array(
				esc_attr( $video_type ),
				'live',
				'chat',
			),
		) );

		echo '<div class="elementor-widget-cmsmasters-video-stream__video-chat">' .
			'<iframe ' . $this->get_render_attribute_string( 'stream-video-chat' ) . '></iframe>' .
		'</div>';
	}

	/**
	 * Get youtube chat params.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video stream youtube chat parameters.
	 */
	public function get_youtube_chat_params( $video_id ) {
		$host = 'https://www.youtube.com/live_chat?';

		$domain = ( isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '' );
		$domain = str_replace( 'www.', '', $domain );
		$url = $host . $video_id . '&embed_domain=' . $domain;

		return $url;
	}

	/**
	 * Get twitch chat params.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video stream twitch chat parameters.
	 */
	public function get_twitch_chat_params( $video_id ) {
		$host = 'https://go.twitch.tv/embed/';

		$parent = 'parent=' . preg_replace( '%^(htt|ft)ps?://|(www\.)%i', '', get_site_url() );

		$url = $host . str_replace( '/', '', $video_id ) . '/chat?' . explode( '/', $parent )[0];

		return $url;
	}

	/**
	 * Print play video text output on the frontend.
	 *
	 * Written in PHP and used to generate the play video text.
	 *
	 * @since 1.15.4
	 */
	public function print_a11y_text( $cover_image ) {
		if ( empty( $cover_image['alt'] ) ) {
			return esc_html__( 'Play Video', 'cmsmasters-elementor' );
		} else {
			return esc_html__( 'Play Video about', 'cmsmasters-elementor' ) . ' ' . esc_attr( $cover_image['alt'] );
		}
	}

	/**
	 * Whether the video stream widget has an cover image or not.
	 *
	 * Used to determine whether an cover image was set for the video stream.
	 *
	 * @since 1.0.0
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 * @since 1.3.8 Fixed `Entrance Animation` control when responsive.
	 * @since 1.15.4 Replaced elementor-screen-only on aria-label attribute.
	 *
	 * @return bool Whether an cover image was set for the video stream.
	 */
	protected function cover_image() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['cover_image']['id'] ) ) {
			$this->add_render_attribute(
				'cover-image',
				array(
					'class' => 'elementor-widget-cmsmasters-video-stream__cover-image',
					'role' => 'button',
					'tabindex' => '0',
					'aria-label' => 'Play video',
				)
			);

			if ( $settings['lightbox'] ) {
				$lightbox_url = esc_url( $this->get_video() );
				$lightbox_animation = ( isset( $settings['lightbox_animation_entrance'] ) ? $settings['lightbox_animation_entrance'] : '' );
				$lightbox_animation_tablet = ( isset( $settings['lightbox_animation_entrance_tablet'] ) ? $settings['lightbox_animation_entrance_tablet'] : '' );
				$lightbox_animation_mobile = ( isset( $settings['lightbox_animation_entrance_mobile'] ) ? $settings['lightbox_animation_entrance_mobile'] : '' );

				$lightbox_options = array(
					'type' => 'video',
					'videoType' => $settings['video_type'],
					'url' => str_replace( '&autoplay=0', '', $lightbox_url ),
					'modalOptions' => array(
						'id' => 'elementor-lightbox-' . $this->get_id(),
						'entranceAnimation' => $lightbox_animation,
						'entranceAnimation_tablet' => $lightbox_animation_tablet,
						'entranceAnimation_mobile' => $lightbox_animation_mobile,
						'videoAspectRatio' => $settings['aspect_ratio'],
					),
				);

				$this->add_render_attribute( 'cover-image', array(
					'data-elementor-open-lightbox' => 'yes',
					'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
				) );

				if ( Plugin::$instance->editor->is_edit_mode() ) {
					$this->add_render_attribute( 'cover-image', array( 'class' => 'elementor-clickable' ) );
				}
			}

			echo '<div ' . $this->get_render_attribute_string( 'cover-image' ) . '>';

			if ( '' !== $settings['cover_image']['id'] ) {
				echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'cover_image' );
			}

			if ( 'yes' === $settings['show_play_icon'] && ! empty( $settings['play_icon']['value'] ) ) {
				echo '<div class="elementor-widget-cmsmasters-video-stream__play-icon' . ( '' === $settings['play_icon_effect'] ? ' disable_effect' : '' ) . '">';

					Icons_Manager::render_icon(
						$settings['play_icon'],
						array(
							'aria-hidden' => 'true',
							'aria-label' => esc_attr( $this->print_a11y_text( $settings['cover_image'] ) ),
						)
					);

				echo '</div>';
			}

			echo '</div>';
		}
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
		$style_depends = array(
			'widget-cmsmasters-video-stream',
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
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'youtube_url',
				'type' => esc_html__( 'YouTube URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'twitch_url',
				'type' => esc_html__( 'Twitch URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
