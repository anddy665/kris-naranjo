<?php
namespace CmsmastersElementor\Modules\Wordpress\Query;

use CmsmastersElementor\Modules\TemplatePreview\Module as TemplatePreviewModule;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Modules\Wordpress\Module as WordPressModule;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Widget_Base;


/**
 * Addon post query class.
 * Wrapper for WP_Query.
 * Used by the various widgets for generating the query, according to the controls added using Group_Control_Query.
 * Each class instance is associated with the specific widget that is passed in the class constructor.
 */
class Post {

	/**
	 * WordPress query manager.
	 *
	 * @since 1.0.0
	 *
	 * @var Query_Manager
	 */
	protected $query_manager = null;

	/**
	 * Base widget class.
	 *
	 * @since 1.0.0
	 *
	 * @var Widget_Base
	 */
	protected $widget;

	protected $query_args;

	protected $prefix;

	protected $widget_settings;

	/**
	 * Addon post query class constructor.
	 *
	 * @param Widget_Base $widget
	 * @param string $group_query_name
	 * @param array $query_args
	 */
	public function __construct( $widget, $group_query_name, $query_args = array() ) {
		/** @var WordPressModule $wordpress_module */
		$wordpress_module = WordPressModule::instance();

		$this->query_manager = $wordpress_module->get_query_manager();

		$this->widget = $widget;
		$this->prefix = "{$group_query_name}_";
		$this->query_args = $query_args;

		$this->widget_settings = wp_parse_args(
			$this->widget->get_settings(),
			$this->get_query_defaults()
		);
	}

	protected function get_query_defaults() {
		$defaults = array();
		$controls = array(
			'post_type' => 'post',
			'posts_in' => array(),
			'orderby' => 'date',
			'order' => 'DESC',
			'offset' => 0,
			'posts_per_page' => 3,
		);

		foreach ( $controls as $key => $value ) {
			$defaults[ $this->prefix . $key ] = $value;
		}

		return array_merge( $defaults, $this->query_args );
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
		$this->get_query_args();

		$this->before_get_query();

		$query = new \WP_Query( $this->query_args );

		$this->after_get_query();

		$this->query_manager->add_to_displayed_ids( wp_list_pluck( $query->posts, 'ID' ) );

		do_action( 'cmsmasters_elementor/query/query_results', $query, $this->widget );

		return $query;
	}

	public function get_query_args() {
		$post_type = $this->get_widget_parameter( 'post_type' );

		if ( 'current_query' === $post_type ) {
			global $wp_query;

			$this->fix_current_query_on_ajax();

			if ( isset( $this->query_args['paged'] ) ) {
				$wp_query->set( 'paged', $this->query_args['paged'] );
			}

			$this->query_args = $wp_query->query_vars;

			$this->set_order_args( true );

			/**
			 * Current query variables.
			 *
			 * Filters the query variables for the current query.
			 *
			 * @since 1.0.0
			 *
			 * @param array $this->query_args Current query variables.
			 */
			$this->query_args = apply_filters( 'cmsmasters_elementor/query/get_query_args/current_query', $this->query_args );

			return $this->query_args;
		}

		if ( 'custom_args' === $post_type ) {
			$custom_args_text = $this->get_widget_parameter( 'custom_args' );

			$custom_args = $this->custom_args_text_to_array( $custom_args_text );

			/**
			 * Custom query arguments.
			 *
			 * Filters the query arguments for the custom query.
			 *
			 * @since 1.0.0
			 *
			 * @param array $custom_args Custom query arguments.
			 */

			$this->set_pagination_args();

			$custom_args = array_replace_recursive( $this->query_args, $custom_args );

			$this->query_args = apply_filters( 'cmsmasters_elementor/query/get_query_args/custom_args', $custom_args );

			return $this->query_args;
		}

		$this->set_common_args();
		$this->set_basic_args();

		if ( 'manual_selection' !== $post_type ) {
			$this->set_additional_query_args();
		}

		/**
		 * Query variables.
		 *
		 * Filters the query variables.
		 *
		 * @since 1.0.0
		 *
		 * @param array $this->query_args Query variables.
		 * @param array $this->widget Current widget.
		 */
		$this->query_args = apply_filters( 'cmsmasters_elementor/query/query_args', $this->query_args, $this->widget );

		return $this->query_args;
	}

