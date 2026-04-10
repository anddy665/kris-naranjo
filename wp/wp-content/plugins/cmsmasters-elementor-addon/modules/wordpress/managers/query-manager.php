<?php
namespace CmsmastersElementor\Modules\Wordpress\Managers;

use CmsmastersElementor\Base\Base_Actions;
use CmsmastersElementor\Base\Base_Widget;
use CmsmastersElementor\Modules\Wordpress\Query;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\TemplateLibrary\Source_Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Query_Manager extends Base_Actions {

	// Supported autocomplete query objects:
	const POST_OBJECT = 'post';

	const MEDIA_OBJECT = 'attachment';

	const TAX_OBJECT = 'tax';

	const USER_OBJECT = 'user';

	const AUTHOR_OBJECT = 'author';

	const TEMPLATE_OBJECT = 'library_template';

	// Objects that are manipulated by js (not sent in AJAX):
	const CPT_TAX_OBJECT = 'cpt_tax';

	const JS_OBJECT = 'js';

	const AUTOCOMPLETE_ERROR_CODE = 'QueryControlAutocomplete';

	const GET_TITLES_ERROR_CODE = 'QueryControlGetTitles';

	private static $supported_query_objects = array(
		self::POST_OBJECT,
		self::MEDIA_OBJECT,
		self::TAX_OBJECT,
		self::USER_OBJECT,
		self::AUTHOR_OBJECT,
		self::TEMPLATE_OBJECT,
	);

	private static $displayed_ids = array();

	protected function init_actions() {
		// Common
		add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ) );
	}

	protected function init_filters() {
		// Editor
		add_filter( 'cmsmasters_elementor/editor/settings', array( $this, 'filter_editor_settings' ) );
	}

	/**
	 * @param Ajax $ajax_manager
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'cmsmasters_control/wp_query/get_titles', array( $this, 'ajax_wp_query_control_get_titles' ) );
		$ajax_manager->register_ajax_action( 'cmsmasters_control/wp_query/filter_autocomplete', array( $this, 'ajax_wp_query_control_filter_autocomplete' ) );
	}

	public function ajax_wp_query_control_get_titles( $request ) {
		$query_data = $this->get_titles_query_data( $request );

		if ( is_wp_error( $query_data ) ) {
			return array();
		}

		$display = $query_data['display'];
		$query_args = $query_data['query'];

		$results = array();

		switch ( $query_data['object'] ) {
			case self::POST_OBJECT:
			case self::MEDIA_OBJECT:
				$query = new \WP_Query( $query_args );

				foreach ( $query->posts as $post ) {
					if ( apply_filters( "cmsmasters_elementor/query/get_value_titles/custom/{$display}", true, $post, $request ) ) {
						$results[ $post->ID ] = $this->format_post_for_display( $post, $display, $request, 'get_value_titles' );
					}
				}

				break;
			case self::TAX_OBJECT:
				$by_field = ! empty( $query_data['by_field'] ) ? $query_data['by_field'] : 'term_taxonomy_id';

				$terms = get_terms( $query_args );

				if ( is_wp_error( $terms ) ) {
					break;
				}

				foreach ( $terms as $term ) {
					if ( apply_filters( "cmsmasters_elementor/query/get_value_titles/tax/{$display}", true, $term, $request ) ) {
						$results[ $term->{$by_field} ] = $this->get_term_name( $term, $display, $request, 'get_value_titles' );
					}
				}

				break;
			case self::USER_OBJECT:
			case self::AUTHOR_OBJECT:
				$user_query = new \WP_User_Query( $query_args );

				foreach ( $user_query->get_results() as $user ) {
					if ( apply_filters( "cmsmasters_elementor/query/get_value_titles/user/{$display}", true, $user, $request ) ) {
						$results[ $user->ID ] = $this->format_user_for_display( $user, $display, $request, 'get_value_titles' );
					}
				}

				break;
			case self::TEMPLATE_OBJECT:
				$query = new \WP_Query( $query_args );

				foreach ( $query->posts as $post ) {
					$document = Plugin::elementor()->documents->get( $post->ID );

					if ( $document ) {
						$results[ $post->ID ] = $post->post_title . ' (' . $document->get_post_type_title() . ')';
					}
				}

				break;
			default:
				$results = apply_filters( "cmsmasters_elementor/query/get_value_titles/{$query_data['filter_type']}", $results, $request );
		}

		return $results;
	}

	private function get_titles_query_data( $data ) {
		if (
			empty( $data['get_titles'] ) ||
			empty( $data['id'] ) ||
			empty( $data['get_titles']['object'] )
		) {
			return new \WP_Error( self::GET_TITLES_ERROR_CODE, 'Empty or incomplete data' );
		}

		$get_titles = $data['get_titles'];

		if ( empty( $get_titles['query'] ) ) {
			$get_titles['query'] = array();
		}

		if ( in_array( $get_titles['object'], self::$supported_query_objects, true ) ) {
			$method_name = 'get_titles_query_for_' . $get_titles['object'];

			$query = $this->$method_name( $data );

			if ( is_wp_error( $query ) ) {
				return $query;
			}

			$get_titles['query'] = $query;
		}

		if ( empty( $get_titles['display'] ) ) {
			$get_titles['display'] = 'minimal';
		}

		return $get_titles;
	}

	private function get_titles_query_for_post( $data ) {
		$query = $data['get_titles']['query'];

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$query['posts_per_page'] = -1;
		$query['post__in'] = (array) $data['id'];

		return $query;
	}

	private function get_titles_query_for_attachment( $data ) {
		$query = $this->get_titles_query_for_post( $data );

		$query['post_type'] = 'attachment';
		$query['post_status'] = 'inherit';

		return $query;
	}

	private function get_titles_query_for_tax( $data ) {
		$by_field = Utils::get_if_not_empty( $data['get_titles'], 'by_field', 'term_taxonomy_id' );

		$query = array(
			$by_field => (array) $data['id'],
			'hide_empty' => false,
		);

		return $query;
	}

	private function get_titles_query_for_user( $data ) {
		$titles = $data['get_titles'];

		if ( ! empty( $titles['query'] ) ) {
			return $titles['query'];
		}

		$query = array(
			'fields' => array( 'ID', 'display_name' ),
			'include' => (array) $data['id'],
		);

		if ( 'detailed' === $titles['display'] ) {
			$query['fields'][] = 'user_email';
		}

		return $query;
	}

	private function get_titles_query_for_author( $data ) {
		$query = $this->get_titles_query_for_user( $data );

		$query['who'] = 'authors';
		$query['has_published_posts'] = true;

		return $query;
	}

	private function get_titles_query_for_library_template( $data ) {
		$query = $data['get_titles']['query'];

		$query['post_type'] = Source_Local::CPT;

		$query['orderby'] = 'meta_value';
		$query['order'] = 'ASC';

		if ( empty( $query['posts_per_page'] ) ) {
			$query['posts_per_page'] = -1;
		}

		return $query;
	}

	/**
	 * @param \WP_Post $post
	 * @param string $display
	 * @param array $data
	 * @param string $filter_name
	 *
	 * @return mixed|string|void
	 */
	private function format_post_for_display( $post, $display, $data, $filter_name = 'get_autocomplete' ) {
		$post_type_obj = get_post_type_object( $post->post_type );
		$post_title = ( $post_type_obj->hierarchical ) ?
			$this->get_post_name_with_parents( $post ) :
			$post->post_title;

		switch ( $display ) {
			case 'minimal':
				$text = $post_title;

				break;
			case 'detailed':
				$text = sprintf( '%1$s: %2$s', $post_type_obj->labels->name, $post_title );

				break;
			default:
				$text = apply_filters( "cmsmasters_elementor/query/{$filter_name}/display/{$display}", $post->post_title, $post->ID, $data );
		}

		return $text;
	}

	/**
	 * get post name with parents
	 *
	 * @param \WP_Post $post
	 * @param int $max
	 *
	 * @return string
	 */
	private function get_post_name_with_parents( $post, $max = 3 ) {
		if ( 0 === $post->post_parent ) {
			return $post->post_title;
		}

		$separator = sprintf( ' %s ', is_rtl() ? '<' : '>' );
		$test_post = $post;
		$names = array();

		while ( $test_post->post_parent > 0 ) {
			$test_post = get_post( $test_post->post_parent );

			if ( ! $test_post ) {
				break;
			}

			$names[] = $test_post->post_title;
		}

		$names = array_reverse( $names );
		$post_title = $separator . $post->post_title;

		if ( $max > count( $names ) ) {
			return implode( $separator, $names ) . $post_title;
		}

		$name_string = '';

		for ( $i = 0; $i < ( $max - 1 ); $i++ ) {
			$name_string .= $names[ $i ] . $separator;
		}

		return sprintf( '%1$s...%2$s', $name_string, $post_title );
	}

	private function get_term_name( $term, $display, $request, $filter_name = 'get_autocomplete' ) {
		global $wp_taxonomies; // TODO: replace with get_taxonomy()

		$term_name = $this->get_term_name_with_parents( $term );

		switch ( $display ) {
			case 'minimal':
				$text = $term_name;

				break;
			case 'detailed':
				$text = sprintf( '%1$s: %2$s', $wp_taxonomies[ $term->taxonomy ]->labels->name, $term_name );

				break;
			default:
				$text = apply_filters( "cmsmasters_elementor/query/{$filter_name}/display/{$display}", $term_name, $request );
		}

		return $text;
	}

	/**
	 * get_term_name_with_parents
	 * @param \WP_Term $term
	 * @param int $max
	 *
	 * @return string
	 */
	private function get_term_name_with_parents( \WP_Term $term, $max = 3 ) {
		if ( 0 === $term->parent ) {
			return $term->name;
		}

		$separator = is_rtl() ? ' < ' : ' > ';
		$test_term = $term;
		$names = array();

		while ( $test_term->parent > 0 ) {
			$test_term = get_term_by( 'term_taxonomy_id', $test_term->parent );

			if ( ! $test_term ) {
				break;
			}

			$names[] = $test_term->name;
		}

		$names = array_reverse( $names );

		if ( count( $names ) < ( $max ) ) {
			return implode( $separator, $names ) . $separator . $term->name;
		}

		$name_string = '';

		for ( $i = 0; $i < ( $max - 1 ); $i++ ) {
			$name_string .= $names[ $i ] . $separator;
		}

		return $name_string . '...' . $separator . $term->name;
	}

	/**
	 * @param \WP_User $user
	 * @param string $display
	 * @param array $data
	 * @param string $filter_name
	 *
	 * @return string
	 */
	private function format_user_for_display( $user, $display, $data, $filter_name = 'get_autocomplete' ) {
		switch ( $display ) {
			case 'minimal':
				$text = $user->display_name;

				break;
			case 'detailed':
				$text = sprintf( '%1$s (%2$s)', $user->display_name, $user->user_email );

				break;
			default:
				$text = apply_filters( "cmsmasters_elementor/query/{$filter_name}/display/{$display}", $user, $data );
		}

		return $text;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function ajax_wp_query_control_filter_autocomplete( $data ) {
		$query_data = $this->autocomplete_query_data( $data );

		if ( is_wp_error( $query_data ) ) {
			/** @var \WP_Error $query_data */
			throw new \Exception( $query_data->get_error_code() . ':' . $query_data->get_error_message() );
		}

		$query_args = $query_data['query'];
		$display = $query_data['display'];

		$results = array();

		switch ( $query_data['object'] ) {
			case self::POST_OBJECT:
			case self::MEDIA_OBJECT:
				$query = new \WP_Query( $query_args );

				foreach ( $query->posts as $post ) {
					if ( apply_filters( "cmsmasters_elementor/query/get_autocomplete/custom/{$display}", true, $post, $data ) ) {
						$results[] = array(
							'id' => $post->ID,
							'text' => $this->format_post_for_display( $post, $display, $data ),
						);
					}
				}

				break;
			case self::TAX_OBJECT:
				$terms = get_terms( $query_args );

				if ( is_wp_error( $terms ) ) {
					break;
				}

				$by_field = Utils::get_if_not_empty( $query_data, 'by_field', 'term_taxonomy_id' );

				foreach ( $terms as $term ) {
					if ( apply_filters( "cmsmasters_elementor/query/get_autocomplete/tax/{$display}", true, $term, $data ) ) {
						$results[] = array(
							'id' => $term->{$by_field},
							'text' => $this->get_term_name( $term, $display, $data ),
						);
					}
				}

				break;
			case self::USER_OBJECT:
			case self::AUTHOR_OBJECT:
				$user_query = new \WP_User_Query( $query_args );

				foreach ( $user_query->get_results() as $user ) {
					if ( apply_filters( "cmsmasters_elementor/query/get_autocomplete/user/{$display}", true, $user, $data ) ) {
						$results[] = array(
							'id' => $user->ID,
							'text' => $this->format_user_for_display( $user, $display, $data ),
						);
					}
				}

				break;
			case self::TEMPLATE_OBJECT:
				$query = new \WP_Query( $query_args );

				foreach ( $query->posts as $post ) {
					$document = Plugin::elementor()->documents->get( $post->ID );

					if ( $document ) {
						$results[] = array(
							'id' => $post->ID,
							'text' => sprintf( '%1$s (%2$s)', $post->post_title, $document->get_post_type_title() ),
						);
					}
				}

				break;
			default:
				$results = apply_filters( "cmsmasters_elementor/query/get_autocomplete/{$query_data['filter_type']}", $results, $data );
		}

		return array( 'results' => $results );
	}

	/**
	 * 'autocomplete' => array(
	 *   'object' => 'post|tax|user|library_template|attachment|js', // required
	 *   'display' => 'minimal(default)|detailed|custom_filter_name',
	 *   'by_field' => 'term_taxonomy_id(default)|term_id', // relevant only if `object` is tax|cpt_tax
	 *   'query' => array(
	 *       'post_type' => 'any|post|page|custom-post-type', // can be an array for multiple post types.
	 *         'any' should not be used if 'object' is 'tax' or 'cpt_tax'.
	 *         ...
	 *    ),
	 * ),
	 *
	 * 'object' (required): the queried object.
	 * Supported values:
	 *   'post': will use WP_Query(), if query['post_type'] is empty or
	 *     missing, will default to 'any'.
	 *   'tax': will use get_terms(), if 'post_type' is provided, will
	 *     first use get_object_taxonomies() to build 'taxonomy' args
	 *     then invoke get_terms(), if both 'taxonomy' and 'post_type'
	 *     are provided, 'post_type' is ignored.
	 *   'cpt_tax': Used in frontend only, will be replaced to 'tax' by js.
	 *     Will use get_object_taxonomies() to build 'taxonomy' args then use get_terms().
	 *   'user': will use WP_User_Query() with the args defined in 'query'.
	 *   'author': will use WP_User_Query() with pre-defined args.
	 *   'library_template': will use WP_Query() with Source_Local::CPT post_type.
	 *   'attachment': will use WP_Query() with attachment post_type.
	 *   'js': query data is populated by JavaScript. By the time the data is
	 *     sent to the server, the 'object' value should be replaced with one of
	 *     the other valid 'object' values and the Query array populated accordingly.
	 *   user_defined: will invoke apply_filters() using the user_defined value as filter
	 *     name `elementor/query/[get_value_titles|get_autocomplete]/{user_defined}`.
	 *
	 * 'display': output format
	 * Supported values:
	 *   'minimal' (default): name only
	 *   'detailed': for Post & Taxonomies -> `[Taxonomy|Post-Type]: [parent] ... [parent] > name`
	 *     for Users & Authors -> `name ([email])`
	 *   user_defined: will invoke apply_filters using the user_defined value as filter
	 *     name, `elementor/query/[get_value_titles|get_autocomplete]/display/{user_defined}`
	 *
	 * `by_field`: value of 'id' field in taxonomy query. Relevant only if `object` is tax|cpt_tax
	 * Supported values:
	 *   'term_taxonomy_id' (default)
	 *   'term_id'
	 *
	 * 'query': array of args to be passed "as-is" to the relevant query function (see 'object').
	 *
	 * @param array $data
	 *
	 * @return array | \WP_Error
	 */
	private function autocomplete_query_data( $data ) {
		if (
			empty( $data['autocomplete'] ) ||
			empty( $data['q'] ) ||
			empty( $data['autocomplete']['object'] )
		) {
			return new \WP_Error( self::AUTOCOMPLETE_ERROR_CODE, 'Empty or incomplete data' );
		}

		$autocomplete = $data['autocomplete'];

		if ( in_array( $autocomplete['object'], self::$supported_query_objects, true ) ) {
			$method_name = 'autocomplete_query_for_' . $autocomplete['object'];

			if ( empty( $autocomplete['display'] ) ) {
				$autocomplete['display'] = 'minimal';

				$data['autocomplete'] = $autocomplete;
			}

			$query = $this->$method_name( $data );

			if ( is_wp_error( $query ) ) {
				return $query;
			}

			$autocomplete['query'] = $query;
		}

		if ( empty( $autocomplete['query']['post_status'] ) ) {
			switch ( $autocomplete['object'] ) {
				case self::POST_OBJECT:
				case self::TEMPLATE_OBJECT:
					$autocomplete['query']['post_status'] = 'publish';

					break;
			}
		}

		return $autocomplete;
	}

	private function autocomplete_query_for_post( $data ) {
		if ( ! isset( $data['autocomplete']['query'] ) ) {
			return new \WP_Error( self::AUTOCOMPLETE_ERROR_CODE, 'Missing autocomplete[`query`] data' );
		}

		$query = $data['autocomplete']['query'];

		if ( empty( $query['post_type'] ) ) {
			$query['post_type'] = 'any';
		}

		$query['s'] = $data['q'];
		$query['posts_per_page'] = -1;

		return $query;
	}

	private function autocomplete_query_for_attachment( $data ) {
		$query = $this->autocomplete_query_for_post( $data );

		if ( is_wp_error( $query ) ) {
			return $query;
		}

		$query['post_type'] = 'attachment';
		$query['post_status'] = 'inherit';

		return $query;
	}

	private function autocomplete_query_for_tax( $data ) {
		$query = $data['autocomplete']['query'];

		if ( empty( $query['taxonomy'] ) && ! empty( $query['post_type'] ) ) {
			$query['taxonomy'] = get_object_taxonomies( $query['post_type'] );
		}

		$query['search'] = $data['q'];
		$query['hide_empty'] = false;

		return $query;
	}

	private function autocomplete_query_for_user( $data ) {
		$autocomplete = $data['autocomplete'];
		$query = $autocomplete['query'];

		if ( ! empty( $query ) ) {
			return $query;
		}

		$query = array(
			'fields' => array( 'ID', 'display_name' ),
			'search_columns' => array(
				'user_login',
				'user_nicename',
			),
			'search' => "*{$data['q']}*",
		);

		if ( 'detailed' === $autocomplete['display'] ) {
			$query['fields'][] = 'user_email';
		}

		return $query;
	}

	private function autocomplete_query_for_author( $data ) {
		$query = $this->autocomplete_query_for_user( $data );

		if ( is_wp_error( $query ) ) {
			return $query;
		}

		$query['who'] = 'authors';

		return $query;
	}

	private function autocomplete_query_for_library_template( $data ) {
		$query = $data['autocomplete']['query'];

		$query['post_type'] = Source_Local::CPT;
		$query['orderby'] = 'meta_value';
		$query['order'] = 'ASC';

		if ( empty( $query['posts_per_page'] ) ) {
			$query['posts_per_page'] = -1;
		}

		$query['s'] = $data['q'];

		return $query;
	}

	public function filter_editor_settings( $settings ) {
		$settings = array_replace_recursive( $settings, array(
			'i18n' => array( 'all' => __( 'All', 'cmsmasters-elementor' ) ),
		) );

		return $settings;
	}

	/**
	 * @param Base_Widget $widget
	 * @param string $name
	 * @param array $query_args
	 * @param array $fallback_args
	 *
	 * @return \WP_Query
	 */
	public function get_query( $widget, $name, $query_args = array(), $fallback_args = array() ) {

		$view = $widget->get_settings( "{$name}_orderby" ) === 'cmsmasters_pm_view';
		$like = $widget->get_settings( "{$name}_orderby" ) === 'cmsmasters_pm_like';

		switch ( $widget->get_settings( "{$name}_post_type" ) ) {
			case 'related':
				$query_instance = new Query\Related( $widget, $name, $query_args, $fallback_args );

				break;
			default:
				$query_instance = new Query\Post( $widget, $name, $query_args );
		}

		if ( $view || $like ) {
			$query_instance = new Query\Popular( $widget, $name, $query_args, $query_instance );
		}
		

		return $query_instance->get_query();
	}

	public static function add_to_displayed_ids( $ids ) {
		$ids = array_merge( self::$displayed_ids, $ids );

		self::$displayed_ids = array_unique( $ids );
	}

	public static function get_displayed_ids() {
		return self::$displayed_ids;
	}
}
