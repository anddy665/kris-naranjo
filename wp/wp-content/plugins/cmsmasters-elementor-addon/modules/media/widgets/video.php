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
use Elementor\Icons_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Video widget.
 *
 * Widget that displays video.
 *
 * @since 1.0.0
 */
class Video extends Base_Widget {

	use Video_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve video widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Video', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve video widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-video';
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
			'playlist',
			'vimeo',
			'dailymotion',
			'facebook',
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
	 * @since 1.1.0 Swapped the "URL" and "Poster" controls and disabled options for "URL" control.
	 * @since 1.5.1 Fixed cover image with insert url.
	 * @since 1.11.8 Fixed Video widget if parent container flex-wrap - wrap.
	 * @since 1.16.4 Added `Custom Aspect Ratio` control and `Custom` option for `Aspect Ratio` control.
	 * @since 1.16.4 Added `Start Autoplay` control for autoplay video on hover ans scroll.
	 * @since 1.17.1 Added `Custom Aspect Ratio` control for the cover image if a lightbox is installed.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_video',
			array( 'label' => __( 'Video', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'video_type',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'youtube' => __( 'YouTube', 'cmsmasters-elementor' ),
					'vimeo' => __( 'Vimeo', 'cmsmasters-elementor' ),
					'dailymotion' => __( 'Dailymotion', 'cmsmasters-elementor' ),
					'facebook' => __( 'Facebook', 'cmsmasters-elementor' ),
					'twitch' => __( 'Twitch', 'cmsmasters-elementor' ),
					'hosted' => __( 'Self Hosted', 'cmsmasters-elementor' ),
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
				'description' => 'To display the playlist insert the playlist link',
				'condition' => array( 'video_type' => 'youtube' ),
			)
		);

