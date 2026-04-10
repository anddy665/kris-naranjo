<?php
namespace CmsmastersElementor\Modules\Blog\Widgets;

use CmsmastersElementor\Modules\Blog\Widgets\Base_Blog\Base_Blog_Customizable;
use CmsmastersElementor\Modules\Slider\Classes\Slider;

use Elementor\Controls_Manager;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon blog slider widget.
 *
 * Addon widget that displays blog slider.
 *
 * @since 1.0.0
 */
class Blog_Slider extends Base_Blog_Customizable {

	/**
	 * @since 1.0.0
	 */
	protected $has_header = false;

	/**
	 * @since 1.0.0
	 */
	protected $has_pagination = false;

	/**
	 * Slider instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Slider
	 */
	protected $slider;

	/**
	 * @since 1.0.0
	 */
	public function get_title() {
		return __( 'Posts Slider', 'cmsmasters-elementor' );
	}

	/**
	 * @since 1.0.0
	 */
	public function get_icon() {
		return 'cmsicon-posts-slider';
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
	 *
	 * Initializing the Addon `blog slider` widget class.
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
		$this->slider = new Slider( $this );

		parent::__construct( $data, $args );
	}

	/**
	 * @since 1.0.0
	 */
	public function register_controls() {
		parent::register_controls();

		$this->register_control_layout();

		$this->slider->register_section_content();
		$this->slider->register_sections_style();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_control_layout() {
		$this->start_injection(
			array(
				'of' => 'alignment',
				'at' => 'before',
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label' => __( 'Posts', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 6,
				'min' => 1,
				'separator' => 'before',
			)
		);

		$this->end_injection();

		$this->start_injection(
			array(
				'of' => 'alignment',
				'at' => 'after',
			)
		);

		$this->slider->register_controls_content_per_view();

		$this->end_injection();
	}

	/**
	 * @since 1.0.0
	 */
	public function render_post_open() {
		$this->slider->render_slide_open();

		parent::render_post_open();
	}

	/**
	 * @since 1.0.0
	 */
	public function render_post_close() {
		parent::render_post_close();

		$this->slider->render_slide_close();
	}

	/**
	 * @since 1.0.0
	 */
	protected function render_posts_inner() {
		$this->slider->render( function () {
			parent::render_posts_inner();
		} );
	}

	/**
	 * Render post markers.
	 *
	 * @since 1.4.0
	 */
	public function marker_post() {
		return '';
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
				'field' => 'read_more_text',
				'type' => esc_html__( 'Read More Text', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_top_author_prefix',
				'type' => esc_html__( 'Author Prefix', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_top_date_date_format_custom',
				'type' => esc_html__( 'Date Format', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_bottom_author_prefix',
				'type' => esc_html__( 'Author Prefix', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'meta_data_bottom_date_date_format_custom',
				'type' => esc_html__( 'Date Format', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'blog_filter_id',
				'type' => esc_html__( 'Filter ID', 'cmsmasters-elementor' ),
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
			array(
				'field' => 'separator_meta_data_top_content',
				'type' => esc_html__( 'Top Meta Data Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_taxonomy_meta_data_top_content',
				'type' => esc_html__( 'Top Taxonomy Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_meta_data_bottom_content',
				'type' => esc_html__( 'Bottom Meta Data Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_taxonomy_meta_data_bottom_content',
				'type' => esc_html__( 'Bottom Taxonomy Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
			array(
				'field' => 'separator_filter_content',
				'type' => esc_html__( 'Separator Content', 'cmsmasters-elementor' ),
				'editor_type' => 'LINE',
			),
		);
	}
}
