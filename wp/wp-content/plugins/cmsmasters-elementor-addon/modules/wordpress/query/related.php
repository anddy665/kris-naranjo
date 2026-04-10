<?php
namespace CmsmastersElementor\Modules\Wordpress\Query;

use CmsmastersElementor\Modules\Wordpress\Query\Post;
use CmsmastersElementor\Utils;

use Elementor\Widget_Base;


/**
 * Addon related query class.
 * Wrapper for WP_Query.
 * Used by the various widgets for generating the query, according to the controls added using Group_Control_Query.
 * Each class instance is associated with the specific widget that is passed in the class constructor.
 */
class Related extends Post {

	private $fallback_args;

	private $related_post_id;

	/**
	 * Addon related query class constructor.
	 *
	 * @param Widget_Base $widget
	 * @param string $group_query_name
	 * @param array $query_args
	 * @param array $fallback_args
	 */
	public function __construct( $widget, $group_query_name, $query_args = array(), $fallback_args = array() ) {
		parent::__construct( $widget, $group_query_name, $query_args );

		$this->fallback_args = $fallback_args;
		$this->related_post_id = -1;
	}

	/**
	 * 1) build query args
	 * 2) invoke callback to fine-tune query-args
	 * 3) generate WP_Query object
	 * 4) if no results & fallback is set, generate a new WP_Query with fallback args
	 * 5) return WP_Query
	 *
	 * @return \WP_Query
	 */
	public function get_query() {
		$query = parent::get_query();

		if ( ! $query->post_count && $this->is_valid_fallback() ) {
			$query = $this->get_fallback_query( $query );
		}

		return $query;
	}

	protected function set_common_args() {
		parent::set_common_args();

		$post_id = get_queried_object_id();

		$this->related_post_id = ( is_singular() && 0 !== $post_id ) ? $post_id : null;

		$this->query_args['post_type'] = get_post_type( $this->related_post_id );
	}

	protected function set_post_exclude_args() {
		parent::set_post_exclude_args();

		if ( ! $this->related_post_id ) {
			return;
		}

		$this->query_args['post__not_in'] = array_merge(
			array( $this->related_post_id ),
			Utils::get_if_isset( $this->query_args, 'post__not_in', array() )
		);
	}

	protected function set_terms_args() {
		$this->build_terms_query( false, 'related_taxonomies' );
		$this->build_terms_query( true );
	}

	protected function generate_terms_list( $control_id ) {
		$terms_array = $this->get_widget_parameter( $control_id );

		if ( empty( $terms_array ) ) {
			return array();
		}

		return ( 'related_taxonomies' === $control_id ) ?
			$this->get_post_terms( $terms_array ) :
			$this->get_terms_taxonomies_list( $terms_array );
	}

	protected function get_post_terms( $maybe_taxonomy ) {
		$terms = array();

		if ( ! is_array( $maybe_taxonomy ) ) {
			$terms[ $maybe_taxonomy ] = wp_get_post_terms( $this->related_post_id, $maybe_taxonomy, array( 'fields' => 'tt_ids' ) );

			return $terms;
		}

		foreach ( $maybe_taxonomy as $taxonomy ) {
			$terms = array_merge( $terms, $this->get_post_terms( $taxonomy ) );
		}

		return $terms;
	}

	protected function set_author_args() {
		if ( empty( $this->get_widget_parameter( 'current_author' ) ) ) {
			return;
		}

		$this->query_args['author__in'] = get_post_field( 'post_author', $this->related_post_id );
	}

	private function is_valid_fallback() {
		$related_fallback = $this->get_widget_parameter( 'related_fallback' );

		if ( empty( $related_fallback ) ) {
			return false;
		}

		$valid = false;

		switch ( $related_fallback ) {
			case 'recent':
				$valid = true;

				break;
			case 'manual_selection':
				if ( ! empty( $this->get_widget_parameter( 'fallback_posts_in' ) ) ) {
					$valid = true;
				}

				break;
		}

		return $valid;
	}

	protected function get_fallback_query( $original_query ) {
		$this->set_fallback_basic_args();

		$this->set_fallback_query_arg(
			'posts_per_page',
			$original_query->query_vars['posts_per_page']
		);

		$this->fallback_args = apply_filters( 'cmsmasters_elementor/query/fallback_query_args', $this->fallback_args, $this->widget );

		return new \WP_Query( $this->fallback_args );
	}

	protected function set_fallback_basic_args() {
		$this->set_fallback_query_arg( 'post_status', 'publish' );
		$this->set_fallback_query_arg( 'ignore_sticky_posts', true );

		if ( 'manual_selection' !== $this->get_widget_parameter( 'related_fallback' ) ) {
			$post_type = $this->query_args['post_type'];

			$this->set_fallback_query_arg( 'orderby', 'date' );
			$this->set_fallback_query_arg( 'order', 'DESC' );
		} else {
			$post_type = array_keys( Utils::get_public_post_types() );

			$this->set_fallback_query_arg( 'orderby', 'rand' );

			$this->set_fallback_query_prefix_arg( 'post__in', array( 0 ), 'fallback_posts_in' );
		}

		$this->set_fallback_query_arg( 'post_type', $post_type );
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	private function set_fallback_query_arg( $key, $value ) {
		if ( ! empty( $this->fallback_args[ $key ] ) ) {
			return;
		}

		$this->fallback_args[ $key ] = $value;
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param string $control_name
	 */
	private function set_fallback_query_prefix_arg( $key, $value, $control_name ) {
		$this->set_fallback_query_arg( $key, $value );

		$settings = $this->widget->get_settings();

		if ( empty( $settings[ $this->prefix . $control_name ] ) ) {
			return;
		}

		$this->fallback_args[ $key ] = $settings[ $this->prefix . $control_name ];
	}

}
