<?php
namespace CmsmastersElementor\Modules\Media\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Media\Module as MediaModule;
use CmsmastersElementor\Modules\Media\Traits\Video_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
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
 * Addon video playlist widget.
 *
 * Addon widget that displays video playlist.
 *
 * @since 1.0.0
 */
class Video_Playlist extends Base_Widget {

	use Video_Widget;

	/**
	 * Get widget title.
	 *
	 * Retrieve video playlist widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Video Playlist', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve video playlist widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-video-playlist';
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
	 * Retrieve the list of scripts the video playlist widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array(
			'youtube-iframe-api',
			'vimeo-iframe-api',
			'perfect-scrollbar-js',
		), parent::get_script_depends() );
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
			'mCustomScrollbarCSS',
			'widget-cmsmasters-video-playlist',
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
	 * Hides elementor widget container to the frontend if `Optimized Markup` is enabled.
	 *
	 * @since 1.16.4
	 */
	public function has_widget_inner_wrapper(): bool {
		return ! Plugin::$instance->experiments->is_feature_active( 'e_optimized_markup' );
	}

	/**
	 * Current provider
	 *
	 * @var sting
	 *
	 * @since 1.0.0
	 */
	private $current_provider = null;

	protected $google_api_key = '';

	/**
	 *
	 * Initializing the widget class.
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

		$this->google_api_key = get_option( 'elementor_google_api_key' );
	}


	/**
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added dynamic for `URL` control.
	 * @since 1.5.1 Added `Aspect Ratio` and `Width` controls for video thumbnails.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_general',
			array( 'label' => esc_html__( 'Playlist', 'cmsmasters-elementor' ) )
		);

		if ( empty( $this->google_api_key ) ) {
			$this->add_control(
				'google_api_key',
				array(
					'raw' => '<strong>' . __( 'YouTube: ', 'cmsmasters-elementor' ) . '</strong>' . __( 'Set ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( 'admin.php?page=cmsmasters' ) ) . '" target="_blank">' . __( 'Google API key', 'cmsmasters-elementor' ) . '</a>' . __( ' and enable ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( 'https://console.developers.google.com/apis/library?project=iron-crane-137113&folder&organizationId' ) ) . '" target="_blank">' . __( 'YouTube Data API v3', 'cmsmasters-elementor' ) . '</a>' . __( ' to display the duration', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'render_type' => 'ui',
				)
			);
		}

		$repeater = new Repeater();

		$repeater->add_control(
			'url',
			array(
				'label' => esc_html__( 'URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'autocomplete' => false,
				'show_external' => false,
				'options' => false,
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'description' => esc_html__( 'Enter the YouTube or Vimeo link', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'Enter the Title for video', 'cmsmasters-elementor' ),
				'description' => esc_html__( 'Leave empty to automatically get title from video', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'subtitle',
			array(
				'label' => esc_html__( 'Author/Subtitle', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'Enter the Author/Subtitle for video', 'cmsmasters-elementor' ),
				'description' => esc_html__( 'Leave empty to automatically get author from video', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'subtitle_link',
			array(
				'label' => __( 'Author/Subtitle Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array( 'active' => true ),
				'placeholder' => __( 'https://your-link.com', 'cmsmasters-elementor' ),
				'condition' => array( 'subtitle!' => '' ),
			)
		);

		$this->add_control(
			'playlist',
			array(
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'url' => MediaModule::YOUTUBE_VIDEO_URL,
						'title' => '',
						'subtitle' => '',
					),
					array(
						'url' => MediaModule::YOUTUBE_ALTERNATE_VIDEO_URL,
						'title' => '',
						'subtitle' => '',
					),
				),
				'title_field' => '<# if ( \'\' === title ) { #>Video <span class="cmsmasters-repeat-item-num"></span> <# } else { #> {{{ title }}} <span class="cmsmasters-repeat-item-num hidden"></span><# } #>',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'list_orientation',
			array(
				'label' => esc_html__( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'vertical' => __( 'Vertical', 'cmsmasters-elementor' ),
					'horizontal' => __( 'Horizontal', 'cmsmasters-elementor' ),
				),
				'default' => 'vertical',
				'label_block' => false,
				'toggle' => false,
				'frontend_available' => true,
				'render_type' => 'template',
				'separator' => 'before',
				'prefix_class' => 'cmsmasters-list-orientation-',
			)
		);

		$this->add_control(
			'list_v_position',
			array(
				'label' => esc_html__( 'List Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'left' => esc_html__( 'Left', 'cmsmasters-elementor' ),
					'right' => esc_html__( 'Right', 'cmsmasters-elementor' ),
				),
				'default' => 'right',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-list-v-pos-',
				'condition' => array( 'list_orientation' => 'vertical' ),
			)
		);

		$this->add_control(
			'list_h_position',
			array(
				'label' => esc_html__( 'List Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => esc_html__( 'Top', 'cmsmasters-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'cmsmasters-elementor' ),
				),
				'default' => 'bottom',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-list-h-pos-',
				'condition' => array( 'list_orientation' => 'horizontal' ),
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label' => esc_html__( 'Aspect Ratio', 'cmsmasters-elementor' ),
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
				'default' => '43',
				'selectors' => array(
					'{{WRAPPER}}' => '--video-aspect-ratio: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'aspect_ratio_custom_height',
			array(
				'label' => esc_html__( 'Height (%)', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => 50,
				),
				'range' => array(
					'%' => array(
						'min' => 40,
						'max' => 150,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--video-aspect-ratio: calc(100 / {{SIZE}}) !important;',
				),
				'condition' => array( 'aspect_ratio' => 'custom' ),
			)
		);

		$this->add_control(
			'playlist_autoplay',
			array(
				'label' => esc_html__( 'Autoplay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'playlist_mute',
			array(
				'label' => esc_html__( 'Mute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'playlist_loop',
			array(
				'label' => esc_html__( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'cc_load_policy',
			array(
				'label' => esc_html__( 'YouTube Captions', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'No', 'cmsmasters-elementor' ),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_list',
			array( 'label' => esc_html__( 'Advanced Options', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'canvas_cover_heading',
			array(
				'label' => esc_html__( 'Cover Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'canvas_cover',
			array(
				'label' => esc_html__( 'Choose Cover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic' => array( 'active' => true ),
				'show_label' => false,
				'frontend_available' => true,
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'canvas_cover',
				'default' => 'full',
				'separator' => 'none',
				'fields_options' => array(
					'size' => array(
						'label' => _x( 'Cover Size', 'Cover Size Control', 'cmsmasters-elementor' ),
					),
				),
				'condition' => array( 'canvas_cover[id]!' => '' ),
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
				'condition' => array( 'canvas_cover[id]!' => '' ),
			)
		);

		$this->add_control(
			'play_icon_note',
			array(
				'raw' => __( 'Set Cover Image to manage Play Icon Settings.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition' => array( 'canvas_cover[id]' => '' ),
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
					'canvas_cover[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_list_heading',
			array(
				'label' => esc_html__( 'Headline Area', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'list_heading_text',
			array(
				'label' => esc_html__( 'Default Headline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Video PlayList', 'cmsmasters-elementor' ),
				'condition' => array(
					'canvas_cover[id]!' => '',
					'show_list_heading' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'list_heading_width',
			array(
				'label' => esc_html__( 'Headline Column Width (in pixels)', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 100,
						'max' => 500,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-list-orientation-horizontal .elementor-widget-cmsmasters-video-playlist__heading' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'list_orientation' => 'horizontal',
					'show_list_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_video_counter',
			array(
				'label' => esc_html__( 'Videos Counter', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'list_heading_counter_suffix',
			array(
				'label' => esc_html__( 'Counter Suffix', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'video', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_list_heading' => 'yes',
					'show_video_counter' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_numbering',
			array(
				'label' => esc_html__( 'Numbering', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => '',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_numbering',
			array(
				'label' => esc_html__( 'Hide On', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'tablet' => array(
						'title' => esc_html__( 'Tablet', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide numbering on tablet resolution only', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => esc_html__( 'Mobile', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide numbering on mobile resolution only', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => esc_html__( 'Both', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide numbering on both resolutions', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'toggle' => true,
				'return_value' => 'yes',
				'condition' => array( 'show_numbering' => 'yes' ),
			)
		);

		$this->add_control(
			'show_image',
			array(
				'label' => esc_html__( 'Item Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_image',
			array(
				'label' => esc_html__( 'Hide On', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'tablet' => array(
						'title' => esc_html__( 'Tablet', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide image on tablet resolution only', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => esc_html__( 'Mobile', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide image on mobile resolution only', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => esc_html__( 'Both', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide image on both resolutions', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'toggle' => true,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_subtitle',
			array(
				'label' => esc_html__( 'Subtitle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_subtitle',
			array(
				'label' => esc_html__( 'Hide On', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'tablet' => array(
						'title' => esc_html__( 'Tablet', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide subtitle on tablet resolution only', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => esc_html__( 'Mobile', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide subtitle on mobile resolution only', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => esc_html__( 'Both', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide subtitle on both resolutions', 'cmsmasters-elementor' ),
					),
				),
				'toggle' => true,
				'label_block' => false,
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_duration',
			array(
				'label' => esc_html__( 'Duration', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cmsmasters-elementor' ),
				'label_off' => esc_html__( 'Hide', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hide_duration',
			array(
				'label' => esc_html__( 'Hide On', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'tablet' => array(
						'title' => esc_html__( 'Tablet', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide duration on tablet resolution only', 'cmsmasters-elementor' ),
					),
					'mobile' => array(
						'title' => esc_html__( 'Mobile', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide duration on mobile resolution only', 'cmsmasters-elementor' ),
					),
					'both' => array(
						'title' => esc_html__( 'Both', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'Hide duration on both resolutions', 'cmsmasters-elementor' ),
					),
				),
				'label_block' => false,
				'toggle' => true,
				'return_value' => 'yes',
				'condition' => array( 'show_duration' => 'yes' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_playlist_container_style',
			array(
				'label' => esc_html__( 'List', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'list_ver_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
					'vw',
				),
				'range' => array(
					'%' => array(
						'min' => 25,
						'max' => 50,
						'step' => 0.1,
					),
					'px' => array(
						'min' => 250,
						'max' => 600,
					),
				),
				'devices' => array(
					'desktop',
					'tablet',
				),
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-list-orientation-vertical .elementor-widget-cmsmasters-video-playlist__list' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-list-orientation-vertical .elementor-widget-cmsmasters-video-playlist__canvas' => 'width: calc( 100% - {{SIZE}}{{UNIT}} );',
				),
				'condition' => array( 'list_orientation' => 'vertical' ),
			)
		);

		$this->add_control(
			'video_playlist_container_list_bg',
			array(
				'label' => esc_html__( 'Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__list,
					{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__canvas' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'video_playlist_container_list_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__list-items' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'video_playlist_container_list_resp_height',
			array(
				'label' => esc_html__( 'Max height on mobile', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 150,
						'max' => 1000,
					),
				),
				'description' => esc_html__( 'Set value for playlist\'s max height - ', 'cmsmasters-elementor' ) . '<b>' . esc_html__( 'will be applied for mobile devices only', 'cmsmasters-elementor' ) . '</b>',
				'selectors' => array(
					'(mobile-){{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__list' => 'max-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'list_orientation' => 'vertical' ),
			)
		);

		$this->add_control(
			'video_playlist_container_heading',
			array(
				'label' => esc_html__( 'Headline', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'video_playlist_container_heading_align',
			array(
				'label' => __( 'Align', 'cmsmasters-elementor' ),
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
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__heading-content' => 'text-align: {{VALUE}};',
				),
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'video_playlist_container_heading_bg',
			array(
				'label' => esc_html__( 'Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__heading' => 'background-color: {{VALUE}}',
				),
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'video_playlist_container_heading_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'video_playlist_container_heading_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-list-orientation-vertical .elementor-widget-cmsmasters-video-playlist__heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-list-orientation-horizontal .elementor-widget-cmsmasters-video-playlist__heading' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_video_playlist_container_heading_style',
			array(
				'separator' => 'before',
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->start_controls_tab(
			'tab_video_playlist_container_heading_title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_control(
			'video_playlist_container_heading_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__heading-title' => 'color: {{VALUE}}',
				),
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'video_playlist_container_heading_title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__heading-title',
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_video_playlist_container_heading_counter',
			array(
				'label' => esc_html__( 'Counter', 'cmsmasters-elementor' ),
				'condition' => array(
					'show_list_heading' => 'yes',
					'show_video_counter' => 'yes',
				),
			)
		);

		$this->add_control(
			'video_playlist_container_heading_counter_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__counter' => 'color: {{VALUE}}',
				),
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'video_playlist_container_heading_counter_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__counter',
				'condition' => array( 'show_list_heading' => 'yes' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_playlist_item_style',
			array(
				'label' => esc_html__( 'List Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'list_hor_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
					'vw',
				),
				'range' => array(
					'%' => array(
						'min' => 15,
						'max' => 100,
					),
					'px' => array(
						'min' => 100,
						'max' => 500,
					),
				),
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item' => 'width: {{SIZE}}{{UNIT}}; flex: 0 0 {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'list_orientation' => 'horizontal' ),
			)
		);

		$this->add_control(
			'playlist_item_gap',
			array(
				'label' => esc_html__( 'Gap Between', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-list-orientation-vertical .elementor-widget-cmsmasters-video-playlist__item' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-list-orientation-horizontal .elementor-widget-cmsmasters-video-playlist__item' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'playlist_item_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-list-orientation-horizontal .elementor-widget-cmsmasters-video-playlist__item-numbering' => 'left: {{LEFT}}{{UNIT}}; top: {{TOP}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'playlist_item_bg',
			array(
				'label' => esc_html__( 'Background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'playlist_active_hover_item_bg',
			array(
				'label' => esc_html__( 'Active/Hover background', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item:not(.active_item):hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item.active_item' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'playlist_item_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item',
			)
		);

		$this->add_control(
			'playlist_item_image',
			array(
				'label' => esc_html__( 'Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'playlist_item_aspect_ratio',
			array(
				'label' => esc_html__( 'Aspect Ratio', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => 'Inherit',
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
				'default' => '',
				'selectors' => array(
					'{{WRAPPER}}' => '--playlist-item-aspect-ratio: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'playlist_item_aspect_ratio_custom_height',
			array(
				'label' => esc_html__( 'Height (%)', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( '%' ),
				'default' => array(
					'unit' => '%',
					'size' => 50,
				),
				'range' => array(
					'%' => array(
						'min' => 40,
						'max' => 150,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--playlist-item-aspect-ratio: calc(100 / {{SIZE}}) !important;',
				),
				'condition' => array( 'playlist_item_aspect_ratio' => 'custom' ),
			)
		);

		$this->add_control(
			'playlist_item_image_v_align',
			array(
				'label' => __( 'Vertical Align', 'cmsmasters-elementor' ),
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
				'selectors_dictionary' => array(
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item' => 'align-items: {{VALUE}};',
				),
				'condition' => array( 'list_orientation' => 'vertical' ),
			)
		);

		$this->add_control(
			'playlist_item_image_width',
			array(
				'label' => esc_html__( 'Width', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'%',
					'px',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-list-orientation-vertical' => '--cmsmasters-item-thumb-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'list_orientation' => 'vertical' ),
			)
		);

		$this->add_control(
			'playlist_item_image_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-item-thumb-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_playlist_item_elements',
			array( 'separator' => 'before' )
		);

		$this->start_controls_tab(
			'tab_playlist_item_element_title',
			array( 'label' => esc_html__( 'Title', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'playlist_item_title_line',
			array(
				'label' => esc_html__( 'Truncate Text to', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'1' => array(
						'title' => esc_html__( '1', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'one line', 'cmsmasters-elementor' ),
					),
					'2' => array(
						'title' => esc_html__( '2', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'two lines', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'one',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-content-title' => '-webkit-line-clamp: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'playlist_item_title_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-content-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'playlist_item_title_active_color',
			array(
				'label' => esc_html__( 'Active/Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item:not(.active_item):hover .elementor-widget-cmsmasters-video-playlist__item-content-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item.active_item .elementor-widget-cmsmasters-video-playlist__item-content-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'playlist_item_title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-content-title',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_playlist_item_element_subtitle',
			array( 'label' => esc_html__( 'Subtitle', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'playlist_item_subtitle_line',
			array(
				'label' => esc_html__( 'Truncate Text to', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'1' => array(
						'title' => esc_html__( '1', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'one line', 'cmsmasters-elementor' ),
					),
					'2' => array(
						'title' => esc_html__( '2', 'cmsmasters-elementor' ),
						'description' => esc_html__( 'two lines', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'one',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-content-author' => '-webkit-line-clamp: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'playlist_item_subtitle_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-content-author' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'playlist_item_subtitle_active_color',
			array(
				'label' => esc_html__( 'Active/Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item:not(.active_item):hover .elementor-widget-cmsmasters-video-playlist__item-content-author' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item.active_item .elementor-widget-cmsmasters-video-playlist__item-content-author' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'playlist_item_subtitle_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-content-author',
			)
		);

		$this->add_responsive_control(
			'playlist_item_subtitle_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-content-author' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_playlist_item_element_number',
			array(
				'label' => esc_html__( 'Numbering', 'cmsmasters-elementor' ),
				'condition' => array( 'show_numbering' => 'yes' ),
			)
		);

		$this->add_control(
			'playlist_item_number_color',
			array(
				'label' => esc_html__( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-numbering-num' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'playlist_item_number_active_color',
			array(
				'label' => esc_html__( 'Active/Hover Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item:not(.active_item):hover .elementor-widget-cmsmasters-video-playlist__item-numbering-num' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item.active_item .elementor-widget-cmsmasters-video-playlist__item-numbering-num' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'playlist_item_number_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-numbering',
			)
		);

		$this->add_responsive_control(
			'playlist_item_number_gap',
			array(
				'label' => esc_html__( 'Gap', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__item-numbering' => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'list_orientation' => 'vertical' ),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_video_playlist_placeholder_style',
			array(
				'label' => __( 'Cover Image / Play Icon', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array( 'canvas_cover[id]!' => '' ),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__canvas-cover',
				'condition' => array( 'canvas_cover[id]!' => '' ),
			)
		);

		$this->add_control(
			'play_icon_title',
			array(
				'label' => __( 'Play Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'canvas_cover[id]!' => '',
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
					'canvas_cover[id]!' => '',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__play-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array(
					'canvas_cover[id]!' => '',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__container:hover .elementor-widget-cmsmasters-video-playlist__play-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
				'condition' => array(
					'canvas_cover[id]!' => '',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__play-icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__play-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__play-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'canvas_cover[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'play_icon_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-video-playlist__play-icon i:before',
				'fields_options' => array(
					'text_shadow_type' => array(
						'label' => __( 'Text Shadow', 'cmsmasters-elementor' ),
					),
				),
				'condition' => array(
					'canvas_cover[id]!' => '',
					'show_play_icon' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Print play video text output on the frontend.
	 *
	 * Written in PHP and used to generate the play video text.
	 *
	 * @since 1.15.4
	 */
	public function print_a11y_text( $canvas_cover ) {
		if ( empty( $canvas_cover['alt'] ) ) {
			return esc_html__( 'Play Video', 'cmsmasters-elementor' );
		} else {
			return esc_html__( 'Play Video about', 'cmsmasters-elementor' ) . ' ' . esc_attr( $canvas_cover['alt'] );
		}
	}

