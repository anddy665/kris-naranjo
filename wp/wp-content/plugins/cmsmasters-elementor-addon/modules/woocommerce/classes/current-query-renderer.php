<?php
namespace CmsmastersElementor\Modules\Woocommerce\Classes;

use CmsmastersElementor\Modules\Woocommerce\Widgets\Base_Widgets\Base_Products;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSmasters Products shortcode class.
 *
 * Products displayed on archive pages.
 *
 * @since 1.0.0
 */
class Current_Query_Renderer extends Base_Products_Renderer {

	/**
	 * Widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Initialize shortcode.
	 *
	 * @param array $settings
	 * @param string $type
	 *
	 * @since 1.0.0
	 */
	public function __construct( $settings = array(), $type = 'products' ) {
		$this->settings = $settings;

		$this->type = $type;

		$this->attributes = $this->parse_attributes( array(
			'paginate' => $settings['pagination_show'],
			'cache' => false,
		) );

		$this->query_args = $this->parse_query_args();
	}

	/**
	 * @since 1.0.0
	 * @since 1.9.2 Fixed products results in taxonomy page.
	 */
	protected function get_query_results() {
		if (
			Utils::is_edit_mode() ||
			isset( $_GET['preview_id'], $_GET['preview_nonce'] )
		) {
			$query = new \WP_Query( $this->query_args );
		} else {
			$query = $GLOBALS['wp_query'];
		}

		$paginated = ! $query->get( 'no_found_rows' );

		// Check is_object to indicate it's called the first time.
		if ( ! empty( $query->posts ) && is_object( $query->posts[0] ) ) {
			$query->posts = array_map( function ( $post ) {
				return $post->ID;
			}, $query->posts );
		}

		$results = (object) array(
			'ids' => wp_parse_id_list( $query->posts ),
			'total' => $paginated ? (int) $query->found_posts : count( $query->posts ),
			'total_pages' => $paginated ? (int) $query->max_num_pages : 1,
			'per_page' => (int) $query->get( 'posts_per_page' ),
			'current_page' => $paginated ? (int) max( 1, $query->get( 'paged', 1 ) ) : 1,
		);

		return $results;
	}

	/**
	 * @since 1.0.0
	 * @since 1.9.2 Fixed products query args in taxonomy page.
	 */
	protected function parse_query_args() {
		$settings = &$this->settings;

		if ( ! is_page( wc_get_page_id( 'shop' ) ) ) {
			$query_args = $GLOBALS['wp_query']->query_vars;
		}

		if (
			Utils::is_edit_mode() ||
			isset( $_GET['preview_id'], $_GET['preview_nonce'] )
		) {
			$query_args = array(
				'post_type' => 'product',
				'post_status' => 'publish',
				'ignore_sticky_posts' => true,
			);
		}

		add_action( "woocommerce_shortcode_before_{$this->type}_loop", function () {
			wc_set_loop_prop( 'is_shortcode', false );
		} );

		if ( $settings['pagination_show'] ) {
			$page = get_query_var( 'paged', 1 );

			if ( 1 < $page ) {
				$query_args['paged'] = $page;
			}

			if ( ! $settings['allow_order'] ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			}

			if ( ! $settings['show_result_count'] ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			}
		}

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return $query_args;
	}

}
