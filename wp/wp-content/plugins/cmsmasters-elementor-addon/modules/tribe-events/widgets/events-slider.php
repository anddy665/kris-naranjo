<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets;

use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Widget;
use CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events\Base_Events_Customizable;
use CmsmastersElementor\Modules\Slider\Classes\Slider;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon events slider widget.
 *
 * Addon widget that displays events slider.
 *
 * @since 1.13.0
 */
class Events_Slider extends Base_Events_Customizable {

	use Tribe_Events_Widget;

	/**
	 * @since 1.13.0
	 */
	protected $has_pagination = false;

	/**
	 * Slider instance.
	 *
	 * @since 1.13.0
	 *
	 * @var Slider
	 */
	protected $slider;

	/**
	 * @since 1.13.0
	 */
	public function get_title() {
		return __( 'Events Slider', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.13.0
	 */
	public function get_icon() {
		return 'cmsicon-events-slider';
	}

	/**
	 * Get widget unique keywords.
	 *
	 * Retrieve the list of unique keywords the widget belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Widget unique keywords.
	 */
	public function get_unique_keywords() {
		return array_unique(
			array_merge(
				parent::get_unique_keywords(),
				array(
					'carousel',
					'slider',
				)
			)
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
	 * @since 1.15.3 Added dependency of connecting swiper styles for widgets with swiper slider after elementor 3.26 version.
	 * @since 1.16.0 Fixed style dependencies.
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return array_merge( array(
			'e-swiper',
		), parent::get_style_depends() );
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
	 * LazyLoad widget use control.
	 *
	 * @since 1.11.1
	 *
	 * @return bool true - with control, false - without control.
	 */
	public function lazyload_widget_use_control() {
		return true;
	}

	/**
	 *
	 * Initializing the Addon `events slider` widget class.
	 *
	 * @since 1.13.0
	 *
	 * @throws \Exception If arguments are missing when initializing a
	 * full widget instance.
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		$this->slider = new Slider( $this );

		parent::__construct( $data, $args );
	}

	/**
	 * @since 1.13.0
	 */
	public function register_controls() {
		parent::register_controls();

		$this->register_control_layout();

		$this->injection_section_event();
		$this->slider->register_section_content();
		$this->slider->register_section_style_arrows();
		$this->slider->register_section_style_bullets();
		$this->slider->register_section_style_fraction();
		$this->slider->register_section_style_progressbar();
		$this->slider->register_section_style_scrollbar();
	}

	/**
	 * Register tribe events slider layout controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	public function injection_section_event() {
		$this->start_injection( array(
			'of' => 'event_section_style',
			'at' => 'before',
		) );

		$this->slider->register_section_style_style_layout();

		$this->end_injection();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function register_control_layout() {
		$this->start_injection( array( 'of' => TribeEventsModule::CONTROL_TEMPLATE_NAME ) );

		$this->add_control(
			'posts_per_page',
			array(
				'label' => __( 'Events', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
				'min' => 1,
			)
		);

		$this->end_injection();

		$this->start_injection(
			array(
				'at' => 'after',
				'of' => 'posts_per_page',
			)
		);

		$this->slider->register_controls_content_per_view();

		$this->end_injection();
	}

	/**
	 * @since 1.13.0
	 */
	public function render_event_open() {
		$this->slider->render_slide_open();

		parent::render_event_open();
	}

	/**
	 * @since 1.13.0
	 */
	public function render_event_close() {
		parent::render_event_close();

		$this->slider->render_slide_close();
	}

	/**
	 * @since 1.13.0
	 */
	protected function render_events_inner() {
		$this->slider->render( function () {
			parent::render_events_inner();
		} );
	}

	/**
	 * Get fields config for WPML.
	 *
	 * @since 1.13.0
	 *
	 * @return array Fields config.
	 */
	public static function get_wpml_fields() {
		return array(
			array(
				'field' => 'read_more_text',
				'type' => esc_html__( 'Read More Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'slider_arrow_text_prev',
				'type' => esc_html__( 'Slider Arrow Prev Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'slider_arrow_text_next',
				'type' => esc_html__( 'Slider Arrow Next Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
