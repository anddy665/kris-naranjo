<?php
namespace CmsmastersElementor\Modules\Media\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
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
 * Addon audio widget.
 *
 * Addon widget that displays audio.
 *
 * @since 1.0.0
 */
class Audio_Playlist extends Base_Widget {

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
		return 'cmsmasters-audio-playlist';
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
		return __( 'Audio Playlist', 'cmsmasters-elementor' );
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
		return 'cmsicon-audio-playlist';
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
			'playlist',
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
			'perfect-scrollbar-js',
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
	 * @since 1.1.0 Added dynamic for `Title`, `Subtitle` and
	 * audio `Links` item controls. Disabled options for 'URL' control.
	 * @since 1.2.3 Fixed error with responsive controls in elementor 3.4.0.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_audio',
			array(
				'label' => __( 'Audio Playlist', 'cmsmasters-elementor' ),
			)
		);

		$repeater = new Repeater();

		/* Start Tab Items Tabs */
		$repeater->start_controls_tabs(
			'audio_items_tabs',
			array()
		);

		/* Start Tab Item Audio Tab */
		$repeater->start_controls_tab(
			'audio_items_tab_audio',
			array(
				'label' => __( 'Audio', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'insert_url',
			array(
				'label' => __( 'External URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
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
				'media_type' => 'audio',
				'description' => 'Supported Audio File Formats: MP3, WAV and OGG',
				'condition' => array( 'insert_url' => '' ),
			)
		);

		$repeater->add_control(
			'external_url',
			array(
				'label' => esc_html__( 'URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					),
				),
				'options' => false,
				'show_external' => false,
				'label_block' => true,
				'condition' => array( 'insert_url' => 'yes' ),
				'description' => 'Supported Audio File Formats: MP3, WAV and OGG',
			)
		);

		$repeater->add_control(
			'track_title',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
				),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'track_subtitle',
			array(
				'label' => esc_html__( 'Subtitle', 'cmsmasters-elementor' ),
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
				),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->end_controls_tab();

		/* Start Tab Item Links Tab */
		$repeater->start_controls_tab(
			'audio_items_tab_links',
			array(
				'label' => __( 'Links', 'cmsmasters-elementor' ),
			)
		);

		$repeater->add_control(
			'audio_source',
			array(
				'label' => __( 'Source', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => array(
					'amazon' => __( 'Amazon', 'cmsmasters-elementor' ),
					'apple' => __( 'Apple', 'cmsmasters-elementor' ),
					'google' => __( 'Google', 'cmsmasters-elementor' ),
					'radiopublic' => __( 'RadioPublic', 'cmsmasters-elementor' ),
					'rss' => __( 'RSS', 'cmsmasters-elementor' ),
					'soundcloud' => __( 'SoundCloud', 'cmsmasters-elementor' ),
					'spotify' => __( 'Spotify', 'cmsmasters-elementor' ),
					'tunein' => __( 'TuneIn', 'cmsmasters-elementor' ),
					'custom_1' => __( 'Custom 1', 'cmsmasters-elementor' ),
					'custom_2' => __( 'Custom 2', 'cmsmasters-elementor' ),
					'custom_3' => __( 'Custom 3', 'cmsmasters-elementor' ),
				),
				'multiple' => true,
				'default' => array(
					'amazon',
					'apple',
				),
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'audio_amazon_url',
			array(
				'label' => __( 'Amazon URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'amazon' ),
			)
		);

		$repeater->add_control(
			'audio_apple_url',
			array(
				'label' => __( 'Apple URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'apple' ),
			)
		);

		$repeater->add_control(
			'audio_google_url',
			array(
				'label' => __( 'Google URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'google' ),
			)
		);

		$repeater->add_control(
			'audio_radiopublic_url',
			array(
				'label' => __( 'RadioPublic URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'radiopublic' ),
			)
		);

		$repeater->add_control(
			'audio_rss_url',
			array(
				'label' => __( 'RSS URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'rss' ),
			)
		);

		$repeater->add_control(
			'audio_soundcloud_url',
			array(
				'label' => __( 'SoundCloud URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'soundcloud' ),
			)
		);

		$repeater->add_control(
			'audio_spotify_url',
			array(
				'label' => __( 'Spotify URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'spotify' ),
			)
		);

		$repeater->add_control(
			'audio_tunein_url',
			array(
				'label' => __( 'TuneIn URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'tunein' ),
			)
		);

		$repeater->add_control(
			'audio_custom_1_url',
			array(
				'label' => __( 'Custom 1 URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'custom_1' ),
			)
		);

		$repeater->add_control(
			'audio_custom_2_url',
			array(
				'label' => __( 'Custom 2 URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'custom_2' ),
			)
		);

		$repeater->add_control(
			'audio_custom_3_url',
			array(
				'label' => __( 'Custom 3 URL', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::URL,
				'dynamic' => array(
					'active' => true,
				),
				'show_external' => false,
				'condition' => array( 'audio_source' => 'custom_3' ),
			)
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
			'audio_list',
			array(
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'title' => '',
						'hosted_url' => array(
							'url' => 'http://mihalich-themes.net/wp-content/uploads/2019/12/Twenty-one-pilots.mp3',
						),
					),
					array(
						'title' => '',
						'hosted_url' => array(
							'url' => 'http://mihalich-themes.net/wp-content/uploads/2019/12/Twenty-one-pilots.mp3',
						),
					),
				),
				'title_field' => '<# if ( \'\' === track_title ) { if ( \'\' !== track_subtitle  ) { #> {{{ track_subtitle }}} <span class="cmsmasters-repeat-item-num hidden"></span><# } else { #>Audio Track <span class="cmsmasters-repeat-item-num"></span> <# } } else { #> {{{ track_title }}} <# if ( \'\' !== track_subtitle ) { #> - {{{ track_subtitle }}} <# } #> <span class="cmsmasters-repeat-item-num hidden"></span><# } #>',
			)
		);

		$this->add_control(
			'audio_size',
			array(
				'label' => __( 'Player Size', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'small' => __( 'Small', 'cmsmasters-elementor' ),
					'medium' => __( 'Medium', 'cmsmasters-elementor' ),
				),
				'default' => 'medium',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-audio-size-',
				'render_type' => 'template',
				'frontend_available' => true,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'audio_playlist_type',
			array(
				'label' => __( 'Playlist Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'label_block' => false,
				'options' => array(
					'static' => array( 'title' => __( 'Static', 'cmsmasters-elementor' ) ),
					'toggle' => array( 'title' => __( 'Toggle', 'cmsmasters-elementor' ) ),
				),
				'default' => 'static',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-audio-playlist-type-',
				'frontend_available' => true,
				'render_type' => 'template',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'audio_player_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array( 'audio_size' => 'small' ),
				'prefix_class' => 'cmsmasters-player-sep-',
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'audio_poster',
			array(
				'label' => __( 'Poster', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'default' => array( 'url' => Utils::get_placeholder_image_src() ),
				'dynamic' => array( 'active' => true ),
				'separator' => 'before',
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name' => 'audio_poster',
				'default' => 'full',
				'separator' => 'none',
				'condition' => array(
					'audio_size' => 'medium',
					'audio_poster[id]!' => '',
				),
			)
		);

		$this->add_control(
			'audio_poster_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'singly' => __( 'Side', 'cmsmasters-elementor' ),
					'with_title' => __( 'With Title', 'cmsmasters-elementor' ),
					'top' => __( 'Top', 'cmsmasters-elementor' ),
				),
				'default' => 'singly',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-poster-position-',
				'render_type' => 'template',
				'condition' => array(
					'audio_size' => 'medium',
					'audio_poster[id]!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'audio_poster_width',
			array(
				'label' => __( 'Width', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 1000,
						'step' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-poster-position-singly .elementor-widget-cmsmasters-audio-playlist__player_left' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-poster-position-with_title .elementor-widget-cmsmasters-audio-playlist__poster' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-poster-position-top .elementor-widget-cmsmasters-audio-playlist__player_left' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'audio_size' => 'medium',
					'audio_poster[id]!' => '',
				),
			)
		);

		$this->end_controls_section();

		// Started Container Style Controls
		$this->start_controls_section(
			'section_audio_container',
			array(
				'label' => esc_html__( 'Container', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'audio_container_bg',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__player-bg',
				'fields_options' => array(
					'color' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__advanced_inner' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-variations' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__volume-progress-wrap' => 'background-color: {{VALUE}};',
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
				'name' => 'audio_container_css_filters',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__player-bg',
				'condition' => array(
					'audio_container_bg_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_control(
			'audio_container_overlay_blend_mode',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__player-bg' => 'mix-blend-mode: {{VALUE}}',
				),
				'condition' => array(
					'audio_container_bg_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_control(
			'audio_container_bg_overlay',
			array(
				'label' => __( 'Background Color Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__player-bg-overlay' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'audio_container_bg_background' => array( 'classic', 'gradient' ),
				),
			)
		);

		$this->add_responsive_control(
			'audio_container_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist-list' => 'padding-bottom: {{BOTTOM}}{{UNIT}};',

					'{{WRAPPER}}.cmsmasters-audio-size-medium .elementor-widget-cmsmasters-audio-playlist__player' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *:first-child' => 'padding-left: {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *:last-child' => 'padding-right: {{RIGHT}}{{UNIT}} !important;',
				),
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'audio_container_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__player-bg',
			)
		);

		$this->add_control(
			'audio_container_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__player-bg,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist.absolute' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'audio_container_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'audio_size' => 'small',
					'audio_player_separator' => 'yes',
				),
			)
		);

		$this->add_control(
			'audio_container_separator_border_width',
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
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__playlist_inner' => 'border-top-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__playlist_item.cmsmasters-playlist-item-separator:after' => 'height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'audio_size' => 'small',
					'audio_player_separator' => 'yes',
				),
			)
		);

		$this->add_control(
			'audio_container_separator_border_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *:not(:last-child)' => 'border-right-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__playlist_inner' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__playlist_item.cmsmasters-playlist-item-separator:after' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'audio_size' => 'small',
					'audio_player_separator' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Started Poster Style Controls
		$this->start_controls_section(
			'section_audio_poster',
			array(
				'label' => esc_html__( 'Poster', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array(
					'audio_size' => 'medium',
					'audio_poster[id]!' => '',
				),
			)
		);

		$this->add_control(
			'audio_poster_h_align',
			array(
				'label' => __( 'Align', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-poster-align-',
				'selectors_dictionary' => array(
					'left' => 'row;',
					'right' => 'row-reverse;',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-poster-position-singly .elementor-widget-cmsmasters-audio-playlist__player' => 'flex-direction: {{VALUE}};',
					'{{WRAPPER}}.cmsmasters-poster-position-with_title .elementor-widget-cmsmasters-audio-playlist__player_left' => 'flex-direction: {{VALUE}};',
				),
				'condition' => array( 'audio_poster_position!' => 'top' ),
			)
		);

		$this->add_responsive_control(
			'audio_poster_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 150,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' => '--audio-poster-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name' => 'audio_poster_border',
				'label' => __( 'Border', 'cmsmasters-elementor' ),
				'fields_options' => array(
					'color' => array(
						'label' => _x( 'Border Color', 'Border Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__poster',
			)
		);

		$this->add_control(
			'audio_poster_border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__poster' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Started Audio Track Name Info Style Controls
		$this->start_controls_section(
			'section_audio_track_name_info',
			array(
				'label' => esc_html__( 'Track Name Info', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_control(
			'audio_track_name_info_one_line',
			array(
				'label' => __( 'In a row', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-track-name-info-one-line-',
			)
		);

		$this->add_control(
			'audio_track_name_info_title_count',
			array(
				'label' => __( 'Track title line count', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'1' => __( '1', 'cmsmasters-elementor' ),
					'2' => __( '2', 'cmsmasters-elementor' ),
				),
				'default' => '1',
				'label_block' => false,
				'toggle' => false,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track_name_info_inner .elementor-widget-cmsmasters-audio-playlist__track-name-title' => '-webkit-line-clamp: {{VALUE}};',
				),
				'condition' => array( 'audio_track_name_info_one_line' => '' ),
			)
		);

		$this->add_control(
			'audio_track_name_info_v_align',
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
				'default' => 'middle',
				'toggle' => false,
				'selectors_dictionary' => array(
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-poster-position-with_title .elementor-widget-cmsmasters-audio-playlist__track_name_info' => 'align-self: {{VALUE}};',
				),
				'condition' => array(
					'audio_poster[id]!' => '',
					'audio_poster_position' => 'with_title',
				),
			)
		);

		$this->add_control(
			'audio_track_name_info_h_singly_align',
			array(
				'label' => __( 'Align', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => array(
					'left' => array(
						'title' => __( 'Left', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => __( 'Right', 'cmsmasters-elementor' ),
						'icon' => 'eicon-h-align-right',
					),
				),
				'default' => 'left',
				'toggle' => false,
				'prefix_class' => 'cmsmasters-track-name-info-h-align-',
				'selectors_dictionary' => array(
					'left' => 'text-align: left;',
					'right' => 'text-align: right;',
				),
				'selectors' => array(
					'{{WRAPPER}}:not(.cmsmasters-poster-position-top) .elementor-widget-cmsmasters-audio-playlist__track_name_info_inner' => '{{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'audio_poster[id]',
							'operator' => '=',
							'value' => '',
						),
						array(
							'name' => 'audio_poster_position',
							'operator' => '!==',
							'value' => 'top',
						),
					),
				),
			)
		);

		$this->add_control(
			'audio_track_name_info_h_top_align',
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
				'prefix_class' => 'cmsmasters-track-name-info-h-align-',
				'selectors_dictionary' => array(
					'left' => 'text-align: left;',
					'center' => 'text-align: center;',
					'right' => 'text-align: right;',
				),
				'selectors' => array(
					'{{WRAPPER}}:not(.cmsmasters-poster-position-with_title) .elementor-widget-cmsmasters-audio-playlist__track_name_info_inner' => '{{VALUE}}',
				),
				'condition' => array(
					'audio_poster[id]!' => '',
					'audio_poster_position' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'audio_track_name_info_ver_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'allowed_dimensions' => 'vertical',
				'placeholder' => array(
					'top' => '',
					'right' => 'auto',
					'bottom' => '',
					'left' => 'auto',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track_name_info' => 'margin-top: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .cmsmasters-empty-poster .elementor-widget-cmsmasters-audio-playlist__track_name_info' => 'margin-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-poster-position-singly .elementor-widget-cmsmasters-audio-playlist__track_name_info' => 'margin-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-poster-position-with_title .elementor-widget-cmsmasters-audio-playlist__player_left' => 'margin-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-poster-position-top .elementor-widget-cmsmasters-audio-playlist__track_name_info' => 'margin-bottom: {{BOTTOM}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'audio_track_name_info_typography',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track_name_info_inner',
				),
				'condition' => array( 'audio_track_name_info_one_line' => 'yes' ),
			)
		);

		$this->add_control(
			'audio_track_name_info_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track_name_info_inner' => 'color: {{VALUE}}',
				),
				'condition' => array( 'audio_track_name_info_one_line' => 'yes' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_audio_track_name_info_style',
			array(
				'separator' => 'before',
				'condition' => array( 'audio_track_name_info_one_line' => '' ),
			)
		);

		$this->start_controls_tab(
			'tab_audio_track_name_info_title_style',
			array(
				'label' => esc_html__( 'Title', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'audio_track_name_info_title_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-name-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'audio_track_name_info_title_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-name-title',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_audio_track_name_info_subtitle_style',
			array(
				'label' => esc_html__( 'Subtitle', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'audio_track_name_info_subtitle_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-name-subtitle' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'audio_track_name_info_subtitle_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-name-subtitle',
			)
		);

		$this->add_responsive_control(
			'audio_track_name_info_subtitle_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}:not(.cmsmasters-track-name-info-one-line-yes) .elementor-widget-cmsmasters-audio-playlist__track-name-subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Started Control Button Style Controls
		$this->start_controls_section(
			'section_audio_control_buttons',
			array(
				'label' => esc_html__( 'Control Buttons', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'audio_control_button_prev_next',
			array(
				'label' => __( 'Prev & Next', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array( 'title' => __( 'None', 'cmsmasters-elementor' ) ),
					'next' => array( 'title' => __( 'Next', 'cmsmasters-elementor' ) ),
					'both' => array( 'title' => __( 'Both', 'cmsmasters-elementor' ) ),
				),
				'default' => 'both',
				'label_block' => true,
				'toggle' => false,
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_control(
			'audio_control_button_backward_forward',
			array(
				'label' => __( 'Backward & Forward', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => array( 'title' => __( 'None', 'cmsmasters-elementor' ) ),
					'forward' => array( 'title' => __( 'Forward', 'cmsmasters-elementor' ) ),
					'both' => array( 'title' => __( 'Both', 'cmsmasters-elementor' ) ),
				),
				'default' => 'none',
				'label_block' => true,
				'toggle' => false,
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_control(
			'audio_control_button_backward_size',
			array(
				'label' => __( 'Backward Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'3' => __( '3 sec', 'cmsmasters-elementor' ),
					'5' => __( '5 sec', 'cmsmasters-elementor' ),
					'10' => __( '10 sec', 'cmsmasters-elementor' ),
					'15' => __( '15 sec', 'cmsmasters-elementor' ),
					'30' => __( '30 sec', 'cmsmasters-elementor' ),
					'60' => __( '1 min', 'cmsmasters-elementor' ),
					'120' => __( '2 min', 'cmsmasters-elementor' ),
					'180' => __( '3 min', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
				'condition' => array(
					'audio_size' => 'medium',
					'audio_control_button_backward_forward' => 'both',
				),
			)
		);

		$this->add_control(
			'audio_control_button_forward_size',
			array(
				'label' => __( 'Forward Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'3' => __( '3 sec', 'cmsmasters-elementor' ),
					'5' => __( '5 sec', 'cmsmasters-elementor' ),
					'10' => __( '10 sec', 'cmsmasters-elementor' ),
					'15' => __( '15 sec', 'cmsmasters-elementor' ),
					'30' => __( '30 sec', 'cmsmasters-elementor' ),
					'60' => __( '1 min', 'cmsmasters-elementor' ),
					'120' => __( '2 min', 'cmsmasters-elementor' ),
					'180' => __( '3 min', 'cmsmasters-elementor' ),
				),
				'frontend_available' => true,
				'condition' => array(
					'audio_size' => 'medium',
					'audio_control_button_backward_forward!' => 'none',
				),
			)
		);

		$this->add_control(
			'audio_control_button_volume',
			array(
				'label' => __( 'Volume', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'description' => 'The volume can be changed on the frontend only',
			)
		);

		$this->add_control(
			'audio_control_button_loop',
			array(
				'label' => __( 'Loop', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_control(
			'audio_control_button_shuffle',
			array(
				'label' => __( 'Shuffle', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_responsive_control(
			'audio_control_buttons_font_size',
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
			'audio_control_medium_buttons_play_font_size',
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
					'{{WRAPPER}}' => '--medium-buttons-play-font-size: {{SIZE}};',
				),
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_responsive_control(
			'audio_control_small_buttons_play_font_size',
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
				'condition' => array( 'audio_size' => 'small' ),
			)
		);

		$this->add_responsive_control(
			'audio_control_buttons_medium_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 3,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.cmsmasters-audio-size-medium .elementor-widget-cmsmasters-audio-playlist__controls-button' => 'margin: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-size-medium .elementor-widget-cmsmasters-audio-playlist__volume-wrap' => 'margin: 0 {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_responsive_control(
			'audio_control_buttons_h_small_gap',
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
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2 ); padding-right: calc( {{SIZE}}{{UNIT}} / 2 );',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *:first-child' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *:last-child' => 'padding-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small.cmsmasters-player-sep-yes .elementor-widget-cmsmasters-audio-playlist__player > *' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_size' => 'small' ),
			)
		);

		$this->add_responsive_control(
			'audio_control_buttons_v_small_gap',
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
					'{{WRAPPER}}.cmsmasters-audio-size-small .elementor-widget-cmsmasters-audio-playlist__player > *' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-audio-size-small.cmsmasters-player-sep-yes .elementor-widget-cmsmasters-audio-playlist__player > *' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_size' => 'small' ),
			)
		);

		$this->start_controls_tabs(
			'tabs_audio_control_buttons_color',
			array(
				'separator' => 'before',
			)
		);

		$this->start_controls_tab(
			'tab_audio_control_buttons_color_normal',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'audio_control_buttons_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__controls-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__controls-button.cmsmasters-button-off-active:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__controls-button.cmsmasters-button-off-active.cmsmasters-active-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__advanced-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_audio_control_buttons_hover_color',
			array(
				'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'audio_control_buttons_hover_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__controls-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__advanced-icon:hover' => 'color: {{VALUE}}; fill: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__volume-inner:hover > .elementor-widget-cmsmasters-audio-playlist__controls-button' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_audio_control_buttons_active_color',
			array(
				'label' => esc_html__( 'Active', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'audio_control_buttons_active_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__controls-button.cmsmasters-active-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-advanced-opened .elementor-widget-cmsmasters-audio-playlist__advanced-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'audio_control_buttons_text_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__controls-button > i, {{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-name-title, {{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__advanced > i',
				'fields_options' => array(
					'text_shadow_type' => array( 'label' => _x( 'Shadow', 'Text Shadow', 'cmsmasters-elementor' ) ),
				),
			)
		);

		$this->end_controls_section();

		// Started Progress Container Style Controls
		$this->start_controls_section(
			'section_audio_progress',
			array(
				'label' => esc_html__( 'Progress Container', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'audio_progress_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
				),
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress-container' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_control(
			'audio_progress_container',
			array(
				'label' => __( 'Progress', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'audio_progress_external_color',
			array(
				'label' => __( 'External Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress-inner' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__volume-progress:before' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name' => 'audio_progress_inner_color',
				'selector' => '
					{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress-inner > div,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress-inner > div:before,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__volume-progress > div,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__volume-progress > span
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
				'name' => 'audio_progress_bd',
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
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress',
			)
		);

		$this->add_control(
			'audio_progress_bd_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress > div,
					{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress > div > div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'audio_progress_height',
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
				'name' => 'audio_progress_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress-inner',
			)
		);

		$this->add_control(
			'audio_progress_current_total_time',
			array(
				'label' => __( 'Current & Total Time', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'audio_progress_current_total_time_show',
			array(
				'label' => __( 'Show', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'audio_progress_current_total_time_position',
			array(
				'label' => __( 'Position', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'top' => array(
						'title' => __( 'Top', 'cmsmasters-elementor' ),
					),
					'inside' => array(
						'title' => __( 'Inside', 'cmsmasters-elementor' ),
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'cmsmasters-elementor' ),
					),
				),
				'default' => 'top',
				'label_block' => false,
				'toggle' => false,
				'prefix_class' => 'cmsmasters-current-total-time-position-',
				'render_type' => 'template',
				'condition' => array(
					'audio_size' => 'medium',
					'audio_progress_current_total_time_show' => 'yes',
				),
			)
		);

		$this->add_control(
			'audio_progress_current_total_time_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__current-time' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__total-time' => 'color: {{VALUE}};',
				),
				'condition' => array( 'audio_progress_current_total_time_show' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'audio_progress_current_total_time_size',
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
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__current-time' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__total-time' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__progress-time' => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_progress_current_total_time_show' => 'yes' ),
			)
		);

		$this->add_responsive_control(
			'audio_progress_current_total_time_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 6,
						'max' => 15,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--time-gap: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'audio_progress_current_total_time_show' => 'yes',
					'audio_size' => 'medium',
				),
			)
		);

		$this->end_controls_section();

		// Started Advanced Style Controls
		$this->start_controls_section(
			'section_audio_advanced',
			array(
				'label' => esc_html__( 'Advanced', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_control(
			'audio_advanced_speed',
			array(
				'label' => __( 'Speed', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'audio_advanced_download',
			array(
				'label' => __( 'Download', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'audio_advanced_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__advanced_inner' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-title-wrap' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-rate' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__download' => 'color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'audio_advanced_speed',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'name' => 'audio_advanced_download',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_control(
			'audio_advanced_hover_color',
			array(
				'label' => __( 'Hover & Active Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-button.cmsmasters-choose-speed' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-title-wrap:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__speed-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__download:hover' => 'color: {{VALUE}}',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'audio_advanced_speed',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'name' => 'audio_advanced_download',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'audio_advanced_padding',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__advanced_inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'conditions' => array(
					'relation' => 'or',
					'terms' => array(
						array(
							'name' => 'audio_advanced_speed',
							'operator' => '=',
							'value' => 'yes',
						),
						array(
							'name' => 'audio_advanced_download',
							'operator' => '=',
							'value' => 'yes',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'audio_advanced_item_gap',
			array(
				'label' => __( 'Item Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__advanced_inner > * + *' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'audio_advanced_speed' => 'yes',
					'audio_advanced_download' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		// Started Playlist Style Controls
		$this->start_controls_section(
			'section_audio_playlist',
			array(
				'label' => __( 'Playlist', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'audio_playlist_absolute',
			array(
				'label' => __( 'Absolute', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'prefix_class' => 'cmsmasters-audio-playlist-absolute-',
				'render_type' => 'template',
				'condition' => array( 'audio_playlist_type' => 'toggle' ),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_max_height',
			array(
				'label' => __( 'Max Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 150,
						'max' => 400,
						'step' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist-list' => 'max-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'audio_playlist_bg',
			array(
				'label' => __( 'Background Color Overlay', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_inner' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_medium_vertical_gap',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'allowed_dimensions' => 'vertical',
				'placeholder' => array(
					'top' => '',
					'right' => 'auto',
					'bottom' => '',
					'left' => 'auto',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_inner' => 'padding-top: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist-list' => 'padding-bottom: {{BOTTOM}}{{UNIT}};',
				),
				'condition' => array( 'audio_size' => 'medium' ),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_small_gap',
			array(
				'label' => __( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} 0 {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist-list' => 'padding-bottom: {{BOTTOM}}{{UNIT}};',
				),
				'condition' => array( 'audio_size' => 'small' ),
			)
		);

		$this->end_controls_section();

		// Started Playlist Item Style Controls
		$this->start_controls_section(
			'section_audio_playlist_item',
			array(
				'label' => esc_html__( 'Playlist Item', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'audio_playlist_item_one_line',
			array(
				'label' => __( 'In a row', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-track-name-one-line-',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'audio_playlist_item_typography',
				'selector' => '{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-info',
				'fields_options' => array(
					'line_height' => array(
						'selectors' => array(
							'{{SELECTOR}}' => 'line-height: {{SIZE}}{{UNIT}}',
							'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-equalizer_wrap' => 'height: {{SIZE}}{{UNIT}}',
							'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-number' => 'height: {{SIZE}}{{UNIT}}',
						),
					),
				),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_item_gap',
			array(
				'label' => __( 'Gap Between', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_item:after' => 'bottom: calc( -{{SIZE}}{{UNIT}} / 2 );',
				),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_item_padding',
			array(
				'label' => esc_html__( 'Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs(
			'tabs_audio_playlist_item_style',
			array(
				'separator' => 'before',
			)
		);

		$this->start_controls_tab(
			'tab_audio_playlist_item_normal',
			array(
				'label' => esc_html__( 'Normal', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'audio_playlist_item_normal_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-podcast a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-podcast a > svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-equalizer-item' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'audio_playlist_item_normal_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_item' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_audio_playlist_item_hover',
			array( 'label' => esc_html__( 'Hover', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'audio_playlist_item_hover_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-podcast a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-active-track > .elementor-widget-cmsmasters-audio-playlist__track-podcast a:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-podcast a:hover > svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-active-track > .elementor-widget-cmsmasters-audio-playlist__track-podcast > a:hover > svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track:hover .elementor-widget-cmsmasters-audio-playlist__track-equalizer-item' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'audio_playlist_item_hover_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_item:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_audio_playlist_item_active',
			array( 'label' => esc_html__( 'Active', 'cmsmasters-elementor' ) )
		);

		$this->add_control(
			'audio_playlist_item_active_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-active-track > .elementor-widget-cmsmasters-audio-playlist__track' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-active-track:hover > .elementor-widget-cmsmasters-audio-playlist__track' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-active-track > .elementor-widget-cmsmasters-audio-playlist__track-podcast a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-active-track > .elementor-widget-cmsmasters-audio-playlist__track-podcast svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-active-track > .elementor-widget-cmsmasters-audio-playlist__track .elementor-widget-cmsmasters-audio-playlist__track-equalizer-item' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .cmsmasters-active-track:hover > .elementor-widget-cmsmasters-audio-playlist__track .elementor-widget-cmsmasters-audio-playlist__track-equalizer-item' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'audio_playlist_item_active_bg',
			array(
				'label' => __( 'Background Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_item.cmsmasters-active-track' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__playlist_item.cmsmasters-active-track:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'audio_playlist_item_separator',
			array(
				'label' => __( 'Separator', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'audio_playlist_item_marker_heading',
			array(
				'label' => __( 'Marker', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'audio_playlist_item_marker',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'number' => __( 'Number', 'cmsmasters-elementor' ),
				),
				'default' => 'none',
				'label_block' => false,
				'toggle' => false,
				'render_type' => 'template',
				'prefix_class' => 'cmsmasters-playlist-marker-',
			)
		);

		$this->add_control(
			'audio_playlist_item_number_additional_symbol',
			array(
				'label' => __( 'Number Additional Symbol', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your additional symbol', 'cmsmasters-elementor' ),
				'label_block' => true,
				'condition' => array( 'audio_playlist_item_marker' => 'number' ),
			)
		);

		$this->add_control(
			'audio_playlist_item_icon',
			array(
				'label' => esc_html__( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-itunes-note',
					'library' => 'fa-brands',
				),
				'condition' => array( 'audio_playlist_item_marker' => 'icon' ),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_item_icon_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 3,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-info' => 'padding-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_playlist_item_marker!' => 'none' ),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_item_icon_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 30,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}' => '--cmsmasters-audio-playlist-item-icon-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-playlist-marker-number .elementor-widget-cmsmasters-audio-playlist__track-number' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.cmsmasters-playlist-marker-number .elementor-widget-cmsmasters-audio-playlist__track-number > span:before' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				),
				'condition' => array( 'audio_playlist_item_marker!' => 'none' ),
			)
		);

		$this->end_controls_section();

		// Started Playlist Item Links Style Controls
		$this->start_controls_section(
			'section_audio_playlist_item_links',
			array(
				'label' => esc_html__( 'Playlist Item Links', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'audio_source_sequence',
			array(
				'label' => __( 'Sequence of elements', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::SELECTIZE,
				'label_block' => true,
				'options' => array(
					'amazon' => __( 'Amazon', 'cmsmasters-elementor' ),
					'apple' => __( 'Apple', 'cmsmasters-elementor' ),
					'google' => __( 'Google', 'cmsmasters-elementor' ),
					'radiopublic' => __( 'RadioPublic', 'cmsmasters-elementor' ),
					'rss' => __( 'RSS', 'cmsmasters-elementor' ),
					'soundcloud' => __( 'SoundCloud', 'cmsmasters-elementor' ),
					'spotify' => __( 'Spotify', 'cmsmasters-elementor' ),
					'tunein' => __( 'TuneIn', 'cmsmasters-elementor' ),
					'custom_1' => __( 'Custom 1', 'cmsmasters-elementor' ),
					'custom_2' => __( 'Custom 2', 'cmsmasters-elementor' ),
					'custom_3' => __( 'Custom 3', 'cmsmasters-elementor' ),
				),
				'multiple' => true,
				'control_options' => array(
					'plugins' => array(
						'drag_drop',
					),
				),
				'default' => array(
					'amazon',
					'apple',
					'google',
					'radiopublic',
					'rss',
					'soundcloud',
					'spotify',
					'tunein',
					'custom_1',
					'custom_2',
					'custom_3',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'audio_playlist_item_icon_links_hidden',
			array(
				'label' => __( 'Hidden Links', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'prefix_class' => 'cmsmasters-track-links-hidden-',
				'description' => 'When turned on, links will be displayed only on hover and on the active track',
			)
		);

		$this->add_responsive_control(
			'audio_playlist_item_icon_links_gap',
			array(
				'label' => __( 'Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-podcast > a' => 'margin-left: calc( {{SIZE}}{{UNIT}} / 2 ); margin-right: calc( {{SIZE}}{{UNIT}} / 2 );',
				),
			)
		);

		$this->add_responsive_control(
			'audio_playlist_item_icon_links_size',
			array(
				'label' => __( 'Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 20,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-podcast' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .elementor-widget-cmsmasters-audio-playlist__track-podcast svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'audio_playlist_item_icon_links_custom_1',
			array(
				'label' => esc_html__( 'Custom 1', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-itunes-note',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'audio_playlist_item_icon_links_custom_2',
			array(
				'label' => esc_html__( 'Custom 2', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-itunes-note',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'audio_playlist_item_icon_links_custom_3',
			array(
				'label' => esc_html__( 'Custom 3', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-itunes-note',
					'library' => 'fa-brands',
				),
			)
		);

		$this->end_controls_section();

		// Started Icons Controls
		$this->start_controls_section(
			'section_audio_icons',
			array(
				'label' => esc_html__( 'Icons', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'amazon_icon',
			array(
				'label' => esc_html__( 'Amazon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-amazon',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'apple_icon',
			array(
				'label' => esc_html__( 'Apple', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-apple',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'google_icon',
			array(
				'label' => esc_html__( 'Google', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-google',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'radiopublic_icon',
			array(
				'label' => esc_html__( 'RadioPublic', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-itunes-note',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'rss_icon',
			array(
				'label' => esc_html__( 'RSS', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fas fa-rss',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'soundcloud_icon',
			array(
				'label' => esc_html__( 'SoundCloud', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-soundcloud',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'spotify_icon',
			array(
				'label' => esc_html__( 'Spotify', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-spotify',
					'library' => 'fa-brands',
				),
			)
		);

		$this->add_control(
			'tunein_icon',
			array(
				'label' => esc_html__( 'TuneIn', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => array(
					'value' => 'fab fa-itunes-note',
					'library' => 'fa-brands',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render audio poster on the widget frontend.
	 *
	 * Written in PHP and used to generate the audio poster HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_poster() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['audio_poster']['id'] ) ) {
			return;
		}

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__poster">' .
			Group_Control_Image_Size::get_attachment_image_html( $settings, 'audio_poster' ) . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'</div>';
	}

	protected function print_icon_control( $icon_control ) {
		$icons = array(
			'play_icon' => 'fas fa-play',
			'prev_icon' => 'fas fa-fast-backward',
			'next_icon' => 'fas fa-fast-forward',
			'backward_icon' => 'fas fa-undo',
			'forward_icon' => 'fas fa-redo-alt',
			'shuffle_icon' => 'fas fa-bezier-curve',
			'loop_icon' => 'fas fa-retweet',
			'volume_up_icon' => 'fas fa-volume-up',
			'list_icon' => 'fas fa-list',
		);

		return $icons[ $icon_control ];
	}

	/**
	 * Print control button.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio control button.
	 */
	protected function print_control_button( $button, $title, $add_icon, $icon_class, $icon, $button_attrs = array(), $icon_attrs = array( 'tabindex' => '0' ) ) {
		$button_att = '';
		$icon_att = '';

		if ( ! empty( $button_attrs ) && is_array( $button_attrs ) ) {
			foreach ( $button_attrs as $attr_name => $attr_value ) {
				if ( is_bool( $attr_value ) && true === $attr_value ) {
					$button_att .= ' ' . esc_attr( $attr_name );
				} else {
					$button_att .= sprintf( ' %s="%s"', esc_attr( $attr_name ), esc_attr( $attr_value ) );
				}
			}
		}

		if ( ! empty( $icon_attrs ) && is_array( $icon_attrs ) ) {
			foreach ( $icon_attrs as $attr_name => $attr_value ) {
				if ( is_bool( $attr_value ) && true === $attr_value ) {
					$icon_att .= ' ' . esc_attr( $attr_name );
				} else {
					$icon_att .= sprintf( ' %s="%s"', esc_attr( $attr_name ), esc_attr( $attr_value ) );
				}
			}
		}

		echo '<div 
			class="elementor-widget-cmsmasters-audio-playlist__controls-button cmsmasters-player-' . esc_attr( $button ) . ( 'volume' === $button ? ' cmsmasters-volume-up-active' : '' ) . '" 
			title="' . esc_attr( $title ) . '"' .
			$button_att . // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'>';

		if ( $add_icon ) {
			Icons_Manager::render_icon(
				array(
					'value' => $this->print_icon_control( $icon ),
					'library' => 'fa-solid',
				),
				array_merge(
					array(
						'class' => 'elementor-widget-cmsmasters-audio-playlist' . $icon_class,
						'aria-hidden' => 'true',
						'aria-label' => ucfirst( $button ),
					),
					$icon_attrs
				)
			);

			if ( 'play' === $button ) {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-pause',
						'library' => 'fa-solid',
					),
					array(
						'aria-hidden' => 'true',
						'aria-label' => 'Pause',
						'role' => 'button',
						'tabindex' => '-1',
					)
				);
			}

			if ( 'volume' === $button ) {
				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-volume-down',
						'library' => 'fa-solid',
					),
					array(
						'class' => 'elementor-widget-cmsmasters-audio-playlist__volume-icon cmsmasters-volume-down',
						'aria-hidden' => 'true',
						'aria-label' => 'Volume Down',
					)
				);

				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-volume-off',
						'library' => 'fa-solid',
					),
					array(
						'class' => 'elementor-widget-cmsmasters-audio-playlist__volume-icon cmsmasters-volume-off',
						'aria-hidden' => 'true',
						'aria-label' => 'Volume Off',
					)
				);

				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-volume-mute',
						'library' => 'fa-solid',
					),
					array(
						'class' => 'elementor-widget-cmsmasters-audio-playlist__volume-icon cmsmasters-volume-mute',
						'aria-hidden' => 'true',
						'aria-label' => 'Volume Mute',
					)
				);
			}
		}

		echo '</div>';
	}

	/**
	 * Print current time.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio current time.
	 */
	protected function print_current_time() {
		echo '<div class="elementor-widget-cmsmasters-audio-playlist__current-time" title="' . esc_attr__( 'Current Time', 'cmsmasters-elementor' ) . '">' .
			'<span class="elementor-widget-cmsmasters-audio-playlist__current-time-value">' .
				esc_html( '00:00' ) .
			'</span>' .
		'</div>';
	}

	/**
	 * Print progress.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio progress.
	 */
	protected function print_progress() {
		echo '<div class="elementor-widget-cmsmasters-audio-playlist__progress-wrap">' .
			'<div class="elementor-widget-cmsmasters-audio-playlist__progress">' .
				'<div class="elementor-widget-cmsmasters-audio-playlist__progress-inner"></div>' .
				'<span class="elementor-widget-cmsmasters-audio-playlist__progress-time">' .
					'<span class="elementor-widget-cmsmasters-audio-playlist__progress-time-value"></span>' .
				'</span>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Print total time.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio total time.
	 */
	protected function print_total_time() {
		echo '<div class="elementor-widget-cmsmasters-audio-playlist__total-time" title="' . esc_attr__( 'Total Time', 'cmsmasters-elementor' ) . '">' .
			'<span class="elementor-widget-cmsmasters-audio-playlist__total-time-value">' .
				esc_html( '00:00' ) .
			'</span>' .
		'</div>';
	}

	/**
	 * Print volume.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio volume.
	 */
	protected function print_volume() {
		echo '<div class="elementor-widget-cmsmasters-audio-playlist__volume-wrap">' .
			'<div class="elementor-widget-cmsmasters-audio-playlist__volume-inner">';

				$this->print_control_button(
					'volume',
					'100%',
					true,
					'__volume-icon cmsmasters-volume-up',
					'volume_up_icon',
					array( 'tabindex' => '0' ),
					array()
				);

				echo '<div class="elementor-widget-cmsmasters-audio-playlist__volume-progress-wrap">' .
					'<div class="elementor-widget-cmsmasters-audio-playlist__volume-progress"></div>' .
				'</div>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Render progress for medium type on the widget frontend.
	 *
	 * Written in PHP and used to generate the progress HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_progress_medium() {
		$settings = $this->get_settings_for_display();

		$time_position = $settings['audio_progress_current_total_time_position'];

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__progress-container">';

		if ( 'top' !== $time_position ) {
			$this->print_progress();
		}

		if ( 'yes' === $settings['audio_progress_current_total_time_show'] ) {
			$this->print_current_time();

			$this->print_total_time();
		}

		if ( 'top' === $time_position ) {
			$this->print_progress();
		}

		echo '</div>';
	}

	/**
	 * Print control buttons left.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio control buttons left.
	 */
	protected function print_control_buttons_left() {
		$settings = $this->get_settings_for_display();

		$audio_list = $settings['audio_list'];

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__control_buttons_left">';

		if ( 'both' === $settings['audio_control_button_prev_next'] && '1' < count( $audio_list ) ) {
			$this->print_control_button(
				'prev',
				'Prev',
				true,
				'__prev-icon',
				'prev_icon',
				array(
					'disabled' => true,
					'tabindex' => '-1',
				),
				array()
			);
		}

		if ( 'both' === $settings['audio_control_button_backward_forward'] ) {
			$this->print_control_button( 'backward', 'Backward', true, '__backward-icon', 'backward_icon' );
		}

		$this->print_control_button( 'play', 'Play', true, '__play-icon', 'play_icon' );

		if ( 'none' !== $settings['audio_control_button_backward_forward'] ) {
			$this->print_control_button( 'forward', 'Forward', true, '__forward-icon', 'forward_icon' );
		}

		if ( 'none' !== $settings['audio_control_button_prev_next'] && '1' < count( $audio_list ) ) {
			$this->print_control_button(
				'next',
				'Next',
				true,
				'__next-icon',
				'next_icon',
				array( 'tabindex' => '0' ),
				array()
			);
		}

		echo '</div>';
	}

	/**
	 * Print track name.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio track name.
	 */
	protected function print_track_name( $audio_url, $track_title_cl, $track_title_media, $one_line_control, $track_separator_cl, $track_subtitle_cl, $track_subtitle_media ) {
		$settings = $this->get_settings_for_display();

		echo '<span class="elementor-widget-cmsmasters-audio-playlist' . esc_attr( $track_title_cl ) . '">';

		$id = attachment_url_to_postid( $audio_url );
		$track_title_metadata = isset( wp_get_attachment_metadata( $id )['title'] ) ? wp_get_attachment_metadata( $id )['title'] : '';
		$track_title = ! empty( $track_title_media ) ? esc_html( $track_title_media ) : esc_html( $track_title_metadata );

		if ( '' !== $track_title ) {
			echo esc_html( $track_title );
		} else {
			echo esc_html__( 'Enter track name', 'cmsmasters-elementor' );
		}

		echo '</span>';

		$track_subtitle_metadata = isset( wp_get_attachment_metadata( $id )['artist'] ) ? wp_get_attachment_metadata( $id )['artist'] : '';
		$track_subtitle = ! empty( $track_subtitle_media ) ? esc_html( $track_subtitle_media ) : esc_html( $track_subtitle_metadata );

		if ( '' !== $track_subtitle ) {
			if ( 'yes' === $settings[ $one_line_control ] ) {
				echo '<span class="elementor-widget-cmsmasters-audio-playlist' . esc_attr( $track_separator_cl ) . '">-</span>';
			}

			echo '<span class="elementor-widget-cmsmasters-audio-playlist' . esc_attr( $track_subtitle_cl ) . '">' .
				esc_html( $track_subtitle ) .
			'</span>';
		}
	}

	/**
	 * Print control buttons right.
	 *
	 * @since 1.0.0
	 *
	 * @return array Audio control buttons right.
	 */
	protected function print_control_buttons_right() {
		$settings = $this->get_settings_for_display();

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__control_buttons_right">';

		if ( $settings['audio_control_button_shuffle'] && '1' < count( $settings['audio_list'] ) ) {
			$this->print_control_button( 'shuffle', 'Shuffle', true, '__shuffle-icon', 'shuffle_icon' );
		}

		if ( $settings['audio_control_button_loop'] ) {
			$this->print_control_button( 'loop cmsmasters-loop-disabled', 'Loop', true, '__loop-icon', 'loop_icon' );
		}

		if ( $settings['audio_control_button_volume'] ) {
			$this->print_volume();
		}

		if ( 'toggle' === $settings['audio_playlist_type'] && '1' < count( $settings['audio_list'] ) ) {
			$this->print_control_button( 'list', 'Show Playlist', true, '__list-icon', 'list_icon' );
		}

		echo '</div>';
	}

	/**
	 * Render track name for medium type on the widget frontend.
	 *
	 * Written in PHP and used to generate the track name HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_track_name_info() {
		$settings = $this->get_settings_for_display();

		$poster = $settings['audio_poster']['id'];

		if ( ! empty( $poster ) && 'with_title' === $settings['audio_poster_position'] ) {
			$this->print_audio_poster();

			echo '<div class="elementor-widget-cmsmasters-audio-playlist__track_name_info_wrap">';
				$this->print_advanced();
		}

		$list = $settings['audio_list'];

		if ( ! empty( $list[0]['insert_url'] ) ) {
			$audio_url = $list[0]['external_url']['url'];
		} else {
			$audio_url = $list[0]['hosted_url']['url'];
		}

		if ( empty( $audio_url ) ) {
			return;
		}

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__track_name_info">' .
			'<div class="elementor-widget-cmsmasters-audio-playlist__track_name_info_inner">';

		if ( ! empty( $list[0]['insert_url'] ) ) {
			$track_title = pathinfo( $list[0]['external_url']['url'] )['filename'];
		} else {
			$track_title = $list[0]['track_title'];
		}

				$this->print_track_name(
					$audio_url,
					'__track-name-title',
					$track_title,
					'audio_track_name_info_one_line',
					'__track-name-separator',
					'__track-name-subtitle',
					$list[0]['track_subtitle']
				);

			echo '</div>' .
		'</div>';

		if ( ! empty( $poster ) && 'with_title' === $settings['audio_poster_position'] ) {
			echo '</div>';
		}
	}

	/**
	 * Render track name for medium type on the widget frontend.
	 *
	 * Written in PHP and used to generate the track name HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_advanced() {
		$settings = $this->get_settings_for_display();

		$list = $settings['audio_list'];

		$speeds = array(
			'0.5' => 'backward-0.5',
			'0.75' => 'backward-0.75',
			'Normal' => 'normal cmsmasters-choose-speed',
			'1.25' => 'forward-1.25',
			'1.5' => 'forward-1.5',
			'1.75' => 'forward-1.75',
			'2' => 'forward-2',
		);

		if ( 'yes' === $settings['audio_advanced_download'] || 'yes' === $settings['audio_advanced_speed'] ) {
			echo '<div class="elementor-widget-cmsmasters-audio-playlist__advanced">';

				Icons_Manager::render_icon(
					array(
						'value' => 'fas fa-ellipsis-v',
						'library' => 'fa-solid',
					),
					array(
						'class' => 'elementor-widget-cmsmasters-audio-playlist__advanced-icon',
						'title' => esc_attr__( 'Advanced', 'cmsmasters-elementor' ),
						'aria-hidden' => 'true',
						'aria-label' => 'Advanced',
						'role' => 'button',
						'tabindex' => '0',
					)
				);

				echo '<div class="elementor-widget-cmsmasters-audio-playlist__advanced_inner">';

			if ( 'yes' === $settings['audio_advanced_speed'] ) {
				echo '<span class="elementor-widget-cmsmasters-audio-playlist__speed">' .
					'<span class="elementor-widget-cmsmasters-audio-playlist__speed-variations">';

				foreach ( $speeds as $key => $value ) {
					echo '<span class="elementor-widget-cmsmasters-audio-playlist__speed-button cmsmasters-player-speed-' . esc_attr( $value ) . '">' .
						esc_html( $key ) .
					'</span>';
				}

				echo '</span>' .
					'<span class="elementor-widget-cmsmasters-audio-playlist__speed-title-wrap">' .
						'<span class="elementor-widget-cmsmasters-audio-playlist__speed-title">' . esc_html__( 'Speed:', 'cmsmasters-elementor' ) . '</span>' .
						'<span class="elementor-widget-cmsmasters-audio-playlist__speed-rate" tabindex="0">' . esc_html__( 'Normal', 'cmsmasters-elementor' ) . '</span>' .
					'</span>' .
				'</span>';
			}

			if ( 'yes' === $settings['audio_advanced_download'] ) {
				$audio_url = '';

				if ( ! empty( $list[0]['insert_url'] ) ) {
					$audio_url = $list[0]['external_url']['url'];
				} else {
					$audio_url = $list[0]['hosted_url']['url'];
				}

				$path_parts = pathinfo( $audio_url )['filename'];

				echo '<a class="elementor-widget-cmsmasters-audio-playlist__download" href="' . esc_url( $audio_url ) . '" download="' . esc_attr( $path_parts ) . '">';

					Icons_Manager::render_icon(
						array(
							'value' => 'fas fa-download',
							'library' => 'fa-solid',
						),
						array(
							'aria-hidden' => 'true',
							'aria-label' => 'Download',
						)
					);

					echo '<span>' . esc_html__( 'Download', 'cmsmasters-elementor' ) . '</span>' .
				'</a>';
			}

			echo '</div>' .
			'</div>';
		}
	}

	/**
	 * Get audio link on the widget frontend.
	 *
	 * Written in PHP and used to generate the audio link.
	 *
	 * @since 1.0.0
	 */
	protected function get_audio_link( $item, $audio_item, $default_icon_value, $default_icon_library ) {
		$settings = $this->get_settings_for_display();

		foreach ( $item['audio_source'] as $source_item ) {
			if ( $audio_item === $source_item ) {
				if ( 'custom_1' === $source_item || 'custom_2' === $source_item || 'custom_3' === $source_item ) {
					$icon = $settings[ 'audio_playlist_item_icon_links_' . $source_item ];
				} else {
					$icon = $settings[ $source_item . '_icon' ];
				}

				$source_item_url = $item[ 'audio_' . $source_item . '_url' ]['url'];

				if ( $source_item_url && $icon ) {
					echo '<a href="' . esc_url( $source_item_url ) . '" title="' . esc_attr( $source_item ) . '" target="_blank">';

					if ( '' !== $icon['value'] ) {
						Icons_Manager::render_icon( $icon, array(
							'aria-hidden' => 'true',
							'aria-label' => esc_attr( ucfirst( $audio_item ) ) . ' Link',
						) );
					} else {
						Icons_Manager::render_icon(
							array(
								'value' => $default_icon_value,
								'library' => $default_icon_library,
							),
							array(
								'aria-hidden' => 'true',
								'aria-label' => esc_attr( ucfirst( $audio_item ) ) . ' Link',
							)
						);
					}

					echo '</a>';
				}
			}
		}
	}

	/**
	 * Render audio link on the widget frontend.
	 *
	 * Written in PHP and used to generate the audio link HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_link( $item ) {
		$settings = $this->get_settings_for_display();

		foreach ( $settings['audio_source_sequence'] as $audio_item ) {
			switch ( $audio_item ) {
				case 'apple':
					$this->get_audio_link( $item, $audio_item, 'fab fa-apple', 'fa-brands' );

					break;
				case 'spotify':
					$this->get_audio_link( $item, $audio_item, 'fab fa-spotify', 'fa-brands' );

					break;
				case 'soundcloud':
					$this->get_audio_link( $item, $audio_item, 'fab fa-soundcloud', 'fa-brands' );

					break;
				case 'google':
					$this->get_audio_link( $item, $audio_item, 'fab fa-google', 'fa-brands' );

					break;
				case 'amazon':
					$this->get_audio_link( $item, $audio_item, 'fab fa-amazon', 'fa-brands' );

					break;
				case 'tunein':
					$this->get_audio_link( $item, $audio_item, 'fab fa-itunes-note', 'fa-brands' );

					break;
				case 'radiopublic':
					$this->get_audio_link( $item, $audio_item, 'fab fa-itunes-note', 'fa-brands' );

					break;
				case 'rss':
					$this->get_audio_link( $item, $audio_item, 'fas fa-rss', 'fa-solid' );

					break;
				case 'custom_1':
					$this->get_audio_link( $item, $audio_item, 'fab fa-itunes-note', 'fa-brands' );

					break;
				case 'custom_2':
					$this->get_audio_link( $item, $audio_item, 'fab fa-itunes-note', 'fa-brands' );

					break;
				case 'custom_3':
					$this->get_audio_link( $item, $audio_item, 'fab fa-itunes-note', 'fa-brands' );

					break;
			}
		}
	}

	/**
	 * Render audio item output on the frontend.
	 *
	 * Written in PHP and used to generate the audio item HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_marker() {
		$settings = $this->get_settings_for_display();

		$item_marker = ( isset( $settings['audio_playlist_item_marker'] ) ? $settings['audio_playlist_item_marker'] : '' );

		if ( 'icon' === $item_marker ) {
			echo '<div class="elementor-widget-cmsmasters-audio-playlist__track-equalizer_wrap">';

			if ( '' !== $settings['audio_playlist_item_icon']['value'] ) {
				Icons_Manager::render_icon( $settings['audio_playlist_item_icon'], array( 'aria-hidden' => 'true' ) );
			} else {
				Icons_Manager::render_icon(
					array(
						'value' => 'fab fa-itunes-note',
						'library' => 'fa-brands',
					),
					array( 'aria-hidden' => 'true' )
				);
			}

			echo '<div class="elementor-widget-cmsmasters-audio-playlist__track-equalizer">' .
					'<div class="elementor-widget-cmsmasters-audio-playlist__track-equalizer-item"></div>' .
					'<div class="elementor-widget-cmsmasters-audio-playlist__track-equalizer-item"></div>' .
					'<div class="elementor-widget-cmsmasters-audio-playlist__track-equalizer-item"></div>' .
				'</div>' .
			'</div>';
		}

		if ( 'number' === $item_marker ) {
			echo '<span class="elementor-widget-cmsmasters-audio-playlist__track-number">' .
				'<span class="elementor-widget-cmsmasters-audio-playlist__track-number-text"></span>';

			$additional_symbol = $settings['audio_playlist_item_number_additional_symbol'];

			if ( '' !== $additional_symbol ) {
				echo '<span class="elementor-widget-cmsmasters-audio-playlist__track-number-additional-symbol">' .
					esc_html( $additional_symbol ) .
				'</span>';
			}

			echo '</span>';
		}
	}

	/**
	 * Render audio item output on the frontend.
	 *
	 * Written in PHP and used to generate the audio item HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_item( $item, $active ) {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $item['insert_url'] ) ) {
			$audio_url = $item['external_url']['url'];
		} else {
			$audio_url = $item['hosted_url']['url'];
		}

		if ( empty( $audio_url ) ) {
			return;
		}

		echo '<li class="' .
			'elementor-widget-cmsmasters-audio-playlist__playlist_item' .
			( 'yes' === $settings['audio_playlist_item_separator'] ? ' cmsmasters-playlist-item-separator' : '' ) .
			( $active ? ' cmsmasters-active-track' : '' ) .
		'">' .
			'<span class="elementor-widget-cmsmasters-audio-playlist__track" data-href="' . esc_url( $audio_url ) . '" tabindex="0">';

		$this->print_audio_marker();

		echo '<span class="elementor-widget-cmsmasters-audio-playlist__track-info">';

		if ( ! empty( $item['insert_url'] ) ) {
			$track_title = pathinfo( $item['external_url']['url'] )['filename'];
		} else {
			$track_title = $item['track_title'];
		}

		$this->print_track_name(
			$audio_url,
			'__track-title',
			$track_title,
			'audio_playlist_item_one_line',
			'__track-separator',
			'__track-subtitle',
			$item['track_subtitle']
		);

		echo '</span>' .
		'</span>' .
		'<span class="elementor-widget-cmsmasters-audio-playlist__track-podcast">';

			$this->print_audio_link( $item );

		echo '</span>' .
		'</li>';
	}

	/**
	 * Render audio playlist output on the frontend.
	 *
	 * Written in PHP and used to generate the audio playlist HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_playlist() {
		$settings = $this->get_settings_for_display();

		$list = $settings['audio_list'];

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__playlist ' . esc_attr( $settings['audio_playlist_type'] ) . '">' .
			'<div class="elementor-widget-cmsmasters-audio-playlist__playlist_inner">' .
				'<ul class="elementor-widget-cmsmasters-audio-playlist__playlist-list">';

		$active = true;

		foreach ( $list as $item ) {
			$this->print_audio_item( $item, $active );

			if ( $active ) {
				$active = false;
			}
		}

		echo '</ul>' .
			'</div>' .
		'</div>';
	}

	/**
	 * Render audio medium player output on the frontend.
	 *
	 * Written in PHP and used to generate the audio medium player HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_medium_player() {
		$settings = $this->get_settings_for_display();

		$poster = $settings['audio_poster']['id'];
		$poster_position = $settings['audio_poster_position'];

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__player_left">';

		if ( ! empty( $poster ) && 'with_title' !== $poster_position ) {
			$this->print_audio_poster();
		}

		if ( 'with_title' === $poster_position ) {
			$this->print_track_name_info();
		}

		echo '</div>' .
		'<div class="elementor-widget-cmsmasters-audio-playlist__player_right' . ( empty( $poster ) ? ' cmsmasters-empty-poster' : '' ) . '">';

		if ( 'with_title' !== $poster_position ) {
			$this->print_advanced();

			$this->print_track_name_info();
		}

		$this->print_progress_medium();

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__control_buttons_wrap">';

		$this->print_control_buttons_left();

		$this->print_control_buttons_right();

		echo '</div>' .
		'</div>';
	}

	/**
	 * Render audio small player output on the frontend.
	 *
	 * Written in PHP and used to generate the audio small player HTML.
	 *
	 * @since 1.0.0
	 */
	protected function print_audio_small_player() {
		$settings = $this->get_settings_for_display();

		$time_show = $settings['audio_progress_current_total_time_show'];

		$this->print_control_button( 'play fas fa-play', 'Play', false, '__play-icon', '' );

		if ( 'yes' === $time_show ) {
			$this->print_current_time();
		}

		$this->print_progress();

		if ( 'yes' === $time_show ) {
			$this->print_total_time();
		}

		if ( $settings['audio_control_button_volume'] ) {
			$this->print_volume();
		}

		if ( 'toggle' === $settings['audio_playlist_type'] && '1' < count( $settings['audio_list'] ) ) {
			$this->print_control_button( 'list', 'Show Playlist', true, '__list-icon', 'list_icon' );
		}
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

		$list = $settings['audio_list'];
		$speed = $settings['audio_advanced_speed'];
		$download = $settings['audio_advanced_download'];
		$advanced = '';

		if ( ! empty( $poster ) && 'with_title' === $settings['audio_poster_position'] ) {
			$this->print_audio_poster();

			$this->print_advanced();
		}

		if ( $speed || $download ) {
			$advanced = ' cmsmasters-enable-advanced';
		}

		if ( ! empty( $list[0]['insert_url'] ) ) {
			$audio_url = $list[0]['external_url']['url'];
		} else {
			$audio_url = $list[0]['hosted_url']['url'];
		}

		echo '<div class="elementor-widget-cmsmasters-audio-playlist__player_wrap">' .
			'<div class="elementor-widget-cmsmasters-audio-playlist__player-bg">' .
				'<div class="elementor-widget-cmsmasters-audio-playlist__player-bg-overlay"></div>' .
				'<audio class="elementor-widget-cmsmasters-audio-playlist__player-audio" preload="metadata" itemprop="audio" tabindex="-1" type="audio/mpeg">' .
					'<source class="elementor-widget-cmsmasters-audio-playlist__player-source" type="audio/mp3" autoplay="autoplay" src="' . esc_url( $audio_url ) . '">' .
				'</audio>' .
				'<div class="elementor-widget-cmsmasters-audio-playlist__player' . esc_attr( $advanced ) . '">';

		if ( 'medium' === $settings['audio_size'] ) {
			$this->print_audio_medium_player();
		} else {
			$this->print_audio_small_player();
		}

		echo '</div>';

		$this->print_audio_playlist();

		echo '</div>' .
		'</div>';
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
			'widget-cmsmasters-audio-playlist',
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
			array(
				'field' => 'audio_playlist_item_number_additional_symbol',
				'type' => esc_html__( 'Number Additional Symbol', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
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
			'audio_list' => array(
				'external_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'External Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				array(
					'field' => 'track_title',
					'type' => esc_html__( 'Track Title', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'track_subtitle',
					'type' => esc_html__( 'Track Subtitle', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				'audio_amazon_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Amazon Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_apple_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Apple Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_google_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Google Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_radiopublic_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'RadioPublic Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_rss_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'RSS Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_soundcloud_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'SoundCloud Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_spotify_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Spotify Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_tunein_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Tunein Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_custom_1_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Custom 1 Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_custom_2_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Custom 2 Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
				'audio_custom_3_url' => array(
					'field' => 'url',
					'type' => esc_html__( 'Custom 3 Link', 'cmsmasters-elementor' ),
					'editor_type' => 'LINK',
				),
			),
		);
	}
}
