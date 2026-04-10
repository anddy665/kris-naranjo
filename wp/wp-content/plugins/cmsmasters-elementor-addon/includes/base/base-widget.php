<?php
namespace CmsmastersElementor\Base;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Utils;
use CmsmastersElementor\Plugin as CmsmastersElementorPlugin;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon base widget class.
 *
 * An abstract class to register new Addon widgets.
 *
 * This class extends the `Elementor\Widget_Base` class to inherit
 * his properties and methods, and must be extended in order to
 * register new Addon widgets.
 *
 * @since 1.0.0
 */
abstract class Base_Widget extends Widget_Base {

	const WIDGET_NAME_PREFIX = 'cmsmasters-';

	/**
	 * Condition sets.
	 *
	 * Holds widget controls condition sets.
	 *
	 * @since 1.0.1
	 *
	 * @var array Widget controls condition sets.
	 */
	protected $condition_sets = array();

	/**
	 * Original settings.
	 *
	 * Holds the settings from db.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $original_settings = array();

	/**
	 * Base Widget constructor.
	 *
	 * Initializing the widget base class.
	 *
	 * @since 1.1.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->original_settings = Utils::get_if_isset( $data, 'settings', array() );

		parent::__construct( $data, $args );
	}

	/**
	 * Get widget name.
	 *
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public function get_name() {
		return self::WIDGET_NAME_PREFIX . self::get_widget_class_name();
	}

	/**
	 * LazyLoad widget use control.
	 *
	 * @since 1.11.1
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return false;
	}

	/**
	 * LazyLoad widget status by default.
	 *
	 * @since 1.11.1
	 *
	 * @return string 'enable' - for enable lazyload widget, '' - for disable lazyload widget.
	 */
	public function lazyload_widget_status_by_default() {
		return '';
	}

	/**
	 * Get widget class name.
	 *
	 * Converts the php class name to widget name.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name.
	 */
	public static function get_widget_class_name() {
		$reflection = new \ReflectionClass( get_called_class() );
		$widget_name = str_replace( '_', '-', $reflection->getShortName() );

		return strtolower( $widget_name );
	}

	/**
	 * Get widget name prefix.
	 *
	 * Retrieve the widget name prefix.
	 *
	 * @since 1.0.0
	 *
	 * @return string The widget name prefix.
	 */
	public function get_name_prefix() {
		return self::WIDGET_NAME_PREFIX;
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		$basic_keywords = array( 'cmsmasters' );

		$keywords = array_merge(
			$basic_keywords,
			$this->get_global_keywords(),
			$this->get_unique_keywords()
		);

		return array_unique( $keywords );
	}

