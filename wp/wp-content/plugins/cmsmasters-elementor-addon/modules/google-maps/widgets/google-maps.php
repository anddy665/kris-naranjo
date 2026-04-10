<?php
namespace CmsmastersElementor\Modules\GoogleMaps\Widgets;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Plugin;
use Elementor\Repeater;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


class Google_Maps extends Base_Widget {

	protected $google_api_key = '';

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
		return __( 'Google Maps', 'cmsmasters-elementor' );
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
		return 'cmsicon-google-maps';
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
			'google',
			'map',
			'embed',
			'location',
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
	 *
	 * @return array Widget script dependencies.
	 */
	public function get_script_depends() {
		return array_merge( array( 'google-maps-api' ), parent::get_script_depends() );
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
			'widget-cmsmasters-google-maps',
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

		$this->add_script_depends( 'google-maps' );
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
	 */
	protected function register_controls() {
		if ( empty( $this->google_api_key ) ) {
			$this->print_warning_section();

			return;
		}

		$this->start_controls_section(
			'section_map',
			array(
				'label' => __( 'Map', 'cmsmasters-elementor' ),
			)
		);

		$default_address = __( 'London Eye, London, United Kingdom', 'cmsmasters-elementor' );

		$this->add_control(
			'address_type_global',
			array(
				'label' => __( 'Address Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'address-g' => __( 'Address', 'cmsmasters-elementor' ),
					'coordinates-g' => __( 'Coordinates', 'cmsmasters-elementor' ),
				),
				'default' => 'address-g',
				'toggle' => false,
				'separator' => 'before',
				'render_type' => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'address',
			array(
				'label' => __( 'Location', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => $default_address,
				'default' => $default_address,
				'label_block' => true,
				'frontend_available' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array( TagsModule::POST_META_CATEGORY ),
				),
				'condition' => array(
					'address_type_global' => 'address-g',
				),
			)
		);

		$this->add_control(
			'coordinates_lat_global',
			array(
				'label' => __( 'Latitude', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '51.503324',
				'default' => '51.503324',
				'label_block' => true,
				'frontend_available' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition' => array(
					'address_type_global' => 'coordinates-g',
				),
			)
		);

		$this->add_control(
			'coordinates_lng_global',
			array(
				'label' => __( 'Longitude', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '-0.1195430000000215',
				'default' => '-0.1195430000000215',
				'label_block' => true,
				'frontend_available' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition' => array(
					'address_type_global' => 'coordinates-g',
				),
			)
		);

		$this->add_control(
			'map_type',
			array(
				'label' => __( 'Map Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'roadmap' => 'Roadmap',
					'terrain' => 'Terrain',
					'hybrid' => 'Hybrid',
					'satellite' => 'Satellite',
				),
				'default' => 'roadmap',
				'toggle' => false,
				'label_block' => true,
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'zoom',
			array(
				'label' => __( 'Zoom', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 15,
				),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20,
					),
				),
				'separator' => 'before',
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
						'min' => 450,
						'max' => 1440,
					),
					'%' => array(
						'min' => 20,
						'max' => 100,
					),
					'vh' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default' => array( 'unit' => 'px' ),
				'size_units' => array(
					'px',
					'%',
					'vh',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-google-maps__wrapper' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'address_type',
			array(
				'label' => __( 'Address Type', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'address' => __( 'Address', 'cmsmasters-elementor' ),
					'coordinates' => __( 'Coordinates', 'cmsmasters-elementor' ),
				),
				'default' => 'address',
				'toggle' => false,
				'label_block' => false,
				'render_type' => 'template',
			)
		);

		$repeater->add_control(
			'address_mark',
			array(
				'label' => __( 'Address', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => $default_address,
				'default' => $default_address,
				'label_block' => true,
				'frontend_available' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition' => array(
					'address_type' => array(
						'address',
					),
				),
			)
		);

		$repeater->add_control(
			'coordinates_mark_lat',
			array(
				'label' => __( 'Latitude', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '51.503324',
				'default' => '51.503324',
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition' => array(
					'address_type' => array(
						'coordinates',
					),
				),
			)
		);

		$repeater->add_control(
			'coordinates_mark_lng',
			array(
				'label' => __( 'Longitude', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => '-0.1195430000000215',
				'default' => '-0.1195430000000215',
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition' => array(
					'address_type' => array(
						'coordinates',
					),
				),
			)
		);

		$repeater->add_control(
			'animation_marker',
			array(
				'label' => __( 'Marker Animation', 'cmsmasters-elementor' ),
				'type' => CmsmastersControls::CHOOSE_TEXT,
				'options' => array(
					'bounce' => 'Bounce',
					'drop' => 'Drop',
					'none' => 'None',
				),
				'default' => 'none',
				'toggle' => false,
				'label_block' => false,
				'separator' => 'before',
				'render_type' => 'template',
			)
		);

		$repeater->add_control(
			'mark_icon',
			array(
				'label' => __( 'Custom Marker Image', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::MEDIA,
				'separator' => 'before',
				'frontend_available' => true,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'mark_desc',
			array(
				'label' => __( 'Description', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'description' => __( 'Click on the marker to display a description', 'cmsmasters-elementor' ),
				'label_block' => true,
				'separator' => 'before',
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'desc_show',
			array(
				'label' => __( 'Show Description Immediately', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'frontend_available' => true,
			)
		);

		$repeater->add_control(
			'mark_title',
			array(
				'label' => __( 'Marker Hint', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Marker Hint', 'cmsmasters-elementor' ),
				'description' => __( 'Hover on the marker to display a hint', 'cmsmasters-elementor' ),
				'default' => '',
				'label_block' => true,
				'dynamic' => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
			)
		);

		$repeater->end_controls_tabs();

		$this->add_control(
			'markers',
			array(
				'label' => __( 'Markers', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'prevent_empty' => false,
				'default' => array(
					array( 'address_mark' ),
				),
				'title_field' => '<span style="text-transform: capitalize;"> <# if ( address_type === \'address\' ) { #> {{{ address_mark }}} <# } else { #> {{{ coordinates_mark_lat }}}, {{{ coordinates_mark_lng }}} <# } #> </span>',
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'zoom_control',
			array(
				'label' => __( 'Zoom Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'map_type_control',
			array(
				'label' => __( 'Map Type Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'street_view_control',
			array(
				'label' => __( 'Street View Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'fullscreen_control',
			array(
				'label' => __( 'Fullscreen Control', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'gesture_handling',
			array(
				'label' => __( 'Scroll Wheel Zoom', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'view',
			array(
				'label' => __( 'View', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_map_style',
			array(
				'label' => __( 'Map Style', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'map_style' );

		foreach ( array(
			'normal' => __( 'Normal', 'cmsmasters-elementor' ),
			'hover' => __( 'Hover', 'cmsmasters-elementor' ),
		) as $key => $label ) {

			if ( 'normal' === $key ) {
				$selector = '{{WRAPPER}} .elementor-widget-cmsmasters-google-maps__wrapper';
			} else {
				$selector = '{{WRAPPER}}:hover .elementor-widget-cmsmasters-google-maps__wrapper';
			}

			$this->start_controls_tab(
				'google_map_' . $key,
				array(
					'label' => $label,
				)
			);

			$this->add_group_control(
				Group_Control_Css_Filter::get_type(),
				array(
					'name' => 'css_filters_' . $key,
					'selector' => $selector,
				)
			);

			if ( 'hover' === $key ) {
				$this->add_control(
					'hover_transition',
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
							'{{WRAPPER}} .elementor-widget-cmsmasters-google-maps__wrapper' => 'transition-duration: {{SIZE}}s',
						),
					)
				);
			}

			$this->end_controls_tab();
		}

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'border_radius',
			array(
				'label' => __( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					'{{WRAPPER}} .elementor-widget-cmsmasters-google-maps__wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'custom_styling',
			array(
				'label' => __( 'JSON Maps Style', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'description' => 'Get your custom styling from <a href="//snazzymaps.com/" target="_blank">here</a>',
				'label_block' => true,
				'separator' => 'before',
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	protected function print_warning_section() {
		$this->start_controls_section(
			'section_warning',
			array(
				'label' => __( 'Google Map', 'cmsmasters-elementor' ),
			)
		);

		$this->add_control(
			'warning',
			array(
				'raw' => '<strong>' . __( 'Google Map: ', 'cmsmasters-elementor' ) . '</strong>' . __( 'Add Google Map api key. ', 'cmsmasters-elementor' ) . '<a href="' . esc_url( admin_url( 'admin.php?page=cmsmasters-addon-settings' ) ) . '" target="_blank">' . __( 'Go to the api settings page', 'cmsmasters-elementor' ) . '</a>',
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render google maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 */
	protected function render() {
		if ( ! $this->google_api_key ) {
			return;
		}

		echo '<div class="elementor-widget-cmsmasters-google-maps__wrapper"></div>';
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
				'field' => 'address',
				'type' => esc_html__( 'Address', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'coordinates_lat_global',
				'type' => esc_html__( 'Latitude', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'coordinates_lng_global',
				'type' => esc_html__( 'Longitude', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'custom_styling',
				'type' => esc_html__( 'JSON Maps Style', 'cmsmasters-elementor' ),
				'editor_type' => 'AREA',
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
			'markers' => array(
				array(
					'field' => 'address_mark',
					'type' => esc_html__( 'Address Mark', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'coordinates_mark_lat',
					'type' => esc_html__( 'Latitude', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'coordinates_mark_lng',
					'type' => esc_html__( 'Longitude', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
				array(
					'field' => 'mark_desc',
					'type' => esc_html__( 'Description', 'cmsmasters-elementor' ),
					'editor_type' => 'VISUAL',
				),
				array(
					'field' => 'mark_title',
					'type' => esc_html__( 'Marker Hint', 'cmsmasters-elementor' ),
					'editor_type' => 'LINE',
				),
			),
		);
	}
}