	/**
	 * Render video playlist widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @since 1.15.4 Replaced elementor-screen-only on aria-label attribute.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$playlist = $settings['playlist'];

		$this->add_render_attribute(
			'canvas-cover',
			array(
				'class' => 'elementor-widget-cmsmasters-video-playlist__canvas-cover',
				'role' => 'button',
				'tabindex' => '0',
				'aria-label' => 'Play video',
			)
		);

		echo '<div class="elementor-widget-cmsmasters-video-playlist__container">' .
			'<div class="elementor-widget-cmsmasters-video-playlist__canvas">' .
				'<div ' . $this->get_render_attribute_string( 'canvas-cover' ) . '">';

		if ( '' !== $settings['canvas_cover']['id'] ) {
			echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'canvas_cover' );
		}

		if ( 'yes' === $settings['show_play_icon'] && ! empty( $settings['play_icon']['value'] ) ) {
			echo '<div class="elementor-widget-cmsmasters-video-playlist__play-icon' . ( 'yes' === $settings['play_icon_effect'] ? ' disable_effect' : '' ) . '">';

				Icons_Manager::render_icon(
					$settings['play_icon'],
					array(
						'aria-hidden' => 'true',
						'aria-label' => esc_attr( $this->print_a11y_text( $settings['canvas_cover'] ) ),
					)
				);

			echo '</div>';
		}

				echo '</div>' .
				'<div class="elementor-widget-cmsmasters-video-playlist__canvas-overlay"></div>' .
			'</div>' .
			'<div class="elementor-widget-cmsmasters-video-playlist__list">';

		if ( 'yes' === $settings['show_list_heading'] ) {
			$this->print_item_heading( $settings, $playlist );
		}

				echo '<div class="elementor-widget-cmsmasters-video-playlist__list-items">' .
					'<div class="elementor-widget-cmsmasters-video-playlist__list-items-content">';

						$this->print_item_item( $playlist, $settings );

					echo '</div>' .
				'</div>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Print item item
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$subtitle_link` variable. Added check on empty.
	 */
	public function print_item_item( $playlist, $settings ) {
		foreach ( $playlist as $index => $item ) {
			if ( '' !== $item['url'] ) {
				$video_data = $this->get( $item['url'] );
				$title = ! empty( $item['title'] ) ? $item['title'] : $video_data['title'];
				$author_name = ! empty( $video_data['author_name'] ) ? $video_data['author_name'] : false;
				$author_url = ! empty( $video_data['author_url'] ) ? esc_url( $video_data['author_url'] ) : '';
				$author_tag = ! empty( $video_data['author_url'] ) ? 'a href="' . $author_url . '"' : 'span';
				$subtitle = $item['subtitle'];
				$subtitle_link = ( isset( $item['subtitle_link'] ) ? $item['subtitle_link']['url'] : '' );
				$thumb = isset( $video_data['thumbnail_medium'] ) ? $video_data['thumbnail_medium'] : $video_data['thumbnail_default'];
				$duration = ! empty( $video_data['duration'] ) ? $video_data['duration'] : false;
				$hide = $this->__get_hide_classes( $settings );
				$size = ( isset( $settings['aspect_ratio_custom_height'] ) ? $settings['aspect_ratio_custom_height']['size'] : '' );
				$unit = ( isset( $settings['aspect_ratio_custom_height'] ) ? $settings['aspect_ratio_custom_height']['unit'] : '' );

				$data = array(
					'data-id' => esc_attr( $item['_id'] ),
					'data-video_id' => esc_attr( $video_data['video_id'] ),
					'data-provider' => esc_attr( strtolower( $video_data['provider_name'] ) ),
					'data-html' => esc_attr( str_replace( array( '"', "'" ), '', wp_json_encode( $this->adjust_height( $video_data['html'] ) ) ) ),
					'data-height' => esc_attr( $size . $unit ),
					'data-video_index' => absint( $index ) + 1,
				);

				echo '<div class="elementor-widget-cmsmasters-video-playlist__item"';

					foreach ( $data as $key => $value ) {
						Utils::print_unescaped_internal_string( ' ' . $key . '="' . $value . '"' );
					}

				echo '>';

				if ( 'yes' === $settings['show_numbering'] && 'vertical' === $settings['list_orientation'] ) {
					$this->print_item_index( $index, $hide, $settings );
				}

				if ( '' !== $thumb ) {
					$this->print_item_thumb( $hide, $thumb, $title, $duration, $index );
				}

				if (
					'' !== $title ||
					'' !== $author_name ||
					'' !== $subtitle ||
					( 'yes' === $settings['show_duration'] && $duration )
				) {
					$this->print_item_content( $item, $title, $author_name, $author_tag, $subtitle, $subtitle_link, $hide );
				}

				echo '</div>';
			}
		}
	}

