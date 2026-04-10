<?php
namespace CmsmastersElementor\Modules\Media\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Addon audio widget.
 *
 * Addon widget that displays audio.
 *
 * @since 1.0.0
 */
class Audio extends Base_Widget {

	/**
	 * Current instance.
	 *
	 * @var array
	 */
	protected $_current_instance = array();

	/**
	 * Get widget name.
	 *
	 * Retrieve audio widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'cmsmasters-audio';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve audio widget title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Audio', 'cmsmasters-elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve audio widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'cmsicon-audio';
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
			'audio',
			'player',
			'embed',
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
	 * Retrieve the list of scripts the audio playlist widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'jquery-ui-draggable',
			'jquery-ui-slider',
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
			'widget-cmsmasters-audio',
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
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Disabled options for dynamic fields.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_audio',
			array(
				'label' => __( 'Audio', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'audio_type',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'soundcloud' => __( 'SoundCloud', 'cmsmasters-elementor' ),
					'mixcloud' => __( 'Mixcloud', 'cmsmasters-elementor' ),
					'deezer' => __( 'Deezer', 'cmsmasters-elementor' ),
					'spotify' => __( 'Spotify', 'cmsmasters-elementor' ),
					'hosted' => __( 'Self Hosted', 'cmsmasters-elementor' ),
				),
				'default' => 'soundcloud',
				'frontend_available' => true,
				'prefix_class' => 'cmsmasters-audio-type-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'soundcloud_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'options' => false,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'default' => array(
					'url' => 'https://soundcloud.com/shchxango/john-coltrane-1963-my-favorite',
				),
				'show_external' => false,
				'description' => 'To display the playlist insert the playlist link',
				'condition' => array( 'audio_type' => 'soundcloud' ),
			)
		);

		$this->add_control(
			'mixcloud_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'options' => false,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'default' => array(
					'url' => 'https://www.mixcloud.com/lowlight/best-ambient-of-2019/',
				),
				'show_external' => false,
				'condition' => array( 'audio_type' => 'mixcloud' ),
			)
		);

		$this->add_control(
			'deezer_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'options' => false,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'default' => array(
					'url' => 'https://www.deezer.com/ru/playlist/5922972724',
				),
				'show_external' => false,
				'condition' => array( 'audio_type' => 'deezer' ),
			)
		);

		$this->add_control(
			'spotify_link',
			array(
				'label' => __( 'Link', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'options' => false,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'default' => array(
					'url' => 'https://play.spotify.com/artist/6mdiAmATAx73kdxrNrnlao/blah/blah/blah',
				),
				'show_external' => false,
				'condition' => array( 'audio_type' => 'spotify' ),
			)
		);

		$this->add_control(
			'hosted_insert_link',
			array(
				'label' => __( 'External URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition' => array( 'audio_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'hosted_link',
			array(
				'label' => __( 'Choose File', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => array(
					'active' => true,
					'categories' => array( TagsModule::MEDIA_CATEGORY ),
				),
				'media_type' => 'audio',
				'frontend_available' => true,
				'condition' => array(
					'hosted_insert_link' => '',
					'audio_type' => 'hosted',
				),
			)
		);

		$this->add_control(
			'external_link',
			array(
				'label' => esc_html__( 'URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'options' => false,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'show_external' => false,
				'label_block' => true,
				'frontend_available' => true,
				'condition' => array(
					'hosted_insert_link' => 'yes',
					'audio_type' => 'hosted',
				),
			)
		);

		$this->add_control(
			'hosted_additional_info',
			array(
				'label' => esc_html__( 'Additional Info', 'cmsmasters-elementor' ),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'condition' => array( 'audio_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'visual',
			array(
				'label' => __( 'Visual Player', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array(
					'audio_type' => array(
						'soundcloud',
						'spotify',
					),
				),
			)
		);

		$this->add_control(
			'options',
			array(
				'label' => __( 'Additional Options', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'audio_type',
							'operator' => '!==',
							'value' => 'spotify',
						),
						array(
							'relation' => 'and',
							'terms' => array(
								array(
									'name' => 'audio_type',
									'operator' => '=',
									'value' => 'spotify',
								),
								array(
									'name' => 'visual',
									'operator' => '=',
									'value' => 'yes',
								),
							),
						),
					),
				),
			)
		);

		// SoundCloud
		$this->add_control(
			'sc_show_artwork',
			array(
				'label' => __( 'Artwork', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'cmsmasters-elementor' ),
				'label_on' => __( 'Show', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array(
					'visual' => 'no',
					'audio_type' => 'soundcloud',
				),
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
				'condition' => array(
					'visual' => 'yes',
					'audio_type' => 'soundcloud',
				),
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
				'condition' => array( 'audio_type' => 'soundcloud' ),
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
				'condition' => array( 'audio_type' => 'soundcloud' ),
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
				'condition' => array( 'audio_type' => 'soundcloud' ),
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
				'condition' => array( 'audio_type' => 'soundcloud' ),
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
				'condition' => array( 'audio_type' => 'soundcloud' ),
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
				'condition' => array( 'audio_type' => 'soundcloud' ),
			)
		);

		$this->add_control(
			'sc_color',
			array(
				'label' => __( 'Controls Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array( 'audio_type' => 'soundcloud' ),
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__container iframe' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_type' => 'soundcloud' ),
			)
		);

		// Mixcloud
		$this->add_control(
			'mx_hide_cover',
			array(
				'label' => __( 'Cover', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'off', 'cmsmasters-elementor' ),
				'label_off' => __( 'on', 'cmsmasters-elementor' ),
				'default' => 'yes',
				'condition' => array( 'audio_type' => 'mixcloud' ),
			)
		);

		$this->add_control(
			'mx_hide_artwork',
			array(
				'label' => __( 'Artwork', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array(
					'audio_type' => 'mixcloud',
					'mx_hide_cover' => 'yes',
				),
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
				'condition' => array(
					'audio_type' => 'mixcloud',
					'mx_hide_cover' => 'yes',
				),
			)
		);

		$this->add_control(
			'mx_mini',
			array(
				'label' => __( 'Mini', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array(
					'audio_type' => 'mixcloud',
					'mx_hide_cover' => 'yes',
				),
			)
		);

		$this->add_control(
			'mx_hide_tracklist',
			array(
				'label' => __( 'Tracklist', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array(
					'audio_type' => 'mixcloud',
					'mx_hide_cover' => 'yes',
					'mx_mini' => 'yes',
				),
			)
		);

		// Deezer
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
				'condition' => array( 'audio_type' => 'deezer' ),
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
				'condition' => array(
					'audio_type' => 'deezer',
					'dz_format' => 'classic',
				),
			)
		);

		$this->add_control(
			'dz_playlist',
			array(
				'label' => __( 'Playlist', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => array(
					'audio_type' => 'deezer',
					'dz_format' => 'classic',
				),
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
				'render_type' => 'template',
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-audio-type-deezer.cmsmasters-audio-dz-format-square .elementor-widget-cmsmasters-audio__iframe' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-type-deezer.cmsmasters-audio-dz-format-classic .elementor-widget-cmsmasters-audio__iframe' => 'height: {{SIZE}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'and',
					'terms' => array(
						array(
							'name' => 'audio_type',
							'operator' => '=',
							'value' => 'deezer',
						),
						array(
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
					),
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
					'{{WRAPPER}}.cmsmasters-audio-type-deezer.cmsmasters-audio-dz-format-square .elementor-widget-cmsmasters-audio__iframe' => '{{VALUE}}',
				),
				'condition' => array(
					'audio_type' => 'deezer',
					'dz_format' => 'square',
				),
			)
		);

		$this->add_control(
			'dz_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array( 'audio_type' => 'deezer' ),
			)
		);

		// Spotify
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__container iframe' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'visual' => 'yes',
					'audio_type' => 'spotify',
				),
			)
		);

		// Hosted
		$this->add_control(
			'hs_player_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array( 'audio_type' => 'hosted' ),
				'prefix_class' => 'cmsmasters-player-sep-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'soundcloud',
			)
		);

		$this->end_controls_section();

		// Started Container Style Controls
		$this->start_controls_section(
			'section_hs_container',
			array(
				'label' => esc_html__( 'Container', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array( 'audio_type' => 'hosted' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'hs_container_bg',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio__player-bg',
				'fields_options' => array(
					'color' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-audio__volume-progress-wrap' => 'background-color: {{VALUE}};',
						),
					),
					'background' => array(
						'description' => 'When choosing the Background Image the Color option should be also set.  Color will be applied as the background for Advanced and Volume block',
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name' => 'hs_container_css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio__player-bg',
				'condition' => array(
					'hs_container_bg_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_control(
			'hs_container_overlay_blend_mode',
			array(
				'label' => __( 'Blend Mode', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Normal', 'cmsmasters-elementor' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'luminosity' => 'Luminosity',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player-bg' => 'mix-blend-mode: {{VALUE}}',
				),
				'condition' => array(
					'hs_container_bg_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_control(
			'hs_container_bg_overlay',
			array(
				'label' => __( 'Background Color Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player-bg-overlay' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'hs_container_bg_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_responsive_control(
			'hs_container_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'allowed_dimensions' => 'horizontal',
				'placeholder' => array(
					'top' => 'auto',
					'right' => '',
					'bottom' => 'auto',
					'left' => '',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
				),
				'condition' => array( 'audio_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'hs_container_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player-bg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'hs_container_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio__player-bg',
			)
		);

		$this->add_control(
			'hs_container_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'hs_player_separator' => 'yes',
				),
			)
		);

		$this->add_control(
			'hs_container_separator_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 5,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player > *:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'hs_player_separator' => 'yes',
				),
			)
		);

		$this->add_control(
			'hs_container_separator_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player > *:not(:last-child)' => 'border-right-color: {{VALUE}};',
				),
				'condition' => array(
					'hs_player_separator' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Started Control Button Style Controls
		$this->start_controls_section(
			'section_hs_control_buttons',
			array(
				'label' => esc_html__( 'Control Buttons', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array( 'audio_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'hs_control_button_volume',
			array(
				'label' => __( 'Volume', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description' => 'The volume can be changed on the frontend only',
			)
		);

		$this->add_responsive_control(
			'hs_control_buttons_font_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--buttons-font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hs_control_buttons_play_font_size',
			array(
				'label' => __( 'Play Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--buttons-play-font-size: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'hs_control_buttons_h_gap',
			array(
				'label' => __( 'Horizontal Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player > *' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 ); padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player > *:first-child' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player > *:last-child' => 'padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-player-sep-yes .elementor-widget-cmsmasters-audio__player > *' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hs_control_buttons_v_gap',
			array(
				'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 2,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__player > *' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-player-sep-yes .elementor-widget-cmsmasters-audio__player > *' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_hs_control_buttons_color',
			array(
				'separator' => 'before',
			)
		);

		$this->start_controls_tab(
			'tab_hs_control_buttons_color_normal',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'hs_control_buttons_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__controls-button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hs_control_buttons_hover_color',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'hs_control_buttons_hover_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__controls-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__volume-inner:hover > .elementor-widget-cmsmasters-audio__controls-button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'hs_control_buttons_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio__controls-button > i',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => _x( 'Shadow', 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
			)
		);

		$this->end_controls_section();

		// Started Progress Container Style Controls
		$this->start_controls_section(
			'section_hs_progress',
			array(
				'label' => esc_html__( 'Progress Container', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array( 'audio_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'hs_progress_container',
			array(
				'label' => __( 'Progress', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'hs_progress_external_color',
			array(
				'label' => __( 'External Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress-inner' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__volume-progress:before' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'hs_progress_inner_color',
				'selector' => '
					{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress-inner > div,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress-inner > div:before,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio__volume-progress > div,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio__volume-progress > span
				',
				'fields_options' => array(
					'background' => array(
						'label' => _x( 'Inner Background', 'Background Control', 'cmsmasters-elementor' ),
					),
				),
				'exclude' => array(
					'image',
					'position',
					'xpos',
					'ypos',
					'attachment',
					'attachment_alert',
					'repeat',
					'size',
					'bg_width',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'hs_progress_bd',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'width' => array(
						'selectors' => array(
							'{{SELECTOR}}' => '--progress-bd-top-width: {{TOP}}{{UNIT}}; --progress-bd-right-width: {{RIGHT}}{{UNIT}}; --progress-bd-bottom-width: {{BOTTOM}}{{UNIT}}; --progress-bd-left-width: {{LEFT}}{{UNIT}};',
						),
					),
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress',
			)
		);

		$this->add_control(
			'hs_progress_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress > div,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress > div > div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hs_progress_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--progress-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'hs_progress_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress-inner',
			)
		);

		$this->add_control(
			'hs_progress_current_total_time',
			array(
				'label' => __( 'Current & Total Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'hs_progress_current_total_time_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'hs_progress_current_total_time_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__current-time' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__total-time' => 'color: {{VALUE}};',
				),
				'condition' => array( 'hs_progress_current_total_time_show' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'hs_progress_current_total_time_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__current-time' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__total-time' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__progress-time' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'hs_progress_current_total_time_show' => 'yes' ),
			)
		);

		$this->end_controls_section();

		// Started Progress Container Style Controls
		$this->start_controls_section(
			'section_hs_additional_info',
			array(
				'label' => esc_html__( 'Additional Info', 'cmsmasters-elementor' ),
				'show_label' => false,
				'condition' => array( 'audio_type' => 'hosted' ),
			)
		);

		$this->add_control(
			'hs_additional_info_align',
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
				'default' => 'left',
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__additional_info' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'hs_additional_info_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio__additional_info',
			)
		);

		$this->add_control(
			'hs_additional_info_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 25,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio__additional_info' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render audio widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$audio_type = $settings['audio_type'];

		if ( 'hosted' === $audio_type ) {
			if ( '' === $settings['hosted_insert_link'] ) {
				$audio_type_link = $settings['hosted_link'];
			} else {
				$audio_type_link = $settings['external_link'];
			}
		} else {
			$audio_type_link = $settings[ $audio_type . '_link' ];
		}

		if ( empty( $audio_type_link ) ) {
			return;
		}

		$this->_current_instance = $settings;

		$filter_function = 'filter_' . $this->_current_instance['audio_type'] . '_result';

		if ( method_exists( $this, $filter_function ) ) {
			add_filter( 'oembed_result', array( $this, $filter_function ), 50, 3 );
		}

		$audio_html = wp_oembed_get( esc_url( $audio_type_link['url'] ), wp_embed_defaults() );

		if ( method_exists( $this, $filter_function ) ) {
			remove_filter( 'oembed_result', array( $this, $filter_function ), 50 );
		}

		if ( $audio_html || 'deezer' === $audio_type ) {
			echo '<div class="elementor-widget-cmsmasters-audio__container">';

			if ( 'deezer' === $audio_type ) {
				$this->filter_deezer_result();
			} else {
				Utils::print_unescaped_internal_string( $audio_html );
			}

			echo '</div>';
		}

		if ( 'hosted' === $audio_type ) {
			$this->print_audio_hosted_player();
		}
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

		$visual = 'yes' === $this->_current_instance['visual'] ? 'true' : 'false';

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
	public function filter_deezer_result() {
		$deezer_link = trim( $this->_current_instance['deezer_link']['url'], '/' );

		if ( empty( $deezer_link ) ) {
			return;
		}

		preg_match( '/((?:[^\/]+))\/((?:[^\/]+))$/', wp_parse_url( $deezer_link, PHP_URL_PATH ), $matches );

		$explode_link = explode( '/', $matches[0] );
		$type = array_shift( $explode_link );
		$id = array_pop( $explode_link );
		$height_size = ( isset( $this->_current_instance['dz_height']['size'] ) ? $this->_current_instance['dz_height']['size'] : '90' );
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

		echo '<iframe class="elementor-widget-cmsmasters-audio__iframe" 
			src="' . esc_url( $url ) . esc_attr( $color ) . '" ' .
			esc_attr( $width ) .
			esc_attr( $height ) . ' 
			scrolling="no" 
			frameborder="0" 
			allowtransparency="true" 
			title="Deezer Embed"
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

		if ( 'yes' !== $this->_current_instance['visual'] ) {
			$html = str_replace( 'height="380"', 'height="80"', $html );
		}

		return $html;
	}

	/**
	 * Print hosted current time.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio hosted current time.
	 */
	protected function print_current_time() {
		echo '<div class="elementor-widget-cmsmasters-audio__current-time" title="' . esc_attr__( 'Current Time', 'cmsmasters-elementor' ) . '">' .
			'<span class="elementor-widget-cmsmasters-audio__current-time-value">' .
				esc_html( '00:00' ) .
			'</span>' .
		'</div>';
	}

