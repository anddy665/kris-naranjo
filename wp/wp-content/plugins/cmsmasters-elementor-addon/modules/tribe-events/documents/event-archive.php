<?php
namespace CmsmastersElementor\Modules\TribeEvents\Documents;

use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Modules\TemplatePages\Documents\Archive;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Document;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Event Archive documents.
 *
 * An abstract class that provides the needed properties and methods to
 * manage and handle Event Archive documents in inheriting classes.
 *
 * @since 1.13.0
 */
class Event_Archive extends Archive {

	use Tribe_Events_Document;

	/**
	 * Document post type meta key.
	 */
	const EVENTS_ARCHIVE_TEMPLATE_TYPE_META = '_cmsmasters_tribe_events_archive_post_type';

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.13.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'cmsmasters_tribe_events_archive';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.13.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Events Archive', 'cmsmasters-elementor' );
	}

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.13.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location_include'] = array(
			'tribe_events_archive',
			'post_tag',
			'tribe_events_cat',
		);

		$properties['locations_category'] = 'child';

		$properties = apply_filters( 'cmsmasters_elementor/documents/event_archive/get_properties', $properties );

		return $properties;
	}

	/**
	 * Get editor panel categories.
	 *
	 * Retrieve the list of categories the element belongs to.
	 *
	 * @since 1.13.0
	 *
	 * @return array Editor panel categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = array(
			self::TRIBE_EVENTS_ARCHIVE_WIDGETS_CATEGORY => array( 'title' => self::get_title() ),
			self::TRIBE_EVENTS_WIDGETS_CATEGORY => array(
				'title' => __( 'Tribe Events', 'cmsmasters-elementor' ),
				'active' => true,
			),
		);

		$categories += parent::get_editor_panel_categories();

		unset( $categories[ self::ARCHIVE_WIDGETS_CATEGORY ] );

		return $categories;
	}

	/**
	 * Save template type.
	 *
	 * Set document name as template type meta key.
	 *
	 * Set archive template post type meta key if selected.
	 *
	 * @since 1.13.0
	 */
	public function save_template_type() {
		parent::save_template_type();

		if ( ! empty( $_REQUEST[ self::EVENTS_ARCHIVE_TEMPLATE_TYPE_META ] ) ) {
			$template_type = $_REQUEST[ self::EVENTS_ARCHIVE_TEMPLATE_TYPE_META ];

			$this->update_meta( self::EVENTS_ARCHIVE_TEMPLATE_TYPE_META, $template_type );
		}
	}

	public static function get_archive_preview_type_options_choices( $woo = false, $tribe_events = true ) {
		$options = array();

		$post_type_object = get_post_type_object( TribeEventsModule::$post_type );

		if ( $post_type_object->has_archive ) {
			/* translators: Preview dynamic content field archive options. %s: Post type name */
			$options[ 'post_type_archive/' . TribeEventsModule::$post_type ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $post_type_object->label );
		}

		$object_taxonomies = get_object_taxonomies( TribeEventsModule::$post_type, 'objects' );
		$filtered_taxonomies_object = wp_filter_object_list( $object_taxonomies, array(
			'public' => true,
			'show_in_nav_menus' => true,
		) );

		foreach ( $filtered_taxonomies_object as $slug => $object ) {
			$label = $object->label;

			if ( 'tribe_events_cat' === $slug ) {
				$label = __( 'Event Category', 'cmsmasters-elementor' );
			} elseif ( 'post_tag' === $slug ) {
				$label = __( 'Event Tag', 'cmsmasters-elementor' );
			}

			/* translators: Preview dynamic content field archive options. %s: Taxonomy name */
			$options[ "taxonomy/{$slug}" ] = sprintf( __( '%s archive', 'cmsmasters-elementor' ), $label );
		}

		return array(
			'archive' => array(
				'label' => __( 'Events Archive', 'cmsmasters-elementor' ),
				'options' => $options,
			),
			'page/search' => __( 'Events search results', 'cmsmasters-elementor' ),
		);
	}

	protected function set_default_preview() {
		if ( ! empty( $this->preview_type_default ) ) {
			return;
		}

		$template_type = $this->get_main_meta( self::EVENTS_ARCHIVE_TEMPLATE_TYPE_META );

		list( $rule_type, $rule_subtype ) = array_pad( explode( '/', $template_type ), 2, '' );

		$posts_args = array(
			'fields' => 'ids',
			'numberposts' => 1,
		);

		switch ( $rule_type ) {
			case 'taxonomy':
				$taxonomy_ids = get_terms( array(
					'taxonomy' => $rule_subtype,
					'fields' => 'ids',
					'orderby' => 'count',
					'order' => 'DESC',
					'number' => 1,
				) );

				if ( ! empty( $taxonomy_ids ) ) {
					$this->preview_type_default = $template_type;
					$this->preview_id_default = $taxonomy_ids[0];
				}

				break;
			case 'page':
				if ( 'search' === $rule_subtype ) {
					$posts_args['s'] = '';
					$recent_posts = get_posts( $posts_args );

					if ( ! empty( $recent_posts ) ) {
						$this->preview_type_default = $template_type;
					}

					break;
				} else {
					$template_type = sprintf( 'post_type_archive/%s', TribeEventsModule::$post_type );
				}
			case 'post_type_archive':
			default:
				$posts_args['post_type'] = TribeEventsModule::$post_type;
				$recent_posts = get_posts( $posts_args );

				if ( ! empty( $recent_posts ) ) {
					$this->preview_type_default = $template_type;
				}
		}
	}

	public function get_locations_default() {
		$default_locations = parent::get_locations_default();

		if ( ! empty( $default_locations ) ) {
			return $default_locations;
		}

		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$rules_manager = $locations_module->get_rules_manager();

		$template_type = $this->get_main_meta( self::EVENTS_ARCHIVE_TEMPLATE_TYPE_META );

		list( $rule_type, $rule_subtype ) = array_pad( explode( '/', $template_type ), 2, '' );

		switch ( $rule_type ) {
			case 'post_type_archive':
				$rule_subtype = "{$rule_subtype}_archive";

				break;
			case 'page':
				if ( 'shop' === $rule_subtype ) {
					$rule_subtype = "{$rule_subtype}_{$rule_type}";
				} else {
					$rule_subtype = sprintf( "%s_{$rule_subtype}", TribeEventsModule::$post_type );
				}

				break;

		}

		if ( $rules_manager->get_rule_instance( $rule_subtype ) ) {
			$default_locations[] = array(
				'stmt' => 'include',
				'main' => 'archive',
				'addl' => $rule_subtype,
			);
		}

		return $default_locations;
	}

}