		$this->add_control(
			'vimeo_url',
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
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (Vimeo)',
				'default' => MediaModule::VIMEO_VIDEO_URL,
				'label_block' => true,
				'condition' => array( 'video_type' => 'vimeo' ),
			)
		);

		$this->add_control(
			'dailymotion_url',
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
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (Dailymotion)',
				'default' => MediaModule::DAILYMOTION_VIDEO_URL,
				'label_block' => true,
				'condition' => array( 'video_type' => 'dailymotion' ),
			)
		);

		$this->add_control(
			'facebook_url',
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
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (Facebook)',
				'default' => MediaModule::FACEBOOK_VIDEO_URL,
				'label_block' => true,
				'condition' => array( 'video_type' => 'facebook' ),
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
				'placeholder' => __( 'Enter your URL', 'cmsmasters-elementor' ) . ' (Twitch Collection)',
				'default' => MediaModule::TWITCH_VIDEO_URL,
				'label_block' => true,
				'frontend_available' => true,
				'condition' => array( 'video_type' => 'twitch' ),
			)
		);

		$this->add_control(
			'insert_url',
			array(
				'label' => __( 'External URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array( 'video_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'hosted_url',
			array(
				'label' => __( 'Choose Video', 'cmsmasters-elementor' ),
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

		$this->add_control(
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

		$this->add_control(
			'poster',
			array(
				'label' => __( 'Poster', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => array( 'video_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'autoplay_description_6',
			array(
				'raw' => __( 'If both Poster and Cover Image are set, Poster will appear hidden under Cover Image.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition' => array(
					'video_type' => 'hosted',
					'cover_image[url]!' => '',
					'poster[id]!' => '',
				),
			)
		);

		$this->add_control(
			'start',
			array(
				'label' => __( 'Start Time', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'noCalendar' => true,
					'time_24hr' => true,
					'enableSeconds' => true,
					'defaultHour' => 0,
					'dateFormat' => 'H:i:S',
				),
				'separator' => 'before',
				'condition' => array(
					'loop' => '',
					'video_type!' => array(
						'facebook',
						'twitch',
					),
				),
			)
		);

		$this->add_control(
			'end',
			array(
				'label' => __( 'End Time', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::DATE_TIME,
				'picker_options' => array(
					'noCalendar' => true,
					'time_24hr' => true,
					'enableSeconds' => true,
					'defaultHour' => 0,
					'dateFormat' => 'H:i:S',
				),
				'condition' => array(
					'loop' => '',
					'video_type' => array(
						'youtube',
						'hosted',
					),
				),
			)
		);

		$this->add_control(
			'end_description',
			array(
				'raw' => __( 'When Loop setting is enabled, Start and End Time settings will not be applied.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'loop' => '',
					'video_type' => array(
						'youtube',
						'hosted',
					),
				),
			)
		);

		$this->end_controls_section();

		// Started Video Options Controls
		$this->start_controls_section(
			'section_video_options',
			array( 'label' => __( 'Video Options', 'cmsmasters-elementor' ) )
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
				'condition' => array(
					'video_type!' => array(
						'vimeo',
						'facebook',
						'twitch',
					),
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label' => __( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'video_type',
									'operator' => '!==',
									'value' => 'hosted',
								),
								array(
									'name' => 'cover_image[url]',
									'operator' => '=',
									'value' => '',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'video_type',
									'operator' => '=',
									'value' => 'hosted',
								),
								array(
									'name' => 'controls',
									'operator' => '=',
									'value' => 'yes',
								),
								array(
									'name' => 'cover_image[url]',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'poster[id]',
									'operator' => '=',
									'value' => '',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'autoplay_type',
			array(
				'label' => __( 'Start Autoplay', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'disable' => array( 'title' => __( 'Disable', 'cmsmasters-elementor' ) ),
					'hover' => array( 'title' => __( 'Hover', 'cmsmasters-elementor' ) ),
					'scroll' => array( 'title' => __( 'Scroll', 'cmsmasters-elementor' ) ),
				),
				'default' => 'disable',
				'label_block' => false,
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'video_type',
									'operator' => '===',
									'value' => 'youtube',
								),
								array(
									'name' => 'video_type',
									'operator' => '===',
									'value' => 'vimeo',
								),
								array(
									'name' => 'video_type',
									'operator' => '===',
									'value' => 'hosted',
								),
							),
						),
						array(
							'name' => 'autoplay',
							'operator' => '!==',
							'value' => 'yes',
						),
						array(
							'name' => 'lightbox',
							'operator' => '!==',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'autoplay_description',
			array(
				'raw' => __( 'When Cover Image is set, Autoplay and Mute settings will not be accessible. Video will play upon click on Cover Image and will be muted.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'video_type!' => 'hosted',
					'cover_image[url]!' => '',
				),
			)
		);

		// For Hosted
		$this->add_control(
			'autoplay_description_2',
			array(
				'raw' => __( 'When Player Controls are disabled, Autoplay and Mute settings will not be accessible. Video will play upon loading and will be muted.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'video_type' => 'hosted',
					'controls' => '',
					'cover_image[url]' => '',
					'poster[id]' => '',
				),
			)
		);

		$this->add_control(
			'autoplay_description_3',
			array(
				'raw' => __( 'When Cover Image or Poster are set, Autoplay setting will not be accessible. Video will play upon click on Cover Image or Poster.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'video_type',
							'operator' => '=',
							'value' => 'hosted',
						),
						array(
							'name' => 'controls',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'cover_image[url]',
									'operator' => '!==',
									'value' => '',
								),
								array(
									'name' => 'poster[id]',
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
			'autoplay_description_4',
			array(
				'raw' => __( 'When Cover Image is set and Player Controls are disabled, Autoplay setting will not be accessible. Video will play upon click on Cover Image.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'video_type' => 'hosted',
					'controls' => '',
					'cover_image[url]!' => '',
					'poster[id]' => '',
				),
			)
		);

		$this->add_control(
			'autoplay_description_5',
			array(
				'raw' => __( 'When Poster is set and Player Controls are disabled, Autoplay and Mute settings will not be accessible. Video will play upon loading and will be muted.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'video_type' => 'hosted',
					'controls' => '',
					'cover_image[url]' => '',
					'poster[id]!' => '',
				),
			)
		);

		// Other
		$this->add_control(
			'autoplay_description_7',
			array(
				'raw' => __( 'When Autoplay is enabled, Mute setting will not be accessible. Video will play upon loading and will be muted.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'cover_image[url]',
							'operator' => '=',
							'value' => '',
						),
						array(
							'name' => 'autoplay',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'name' => 'video_type',
									'operator' => '!==',
									'value' => 'hosted',
								),
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'video_type',
											'operator' => '=',
											'value' => 'hosted',
										),
										array(
											'name' => 'controls',
											'operator' => '=',
											'value' => 'yes',
										),
										array(
											'name' => 'poster[id]',
											'operator' => '=',
											'value' => '',
										),
									),
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'playsinline',
			array(
				'label' => __( 'Autoplay On Mobile', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'cover_image[url]',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'video_type',
									'operator' => 'in',
									'value' => array(
										'youtube',
										'vimeo',
										'dailymotion',
										'facebook',
									),
								),
								array(
									'name' => 'autoplay',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'cover_image[url]',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'controls',
									'operator' => '=',
									'value' => 'yes',
								),
								array(
									'name' => 'video_type',
									'operator' => '=',
									'value' => 'twitch',
								),
								array(
									'name' => 'autoplay',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'controls',
									'operator' => '=',
									'value' => 'yes',
								),
								array(
									'name' => 'video_type',
									'operator' => '=',
									'value' => 'hosted',
								),
								array(
									'name' => 'cover_image[url]',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'poster[id]',
									'operator' => '=',
									'value' => '',
								),
								array(
									'name' => 'autoplay',
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
			'mute',
			array(
				'label' => __( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'cover_image[url]',
							'operator' => '=',
							'value' => '',
						),
						array(
							'name' => 'autoplay',
							'operator' => '=',
							'value' => '',
						),
						array(
							'relation' => 'or',
							'terms' => array(
								array(
									'relation' => 'and',
									'terms' => array(
										array(
											'name' => 'video_type',
											'operator' => '=',
											'value' => 'hosted',
										),
										array(
											'name' => 'controls',
											'operator' => '=',
											'value' => 'yes',
										),
										array(
											'name' => 'poster[id]',
											'operator' => '=',
											'value' => '',
										),
									),
								),
								array(
									'name' => 'video_type',
									'operator' => '!==',
									'value' => 'hosted',
								),
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array(
					'video_type' => array(
						'youtube',
						'vimeo',
						'hosted',
					),
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
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'video_type',
									'operator' => 'in',
									'value' => array(
										'youtube',
										'hosted',
									),
								),
								array(
									'name' => 'controls',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
						array(
							'name' => 'video_type',
							'operator' => 'in',
							'value' => array(
								'facebook',
								'twitch',
							),
						),
					),
				),
			)
		);

		$this->add_control(
			'showinfo',
			array(
				'label' => __( 'Video Info', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'video_type' => 'dailymotion' ),
			)
		);

		$this->add_control(
			'logo',
			array(
				'label' => __( 'Show Dailymotion Logo', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'video_type' => 'dailymotion' ),
			)
		);

		$this->add_control(
			'color',
			array(
				'label' => __( 'Controls Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'separator' => 'before',
				'condition' => array(
					'video_type' => array(
						'vimeo',
						'dailymotion',
					),
				),
			)
		);

		// Vimeo.
		$this->add_control(
			'vimeo_portrait',
			array(
				'label' => __( 'Intro Portrait', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'video_type' => 'vimeo' ),
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
				'condition' => array( 'video_type' => 'vimeo' ),
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
				'condition' => array( 'video_type' => 'vimeo' ),
			)
		);

		// Hosted
		$this->add_control(
			'download_button',
			array(
				'label' => __( 'Download Button', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'condition' => array(
					'video_type' => 'hosted',
					'controls' => 'yes',
				),
			)
		);

		$this->add_control(
			'picture_in_picture',
			array(
				'label' => __( 'Picture In Picture', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'condition' => array(
					'video_type' => 'hosted',
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
				'label' => __( 'Show YouTube Logo', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array(
					'video_type' => 'youtube',
					'controls' => 'yes',
				),
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
							'name' => 'lightbox',
							'operator' => '!==',
							'value' => 'yes',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'cover_image[url]',
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
			'video_minimize_overlay_description',
			array(
				'raw' => __( 'To minimize video only when played, you need to set Cover image first.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array(
					'lightbox!' => 'yes',
					'video_minimize' => 'yes',
					'cover_image[url]' => '',
				),
			)
		);

		$this->add_control(
			'video_minimize_overlay',
			array(
				'label' => __( 'Minimize only when played', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => array(
					'lightbox!' => 'yes',
					'video_minimize' => 'yes',
				),
			)
		);

		$this->add_control(
			'rel',
			array(
				'label' => __( 'Suggested Videos', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Current Video Channel', 'cmsmasters-elementor' ),
					'yes' => __( 'Any Video', 'cmsmasters-elementor' ),
				),
				'separator' => 'before',
				'condition' => array( 'video_type' => 'youtube' ),
			)
		);

		$this->add_control(
			'yt_privacy',
			array(
				'label' => __( 'Privacy Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'description' => __( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'cmsmasters-elementor' ),
				'separator' => 'before',
				'condition' => array( 'video_type' => 'youtube' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cover_image',
			array( 'label' => __( 'Cover Image / Play Icon', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'cover_image',
			array(
				'label' => __( 'Cover Image', 'cmsmasters-elementor' ),
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
				'condition' => array( 'cover_image[url]!' => '' ),
			)
		);

		$this->add_control(
			'show_play_icon',
			array(
				'label' => __( 'Play Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'separator' => 'before',
				'condition' => array( 'cover_image[url]!' => '' ),
			)
		);

		$this->add_control(
			'play_icon_note',
			array(
				'raw' => __( 'Set Cover Image to manage Play Icon Settings.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'render_type' => 'ui',
				'condition' => array( 'cover_image[url]' => '' ),
			)
		);

		$this->add_control(
			'play_icon',
			array(
				'label' => esc_html__( 'Icon for Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
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
				'condition' => array(
					'cover_image[url]!' => '',
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
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-video-lightbox-',
				'condition' => array( 'cover_image[url]!' => '' ),
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
					'11' => '1:1',
					'916' => '9:16',
					'custom' => 'Custom',
				),
				'selectors_dictionary' => array(
					'169' => '1.77777',
					'219' => '2.33333',
					'43' => '1.33333',
					'32' => '1.5',
					'11' => '1',
					'916' => '0.5625',
					'custom' => '2',
				),
				'default' => '169',
				'frontend_available' => true,
				'prefix_class' => 'elementor-aspect-ratio-',
				'selectors' => array(
					'{{WRAPPER}}' => '--video-aspect-ratio: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'aspect_ratio_custom',
			array(
				'label' => __( 'Custom Aspect Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0.5,
						'max' => 4,
						'step' => 0.05,
					),
				),
				'default' => array( 'size' => 2 ),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--video-aspect-ratio: {{SIZE}};',
				),
				'condition' => array( 'aspect_ratio' => 'custom' ),
			)
		);

		$this->add_responsive_control(
			'video_width',
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
				'default' => array( 'unit' => '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__container' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'video_position',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__container' => '{{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_placeholder_style',
			array(
				'label' => __( 'Cover Image / Play Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'cover_image[url]!' => '' ),
			)
		);

		$this->add_responsive_control(
			'cover_image_aspect_ratio_custom',
			array(
				'label' => __( 'Custom Aspect Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0.5,
						'max' => 4,
						'step' => 0.05,
					),
				),
				'frontend_available' => true,
				'selectors' => array(
					'{{WRAPPER}}' => '--cover-image-aspect-ratio-custom: {{SIZE}};',
				),
				'condition' => array(
					'cover_image[url]!' => '',
					'lightbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'cover_image_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__cover-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'cover_image[url]!' => '',
					'lightbox' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video__cover-image img',
				'condition' => array( 'cover_image[url]!' => '' ),
			)
		);

		$this->add_control(
			'play_icon_title',
			array(
				'label' => __( 'Play Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'cover_image[url]!' => '',
					'show_play_icon' => 'yes',
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
				'condition' => array(
					'cover_image[url]!' => '',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__play-icon i:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__play-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'cover_image[url]!' => '',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__wrap:hover .elementor-widget-cmsmasters-video__play-icon i:before' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__wrap:hover .elementor-widget-cmsmasters-video__play-icon svg' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'cover_image[url]!' => '',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__play-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__play-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__play-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'cover_image[url]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'play_icon_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video__play-icon i:before',
				'fields_options' => array(
					'text_shadow_type' => array(
						'label' => __( 'Text Shadow', 'cmsmasters-elementor' ),
					),
				),
				'condition' => array(
					'cover_image[url]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_lightbox_style',
			array(
				'label' => __( 'Lightbox', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'cover_image[url]!' => '',
					'lightbox' => 'yes',
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
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'min' => 600,
						'max' => 1600,
					),
					'%' => array(
						'min' => 30,
						'max' => 100,
					),
				),
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
									'name' => 'cover_image[url]',
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
			'{{WRAPPER}} .minimize .elementor-widget-cmsmasters-video__inner' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
		);

		$video_aspect_ratio = array(
			'219' => '0.428571',
			'169' => '0.5625',
			'43' => '0.75',
			'32' => '0.666666',
			'11' => '1',
			'916' => '1.778',
			'custom' => 'calc(1 / var(--video-aspect-ratio))',
		);

		foreach ( $video_aspect_ratio as $ratio => $index ) {
			$ratio_class = "{{WRAPPER}}.elementor-aspect-ratio-{$ratio} .minimize .elementor-widget-cmsmasters-video__inner";
			$selector = "height: calc( {{SIZE}}{{UNIT}} * {$index} ); max-height: calc( {{SIZE}}{{UNIT}} * {$index} );";

			$video_aspect_ratio_selectors[ $ratio_class ] = $selector;
		}

		$this->add_responsive_control(
			'video_minimize_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array( 'size' => '280' ),
				'range' => array(
					'px' => array(
						'min' => 280,
						'max' => 600,
					),
				),
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
					'{{WRAPPER}}.cmsmasters-ver-position-on-scroll-top .minimize .elementor-widget-cmsmasters-video__inner' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
					'{{WRAPPER}}.cmsmasters-ver-position-on-scroll-bottom .minimize .elementor-widget-cmsmasters-video__inner' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
					'{{WRAPPER}}.cmsmasters-hor-position-on-scroll-right .minimize .elementor-widget-cmsmasters-video__inner' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
					'{{WRAPPER}}.cmsmasters-hor-position-on-scroll-left .minimize .elementor-widget-cmsmasters-video__inner' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-but-view-framed .elementor-widget-cmsmasters-video__close-button' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'video_minimize_close_but_secondary',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}}.cmsmasters-close-but-view-framed .elementor-widget-cmsmasters-video__close-button:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'video_minimize_close_but_secondary_hover',
			array(
				'label' => __( 'Secondary Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button:hover' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'padding: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'video_minimize_close_but' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'video_minimize_circle_close_but_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video__close-button',
				'condition' => array(
					'video_minimize_close_but' => 'yes',
					'video_minimize_close_but_view!' => 'default',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render video widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.5.1 Fixed cover image with insert url.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$video_type = $settings['video_type'];
		$video_url = $settings[ $video_type . '_url' ];

		if ( 'hosted' === $video_type ) {
			$video_url = $this->get_hosted_video_url();
		}

		if ( empty( $video_url ) ) {
			return;
		}

		if ( 'hosted' === $video_type ) {
			ob_start();

			$this->render_hosted_video();

			$video_html = ob_get_clean();
		}

		if ( 'youtube' === $video_type || 'vimeo' === $video_type || 'dailymotion' === $video_type ) {
			$embed_params = $this->get_embed_params();
			$embed_options = $this->get_embed_options();

			$video_html = Embed::get_embed_html( $video_url, $embed_params, $embed_options );
		}

		$this->add_render_attribute( 'video-container', 'class', array(
			'elementor-widget-cmsmasters-video__container',
			'elementor-open-' . ( $settings['lightbox'] ? 'lightbox' : 'inline' ),
			( '' === $settings['video_minimize_overlay'] ? ' minimize_always' : '' ),
		) );

		echo '<div ' . $this->get_render_attribute_string( 'video-container' ) . '>' .
			'<div class="elementor-widget-cmsmasters-video__wrap">' .
				'<div class="' . ( ! $settings['lightbox'] ? 'elementor-widget-cmsmasters-video__inner' : '' ) . '">' .
					'<span class="elementor-widget-cmsmasters-video__close-button eicon-close" role="button" tabindex="0"></span>';

		if ( ! $settings['lightbox'] ) {
			if ( 'facebook' === $video_type ) {
				$this->get_facebook_params();
			} elseif ( 'twitch' === $video_type ) {
				$this->get_twitch_params();
			} else {
				Utils::print_unescaped_internal_string( $video_html ); // XSS ok.
			}
		}

		if ( ! empty( $settings['cover_image']['url'] ) ) {
			$this->print_cover_image( $video_type, $video_url );
		}

				echo '</div>' .
			'</div>' .
		'</div>';
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
	 * Print image cover image output on the frontend.
	 *
	 * Written in PHP and used to generate the image cover image.
	 *
	 * @since 1.0.0
	 * @since 1.15.4 Replaced elementor-screen-only on aria-label attribute.
	 */
	protected function print_cover_image( $video_type, $video_url ) {
		$settings = $this->get_settings_for_display();

		if ( $settings['lightbox'] ) {
			$this->print_lightbox( $video_type, $video_url );
		}

		$this->add_render_attribute(
			'cover-image',
			array(
				'class' => 'elementor-widget-cmsmasters-video__cover-image',
				'role' => 'button',
				'tabindex' => '0',
				'aria-label' => 'Play video',
			)
		);

		echo '<div ' . $this->get_render_attribute_string( 'cover-image' ) . '>';

		echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'cover_image' );

		if ( 'yes' === $settings['show_play_icon'] && ! empty( $settings['play_icon']['value'] ) ) {
			echo '<div class="elementor-widget-cmsmasters-video__play-icon' . ( 'yes' === $settings['play_icon_effect'] ? ' disable_effect' : '' ) . '">';

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
			'&autoplay=true',
			( 'playsinline=' . ( $settings['playsinline'] ? '1' : '0' ) ),
			'mute=true',
			( 'allowfullscreen=' . ( $settings['fs'] ? 'true' : 'false' ) ),
		);

		$lightbox_url = $host . $url . implode( '&', $options );

		return $lightbox_url;
	}

	/**
	 * Get twitch lightbox url.
	 *
	 * @since 1.0.0
	 */
	protected function get_twitch_lightbox_url() {
		$settings = $this->get_settings_for_display();

		$twitch_url = $settings['twitch_url'];
		$basename_twitch = basename( $twitch_url );

		if ( wp_parse_url( $twitch_url, PHP_URL_QUERY ) ) {
			$type_url = '' . wp_parse_url( $twitch_url, PHP_URL_QUERY );
		} else {
			$type = ! is_numeric( $basename_twitch ) ? 'channel=' : 'video=';

			$type_url = $type . $basename_twitch;
		}

		$autoplay = '&autoplay=true';
		$playsinline = ( 'playsinline=' . ( $settings['playsinline'] ? '1' : '0' ) );
		$mute = 'mute=true';
		$allowfullscreen = ( 'allowfullscreen=' . ( $settings['fs'] ? 'true' : 'false' ) );

		$options = array(
			$autoplay,
			$playsinline,
			$mute,
			$allowfullscreen,
		);

		$host = 'https://player.twitch.tv/?';
		$parent = '&parent=' . preg_replace( '%^(htt|ft)ps?://|(www\.)%i', '', get_site_url() );

		$lightbox_url = $host . $type_url . explode( '/', $parent )[0] . implode( '&', $options );

		return $lightbox_url;
	}

	/**
	 * Print lightbox output on the frontend.
	 *
	 * Written in PHP and used to generate the lightbox.
	 *
	 * @since 1.0.0
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 * @since 1.3.8 Fixed `Entrance Animation` control when responsive.
	 */
	protected function print_lightbox( $video_type, $video_url ) {
		$settings = $this->get_settings_for_display();

		if ( 'youtube' === $video_type || 'vimeo' === $video_type || 'dailymotion' === $video_type ) {
			$embed_params = $this->get_embed_params();
			$embed_options = $this->get_embed_options();
		}

		if ( 'hosted' === $video_type ) {
			$lightbox_url = $video_url;
		} elseif ( 'youtube' === $video_type || 'vimeo' === $video_type || 'dailymotion' === $video_type ) {
			$lightbox_url = Embed::get_embed_url( $video_url, $embed_params, $embed_options );
		} else {
			$get_type_lightbox_url = 'get_' . $video_type . '_lightbox_url';

			$lightbox_url = $this->$get_type_lightbox_url();
		}

		$lightbox_animation = ( isset( $settings['lightbox_animation_entrance'] ) ? $settings['lightbox_animation_entrance'] : '' );
		$lightbox_animation_tablet = ( isset( $settings['lightbox_animation_entrance_tablet'] ) ? $settings['lightbox_animation_entrance_tablet'] : '' );
		$lightbox_animation_mobile = ( isset( $settings['lightbox_animation_entrance_mobile'] ) ? $settings['lightbox_animation_entrance_mobile'] : '' );

		$aspect_ratio = ( isset( $settings['aspect_ratio'] ) ? $settings['aspect_ratio'] : '169' );
		$aspect_ratio_custom = ( isset( $settings['aspect_ratio_custom'] ) ? $settings['aspect_ratio_custom']['size'] : '169' );
		$aspect_ratio_size = ( 'custom' === $aspect_ratio ? $aspect_ratio_custom : $aspect_ratio );

		$lightbox_options = array(
			'type' => 'video',
			'videoType' => $video_type,
			'url' => $lightbox_url,
			'modalOptions' => array(
				'id' => 'elementor-lightbox-' . $this->get_id(),
				'entranceAnimation' => $lightbox_animation,
				'entranceAnimation_tablet' => $lightbox_animation_tablet,
				'entranceAnimation_mobile' => $lightbox_animation_mobile,
				'videoAspectRatio' => $aspect_ratio_size,
			),
		);

		if ( 'hosted' === $video_type ) {
			$lightbox_options['videoParams'] = $this->get_hosted_params();
		}

		$this->add_render_attribute( 'cover-image', array(
			'data-elementor-open-lightbox' => 'yes',
			'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
		) );

		if ( Plugin::$instance->editor->is_edit_mode() ) {
			$this->add_render_attribute( 'cover-image', array( 'class' => 'elementor-clickable' ) );
		}
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {
		$settings = $this->get_settings_for_display();

		$video_type = $settings['video_type'];

		if ( 'hosted' !== $video_type ) {
			$url = $settings[ $video_type . '_url' ];
		} else {
			$url = $this->get_hosted_video_url();
		}

		echo esc_url( $url );
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget facebook parameters.
	 *
	 * @since 1.0.0
	 * @since 1.5.1 Fixed cover image with insert url.
	 *
	 * @return array Video facebook parameters.
	 */
	public function get_facebook_params() {
		$settings = $this->get_settings_for_display();

		$src = ! empty( $settings['cover_image']['url'] ) ? ' data-lazy-load="' : ' src="';
		$host = 'https://web.facebook.com/plugins/video.php?';
		$url = 'href=' . esc_url( $settings['facebook_url'] );

		$autoplay_setting = $settings['autoplay'];

		$autoplay = ( '&autoplay=' . ( $autoplay_setting ? 'true' : 'false' ) );
		$playsinline = ( 'playsinline=' . ( $autoplay_setting && $settings['playsinline'] ? '1' : '0' ) );
		$mute = ( 'mute=' . ( $autoplay_setting || ( ! $autoplay_setting && $settings['mute'] ) ? 'true' : 'false' ) );
		$allowfullscreen = 'allowfullscreen=' . ( $settings['fs'] ? 'true' : 'false' );

		$params = array(
			$autoplay,
			$playsinline,
			$mute,
			$allowfullscreen,
		);

		$this->add_render_attribute( 'facebook-iframe', array(
			'class' => 'elementor-video-iframe',
			( ! empty( $settings['cover_image']['url'] ) ? 'data-lazy-load' : 'src' ) => $host . $url . implode( '&', $params ),
			'frameborder' => '0',
			'scrolling' => 'no',
			'title' => esc_attr__( 'Facebook Video Player', 'cmsmasters-elementor' ),
		) );

		echo '<iframe ' . $this->get_render_attribute_string( 'facebook-iframe' ) . '></iframe>';
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget twitch parameters.
	 *
	 * @since 1.0.0
	 * @since 1.5.1 Fixed cover image with insert url.
	 *
	 * @return array Video twitch parameters.
	 */
	public function get_twitch_params() {
		$settings = $this->get_settings_for_display();

		$twitch_url = esc_url( $settings['twitch_url'] );
		$basename_twitch = basename( $twitch_url );

		if ( wp_parse_url( $twitch_url, PHP_URL_QUERY ) ) {
			$type_url = '' . wp_parse_url( $twitch_url, PHP_URL_QUERY );
		} else {
			$type = ! is_numeric( $basename_twitch ) ? 'channel=' : 'video=';

			$type_url = $type . $basename_twitch;
		}

		$autoplay_setting = $settings['autoplay'];
		$autoplay = ( '&autoplay=' . ( $autoplay_setting ? 'true' : 'false' ) );
		$playsinline = ( 'playsinline=' . ( $autoplay_setting && $settings['playsinline'] ? '1' : '0' ) );

		if ( $autoplay_setting || ( ! $autoplay_setting && $settings['mute'] ) ) {
			$muted = 'muted=true';
		} else {
			$muted = 'muted=false';
		}

		$allowfullscreen = ( '&allowfullscreen=' . ( $settings['fs'] ? 'true' : 'false' ) );

		$options = array(
			$autoplay,
			$playsinline,
			$muted,
			$allowfullscreen,
		);

		$src = ! empty( $settings['cover_image']['url'] ) ? 'data-lazy-load="' : 'src="';
		$host = 'https://player.twitch.tv/?';
		$parent = '&parent=' . preg_replace( '%^(htt|ft)ps?://|(www\.)%i', '', get_site_url() );
		$url = $host . $type_url . explode( '/', $parent )[0] . implode( '&', $options );

		$this->add_render_attribute( 'twitch-iframe', array(
			'class' => 'elementor-video-iframe',
			( ! empty( $settings['cover_image']['url'] ) ? 'data-lazy-load' : 'src' ) => $url,
			'frameborder' => '0',
			'layout' => 'video',
			'scrolling' => 'no',
			'title' => esc_attr__( 'Twitch Video Player', 'cmsmasters-elementor' ),
		) );

		echo '<iframe ' . $this->get_render_attribute_string( 'twitch-iframe' ) . '></iframe>';
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget youtube parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video youtube parameters.
	 */
	public function get_youtube_params( $params_dictionary, $params, $parse_url ) {
		$settings = $this->get_settings_for_display();

		$params_dictionary = array(
			'controls',
			'loop',
			'fs',
			'rel',
			'playsinline',
		);

		if ( $settings['loop'] ) {
			$video_properties = Embed::get_video_properties( $settings['youtube_url'] );

			$params['playlist'] = $video_properties['video_id'];
		}

		if (
			'yes' === $settings['autoplay'] ||
			( 'yes' !== $settings['autoplay'] && 'yes' === $settings['mute'] )
		) {
			$params['mute'] = '1';
		}

		if ( $parse_url && false !== stristr( $parse_url, 'list=' ) ) {
			$list_id = explode( '&', substr( stristr( $parse_url, 'list=' ), 5 ) );

			$params['listType'] = 'playlist';
			$params['list'] = $list_id[0];
		}

		$params['modestbranding'] = $settings['modestbranding'] ? '0' : '1';

		$params['cc_load_policy'] = $settings['cc_load_policy'] ? '1' : '0';

		$params['enablejsapi'] = '1';

		$settings['start'] ? $params['start'] = $this->start_time() : '';

		if ( $settings['end'] && $this->end_time() ) {
			$params['end'] = $this->end_time();
		}

		$params['wmode'] = 'opaque';

		if ( $settings['lightbox'] ) {
			$params['mute'] = '1';
			$params['autoplay'] = 'true';
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
	 * Get embed params.
	 *
	 * Retrieve video widget vimeo parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video vimeo parameters.
	 */
	public function get_vimeo_params( $params_dictionary, $params, $parse_url ) {
		$settings = $this->get_settings_for_display();

		$params_dictionary = array(
			'loop',
			'vimeo_title' => 'title',
			'vimeo_portrait' => 'portrait',
			'vimeo_byline' => 'byline',
			'playsinline',
		);

		if (
			'yes' === $settings['autoplay'] ||
			( 'yes' !== $settings['autoplay'] && 'yes' === $settings['mute'] )
		) {
			$params['muted'] = 'true';
		}

		$params['color'] = str_replace( '#', '', $settings['color'] );
		$params['autopause'] = '0';

		$params['api'] = '1';

		if ( $settings['lightbox'] ) {
			$params['autoplay'] = 'true';
			$params['muted'] = 'true';
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
	 * Get embed params.
	 *
	 * Retrieve video widget dailymotion parameters.
	 *
	 * @since 1.0.0
	 *
	 * @return array Video dailymotion parameters.
	 */
	public function get_dailymotion_params( $params_dictionary, $params, $parse_url ) {
		$settings = $this->get_settings_for_display();

		$params_dictionary = array(
			'controls',
			'showinfo' => 'ui-start-screen-info',
			'logo' => 'ui-logo',
			'playsinline',
		);

		if (
			'yes' === $settings['autoplay'] ||
			( 'yes' !== $settings['autoplay'] && 'yes' === $settings['mute'] )
		) {
			$params['mute'] = 'true';
		}

		$params['ui-highlight'] = str_replace( '#', '', $settings['color'] );
		$settings['start'] ? $params['start'] = $this->start_time() : '';

		if ( $settings['lightbox'] ) {
			$params['autoplay'] = 'true';
			$params['mute'] = 'true';
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

	public function start_time() {
		$settings = $this->get_settings_for_display();

		$start_time = explode( ':', $settings['start'] );

		$start_hours = ( $start_time[0] ? $start_time[0] * 360 : 0 );
		$start_minute = ( $start_time[1] ? $start_time[1] * 60 : 0 );
		$start_second = ( $start_time[2] ? $start_time[2] : 0 );

		$start = ( $start_hours + $start_minute + $start_second );

		return $start;
	}

	public function end_time() {
		$settings = $this->get_settings_for_display();

		$end_time = explode( ':', $settings['end'] );

		$end_hours = ( $end_time[0] ? $end_time[0] * 360 : 0 );
		$end_minute = ( $end_time[1] ? $end_time[1] * 60 : 0 );
		$end_second = ( $end_time[2] ? $end_time[2] : 0 );

		$end = ( $end_hours + $end_minute + $end_second );
		$start = $this->start_time();

		if ( $end > $start ) {
			$end = $end;
		} else {
			$end = '';
		}

		return $end;
	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget embed parameters.
	 *
	 * @since 1.0.0
	 * @since 1.5.1 Fixed cover image with insert url.
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params() {
		$settings = $this->get_settings_for_display();

		$params = array();
		$params_dictionary = array();
		$video_type = $settings['video_type'];
		$video_url = $settings[ $video_type . '_url' ];
		$parse_url = wp_parse_url( $video_url, PHP_URL_QUERY );

		if ( $settings['autoplay'] && empty( $settings['cover_image']['url'] ) ) {
			$params['autoplay'] = '1';
		}

		$video_type_params = 'get_' . $video_type . '_params';

		$params = $this->$video_type_params( $params_dictionary, $params, $parse_url );

		return $params;
	}

	/**
	 * @since 1.0.0
	 * @since 1.5.1 Fixed cover image with insert url.
	 */
	private function get_embed_options() {
		$settings = $this->get_settings_for_display();

		$video_type = $settings['video_type'];

		$embed_options = array();

		if ( 'youtube' === $video_type ) {
			$embed_options['privacy'] = $settings['yt_privacy'];
		} elseif ( 'vimeo' === $video_type ) {
			$embed_options['start'] = ( $settings['start'] ? $this->start_time() : '' );
		}

		$embed_options['lazy_load'] = ! empty( $settings['cover_image']['url'] );

		return $embed_options;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param bool $from_media
	 *
	 * @return string
	 */
	private function get_hosted_video_url() {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings['insert_url'] ) ) {
			$video_url = $settings['external_url']['url'];
		} else {
			$video_url = $settings['hosted_url']['url'];
		}

		if ( empty( $video_url ) ) {
			return '';
		}

		if ( $settings['start'] || $settings['end'] ) {
			$video_url .= '#t=';
		}

		if ( $settings['start'] ) {
			$video_url .= $this->start_time();
		}

		if ( $settings['end'] && $this->end_time() ) {
			$video_url .= ',' . $this->end_time();
		}

		return $video_url;
	}

	/**
	 * @since 1.0.0
	 */
	private function get_hosted_params() {
		$settings = $this->get_settings_for_display();

		$video_params = array();

		if ( $settings['controls'] ) {
			$video_params['controls'] = '';
		}

		$autoplay_type = ( isset( $settings['autoplay_type'] ) ? $settings['autoplay_type'] : '' );

		if ( ! empty( $autoplay_type ) && 'default' !== $autoplay_type ) {
			$video_params['muted'] = 'muted';
		}

		if ( $settings['autoplay'] ) {
			$video_params['autoplay'] = '';
			$video_params['muted'] = 'muted';
		}

		if ( $settings['mute'] ) {
			$video_params['muted'] = 'muted';
		}

		if ( ! $settings['controls'] ) {
			$video_params['autoplay'] = '';
			$video_params['muted'] = 'muted';
		}

		if ( $settings['playsinline'] ) {
			$video_params['playsinline'] = '';
		}

		if ( $settings['loop'] ) {
			$video_params['loop'] = '';
		}

		if ( '' === $settings['picture_in_picture'] ) {
			$video_params['disablePictureInPicture'] = '';
		}

		if ( ! $settings['download_button'] || ! $settings['fs'] ) {
			$video_params['controlsList'] = ( ( ! $settings['fs'] ? 'nofullscreen ' : '' ) . ( ! $settings['download_button'] ? 'nodownload' : '' ) );
		}

		if ( $settings['poster']['url'] ) {
			$video_params['poster'] = $settings['poster']['url'];
		}

		return $video_params;
	}

	/**
	 * @since 1.0.0
	 * @since 1.5.1 Fixed cover image with insert url.
	 */
	private function render_hosted_video() {
		$settings = $this->get_settings_for_display();

		$video_url = $this->get_hosted_video_url();

		if ( empty( $video_url ) ) {
			return;
		}

		$video_params = $this->get_hosted_params();

		echo '<video ' .
			'class="elementor-widget-cmsmasters-video__hosted" ' .
			( ! empty( $settings['cover_image']['url'] ) ? ' data-lazy-load="' : ' src="' ) . esc_url( $video_url ) . '" ' .
			Utils::render_html_attributes( $video_params ) .
		'></video>';
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
			'widget-cmsmasters-video',
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
			'external_url' => array(
				'field' => 'url',
				'type' => esc_html__( 'Self Hosted URL', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
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
			array(
				'field' => 'facebook_url',
				'type' => esc_html__( 'Facebook URL', 'cmsmasters-elementor' ),
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
