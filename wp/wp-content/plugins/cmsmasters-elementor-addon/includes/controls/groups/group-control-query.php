<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;
use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Group_Control_Query extends Group_Control_Base {

	protected static $fields = array();

	protected static $presets = array();

	protected static $default_condition = array();

	/**
	 * Get group control type.
	 *
	 * Retrieve the group control type.
	 *
	 * @since 1.0.0
	 *
	 * @return string Group control type.
	 */
	public static function get_type() {
		return CmsmastersControls::QUERY_GROUP;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the group control. Used to return the
	 * default options while initializing the group control.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default group control options.
	 */
	protected function get_default_options() {
		return array( 'popover' => false );
	}

	/**
	 * Get child default arguments.
	 *
	 * Retrieve the default arguments for all the child controls for
	 * a specific group control.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default arguments for all the child controls.
	 */
	protected function get_child_default_args() {
		$args = parent::get_child_default_args();

		$args['presets'] = array( 'full' );

		return $args;
	}

	/**
	 * Init arguments.
	 *
	 * Initializing group control arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Group control arguments.
	 */
	protected function init_args( $arguments ) {
		parent::init_args( $arguments );

		static::init_fields_by_prefix( $this->get_controls_prefix() );
	}

	/**
	 * Init fields.
	 *
	 * Initialize group control fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array Group control fields
	 */
	protected function init_fields() {
		if ( empty( static::$fields ) ) {
			static::init_fields_by_prefix( $this->get_controls_prefix() );
		}

		return static::$fields;
	}

	/**
	 * Init prefixed fields.
	 *
	 * Initialize group control prefixed fields.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix Group control prefix.
	 *
	 * @return array Group control fields.
	 */
	protected static function init_fields_by_prefix( $prefix ) {
		static::$fields['post_type'] = array(
			'label' => __( 'Source', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'manual_selection' => __( 'Manual Selection', 'cmsmasters-elementor' ),
				'current_query' => __( 'Current Query', 'cmsmasters-elementor' ),
				'custom_args' => __( 'Custom Arguments', 'cmsmasters-elementor' ),
			),
		);

		// Manual posts selection control
		static::$fields['posts_in'] = array(
			'label' => __( 'Manual Selection', 'cmsmasters-elementor' ),
			'label_block' => true,
			'show_label' => false,
			'type' => CmsmastersControls::QUERY,
			'description' => __( 'Search & select entries to show.', 'cmsmasters-elementor' ),
			'options' => array(),
			'multiple' => true,
			'autocomplete' => array(
				'object' => Query_Manager::POST_OBJECT,
				'display' => 'detailed',
			),
			'condition' => array( 'post_type' => 'manual_selection' ),
			'export' => false,
		);

		static::$default_condition = array(
			'post_type!' => array(
				'manual_selection',
				'current_query',
				'custom_args',
			),
		);

		// Include & Exclude query controls
		static::init_include_exclude_fields( $prefix );

		// Author controls
		static::init_author_fields();

		// Date query controls
		static::init_date_fields();

		// Sort, order & pagination controls
		static::init_order_paging_fields();

		// Miscellaneous controls
		static::init_misc_fields();

		static::init_presets();
	}

	private static function init_include_exclude_fields( $prefix ) {
		static::$fields['query_args'] = array( 'type' => Controls_Manager::TABS );

		// Include tab query controls
		static::init_include_tab_fields( $prefix );

		// Exclude tab query controls
		static::init_exclude_tab_fields( $prefix );
	}

	private static function init_include_tab_fields( $prefix ) {
		$tabs_wrapper = "{$prefix}query_args";

		static::$fields['query_include'] = array(
			'type' => Controls_Manager::TAB,
			'label' => __( 'Include', 'cmsmasters-elementor' ),
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
		);

		static::$fields['include_term_ids'] = array(
			'label' => __( 'Terms', 'cmsmasters-elementor' ),
			'label_block' => true,
			'type' => CmsmastersControls::QUERY,
			'description' => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'cmsmasters-elementor' ),
			'options' => array(),
			'multiple' => true,
			'autocomplete' => array(
				'object' => Query_Manager::CPT_TAX_OBJECT,
				'display' => 'detailed',
			),
			'group_prefix' => $prefix,
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab' => "{$prefix}query_include",
			'export' => false,
		);
	}

	private static function init_exclude_tab_fields( $prefix ) {
		$tabs_wrapper = "{$prefix}query_args";

		static::$fields['query_exclude'] = array(
			'type' => Controls_Manager::TAB,
			'label' => __( 'Exclude', 'cmsmasters-elementor' ),
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
		);

		$inner_wrapper = "{$prefix}query_exclude";

		static::$fields['exclude_term_ids'] = array(
			'label' => __( 'Terms', 'cmsmasters-elementor' ),
			'label_block' => true,
			'type' => CmsmastersControls::QUERY,
			'description' => __( 'Terms are items in a taxonomy. The available taxonomies are: Categories, Tags, Formats and custom taxonomies.', 'cmsmasters-elementor' ),
			'options' => array(),
			'multiple' => true,
			'autocomplete' => array(
				'object' => Query_Manager::CPT_TAX_OBJECT,
				'display' => 'detailed',
			),
			'group_prefix' => $prefix,
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab' => $inner_wrapper,
			'export' => false,
		);

		static::$fields['posts_not_in'] = array(
			'label' => __( 'Manual Selection', 'cmsmasters-elementor' ),
			'label_block' => true,
			'type' => CmsmastersControls::QUERY,
			'description' => __( 'Search & select entries to exclude from query.', 'cmsmasters-elementor' ),
			'options' => array(),
			'multiple' => true,
			'autocomplete' => array(
				'object' => Query_Manager::POST_OBJECT,
				'display' => 'detailed',
			),
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab' => $inner_wrapper,
			'export' => false,
		);

		static::$fields['ignore_sticky_posts'] = array(
			'label' => __( 'Ignore Sticky Posts', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SWITCHER,
			'description' => sprintf( '<strong>%s</strong>', __( 'Note: Sticky posts are visible on frontend only.', 'cmsmasters-elementor' ) ),
			'default' => 'yes',
			'condition' => array( 'post_type' => 'post' ),
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab' => $inner_wrapper,
		);

		static::$fields['current_post'] = array(
			'label' => __( 'Exclude Current Post', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SWITCHER,
			'description' => __( 'Set to Yes to exclude current post from this query.', 'cmsmasters-elementor' ),
			'default' => '',
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab' => $inner_wrapper,
		);

		static::$fields['offset'] = array(
			'label' => __( 'Offset', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::NUMBER,
			'description' => __( 'Use this parameter to skip the first few query posts (e.g. set \'3\' to skip the first three posts of the query).', 'cmsmasters-elementor' ),
			'default' => 0,
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab' => $inner_wrapper,
		);

		static::$fields['prevent_duplicates'] = array(
			'label' => __( 'Prevent Duplicates', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SWITCHER,
			'description' => sprintf(
				/* translators: Addon query control group 'Prevent Duplicates' control description. %s: Note */
				__( 'Set to Yes to exclude duplicate posts on one page. %s', 'cmsmasters-elementor' ),
				sprintf( '<strong>%s</strong>', __( 'Note: Affects only the frontend.', 'cmsmasters-elementor' ) )
			),
			'default' => '',
			'condition' => static::$default_condition,
			'tabs_wrapper' => $tabs_wrapper,
			'inner_tab' => $inner_wrapper,
		);
	}

	private static function init_author_fields() {
		static::$fields['author_query'] = array(
			'label' => __( 'Author', 'cmsmasters-elementor' ),
			'label_block' => false,
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'options' => array(
				'include' => array(
					'title' => __( 'Include', 'cmsmasters-elementor' ),
					'description' => __( 'Show only posts associated with selected authors.', 'cmsmasters-elementor' ),
				),
				'exclude' => array(
					'title' => __( 'Exclude', 'cmsmasters-elementor' ),
					'description' => __( 'Show all posts, except associated with selected authors.', 'cmsmasters-elementor' ),
				),
			),
			'default' => '',
			'toggle' => true,
			'separator' => 'before',
			'condition' => static::$default_condition,
		);

		static::$fields['selected_authors'] = array(
			'label' => __( 'Authors', 'cmsmasters-elementor' ),
			'label_block' => true,
			'show_label' => false,
			'type' => CmsmastersControls::QUERY,
			'description' => sprintf( '<strong>%s</strong>', __( 'Note: Show all posts if no authors are selected.', 'cmsmasters-elementor' ) ),
			'options' => array(),
			'default' => array(),
			'multiple' => true,
			'autocomplete' => array(
				'object' => Query_Manager::AUTHOR_OBJECT,
				'display' => 'detailed',
			),
			'placeholder' => __( 'No authors selected', 'cmsmasters-elementor' ),
			'condition' => array_merge( static::$default_condition, array( 'author_query!' => '' ) ),
			'export' => false,
		);
	}

	private static function init_date_fields() {
		static::$fields['select_date'] = array(
			'label' => __( 'Date period', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => array(
				'anytime' => __( 'Anytime', 'cmsmasters-elementor' ),
				'day' => __( 'Past Day(s)', 'cmsmasters-elementor' ),
				'week' => __( 'Past Week(s)', 'cmsmasters-elementor' ),
				'month'  => __( 'Past Month(s)', 'cmsmasters-elementor' ),
				'year' => __( 'Past Year', 'cmsmasters-elementor' ),
				'exact' => __( 'Custom', 'cmsmasters-elementor' ),
			),
			'default' => 'anytime',
			'multiple' => false,
			'separator' => 'before',
			'condition' => static::$default_condition,
		);

		static::$fields['date_count'] = array(
			'label' => __( 'Count', 'cmsmasters-elementor' ),
			'label_block' => true,
			'show_label' => false,
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'description' => __( 'Choose the count of days/weeks/months ago.', 'cmsmasters-elementor' ),
			'description' => __( 'Define period in the number of days/weeks/months.', 'cmsmasters-elementor' ),
			'options' => array(
				'1' => array(
					'title' => '1',
					'description' => __( 'One', 'cmsmasters-elementor' ),
				),
				'2' => array(
					'title' => '2',
					'description' => __( 'Two', 'cmsmasters-elementor' ),
				),
				'3' => array(
					'title' => '3',
					'description' => __( 'Three', 'cmsmasters-elementor' ),
				),
				'4' => array(
					'title' => '4',
					'description' => __( 'Four', 'cmsmasters-elementor' ),
				),
				'5' => array(
					'title' => '5',
					'description' => __( 'Five', 'cmsmasters-elementor' ),
				),
			),
			'default' => '1',
			'condition' => array_merge( static::$default_condition, array(
				'select_date' => array(
					'day',
					'week',
					'month',
				),
			) ),
		);

		static::$fields['date_after'] = array(
			'label' => __( 'After', 'cmsmasters-elementor' ),
			'label_block' => false,
			'type' => Controls_Manager::DATE_TIME,
			'placeholder' => __( 'Choose after date', 'cmsmasters-elementor' ),
			'description' => __( 'Setting an ‘After’ date will show all the posts published since the chosen date (inclusive).', 'cmsmasters-elementor' ),
			'condition' => array_merge( static::$default_condition, array( 'select_date' => 'exact' ) ),
		);

		static::$fields['date_before'] = array(
			'label' => __( 'Before', 'cmsmasters-elementor' ),
			'label_block' => false,
			'type' => Controls_Manager::DATE_TIME,
			'placeholder' => __( 'Choose before date', 'cmsmasters-elementor' ),
			'description' => __( 'Setting a ‘Before’ date will show all the posts published until the chosen date (inclusive).', 'cmsmasters-elementor' ),
			'condition' => array_merge( static::$default_condition, array( 'select_date' => 'exact' ) ),
		);
	}

	private static function init_order_paging_fields() {
		$order_by_options = array(
			'date' => __( 'Date', 'cmsmasters-elementor' ),
			'title' => __( 'Title', 'cmsmasters-elementor' ),
			'comment_count' => __( 'Comments Number', 'cmsmasters-elementor' ),
			'author' => __( 'Author', 'cmsmasters-elementor' ),
			'type' => __( 'Post Type', 'cmsmasters-elementor' ),
			'modified' => __( 'Date Modified', 'cmsmasters-elementor' ),
			'menu_order' => __( 'Menu Order', 'cmsmasters-elementor' ),
			'rand' => __( 'Random', 'cmsmasters-elementor' ),
			'cmsmasters_pm_view' => __( 'View', 'cmsmasters-elementor' ),
			'cmsmasters_pm_like' => __( 'Like', 'cmsmasters-elementor' ),
		);

		$condition_not_custom = array(
			'post_type!' => array(
				// 'current_query',
				'custom_args',
			),
		);

		/**
		 * Query control group order by options.
		 *
		 * Filters the Query control group orderby control options list.
		 *
		 * @since 1.0.0
		 *
		 * @param array $order_by_options Options list.
		 */
		$order_by_options = apply_filters( 'cmsmasters_elementor/controls/groups/query/order_by_options', $order_by_options );

		// Sort & order controls
		static::$fields['orderby'] = array(
			'label' => __( 'Order By', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'options' => $order_by_options,
			'default' => 'date',
			'separator' => 'before',
			'condition' => $condition_not_custom,
		);

		static::$fields['order'] = array(
			'label' => __( 'Order', 'cmsmasters-elementor' ),
			'label_block' => true,
			'show_label' => false,
			'type' => CmsmastersControls::CHOOSE_TEXT,
			'options' => array(
				'asc' => array(
					'title' => __( 'ASC', 'cmsmasters-elementor' ),
					'description' => __( 'Ascending', 'cmsmasters-elementor' ),
				),
				'desc' => array(
					'title' => __( 'DESC', 'cmsmasters-elementor' ),
					'description' => __( 'Descending', 'cmsmasters-elementor' ),
				),
			),
			'default' => 'desc',
			'condition' => $condition_not_custom,
		);

		// Pagination & ignore sticky controls
		static::$fields['posts_per_page'] = array(
			'label' => __( 'Posts Per Page', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::NUMBER,
			'default' => 3,
			'condition' => $condition_not_custom,
		);
	}

	private static function init_misc_fields() {
		// Server side filtering query ID
		static::$fields['filter_id'] = array(
			'label' => __( 'Filter ID', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::TEXT,
			'description' => __( 'You can give a custom ID for that Query to allow server-side filtering of this widget.', 'cmsmasters-elementor' ),
			'default' => '',
			'separator' => 'before',
			'condition' => array( 'post_type!' => 'custom_args' ),
		);

		// Specific query with any supported WP_Query arguments
		static::init_custom_args_fields();
	}

	private static function init_custom_args_fields() {
		$code_description = sprintf(
			/* translators: Addon query control group 'Custom Query Arguments' control description. %s: WP_Query */
			__( 'Here you can add completely custom %s arguments array with any supported parameters.', 'cmsmasters-elementor' ),
			sprintf(
				'<a href="%2$s" target="_blank">%1$s</a>',
				__( 'WP_Query', 'cmsmasters-elementor' ),
				'https://developer.wordpress.org/reference/classes/wp_query/#parameters'
			)
		);

		$code_default = '<?php' . PHP_EOL .
			'array(' . PHP_EOL .
			'	\'post_type\' => \'post\',' . PHP_EOL .
			'	\'orderby\' => \'date\',' . PHP_EOL .
			'	\'order\' => \'DESC\',' . PHP_EOL .
			');' . PHP_EOL;

		static::$fields['custom_args'] = array(
			'label' => __( 'Custom Query Arguments', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::CODE,
			'description' => $code_description,
			'default' => $code_default,
			'language' => 'php',
			'rows' => 20,
			'separator' => 'before',
			'condition' => array( 'post_type' => 'custom_args' ),
		);

		$info_text = sprintf(
			/* translators: Addon query control group 'Custom Query Arguments' control info box. %s: Supported services links */
			__( 'Note: You can write Query arguments array like showed in default example before or use one of the following online generators: %s.', 'cmsmasters-elementor' ),
			static::generate_custom_args_info_links()
		);

		static::$fields['custom_args_info'] = array(
			'label_block' => true,
			'show_label' => false,
			'type' => Controls_Manager::RAW_HTML,
			'raw' => $info_text,
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			'condition' => array( 'post_type' => 'custom_args' ),
		);
	}

	private static function generate_custom_args_info_links() {
		$supported_services = array(
			array(
				'title' => 'GenerateWP',
				'link' => 'https://generatewp.com/wp_query/',
			),
			array(
				'title' => 'CrocoBlock',
				'link' => 'https://crocoblock.com/freemium/tools/wp-query-generator/',
			),
			array(
				'title' => 'Hasty',
				'link' => 'https://www.wp-hasty.com/tools/wordpress-wp-query-loop-generator/',
			),
			array(
				'title' => 'NimbusThemes',
				'link' => 'https://www.nimbusthemes.com/wp_query-wordpress-snippet-generator/',
			),
		);

		$links = array();

		foreach ( $supported_services as $service ) {
			$links[] = sprintf( '<a href="%2$s" target="_blank">%1$s</a>', $service['title'], $service['link'] );
		}

		$last_link = array_pop( $links );

		$info_links = sprintf( '%1$s %2$s %3$s', implode( ', ', $links ), __( 'or', 'cmsmasters-elementor' ), $last_link );

		return $info_links;
	}

	/**
	 * Presets: Filters the controls subsets to be used by the
	 * specific Group_Control_Query instance.
	 *
	 * Possible values:
	 *   'full': (default) all presets
	 *   'author': the author include/exclude controls
	 *   'include': extend the 'author' preset by 'include' tab taxonomy
	 *   'exclude': extend the 'author' preset by 'exclude' tab
	 *     terms & 'manual selection'
	 *   'advanced_exclude': extend the 'exclude' preset with
	 *     'ignore_sticky_posts', 'current_post',
	 *     'prevent_duplicates' & 'offset'
	 *   'date': date query controls
	 *   'order': sort & ordering controls
	 *   'pagination': posts per-page
	 *   'filter_id': allows setting a specific query filter name
	 *     for future usage
	 *   'custom_args': allows setting a specific query with any
	 *     supported WP_Query arguments
	 *
	 * Usage:
	 *   'full': build a Group_Controls_Query with all possible
	 *     controls, when 'full' is passed, the Group_Controls_Query
	 *     will ignore all other preset values.
	 *   $this->add_group_control(
	 *     Group_Control_Query::get_type(),
	 *     array(
	 *       ...
	 *       'presets' => array( 'full' ),
	 *       ...
	 *     )
	 *   );
	 *
	 * Subset: build a Group_Controls_Query with subset of the controls
	 *   in the following example, the Query controls will set only
	 *   the 'include', 'order' & 'pagination' query args.
	 *   $this->add_group_control(
	 *     Group_Control_Query::get_type(),
	 *     array(
	 *       ...
	 *       'presets' => array(
	 *         'include',
	 *         'order',
	 *         'pagination'
	 *       ),
	 *       ...
	 *     )
	 *   );
	 */
	protected static function init_presets() {
		$basic = array(
			'query_args',
			'query_include',
			'query_exclude',
		);

		static::$presets['author'] = array_merge(
			$basic,
			array(
				'author_query',
				'selected_authors',
			)
		);

		static::$presets['include'] = array_merge(
			static::$presets['author'],
			array(
				'posts_in',
				'include_term_ids',
			)
		);

		static::$presets['exclude'] = array_merge(
			static::$presets['author'],
			array(
				'posts_not_in',
				'exclude_term_ids',
			)
		);

		static::$presets['advanced_exclude'] = array_merge(
			static::$presets['exclude'],
			array(
				'ignore_sticky_posts',
				'current_post',
				'prevent_duplicates',
				'offset',
			)
		);

		static::$presets['date'] = array(
			'select_date',
			'date_count',
			'date_after',
			'date_before',
		);

		static::$presets['order'] = array(
			'orderby',
			'order',
		);

		static::$presets['pagination'] = array( 'posts_per_page' );

		static::$presets['filter_id'] = array( 'filter_id' );

		static::$presets['custom_args'] = array( 'custom_args' );
	}

	/**
	 * Prepare fields.
	 *
	 * Process group control fields before adding them to `add_control()`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Group control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		$args = $this->get_args();

		if ( ! empty( $args['presets'] ) ) {
			$fields = $this->filter_fields_by_presets( $fields, $args['presets'] );
		}

		$post_type_args = array();

		if ( ! empty( $args['post_type'] ) ) {
			$post_type_args['post_type'] = $args['post_type'];
		}

		$post_types = Utils::get_public_post_types( $post_type_args );

		// Extend post_type field options with public post types list
		$fields['post_type']['options'] = array_merge( $post_types, $fields['post_type']['options'] );

		// Set post_type field default to first public post type
		$fields['post_type']['default'] = key( $post_types );

		// Set posts_in field object_type to public post types list
		$fields['posts_in']['object_type'] = array_keys( $post_types );

		return parent::prepare_fields( $fields );
	}

	private function filter_fields_by_presets( $fields, $presets ) {
		if ( in_array( 'full', $presets, true ) ) {
			return $fields;
		}

		$control_ids = array();

		foreach ( static::$presets as $static_preset ) {
			$control_ids = array_merge( $control_ids, $static_preset );
		}

		foreach ( $presets as $preset ) {
			if ( ! array_key_exists( $preset, static::$presets ) ) {
				continue;
			}

			$control_ids = array_diff( $control_ids, static::$presets[ $preset ] );
		}

		foreach ( $control_ids as $name ) {
			unset( $fields[ $name ] );
		}

		return $fields;
	}

}