	/**
	 * Get global widget keywords.
	 *
	 * Retrieve the list of global keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget global keywords.
	 */
	public function get_global_keywords() {
		return array();
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
	abstract public function get_unique_keywords();

	/**
	 * Get widget categories.
	 *
	 * Retrieve the widget categories.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( Base_Document::WIDGETS_CATEGORY );
	}

	/**
	 * Add render attributes.
	 *
	 * Used to add attributes to the current element wrapper HTML tag.
	 *
	 * @since 1.0.3
	 */
	protected function add_render_attributes() {
		parent::add_render_attributes();

		$widget_name = $this->get_name();

		/**
		 * After adding widget render attributes.
		 *
		 * The dynamic portion of the hook name, `$widget_name`, refers to the widget name.
		 *
		 * @since 1.0.0
		 *
		 * @param Base_Widget $this Widget base instance.
		 */
		do_action( "cmsmasters_elementor/widget/{$widget_name}/after_add_attributes", $this );
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve the Addon widget initial configuration help URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Addon widget help URL.
	 */
	public function get_custom_help_url() {
		return Utils::get_redirect_url( 'widget-' . $this->get_name() );
	}

	/**
	 * Initialize controls.
	 *
	 * Register the all controls added by `register_controls()`.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Added lazyload Widget controls
	 */
	protected function init_controls() {
		parent::init_controls();

		$this->lazyload_widget_register_controls();

		$widget_name = $this->get_name();

		/**
		 * After initialize controls.
		 *
		 * The dynamic portion of the hook name, `$widget_name`, refers to the widget name.
		 *
		 * @since 1.0.0
		 *
		 * @param Base_Widget $this Widget base instance.
		 */
		do_action( "cmsmasters_elementor/element/{$widget_name}/after_init_controls", $this );
	}

	/**
	 * LazyLoad widget register controls.
	 *
	 * @since 1.11.1
	 * @since 1.11.11 Added Icon Animation Type control for Lazyload Widget.
	 */
	public function lazyload_widget_register_controls() {
		if ( ! $this->lazyload_widget_use_control() ) {
			return;
		}

		$this->start_controls_section(
			'lazyload_widget_content_section',
			array(
				'label' => __( 'Widget Lazy Load', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'lazyload_widget_status',
			array(
				'label' => __( 'Enable Widget Lazy Load', 'cmsmasters-elementor' ),
				'description' => __( 'Delay the website widget loading until it comes into visitor\'s view to improve page load time.', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'enable',
				'default' => $this->lazyload_widget_status_by_default(),
				'render_type' => 'none',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'lazyload_widget_preloader_style_section',
			array(
				'label' => __( 'Widget Lazy Load Preloader', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'lazyload_widget_status' => 'enable',
				),
			)
		);

		$preloader_selector = '{{WRAPPER}} .cmsmasters-lazyload-widget-settings';

		$this->add_control(
			'lazyload_widget_preloader_type',
			array(
				'label' => __( 'Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'icon' => __( 'Icon', 'cmsmasters-elementor' ),
					'grid' => __( 'Grid', 'cmsmasters-elementor' ),
				),
				'default' => 'icon',
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_heading_control',
			array(
				'label' => __( 'Grid', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_count',
			array(
				'label' => __( 'Count', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 8,
				'min' => 1,
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_grid_columns',
			array(
				'label' => __( 'Columns', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 6,
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_columns', '{{VALUE}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_grid_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_height', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_grid_horizontal_gap',
			array(
				'label' => __( 'Horizontal Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_horizontal_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_grid_vertical_gap',
			array(
				'label' => __( 'Vertical Gap', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array(
					'px',
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_vertical_gap', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_grid_container_padding',
			array(
				'label' => __( 'Container Padding', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => array(
					'px',
					'%',
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_container_padding_top', '{{TOP}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'lazyload_widget_preloader_grid_container_padding_right', '{{RIGHT}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'lazyload_widget_preloader_grid_container_padding_bottom', '{{BOTTOM}}{{UNIT}}' ) .
					Utils::prepare_css_var( 'lazyload_widget_preloader_grid_container_padding_left', '{{LEFT}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::ICONS,
				'default' => array(
					'value' => 'fas fa-spinner',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_grid_icon_size',
			array(
				'label' => esc_html__( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_icon_size', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_icon_color',
			array(
				'label' => esc_html__( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_icon_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_icon_animation_type',
			array(
				'label' => __( 'Icon Animation Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'lazyLoadWidgetBlink' => __( 'Blink', 'cmsmasters-elementor' ),
					'lazyLoadWidgetSpinner' => __( 'Spin', 'cmsmasters-elementor' ),
				),
				'default' => 'lazyLoadWidgetSpinner',
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_icon_animation_type', '{{VALUE}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_icon_animation_speed',
			array(
				'label' => esc_html__( 'Icon Animation Speed', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'step' => 100,
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_icon_animation_speed', '{{VALUE}}ms' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
					'lazyload_widget_preloader_grid_icon_animation_type!' => 'none',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BACKGROUND_GROUP,
			array(
				'name' => 'lazyload_widget_preloader_grid_bg',
				'selector' => $preloader_selector,
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'lazyload_widget_preloader_grid_bd',
				'fields_options' => array(
					'width' => array(
						'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
					),
					'color' => array(
						'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => $preloader_selector,
				'separator' => 'before',
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_grid_bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
					'%' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_grid_bd_radius', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'lazyload_widget_preloader_grid',
				'selector' => $preloader_selector,
				'condition' => array(
					'lazyload_widget_preloader_type' => 'grid',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_icon_heading_control',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_icon_source',
			array(
				'label' => __( 'Icon Source', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'default' => __( 'Default', 'cmsmasters-elementor' ),
					'custom' => __( 'Custom', 'cmsmasters-elementor' ),
				),
				'default' => 'default',
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_icon',
			array(
				'label' => __( 'Icon', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
					'lazyload_widget_preloader_icon_source' => 'custom',
				),
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_icon_size',
			array(
				'label' => esc_html__( 'Icon Size', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_icon_size', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_icon_color',
			array(
				'label' => esc_html__( 'Icon Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_icon_color', '{{VALUE}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_icon_animation_type',
			array(
				'label' => __( 'Icon Animation Type', 'cmsmasters-elementor' ),
				'label_block' => false,
				'type' => Controls_Manager::SELECT,
				'options' => array(
					'' => __( 'Default', 'cmsmasters-elementor' ),
					'none' => __( 'None', 'cmsmasters-elementor' ),
					'lazyLoadWidgetBlink' => __( 'Blink', 'cmsmasters-elementor' ),
					'lazyLoadWidgetSpinner' => __( 'Spin', 'cmsmasters-elementor' ),
				),
				'default' => '',
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_icon_animation_type', '{{VALUE}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_icon_animation_speed',
			array(
				'label' => esc_html__( 'Icon Animation Speed', 'cmsmasters-elementor' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'step' => 100,
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_icon_animation_speed', '{{VALUE}}ms' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
					'lazyload_widget_preloader_icon_animation_type!' => 'none',
				),
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_container_divider_control',
			array(
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_container_heading_control',
			array(
				'label' => __( 'Container', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'lazyload_widget_preloader_height',
			array(
				'label' => __( 'Height', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw', 'vh' ),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
					'vw' => array(
						'min' => 0,
						'max' => 100,
					),
					'vh' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_height', '{{SIZE}}{{UNIT}}' ),
				),
				'condition' => array(
					'lazyload_widget_preloader_type' => 'icon',
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BACKGROUND_GROUP,
			array(
				'name' => 'lazyload_widget_preloader_bg',
				'selector' => $preloader_selector,
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BACKGROUND_GROUP,
			array(
				'name' => 'lazyload_widget_preloader_overlay_bg',
				'fields_options' => array(
					'background' => array(
						'label' => esc_html_x( 'Overlay Background Type', 'Background Control', 'cmsmasters-elementor' ),
					),
				),
				'selector' => $preloader_selector,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BORDER_GROUP,
			array(
				'name' => 'lazyload_widget_preloader_bd',
				'fields_options' => array(
					'width' => array(
						'label' => esc_html__( 'Border Width', 'cmsmasters-elementor' ),
					),
					'color' => array(
						'label' => esc_html__( 'Border Color', 'cmsmasters-elementor' ),
						'condition' => array(
							'border!' => array(
								'',
								'none',
							),
						),
					),
				),
				'selector' => $preloader_selector,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'lazyload_widget_preloader_bd_radius',
			array(
				'label' => esc_html__( 'Border Radius', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range' => array(
					'px' => array(
						'max' => 100,
						'min' => 0,
					),
					'%' => array(
						'max' => 50,
						'min' => 0,
					),
				),
				'selectors' => array(
					$preloader_selector => Utils::prepare_css_var( 'lazyload_widget_preloader_bd_radius', '{{SIZE}}{{UNIT}}' ),
				),
			)
		);

		$this->add_group_control(
			CmsmastersControls::VARS_BOX_SHADOW_GROUP,
			array(
				'name' => 'lazyload_widget_preloader',
				'selector' => $preloader_selector,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Check control default.
	 *
	 * Check if settings control has default value.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param string $control_id Control ID.
	 *
	 * @return bool
	 */
	public function is_setting_as_default( $control_id ) {
		$control = $this->get_controls( $control_id );

		if ( ! isset( $control['default'] ) ) {
			return false;
		}

		return $this->get_settings_for_display( $control_id ) === $control['default'];
	}

	/**
	 * Get render tag.
	 *
	 * Used to retrieve the rendered html tag with attributes and content.
	 *
	 * @since 1.0.0
	 * @since 1.3.1 Fixed for PHP 8.
	 *
	 * @param string $tag Html tag name.
	 * @param string $element The element.
	 * @param string $content Rendered tag content.
	 *
	 * @return string Rendered html tag with attribute string and
	 * content, or without it if not set.
	 */
	public function get_render_tag( $tag = 'div', $element = '', $content = false ) {
		$attributes = $this->get_render_attribute_string( $element );

		if ( ! empty( $attributes ) ) {
			$attributes = " {$attributes}";
		}

		$output = "<{$tag}{$attributes}>";

		if ( ! is_bool( $content ) ) {
			$output .= "{$content}</{$tag}>";
		}

		return $output;
	}

	/**
	 * Print render tag.
	 *
	 * Print the rendered html tag with attributes and content.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Html tag name.
	 * @param string $element The element.
	 * @param string $content Rendered tag content.
	 */
	public function print_render_tag( $tag = 'div', $element = '', $content = false ) {
		echo $this->get_render_tag( $tag, $element, $content );
	}

	/**
	 * Get render close tag.
	 *
	 * Used to retrieve the html close tag with content before.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Html tag name.
	 * @param string $content Rendered tag content.
	 *
	 * @return string Rendered html close tag with content before
	 * or without it if not set.
	 */
	public function get_render_close_tag( $tag = 'div', $content = false ) {
		$output = '';

		if ( $content ) {
			$output .= $content;
		}

		$output .= "</{$tag}>";

		return $output;
	}

	/**
	 * Print render close tag.
	 *
	 * Print the rendered html close tag with content before.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Html tag name.
	 * @param string $content Rendered tag content.
	 */
	public function print_render_close_tag( $tag = 'div', $content = false ) {
		echo $this->get_render_close_tag( $tag, $content );
	}

	/**
	 * Update control in stack.
	 *
	 * Change the value of an existing control in the stack. When you add new
	 * control you set the `$args` parameter, this method allows you to update
	 * the arguments by passing new data.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param string $control_id Control ID.
	 * @param array  $args Control arguments. Only the new fields you want to update.
	 * @param array  $options Optional. Some additional options. Default is an empty array.
	 *
	 * @return bool
	 */
	public function update_section( $control_id, $args, array $options = array() ) {
		$is_updated = Plugin::$instance->controls_manager->update_control_in_stack( $this, $control_id, $args, $options );

		if ( ! $is_updated ) {
			return false;
		}

		$control = $this->get_controls( $control_id );

		if ( Controls_Manager::SECTION === $control['type'] ) {
			$section_args = $this->get_section_args( $control_id );
			$section_controls = $this->get_section_controls( $control_id );

			foreach ( $section_controls as $section_control_id => $section_control ) {
				$control_condition = Utils::get_if_isset( $section_control, 'condition', array() );
				$section_condition = Utils::get_if_isset( $section_args, 'condition', array() );
				$control_args_new = $section_args;

				if ( is_array( $section_condition ) && is_array( $control_condition ) ) {
					$control_args_new['condition'] = array_merge( $control_condition, $section_condition );
				}

				if ( Utils::get_if_isset( $section_control, 'responsive' ) ) {
					$this->update_responsive_control( $section_control_id, $control_args_new );
				} else {
					$this->update_control( $section_control_id, $control_args_new, $options );
				}
			}
		}

		return true;
	}

	/**
	 * Get placeholder text.
	 *
	 * Utility method that receives an `placeholder` from control arguments.
	 *
	 * @since 1.0.3
	 *
	 * @param string $control_id Control ID.
	 *
	 * @return string
	 */
	public function get_settings_fallback( $control_id ) {
		$text = '';

		if ( $this->is_control_visible( $control_id ) ) {
			$text = $this->get_settings( $control_id );

			if ( ! $text ) {
				$control = $this->get_controls( $control_id );

				if ( $control && ! empty( $control['placeholder'] ) ) {
					$text = $control['placeholder'];
				}
			}
		}

		return $text;
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array();
	}

	/**
	 * Get fields_in_item config for WPML.
	 *
	 * @since 1.3.3
	 *
	 * @return array Fields in item config.
	 */
	public static function get_wpml_fields_in_item() {
		return array();
	}

	/**
	 * Determine the render logic.
	 *
	 * @since 1.11.1
	 * @since 1.11.8 Fixed depended scripts in templates on lazyload widget.
	 */
	protected function render_by_mode() {
		if ( 'enable' === $this->lazyload_widget_get_status() ) {
			$settings = $this->get_settings_for_display();

			$this->add_render_attribute(
				'lazyload_widget_settings',
				array(
					'class' => 'cmsmasters-lazyload-widget-settings',
					'data-settings' => wp_json_encode( $this->get_parsed_dynamic_settings() ),
				)
			);

			$template_ids = $this->get_template_ids();

			if ( ! empty( $template_ids ) ) {
				CmsmastersElementorPlugin::instance()->frontend->lazyload_widget_enqueue_template_assets( $template_ids, $this->get_id() );
			}

			echo '<div ' . $this->get_render_attribute_string( 'lazyload_widget_settings' ) . '>';

				if ( 'grid' === $settings['lazyload_widget_preloader_type'] ) {
					$preloader_grid_icon = $settings['lazyload_widget_preloader_grid_icon'];

					echo '<div class="cmsmasters-lazyload-widget-preloader__grid">';

						for ( $i = 0; $i < $settings['lazyload_widget_preloader_grid_count']; $i++ ) {
							echo '<div class="cmsmasters-lazyload-widget-preloader__grid-item">';

								if ( ! empty( $preloader_grid_icon['value'] ) ) {
									echo '<span class="cmsmasters-lazyload-widget-preloader__grid-icon">' .
										Utils::get_render_icon( $preloader_grid_icon ) .
									'</span>';
								}

							echo '</div>';
						}

					echo '</div>';
				} else {
					echo '<div class="cmsmasters-lazyload-widget-preloader__icon">';

						$preloader_icon = Utils::get_kit_option( 'cmsmasters_lazyload_widget_preloader_icon', array(
							'value' => 'fas fa-spinner',
							'library' => 'fa-solid',
						) );

						if ( 'custom' === $settings['lazyload_widget_preloader_icon_source'] ) {
							$preloader_icon = $settings['lazyload_widget_preloader_icon'];
						}

						if ( ! empty( $preloader_icon['value'] ) ) {
							echo '<span class="cmsmasters-lazyload-widget-preloader__icon-icon">' .
								Utils::get_render_icon( $preloader_icon ) .
							'</span>';
						}

					echo '</div>';
				}

			echo '</div>';

			return;
		}

		parent::render_by_mode();
	}

	/**
	 * Get template IDs.
	 *
	 * @since 1.11.1
	 *
	 * @return array Template IDs.
	 */
	public function get_template_ids() {
		return array();
	}

	/**
	 * LazyLoad widget get status.
	 *
	 * @since 1.11.1
	 *
	 * @return string 'enable' - for enable lazyload widget, '' - for disable lazyload widget.
	 */
	protected function lazyload_widget_get_status() {
		if ( Utils::is_edit_mode() ) {
			return '';
		}

		if (
			$this->lazyload_widget_use_control() &&
			null !== $this->get_settings( 'lazyload_widget_status' )
		) {
			return $this->get_settings( 'lazyload_widget_status' );
		}

		return $this->lazyload_widget_status_by_default();
	}

}
