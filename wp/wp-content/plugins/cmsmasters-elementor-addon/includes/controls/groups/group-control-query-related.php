<?php
namespace CmsmastersElementor\Controls\Groups;

use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Controls\Groups\Group_Control_Query;
use CmsmastersElementor\Modules\Wordpress\Managers\Query_Manager;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Group_Control_Query_Related extends Group_Control_Query {

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
		return CmsmastersControls::QUERY_RELATED_GROUP;
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
		parent::init_fields_by_prefix( $prefix );

		static::$fields['post_type']['options']['related'] = __( 'Related', 'cmsmasters-elementor' );

		$hide_in_related = array(
			'include_term_ids',
			'current_post',
			'offset',
			'prevent_duplicates',
			'author_query',
			'selected_authors',
		);

		foreach ( $hide_in_related as $field ) {
			static::$fields[ $field ]['condition']['post_type!'][] = 'related';
		}

		$related_condition = array( 'post_type' => 'related' );

		static::$fields = ElementorUtils::array_inject(
			static::$fields,
			'include_term_ids',
			array(
				'related_taxonomies' => array(
					'label' => __( 'Taxonomy', 'cmsmasters-elementor' ),
					'label_block' => true,
					'type' => Controls_Manager::SELECT2,
					'options' => static::get_supported_taxonomies(),
					'multiple' => true,
					'condition' => $related_condition,
					'tabs_wrapper' => "{$prefix}query_args",
					'inner_tab' => "{$prefix}query_include",
					'export' => false,
				),
			)
		);

		static::$fields = ElementorUtils::array_inject(
			static::$fields,
			'posts_not_in',
			array(
				'current_post_message' => array(
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __( 'Note: Current Post excludes from related query automatically.', 'cmsmasters-elementor' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'condition' => $related_condition,
					'tabs_wrapper' => "{$prefix}query_args",
					'inner_tab' => "{$prefix}query_exclude",
				),
			)
		);

		static::$fields = ElementorUtils::array_inject(
			static::$fields,
			'selected_authors',
			array(
				'current_author' => array(
					'label' => __( 'Post Author Only', 'cmsmasters-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'description' => __( 'Show posts associated with current post authors.', 'cmsmasters-elementor' ),
					'separator' => 'before',
					'condition' => $related_condition,
				),
			)
		);

		$fallback_controls = array();

		$fallback_controls['related_fallback'] = array(
			'label' => __( 'Fallback', 'cmsmasters-elementor' ),
			'type' => Controls_Manager::SELECT,
			'description' => __( 'Choose what do you want to show if no relevant related results are found.', 'cmsmasters-elementor' ),
			'options' => array(
				'none' => __( 'None', 'cmsmasters-elementor' ),
				'manual_selection' => __( 'Manual Selection', 'cmsmasters-elementor' ),
				'recent' => __( 'Recent Posts', 'cmsmasters-elementor' ),
			),
			'default' => 'fallback_none',
			'separator' => 'before',
			'condition' => $related_condition,
		);

		$fallback_controls['fallback_posts_in'] = array(
			'label' => __( 'Search & Select', 'cmsmasters-elementor' ),
			'label_block' => true,
			'show_label' => false,
			'type' => CmsmastersControls::QUERY,
			'description' => __( 'Search & select entries to show.', 'cmsmasters-elementor' ),
			'options' => array(),
			'multiple' => true,
			'autocomplete' => array( 'object' => Query_Manager::POST_OBJECT ),
			'placeholder' => __( 'No posts selected', 'cmsmasters-elementor' ),
			'condition' => array_merge(
				$related_condition,
				array( 'related_fallback' => 'manual_selection' )
			),
			'export' => false,
		);

		$fallback_controls['fallback_posts_in_message'] = array(
			'type' => Controls_Manager::RAW_HTML,
			'raw' => __( 'Note: Manually selected posts order are random.', 'cmsmasters-elementor' ),
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			'condition' => array_merge(
				$related_condition,
				array( 'related_fallback' => 'manual_selection' )
			),
		);

		static::$fields = ElementorUtils::array_inject( static::$fields, 'current_author', $fallback_controls );
	}

	protected static function get_supported_taxonomies() {
		$supported_taxonomies = array();

		foreach ( array_keys( Utils::get_public_post_types() ) as $name ) {
			foreach ( get_object_taxonomies( $name, 'objects' ) as $key => $tax ) {
				if ( array_key_exists( $key, $supported_taxonomies ) ) {
					continue;
				}

				$label = $tax->label;

				if ( in_array( $label, $supported_taxonomies, true ) ) {
					$label = sprintf( '%1$s (%2$s)', $label, $tax->name );
				}

				$supported_taxonomies[ $key ] = $label;
			}
		}

		return $supported_taxonomies;
	}

	/**
	 * Presets: Extends the Group_Control_Query presets to filter the
	 * controls subsets to be used by the specific
	 * Group_Control_Query_Related instance.
	 *
	 * Changed possible values:
	 *   'author': extended with 'current_author' control
	 *   'include': extended with 'related_taxonomies' control
	 *
	 * Additional possible values:
	 *   'related' : related fallback & selected ID's controls
	 */
	protected static function init_presets() {
		parent::init_presets();

		static::$presets['author'][] = 'current_author';

		static::$presets['include'][] = 'related_taxonomies';

		static::$presets['related'] = array(
			'related_fallback',
			'fallback_posts_in',
		);
	}

}