	/**
	 * Print item heading
	 *
	 * @since 1.0.0
	 */
	public function print_item_heading( $settings, $playlist ) {
		echo '<div class="elementor-widget-cmsmasters-video-playlist__heading">' .
			'<div class="elementor-widget-cmsmasters-video-playlist__heading-content">' .
				'<div class="elementor-widget-cmsmasters-video-playlist__heading-title">';

		if ( '' !== $settings['list_heading_text'] ) {
			echo esc_html( $settings['list_heading_text'] );
		} else {
			echo esc_html__( 'Video PlayList', 'cmsmasters-elementor' );
		}

				echo '</div>';

				echo esc_html( $this->__video_counter( $settings, $playlist ) );

			echo '</div>' .
		'</div>';
	}

	/**
	 * Print item numbering
	 *
	 * @since 1.0.0
	 */
	public function print_item_index( $index, $hide, $settings ) {
		$hide_class = '';
		$sep = ( '' !== $hide['numbering'] || '' !== $hide['image'] ? ' ' : '' );

		if ( 'horizontal' === $settings['list_orientation'] && $hide['image'] !== $hide['numbering'] ) {
			$hide_class = $hide['numbering'] . $sep . $hide['image'];
		} else {
			$hide_class = $hide['numbering'];
		}

		echo '<div class="elementor-widget-cmsmasters-video-playlist__item-numbering' . esc_attr( $hide_class ) . '">' .
			'<div class="elementor-widget-cmsmasters-video-playlist__item-numbering-num">' .
				esc_html( $index + 1 ) .
			'</div>' .
		'</div>';
	}