	/**
	 * Print hosted progress.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio hosted progress.
	 */
	protected function print_progress() {
		echo '<div class="elementor-widget-cmsmasters-audio__progress-wrap">' .
			'<div class="elementor-widget-cmsmasters-audio__progress">' .
				'<div class="elementor-widget-cmsmasters-audio__progress-inner"></div>' .
				'<span class="elementor-widget-cmsmasters-audio__progress-time">' .
					'<span class="elementor-widget-cmsmasters-audio__progress-time-value"></span>' .
				'</span>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Print hosted total time.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio hosted total time.
	 */
	protected function print_total_time() {
		echo '<div class="elementor-widget-cmsmasters-audio__total-time" title="' . esc_attr__( 'Total Time', 'cmsmasters-elementor' ) . '">' .
			'<span class="elementor-widget-cmsmasters-audio__total-time-value">' .
				esc_html( '00:00' ) .
			'</span>' .
		'</div>';
	}

	/**
	 * Print hosted volume.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio hosted volume.
	 */
	protected function print_volume() {
		echo '<div class="elementor-widget-cmsmasters-audio__volume-wrap">' .
			'<div class="elementor-widget-cmsmasters-audio__volume-inner">' .
				'<div class="elementor-widget-cmsmasters-audio__controls-button cmsmasters-player-volume cmsmasters-volume-up-active" title="' . esc_attr__( '100%', 'cmsmasters-elementor' ) . '" role="button" tabindex="0">';

					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-volume-up',
							'library' => 'fa-solid',
						),
						array(
							'class' => 'elementor-widget-cmsmasters-audio__volume-icon cmsmasters-volume-up',
							'aria-hidden' => 'true',
							'aria-label' => 'Volume Up',
						)
					);

					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-volume-down',
							'library' => 'fa-solid',
						),
						array(
							'class' => 'elementor-widget-cmsmasters-audio__volume-icon cmsmasters-volume-down',
							'aria-hidden' => 'true',
							'aria-label' => 'Volume Down',
						),
					);

					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-volume-off',
							'library' => 'fa-solid',
						),
						array(
							'class' => 'elementor-widget-cmsmasters-audio__volume-icon cmsmasters-volume-off',
							'aria-hidden' => 'true',
							'aria-label' => 'Volume Off',
						),
					);

					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-volume-mute',
							'library' => 'fa-solid',
						),
						array(
							'class' => 'elementor-widget-cmsmasters-audio__volume-icon cmsmasters-volume-mute',
							'aria-hidden' => 'true',
							'aria-label' => 'Volume Mute',
						),
					);

				echo '</div>' .
				'<div class="elementor-widget-cmsmasters-audio__volume-progress-wrap">' .
					'<div class="elementor-widget-cmsmasters-audio__volume-progress"></div>' .
				'</div>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Print audio hosted player output on the frontend.
	 *
	 * Written in PHP and used to generate the audio hosted player HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_hosted_player() {
		$settings = $this->get_settings_for_display();

		$link = '';

		$hosted_insert_link = ( isset( $settings['hosted_insert_link'] ) ? $settings['hosted_insert_link'] : '' );
		$external_link = ( isset( $settings['external_link'] ) ? $settings['external_link']['url'] : '' );
		$hosted_link = ( isset( $settings['hosted_link'] ) ? $settings['hosted_link']['url'] : '' );

		$link = ( ! empty( $hosted_insert_link ) ? $external_link : $hosted_link );

		if ( '' === $link ) {
			return;
		}

		echo '<div class="elementor-widget-cmsmasters-audio__player_wrap">' .
			'<div class="elementor-widget-cmsmasters-audio__player-bg">' .
				'<div class="elementor-widget-cmsmasters-audio__player-bg-overlay"></div>' .
				'<audio class="elementor-widget-cmsmasters-audio__player-audio" preload="auto" itemprop="audio" tabindex="-1" type="audio/mpeg">' .
					'<source class="elementor-widget-cmsmasters-audio__player-source" type="audio/mp3" autoplay="autoplay" src="' . esc_url( $link ) . '">' .
				'</audio>' .
				'<div class="elementor-widget-cmsmasters-audio__player">' .
					'<div class="elementor-widget-cmsmasters-audio__controls-button cmsmasters-player-play" title="Play">';

						Icons_Manager::render_icon(
							array(
								'value' => 'fas fa-play',
								'library' => 'fa-solid',
							),
							array(
								'aria-hidden' => 'true',
								'aria-label' => 'Play',
								'role' => 'button',
								'tabindex' => '0',
							)
						);

						Icons_Manager::render_icon(
							array(
								'value' => 'fas fa-pause',
								'library' => 'fa-solid',
							),
							array(
								'aria-hidden' => 'true',
								'aria-label' => 'Pause',
								'role' => 'button',
								'tabindex' => '0',
							)
						);

					echo '</div>';

		if ( 'yes' === $settings['hs_progress_current_total_time_show'] ) {
			$this->print_current_time();
		}

					$this->print_progress();

		if ( 'yes' === $settings['hs_progress_current_total_time_show'] ) {
			$this->print_total_time();
		}

		if ( $settings['hs_control_button_volume'] ) {
			$this->print_volume();
		}

				echo '</div>' .
			'</div>';

		if ( '' !== $settings['hosted_additional_info'] ) {
			echo '<div class="elementor-widget-cmsmasters-audio__additional_info">' . esc_html( $settings['hosted_additional_info'] ) . '</div>';
		}

		echo '</div>';
	}

	/**
	 * Render audio widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 */
	protected function content_template() {}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			'soundcloud_link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Soundcloud Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'mixcloud_link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Mixcloud Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'deezer_link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Deezer Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'spotify_link' => array(
				'field' => 'url',
				'type' => esc_html__( 'Spotify Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			'external_link' => array(
				'field' => 'url',
				'type' => esc_html__( 'External Link', 'cmsmasters-elementor' ),
				'editor_type' => 'LINK',
			),
			array(
				'field' => 'hosted_additional_info',
				'type' => esc_html__( 'Hosted Additional Info', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