	/**
	 * Reproduces `global $wp_query;` on ajax request.
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Added `PHP_URL_QUERY` in `wp_parse_url`.
	 */
	private function fix_current_query_on_ajax() {
		if ( ! Utils::is_ajax() ) {
			return;
		}

		$post_id = Utils::get_document_id();
		$wp_referer = wp_get_referer();

		if (
			preg_match( '/elementor-preview|preview_id/', wp_parse_url( $wp_referer, PHP_URL_QUERY ) ) ||
			Plugin::elementor()->editor->is_edit_mode( $post_id )
		) {
			/** @var TemplatePreviewModule $preview_module */
			$preview_module = TemplatePreviewModule::instance();

			$preview_module->switch_to_preview_query( $post_id );
		} else {
			$request_uri_old = $_SERVER['REQUEST_URI'];
			$php_self_old = $_SERVER['PHP_SELF'];

			$parsed_url = wp_parse_url( $wp_referer );
			$request_uri = $parsed_url['path'];

			if ( isset( $parsed_url['query'] ) ) {
				$query_array = wp_parse_args( $parsed_url['query'] );

				if ( ! empty( $query_array['s'] ) ) {
					$_GET['s'] = wp_unslash( $query_array['s'] );
				}

				$request_uri .= "?{$parsed_url['query']}";
			}

			$_SERVER['REQUEST_URI'] = $request_uri;
			$_SERVER['PHP_SELF'] = '/index.php';

			wp();

			if ( ! empty( $query_array['s'] ) && isset( $_GET['s'] ) ) {
				unset( $_GET['s'] );
			}

			$_SERVER['REQUEST_URI'] = $request_uri_old;
			$_SERVER['PHP_SELF'] = $php_self_old;
		}
	}

	/**
	 * @param string $control_name
	 *
	 * @return mixed|null
	 */
	protected function get_widget_parameter( $control_name ) {
		return Utils::get_if_isset( $this->widget_settings, $this->prefix . $control_name, null );
	}

	protected function set_basic_args( $force = false ) {
		$this->set_order_args( $force );
		$this->set_pagination_args( $force );
	}

	protected function set_order_args( $force = false ) {
		$order = $this->get_widget_parameter( 'order' );

		if ( empty( $order ) ) {
			return;
		}

		$this->set_query_arg( 'order', strtoupper( $order ), $force );

		$orderby = $this->get_widget_parameter( 'orderby' );

		if ( $orderby  === 'cmsmasters_pm_view' || $orderby  === 'cmsmasters_pm_like' ) {
			$this->set_query_arg( 'meta_key', $orderby, $force );
			$orderby = 'meta_value_num';
		}

		/**
		 * Set orderby argument.
		 *
		 * Filters the orderby query argument.
		 *
		 * @since 1.0.0
		 *
		 * @param array $orderby Orderby query argument.
		 */
		$orderby = apply_filters( 'cmsmasters_elementor/query/set_order_args/orderby', $orderby );

		$this->set_query_arg( 'orderby', $orderby, $force );
	}

	protected function set_pagination_args( $force = false ) {
		$this->set_query_arg( 'posts_per_page', $this->get_widget_parameter( 'posts_per_page' ), $force );
	}

	private function custom_args_text_to_array( $text ) {
		// Remove comments
		$text = preg_replace( '/\/\/[^\r\n]*/', '', $text );
		$text_no_comments = preg_replace( '/\/\*[\S\s]*\*\//m', '', $text );

		// Remove variables
		$text_no_vars = preg_replace( '/\$[^\s=]+\s*=\s*((?!array)[\S\s])*/', '', $text_no_comments );

		// Remove not supported characters
		$args_string = str_replace( array( '<?php', ';' ), '', $text_no_vars );

		// Convert text to JSON
		$args_json = $this->custom_args_convert_to_json( $args_string );

		return json_decode( $args_json, true );
	}

	private function custom_args_convert_to_json( $args_string ) {
		// Remove spaces
		$args_no_space = preg_replace( '/\s*/', '', $args_string );

		// Convert PHP array format to JSON
		$args_no_space = str_replace( array( 'array(', '[' ), '{', $args_no_space );
		$args_no_space = str_replace( array( ')', ']' ), '}', $args_no_space );
		$args_no_space = str_replace( array( '=>', '\'', ',}' ), array( ':', '"', '}' ), $args_no_space );

		return $this->custom_args_json_fix_array( $args_no_space );
	}