	/**
	 * Print item content
	 *
	 * @since 1.0.0
	 */
	public function print_item_content( $item, $title, $author_name, $author_tag, $subtitle, $subtitle_link, $hide ) {
		echo '<div class="elementor-widget-cmsmasters-video-playlist__item-content">';

		if ( '' !== $title ) {
			echo '<div class="elementor-widget-cmsmasters-video-playlist__item-content-title">' .
				esc_html( $title ) .
			'</div>';
		}

		if ( '' !== $author_name || '' !== $subtitle ) {
			echo '<div class="elementor-widget-cmsmasters-video-playlist__item-content-author' . esc_attr( $hide['subtitle'] ) . '">';

				$subtitle_attr = '';

				if ( ! empty( $item['subtitle_link']['is_external'] ) ) {
					$subtitle_attr .= ' target="_blank"';
				}

				if ( ! empty( $item['subtitle_link']['nofollow'] ) ) {
					$subtitle_attr .= ' rel="nofollow"';
				}

				$subtitle_tag = ( '' !== $subtitle_link ? 'a href="' . esc_url( $subtitle_link ) . '"' . $subtitle_attr : 'span' );

				$tag = ( '' !== $subtitle ? $subtitle_tag : $author_tag );
				$text = ( '' !== $subtitle ? $subtitle : $author_name );

				Utils::print_unescaped_internal_string( '<' . $tag . '>' );

					echo esc_html( $text );

				Utils::print_unescaped_internal_string( '</' . $tag . '>' );

			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Print item thumb
	 *
	 * @since 1.0.0
	 */
	public function print_item_thumb( $hide, $thumb, $title, $duration, $index ) {
		$settings = $this->get_settings_for_display();

		echo '<div class="elementor-widget-cmsmasters-video-playlist__item-thumb' . esc_attr( $hide['image'] ) . '">' .
			'<div class="elementor-widget-cmsmasters-video-playlist__item-status">';

				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-play',
						'library' => 'fa-solid',
					),
					array(
						'class' => 'playing',
						'aria-hidden' => 'true',
						'aria-label' => 'Play',
					)
				);

				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-pause',
						'library' => 'fa-solid',
					),
					array(
						'class' => 'paused',
						'aria-hidden' => 'true',
						'aria-label' => 'Pause',
					)
				);

				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-stop',
						'library' => 'fa-solid',
					),
					array(
						'class' => 'finished',
						'aria-hidden' => 'true',
						'aria-label' => 'Stop',
					)
				);

			echo '</div>' .
			'<div class="elementor-widget-cmsmasters-video-playlist__item-thumb-ratio">';

		if ( 'yes' === $settings['show_numbering'] && 'horizontal' === $settings['list_orientation'] ) {
			$this->print_item_index( $index, $hide, $settings );
		}

				echo '<img src="' . esc_attr( $thumb ) . '" 
					alt="' . esc_attr( $title ) . '" 
					title="' . esc_attr( $title ) . '" 
					class="elementor-widget-cmsmasters-video-playlist__item-thumb-img">';

		if ( 'yes' === $settings['show_duration'] && $duration ) {
			echo '<div class="elementor-widget-cmsmasters-video-playlist__item-duration' . esc_attr( $hide['duration'] ) . '">' .
				esc_html( $duration ) .
			'</div>';
		}

			echo '</div>' .
		'</div>';
	}

	/**
	 * Change height in iframe to new value from settings
	 * @param [type] $html [description]
	 * @return [type] [description]
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Fixed assignment `$size` and `$unit` variable. Added check on empty.
	 */
	public function adjust_height( $html ) {
		$settings = $this->get_settings_for_display();

		$size = ( isset( $settings['aspect_ratio_custom_height'] ) ? $settings['aspect_ratio_custom_height']['size'] : '' );
		$unit = ( isset( $settings['aspect_ratio_custom_height'] ) ? $settings['aspect_ratio_custom_height']['unit'] : '' );

		$html = preg_replace( '/width=[\'\"]\d+[\'\"]/', 'width="100%"', $html );

		return preg_replace( '/height=[\'\"]\d+[\'\"]/', 'height="' . $size . $unit . '"', $html );
	}

	public function __video_counter( $settings, $playlist ) {
		if ( 'yes' !== $settings['show_video_counter'] ) {
			return;
		}

		echo '<div class="elementor-widget-cmsmasters-video-playlist__counter">' .
			'<span class="elementor-widget-cmsmasters-video-playlist__counter-val"></span>' .
			count( $playlist ) .
			' <span class="elementor-widget-cmsmasters-video-playlist__counter-suffix">';

				if ( '' !== $settings['list_heading_counter_suffix'] ) {
					echo esc_html( $settings['list_heading_counter_suffix'] );
				} elseif ( '' === $settings['list_heading_counter_suffix'] ) {
					if ( 1 < count( $playlist ) ) {
						echo esc_html__( 'videos', 'cmsmasters-elementor' );
					} else {
						echo esc_html__( 'video', 'cmsmasters-elementor' );
					}
				}

			echo '</span>' .
		'</div>';
	}

	/**
	 * Get hide classes for elements
	 *
	 * @var sting
	 *
	 * @since 1.0.0
	 */
	public function __get_hide_classes( $settings ) {
		$keys = array(
			'image',
			'numbering',
			'duration',
			'subtitle',
		);
		$result = array();

		foreach ( $keys as $key ) {
			$hide_this = '';
			$hide = 'hide_' . $key;

			if ( ! empty( $settings[ $hide ] ) ) {
				if ( 'tablet' === $settings[ $hide ] ) {
					$hide_this .= ' cmsmasters-hidden-tablet';
				}

				if ( 'mobile' === $settings[ $hide ] ) {
					$hide_this .= ' cmsmasters-hidden-mobile';
				}

				if ( 'both' === $settings[ $hide ] ) {
					$hide_this .= ' cmsmasters-hidden-both';
				}
			}

			$result[ $key ] = $hide_this;
		}

		return $result;
	}

	/**
	 * Get data for passed video.
	 *
	 * @param [type] $url [description]
	 * @return [type] [description]
	 *
	 * @since 1.0.0
	 */
	public function get( $url = '' ) {
		$data = $this->fetch_embed_data( $url );
		$data = $this->merge_api_data( $data );

		return $data;
	}

	/**
	 * Fetch data from oembed provider
	 *
	 * @param [type] $url [description]
	 * @return [type] [description]
	 *
	 * @since 1.0.0
	 */
	public function fetch_embed_data( $url ) {
		$oembed = _wp_oembed_get_object();
		$data = $oembed->get_data( $url );
		$pattern = '/[\'\"](http[s]?:\/\/.*?)[\'\"]/';

		$this->current_provider = $data->provider_name;

		$html = preg_replace_callback( $pattern, array( $this, 'add_embed_args' ), $data->html );

		$this->current_provider = null;

		return array(
			'url' => $url,
			'title' => $data->title,
			'author_name' => $data->author_name,
			'author_url' => $data->author_url,
			'video_id' => $this->get_id_from_html( $html ),
			'provider_name' => $data->provider_name,
			'html' => $html,
			'thumbnail_default' => $data->thumbnail_url,
		);
	}

	/**
	 * Callback to add required arguments to passed video
	 *
	 * @param [type] $matches [description]
	 *
	 * @since 1.0.0
	 */
	public function add_embed_args( $matches ) {
		$args = array();

		switch ( $this->current_provider ) {
			case 'YouTube':
				$args = array( 'enablejsapi' => 1 );
				break;

			case 'Vimeo':
				$args = array(
					'api' => 1,
					'byline' => 0,
					'title' => 0,
				);
				break;
		}

		return sprintf( '"%s"', add_query_arg( $args, $matches[1] ) );
	}

	/**
	 * Find in passed embed string video ID.
	 *
	 * @return [type] [description]
	 *
	 * @since 1.0.0
	 */
	public function get_id_from_html( $html ) {
		preg_match( '/http[s]?:\/\/[a-zA-Z0-9\.\/]+(video|embed)\/([a-zA-Z0-9\-_]+)/', $html, $matches );

		return ! empty( $matches[2] ) ? $matches[2] : false;
	}

	/**
	 * Add data from main provider API to already fetched data.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function merge_api_data( $data ) {
		$id = $data['video_id'];

		if ( ! $id ) {
			return $data;
		}

		$provider = $data['provider_name'];
		$api_data = array();

		switch ( $provider ) {
			case 'YouTube':
				$api_data = $this->get_youtube_data( $id );
				break;

			case 'Vimeo':
				$api_data = $this->get_vimeo_data( $id );
				break;
		}

		return array_merge( $data, $api_data );
	}

	public function get_video_data( $url ) {
		$cache_name = trim( $url );
		$cache = get_transient( $cache_name );

		if ( $cache ) {
			return $cache;
		}

		$result = wp_remote_get( $url );

		if ( is_wp_error( $result ) || 200 !== $result['response']['code'] ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $result );

		set_transient( $cache_name, $body );

		return $body;
	}

	/**
	 * Fetches YouTube specific data
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function get_youtube_data( $id ) {
		$youtube_base = 'https://www.googleapis.com/youtube/v3/videos';

		$body = $this->get_video_data( add_query_arg(
			array(
				'id' => $id,
				'part' => 'contentDetails',
				'key' => get_option( 'elementor_google_api_key' ),
			),
			$youtube_base
		) );

		if ( ! $body ) {
			return array();
		}

		$body = json_decode( $body, true );
		$items = $body['items'];
		$duration = $items[0]['contentDetails']['duration'];

		if ( ! isset( $items ) || ! isset( $duration ) ) {
			return array();
		}

		return array( 'duration' => $this->convert_duration( $duration ) );
	}

	/**
	 * Fetches Vimeo specific data
	 *
	 * @param [type] $id [description]
	 * @return [type] [description]
	 *
	 * @since 1.0.0
	 */
	public function get_vimeo_data( $id ) {
		$vimeo_base = 'https://vimeo.com/api/v2/video/%1$s.json';

		$body = $this->get_video_data( sprintf( $vimeo_base, $id ) );

		if ( ! $body ) {
			return array();
		}

		$body = json_decode( $body, true );

		if ( ! isset( $body[0] ) ) {
			return array();
		}

		$thumbnail_small = $body[0]['thumbnail_small'];
		$thumbnail_medium = $body[0]['thumbnail_medium'];
		$duration = $body[0]['duration'];

		$result = array(
			'thumbnail_small' => isset( $thumbnail_small ) ? $thumbnail_small : false,
			'thumbnail_medium' => isset( $thumbnail_medium ) ? $thumbnail_medium : false,
			'duration' => isset( $duration ) ? $duration : false,
		);

		$result = array_filter( $result );

		if ( ! empty( $result['duration'] ) ) {
			$result['duration'] = $this->convert_duration( $result['duration'] );
		}

		return $result;
	}

	/**
	 * Conversion duration for video
	 *
	 * @since 1.0.0
	 */
	public function convert_duration( $duration ) {
		if ( 0 < absint( $duration ) ) {
			$items = array(
				zeroise( floor( $duration / 60 ), 2 ),
				zeroise( ( $duration % 60 ), 2 ),
			);
		} else {
			$interval = new \DateInterval( $duration );

			$items = array(
				( 0 < $interval->h ) ? zeroise( $interval->h, 2 ) : false,
				zeroise( $interval->i, 2 ),
				zeroise( $interval->s, 2 ),
			);
		}

		return implode( ':', array_filter( $items ) );
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
			'playlist' => array(
				array(
					'field' => 'url',
					'type' => esc_html__( 'Video Playlist URL', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'title',
					'type' => esc_html__( 'Video Playlist Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'subtitle',
					'type' => esc_html__( 'Video Playlist Subtitle', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'subtitle_link' => array(
					'field' => 'url',
					'type' => esc_html__( 'Video Playlist Author/Subtitle URL', 'cmsmasters-elementor' ),
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
				'field' => 'list_heading_text',
				'type' => esc_html__( 'Video Playlist Headline', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'list_heading_counter_suffix',
				'type' => esc_html__( 'Video Playlist Counter Suffix', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
