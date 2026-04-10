<?php
namespace CmsmastersElementor\Modules\Blog\Widgets\Base_Blog;

use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Module as WordpressModule;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon blog widget class.
 *
 * An abstract class to register new Blog widgets.
 *
 * @since 1.0.0
 */
abstract class Base_Blog extends Base_Widget {

	const QUERY_CONTROL_PREFIX = 'blog';

	/**
	 * Query variables for setting up the WordPress query loop.
	 *
	 * @var array
	 */
	private $query_vars = array();

	/**
	 * The WordPress query instance.
	 *
	* @var \WP_Query
	*/
	private $query;

	/**
	 * Get group name.
	 *
	 * @since 1.6.5
	 *
	 * @return string Group name.
	 */
	public function get_group_name() {
		return 'cmsmasters-blog';
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
		$style_depends = array(
			'widget-cmsmasters-blog',
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
			'blog',
			'posts',
			'query',
			'loop',
			'cpt',
			'custom post type',
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function register_controls() {
		$this->register_query_section_controls();
	}

	/**
	 * @since 1.0.0
	 */
	protected function init_controls() {
		parent::init_controls();

		$this->register_advanced_section_controls();
		$this->register_advanced_style_section_controls();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 * @since 1.11.1 Add lazyload widget functionality.
	 */
	protected function register_query_section_controls() {
		$this->start_controls_section(
			'section_query',
			array(
				'label' => __( 'Query', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_group_control(
			CmsmastersControls::QUERY_RELATED_GROUP,
			array(
				'name' => static::QUERY_CONTROL_PREFIX,
				'presets' => array( 'full' ),
				'exclude' => array( 'posts_per_page' ), // use this setting from Layout section
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_advanced_section_controls() {
		$this->start_controls_section(
			'section_advanced',
			array(
				'label' => __( 'Advanced', 'cmsmasters-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					self::QUERY_CONTROL_PREFIX . '_post_type' => 'current_query',
				),
			)
		);

		$this->add_control(
			'nothing_found_message',
			array(
				'label' => __( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'It seems we can\'t find what you\'re looking for.', 'cmsmasters-elementor' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register pagination controls.
	 *
	 * Adds different input fields to allow the user to change and customize the classes settings.
	 *
	 * @since 1.0.0
	 */
	protected function register_advanced_style_section_controls() {
		$this->start_controls_section(
			'section_nothing_found_style',
			array(
				'tab' => Controls_Manager::TAB_STYLE,
				'label' => __( 'Nothing Found Message', 'cmsmasters-elementor' ),
				'condition' => array(
					'nothing_found_message!' => '',
					self::QUERY_CONTROL_PREFIX . '_post_type' => 'current_query',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'nothing_found_typography',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__nothing-found',
			)
		);

		$this->add_control(
			'nothing_found_color',
			array(
				'label' => __( 'Color', 'cmsmasters-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .cmsmasters-blog__nothing-found' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name' => 'nothing_found_text_shadow',
				'selector' => '{{WRAPPER}} .cmsmasters-blog__nothing-found',
			)
		);

		$this->add_responsive_control(
			'nothing_found_text_shadow_text_align',
			array(
				'label' => __( 'Alignment', 'cmsmasters-elementor' ),
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
					'{{WRAPPER}} .cmsmasters-blog__nothing-found' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Prepare the WordPress Query.
	 *
	 * @since 1.0.0
	 */
	public function init_query() {
		/** @var WordpressModule $wordpress_module */
		$wordpress_module = WordpressModule::instance();

		$this->query = $wordpress_module->get_query_manager()->get_query(
			$this,
			static::QUERY_CONTROL_PREFIX, /* 'posts' */
			$this->get_query_vars()
		);
	}

	/**
	 * Get query variables for setting up the WordPress query loop.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_query_vars() {
		$posts_per_page = $this->get_posts_per_page();
		$query_vars = array();

		if ( $posts_per_page ) {
			$query_vars[ static::QUERY_CONTROL_PREFIX . '_posts_per_page' ] = $posts_per_page;
		}

		return array_merge( $this->query_vars, $query_vars );
	}

	/**
	 * Check if current query is archive.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_current_query() {
		return 'current_query' === $this->get_settings_for_display( self::QUERY_CONTROL_PREFIX . '_post_type' );
	}

	/**
	 * Get number of posts per page.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	protected function get_posts_per_page() {
		return (int) $this->get_settings_for_display( 'posts_per_page' );
	}

	/**
	 * Get blog css classes.
	 *
	 * @since 1.0.0
	 *
	 * @return string[]
	 */
	protected function get_blog_classes() {
		return array( 'cmsmasters-blog', $this->get_name() );
	}

	/**
	 * Render blog.
	 *
	 * @since 1.0.0
	 */
	abstract protected function render_blog();

	/**
	 * Get the WordPress query.
	 *
	 * @since 1.0.0
	 *
	 * @return \WP_Query
	 */
	public function get_query() {
		return $this->query;
	}

	/**
	 * Prepares for post display.
	 *
	 * @since 1.0.0
	 */
	public function prepare_the_post() {
		$query = $this->get_query();

		$query->the_post();

		if ( 'publish' !== get_post_status() ) {
			return;
		}

		$this->add_render_attribute(
			'post',
			array(
				'id' => 'post-' . get_the_ID(),
				'class' => get_post_class( 'cmsmasters-blog__post' ),
			),
			null,
			true
		);
	}

	/**
	 * Render the post.
	 *
	 * @since 1.0.0
	 */
	protected function render_post() {
		if ( 'publish' !== get_post_status() ) {
			return;
		}

		$this->render_post_open();

		$this->render_post_inner();

		$this->render_post_close();
	}

	/**
	 * Render post markers.
	 *
	 * @since 1.4.0
	 */
	abstract public function marker_post();

	/**
	 * Start post rendering.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 Added post marker.
	 */
	public function render_post_open() {
		echo '<article ' . $this->get_render_attribute_string( 'post' ) . '>';
			$this->marker_post();
			echo '<div class="cmsmasters-blog__post-inner">';
	}

	/**
	 * Render all post insides.
	 *
	 * @since 1.0.0
	 */
	abstract protected function render_post_inner();

	/**
	 * End post rendering.
	 *
	 * @since 1.0.0
	 */
	public function render_post_close() {
		echo '</div>' .
		'</article>';
	}

	/**
	 * Wrapper for posts.
	 *
	 * @since 1.0.0
	 */
	protected function render_posts() {
		echo '<div class="cmsmasters-blog__posts-wrap">' .
		'<div class="cmsmasters-blog__posts">';

		$this->render_posts_inner();

		echo '</div>' .
		'</div>';
	}

	/**
	 * Wrapper insides for posts.
	 *
	 * @since 1.0.0
	 */
	protected function render_posts_inner() {
		while ( $this->get_query()->have_posts() ) {
			$this->prepare_the_post();
			$this->render_post();
		}
	}

	/**
	 * Sets up custom WordPress query.
	 *
	 * @param array $query_vars
	 *
	 * @since 1.0.0
	 */
	protected function set_query_vars( array $query_vars = array() ) {
		if ( empty( $query_vars ) ) {
			return;
		}

		$this->query_vars = array_merge( $query_vars, $this->query_vars );
	}

	/**
	 * Render.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$this->init_query();

		$wp_query = $this->get_query();

		if ( ! $wp_query->found_posts ) {
			if ( $this->is_current_query() ) {
				echo '<h4 class="cmsmasters-blog__nothing-found">' .
					( ! empty( $this->get_settings_fallback( 'nothing_found_message' ) ) ? esc_html( $this->get_settings_fallback( 'nothing_found_message' ) ) : esc_html__( 'Posts not found!', 'cmsmasters-elementor' ) ) .
				'</h4>';
			} else {
				Utils::render_alert( esc_html__( 'Posts not found!', 'cmsmasters-elementor' ) );
			}

			return;
		}

		$this->add_render_attribute(
			array(
				'blog' => array(
					'class' => $this->get_blog_classes(),
				),
			)
		);

		echo '<div ' . $this->get_render_attribute_string( 'blog' ) . '>';

		$this->render_blog();

		echo '</div>';

		wp_reset_postdata();
	}

	/**
	 * Render widget plain content.
	 *
	 * Save generated HTML to the database as plain content.
	 *
	 * @since 1.0.0
	 */
	public function render_plain_content() {}
}