	private function custom_args_json_fix_array( $args ) {
		// Fix JSON indexed array format
		$args = preg_replace( '/\{([^:\}]+)\}/', '[$1]', $args );
		$args = preg_replace_callback( '/,\{([^\}]+)\}/', function( $match ) {
			static $count = 0;

			return sprintf( ',"%2$d":{%1$s}', $match[1], $count++ );
		}, $args );

		return $args;
	}

	protected function set_common_args() {
		// Hides drafts & private posts for site admins.
		$this->query_args['post_status'] = 'publish';

		$post_type = $this->get_widget_parameter( 'post_type' );

		if ( 'manual_selection' === $post_type ) {
			$this->set_manual_query_args();
		} else {
			$this->query_args['post_type'] = $post_type;
		}
	}

	protected function set_manual_query_args() {
		$this->query_args['post_type'] = array_keys( Utils::get_public_post_types() );

		$this->set_post_include_args();
	}

	protected function set_post_include_args() {
		$this->set_query_arg( 'post__in', $this->get_widget_parameter( 'posts_in' ) );

		$this->check_post_in_arg();
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 * @param bool $force
	 */
	protected function set_query_arg( $key, $value, $force = false ) {
		if ( isset( $this->query_args[ $key ] ) && ! $force ) {
			return;
		}

		$this->query_args[ $key ] = $value;
	}

	/**
	 * Used to overcome core bug when empty array passed to
	 * WP_Query post__in returns posts.
	 *
	 * @see https://core.trac.wordpress.org/ticket/28099
	 * @source https://core.trac.wordpress.org/ticket/28099#comment:28 fix
	 */
	private function check_post_in_arg() {
		if ( ! empty( $this->query_args['post__in'] ) ) {
			return;
		}

		$this->query_args['post__in'] = array( 0 );
	}

	protected function set_additional_query_args() {
		$this->set_post_exclude_args();

		$this->set_prevent_duplicates();

		$this->set_terms_args();
		$this->set_author_args();
		$this->set_date_args();
	}

	protected function set_post_exclude_args() {
		$this->set_query_arg(
			'ignore_sticky_posts',
			( ! empty( $this->get_widget_parameter( 'ignore_sticky_posts' ) ) ) ? true : false
		);

		$posts_not_in = ( is_singular() && ! empty( $this->get_widget_parameter( 'current_post' ) ) ) ?
			array( get_queried_object_id() ) :
			array();

		$manual_not_in = $this->get_widget_parameter( 'posts_not_in' );

		if ( ! empty( $manual_not_in ) ) {
			$posts_not_in = array_merge( $posts_not_in, $manual_not_in );
		}

		$this->set_query_arg( 'post__not_in', $posts_not_in );
	}

	protected function set_prevent_duplicates() {
		if ( 'yes' !== $this->get_widget_parameter( 'prevent_duplicates' ) ) {
			return;
		}

		$this->set_query_arg( 'post__not_in', array_merge(
			Utils::get_if_isset( $this->query_args, 'post__not_in', array() ),
			$this->query_manager->get_displayed_ids()
		), true );
	}

	protected function set_terms_args() {
		$this->build_terms_query();
		$this->build_terms_query( true );
	}

	protected function build_terms_query( $exclude = false, $control_id = '' ) {
		$action = $exclude ? 'exclude' : 'include';
		$control_id = ( ! empty( $control_id ) ) ? $control_id : "{$action}_term_ids";

		$terms = $this->generate_terms_list( $control_id );

		if ( empty( $terms ) ) {
			return;
		}

		$this->insert_tax_query( $terms, $exclude );
	}

	protected function generate_terms_list( $control_id ) {
		$terms_array = $this->get_widget_parameter( $control_id );

		if ( empty( $terms_array ) ) {
			return array();
		}

		return $this->get_terms_taxonomies_list( $terms_array );
	}

	protected function get_terms_taxonomies_list( $terms_array ) {
		$terms = array();

		foreach ( $terms_array as $id ) {
			$term_data = get_term_by( 'term_taxonomy_id', $id );

			if ( false === $term_data ) {
				continue;
			}

			$taxonomy = $term_data->taxonomy;

			if ( ! isset( $terms[ $taxonomy ] ) ) {
				$terms[ $taxonomy ] = array();
			}

			$terms[ $taxonomy ][] = $id;
		}

		return $terms;
	}

	protected function insert_tax_query( $terms, $not_in ) {
		$tax_query = array();

		foreach ( $terms as $taxonomy => $ids ) {
			$query = array(
				'taxonomy' => $taxonomy,
				'field' => 'term_taxonomy_id',
				'terms' => $ids,
			);

			if ( $not_in ) {
				$query['operator'] = 'NOT IN';
			}

			$tax_query[] = $query;
		}

		if ( empty( $tax_query ) ) {
			return;
		}

		if ( empty( $this->query_args['tax_query'] ) ) {
			$this->query_args['tax_query'] = $tax_query;

			return;
		}

		$this->query_args['tax_query']['relation'] = 'AND';
		$this->query_args['tax_query'][] = $tax_query;
	}

	protected function set_author_args() {
		$author_query = $this->get_widget_parameter( 'author_query' );
		$selected_authors = $this->get_widget_parameter( 'selected_authors' );

		if ( empty( $author_query ) || empty( $selected_authors ) ) {
			return;
		}

		$not = ( 'exclude' === $author_query ) ? 'not_' : '';

		$this->set_query_arg( "author__{$not}in", $selected_authors );
	}

	protected function set_date_args() {
		$select_date = $this->get_widget_parameter( 'select_date' );

		if ( ! empty( $select_date ) ) {
			$date_query = array();

			switch ( $select_date ) {
				case 'day':
				case 'week':
				case 'month':
					$count = $this->get_widget_parameter( 'date_count' );

					$suffix = ( 1 < $count ) ? 's' : '';

					$date_query['after'] = "-{$count} {$select_date}{$suffix}";

					break;
				case 'year':
					$date_query['after'] = '-1 year';

					break;
				case 'exact':
					$after_date = $this->get_widget_parameter( 'date_after' );

					if ( ! empty( $after_date ) ) {
						$date_query['after'] = $after_date;
					}

					$before_date = $this->get_widget_parameter( 'date_before' );

					if ( ! empty( $before_date ) ) {
						$date_query['before'] = $before_date;
					}

					$date_query['inclusive'] = true;

					break;
			}

			$this->set_query_arg( 'date_query', $date_query );
		}
	}

	protected function before_get_query() {
		if ( ! empty( $this->get_widget_parameter( 'filter_id' ) ) ) {
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts_filter_id_query' ) );
		}

		if (
			'manual_selection' !== $this->get_widget_parameter( 'post_type' ) &&
			0 < $this->get_widget_parameter( 'offset' )
		) {
			add_action( 'pre_get_posts', array( $this, 'fix_pagination_query_offset' ), 1 );

			add_filter( 'found_posts', array( $this, 'fix_query_found_posts_offset' ), 1 );
		}
	}

	/**
	 * Fired by `pre_get_posts` WordPress action hook.
	 *
	 * @param \WP_Query $wp_query
	 */
	public function pre_get_posts_filter_id_query( $wp_query ) {
		if ( ! $this->widget ) {
			return;
		}

		$filter_id = $this->get_widget_parameter( 'filter_id' );

		/**
		 * Addon posts widget query args.
		 *
		 * Allows developers to alter individual posts widget queries.
		 *
		 * @since 1.0.0
		 *
		 * @param \WP_Query $wp_query WordPress query class.
		 * @param Widget_Base $this->widget Current widget.
		 */
		do_action( "cmsmasters_elementor/query/{$filter_id}", $wp_query, $this->widget );
	}

	/**
	 * Fired by `pre_get_posts` WordPress action hook.
	 *
	 * @param \WP_Query $wp_query
	 */
	public function fix_pagination_query_offset( &$wp_query ) {
		$offset = $this->get_widget_parameter( 'offset' );

		if ( $offset && $wp_query->is_paged ) {
			$wp_query->query_vars['offset'] = $offset + ( ( $wp_query->query_vars['paged'] - 1 ) * $wp_query->query_vars['posts_per_page'] );
		} else {
			$wp_query->query_vars['offset'] = $offset;
		}
	}

	/**
	 * Fired by `found_posts` WordPress filter hook.
	 *
	 * @param int $found_posts
	 *
	 * @return int
	 */
	public function fix_query_found_posts_offset( $found_posts ) {
		$offset = $this->get_widget_parameter( 'offset' );

		if ( $offset ) {
			$found_posts -= $offset;
		}

		return $found_posts;
	}

	protected function after_get_query() {
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_filter_id_query' ) );

		remove_action( 'pre_get_posts', array( $this, 'fix_pagination_query_offset' ), 1 );

		remove_filter( 'found_posts', array( $this, 'fix_query_found_posts_offset' ), 1 );
	}

}
