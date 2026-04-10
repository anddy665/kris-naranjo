<?php
namespace CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events;

use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\Blog\Classes\Pagination;
use CmsmastersElementor\Modules\Blog\Module as BlogModule;
use CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events\Base_Events;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon events widget class.
 *
 * An abstract class to register new tribe events widgets.
 *
 * @since 1.13.0
 */
abstract class Base_Events_Elements extends Base_Events {

	/**
	 * Pagination instance.
	 *
	 * @since 1.13.0
	 *
	 * @var Pagination
	 */
	protected $pagination;

	/**
	 * Whether tribe events pagination needed.
	 *
	 * @since 1.13.0
	 *
	 * @var bool
	 */
	protected $has_pagination = true;

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
		return parent::get_unique_keywords() + array(
			'template',
			'custom',
		);
	}

	/**
	 * @since 1.13.0
	 */
	public function get_script_depends() {
		return array_merge( array(
			'perfect-scrollbar-js',
			'imagesloaded',
		), parent::get_script_depends() );
	}

	/**
	 * Tribe Events Widget constructor.
	 *
	 * Initializing the widget tribe events class.
	 *
	 * @since 1.13.0
	 *
	 * @param array $data Widget data.
	 * @param array|null $args Widget default arguments.
	 */
	public function __construct( $data = array(), $args = null ) {
		if ( $this->has_pagination ) {
			$this->pagination = new Pagination( $this, static::QUERY_CONTROL_PREFIX );
		}

		parent::__construct( $data, $args );
	}

	/**
	 * @since 1.13.0
	 */
	public function register_controls() {
		$this->register_template_section_controls();

		$this->register_style_section_controls();

		parent::register_controls();

		if ( $this->has_pagination ) {
			$this->pagination->register_controls_content();
		}
	}

	/**
	 * Register tribe events controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function register_template_section_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => __( 'Layout', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register tribe events controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.13.0
	 */
	protected function register_style_section_controls() {
		if ( $this->has_pagination ) {
			$this->pagination->register_controls_style();
		}
	}

	/**
	 * Render widget on ajax.
	 *
	 * @since 1.13.0
	 */
	public function render_ajax( $ajax_vars ) {
		$query_vars = isset( $ajax_vars['query_vars'] ) ? BlogModule::get_allowed_query_vars( $ajax_vars['query_vars'] ) : array();

		if ( ! empty( $query_vars ) ) {
			$this->set_query_vars( $query_vars );
		}

		$this->init_query();

		if ( ! $this->get_query()->found_posts ) {
			wp_die( 0, '', 404 );
		}

		$this->render_events();

		$this->render_pagination();
	}

	/**
	 * Render pagination.
	 *
	 * @since 1.13.0
	 */
	protected function render_pagination() {
		if ( ! $this->has_pagination ) {
			return;
		}

		$this->pagination->set_wp_query( $this->get_query() );
		$this->pagination->render();
	}

	/**
	 * @since 1.13.0
	 */
	protected function render_wrapper() {
		echo '<div class="cmsmasters-tribe-events__events-variable">';

		$this->render_events();

		$this->render_pagination();

		echo '</div>';
	}

	/**
	 * Get query vars.
	 *
	 * @since 1.13.0
	 *
	 * @return array
	 */
	public function get_query_vars() {
		$query_vars = parent::get_query_vars();

		if ( $this->has_pagination && ! AjaxWidgetModule::is_active_ajax() ) {
			$query_vars['paged'] = $this->pagination->get_paged();
		}

		return $query_vars;
	}
}
