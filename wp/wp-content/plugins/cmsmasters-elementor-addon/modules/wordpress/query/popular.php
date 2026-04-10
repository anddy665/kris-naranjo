<?php
namespace CmsmastersElementor\Modules\Wordpress\Query;

use CmsmastersElementor\Modules\Wordpress\Query\Post;

use Elementor\Widget_Base;


/**
 * Addon related query class.
 * Wrapper for WP_Query.
 * Used by the various widgets for generating the query, according to the controls added using Group_Control_Query.
 * Each class instance is associated with the specific widget that is passed in the class constructor.
 */
class Popular extends Post {

	/**
	 * Query
	 *
	 * @var Post
	 */
	private $query;

	/**
	 * Addon related query class constructor.
	 *
	 * @param Widget_Base $widget
	 * @param string $group_query_name
	 * @param array $query_args
	 * @param array $fallback_args
	 */
	public function __construct( $widget, $group_query_name, $query_args, $query ) {
		parent::__construct( $widget, $group_query_name, $query_args );

		$this->query = $query;
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
		$query = $this->query->get_query();

		$found_posts = $query->found_posts;
		$posts_per_page = $query->query['posts_per_page'];

		if ( $found_posts >= $posts_per_page ) {
			$query = $query;
		} else {
			$orderby = $this->get_widget_parameter( 'orderby' );
			$posts_per_page = $posts_per_page - $found_posts;

			$args_not_exist_posts = array(
				'paged' => $query->query['paged'],
				'post_status' => $query->query['post_status'],
				'post_type' => $query->query['post_type'],
				'posts_per_page' => $posts_per_page,
				'ignore_sticky_posts' => $query->query['ignore_sticky_posts'],
				'meta_query' => array(
					$orderby  => array(
						'key' => $orderby,
						'compare' => 'NOT EXISTS',
						'value' => 'any_value', // must be here due to a bug
					),
				),
				'orderby' => array(
					$orderby => $query->query['order'],
				),
			);
			
			if ( isset ( $query->query['blog_posts_per_page'] ) ) {
				$args_not_exist_posts['blog_posts_per_page'] = $posts_per_page;
			}

			if ( isset ( $query->query['date_query'] ) ) {
				$args_not_exist_posts['date_query'] = $query->query['date_query'];
			}

			if ( isset ( $query->query['author__in'] ) ) {
				$args_not_exist_posts['author__in'] = $query->query['author__in'];
			}

			if ( isset ( $query->query['author__not_in'] ) ) {
				$args_not_exist_posts['author__not_in'] = $query->query['author__not_in'];
			}

			if ( isset ( $query->query['tax_query'] ) ) {
				$args_not_exist_posts['tax_query'] = $query->query['tax_query'];
			}

			if ( isset ( $query->query['post__in'] ) ) {
				$args_not_exist_posts['post__in'] = $query->query['post__in'];
			}

			if ( isset ( $query->query['post__not_in'] ) ) {
				$args_not_exist_posts['post__not_in'] = $query->query['post__not_in'];
			}

			$query_not_exist_posts = new \WP_Query($args_not_exist_posts);

			$posts = array_merge( $query->posts, $query_not_exist_posts->posts );
			$order =  $query->query['order'];

			if ( 'ASC' === $order ) {
				$posts = array_merge( $query_not_exist_posts->posts, $query->posts );
			}

			$query_result = new \WP_Query(
				array(
					'paged' => $query->query['paged'],
					'post_status' => $query->query['post_status'],
					'post_type' => $query->query['post_type'],
					'posts_per_page' => $query->query['posts_per_page'],
					'ignore_sticky_posts' => $query->query['ignore_sticky_posts'],
					'post__in' => wp_list_pluck( $posts, 'ID' ),
					'orderby' => 'post__in',
					
				)
			);

			$query = $query_result;
		}

		return $query;
	}
}