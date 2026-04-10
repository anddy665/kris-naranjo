<?php
namespace CmsmastersElementor\Modules\Woocommerce\Classes;

use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSmasters Products shortcode class.
 *
 * @since 1.0.0
 */
class Products_Renderer extends Base_Products_Renderer {

	const QUERY_CONTROL_NAME = 'query'; //Constraint: the class that uses the renderer, must use the same name

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
			'columns' => $settings['columns'],
			'rows' => $settings['columns'] * Utils::get_if_isset( $settings, 'products_pre_page', static::DEFAULT_COLUMNS_AND_ROWS * static::DEFAULT_COLUMNS_AND_ROWS ),
			'paginate' => $settings['pagination_show'],
			'cache' => false,
		) );

		$this->query_args = $this->parse_query_args();
	}

	/**
	 * @since 1.0.0
	 */
	protected function parse_query_args() {
		$settings = &$this->settings;
		$prefix = self::QUERY_CONTROL_NAME;

		$query_args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows' => false === wc_string_to_bool( $this->attributes['paginate'] ),
			'orderby' => $settings[ "{$prefix}_orderby" ],
			'order' => strtoupper( $settings[ "{$prefix}_order" ] ),
		);

		$front_page = is_front_page();

		$query_args['meta_query'] = WC()->query->get_meta_query();
		$query_args['tax_query'] = array();

		if (
			'' !== $settings['pagination_show'] &&
			'' !== $settings['allow_order'] &&
			! $front_page
		) {
			$ordering_args = WC()->query->get_catalog_ordering_args();
		} else {
			$ordering_args = WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );
		}

		$query_args['orderby'] = $ordering_args['orderby'];
		$query_args['order'] = $ordering_args['order'];

		if ( $ordering_args['meta_key'] ) {
			$query_args['meta_key'] = $ordering_args['meta_key'];
		}

		// Visibility.
		$this->set_visibility_query_args( $query_args );

		//Featured.
		$this->set_featured_query_args( $query_args );

		//Sale.
		$this->set_sale_products_query_args( $query_args );

		// IDs.
		$this->set_ids_query_args( $query_args );

		// Set specific types query args.
		if ( method_exists( $this, "set_{$this->type}_query_args" ) ) {
			$this->{"set_{$this->type}_query_args"}( $query_args );
		}

		// Categories & Tags
		$this->set_terms_query_args( $query_args );

		//Exclude.
		$this->set_exclude_query_args( $query_args );

		if ( $settings['pagination_show'] ) {
			$page = absint( empty( $_GET['product-page'] ) ? 1 : $_GET['product-page'] );

			if ( 1 < $page ) {
				$query_args['paged'] = $page;
			}

			if ( ! $settings['allow_order'] || $front_page ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
			}

			if ( ! $settings['show_result_count'] ) {
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
			}
		}

		$query_args['posts_per_page'] = Utils::get_if_isset(
			$this->settings,
			'products_pre_page',
			static::DEFAULT_COLUMNS_AND_ROWS * static::DEFAULT_COLUMNS_AND_ROWS
		);

		$query_args = apply_filters( 'woocommerce_shortcode_products_query', $query_args, $this->attributes, $this->type );

		// Always query only IDs.
		$query_args['fields'] = 'ids';

		return $query_args;
	}

	/**
	 * @since 1.0.0
	 */
	protected function set_ids_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME;

		switch ( $this->settings[ "{$prefix}_post_type" ] ) {
			case 'manual_selection':
				$post__in = $this->settings[ "{$prefix}_posts_in" ];

				break;
			case 'sale':
				$post__in = wc_get_product_ids_on_sale();

				break;
		}

		if ( empty( $post__in ) ) {
			return;
		}

		$query_args['post__in'] = $post__in;

		remove_action( 'pre_get_posts', array( wc()->query, 'product_query' ) );
	}

	/**
	 * Set terms query args.
	 *
	 * @param array $query_args
	 */
	private function set_terms_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME;

		$query_type = $this->settings[ "{$prefix}_post_type" ];

		$no_terms_sources = array(
			'manual_selection',
			'current_query',
			'custom_args',
		);

		if (
			in_array( $query_type, $no_terms_sources, true ) ||
			empty( $this->settings[ "{$prefix}_include_term_ids" ] )
		) {
			return;
		}

		$terms = array();

		foreach ( $this->settings[ "{$prefix}_include_term_ids" ] as $id ) {
			$term_data = get_term_by( 'term_taxonomy_id', $id );

			if ( isset( $term_data->taxonomy ) ) {
				$terms[ $term_data->taxonomy ][] = $id;
			}
		}

		$tax_query = array();

		foreach ( $terms as $taxonomy => $ids ) {
			$query = array(
				'taxonomy' => $taxonomy,
				'field' => 'term_taxonomy_id',
				'terms' => $ids,
			);

			$tax_query[] = $query;
		}

		if ( ! empty( $tax_query ) ) {
			$query_args['tax_query'] = array_merge( $query_args['tax_query'], $tax_query );
		}
	}

	/**
	 * Set featured posts query args.
	 *
	 * @param array $query_args
	 */
	protected function set_featured_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME;

		if ( 'featured' !== $this->settings[ "{$prefix}_post_type" ] ) {
			return;
		}

		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$query_args['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field' => 'term_taxonomy_id',
			'terms' => array( $product_visibility_term_ids['featured'] ),
		);
	}

	/**
	 * @since 1.0.0
	 */
	protected function set_sale_products_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME;

		if ( 'sale' !== $this->settings[ "{$prefix}_post_type" ] ) {
			return;
		}

		parent::set_sale_products_query_args( $query_args );
	}

	/**
	 * Exclude posts query args.
	 *
	 * @param array $query_args
	 */
	protected function set_exclude_query_args( &$query_args ) {
		$prefix = self::QUERY_CONTROL_NAME;

		$posts_not_in = ( is_singular() && ! empty( $this->settings[ "{$prefix}_current_post" ] ) ) ?
			array( get_queried_object_id() ) :
			array();

		$manual_not_in = $this->settings[ "{$prefix}_posts_not_in" ];

		if ( ! empty( $manual_not_in ) ) {
			$posts_not_in = array_merge( $posts_not_in, $manual_not_in );
		}

		$query_args['post__not_in'] = ( ! empty( $query_args['post__not_in'] ) ) ?
			array_merge( $query_args['post__not_in'], $posts_not_in ) :
			$posts_not_in;

		/**
		 * WC populates `post__in` with the ids of the products that are on sale.
		 * Since WP_Query ignores `post__not_in` once `post__in` exists, the ids are filtered manually, using `array_diff`.
		 */
		if ( 'sale' === $this->settings[ "{$prefix}_post_type" ] ) {
			$query_args['post__in'] = array_diff( $query_args['post__in'], $query_args['post__not_in'] );
		}

		$terms_not_in = $this->settings[ "{$prefix}_exclude_term_ids" ];

		if ( ! empty( $terms_not_in ) ) {
			$terms = array();

			foreach ( $terms_not_in as $exclude_id ) {
				$term_data = get_term_by( 'term_taxonomy_id', $exclude_id );

				$terms[ $term_data->taxonomy ][] = $exclude_id;
			}

			$tax_query = array();

			foreach ( $terms as $taxonomy => $ids ) {
				$tax_query[] = array(
					'taxonomy' => $taxonomy,
					'field' => 'term_id',
					'terms' => $ids,
					'operator' => 'NOT IN',
				);
			}

			if ( empty( $query_args['tax_query'] ) ) {
				$query_args['tax_query'] = $tax_query;
			} else {
				$query_args['tax_query']['relation'] = 'AND';

				$query_args['tax_query'][] = $tax_query;
			}
		}
	}

}
