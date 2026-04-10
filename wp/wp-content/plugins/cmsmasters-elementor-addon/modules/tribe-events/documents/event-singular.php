<?php
namespace CmsmastersElementor\Modules\TribeEvents\Documents;

use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Modules\TemplatePages\Documents\Singular;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Document;

use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Event_Singular extends Singular {

	use Tribe_Events_Document;

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
		return 'cmsmasters_tribe_events_singular';
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
		return __( 'Event Singular', 'cmsmasters-elementor' );
	}

	public static $widgets_visibility = array(
		'Event_Content' => true,
		'Event_Venue_Map' => true,
	);

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
			'tribe_events',
			'tribe_venue',
			'tribe_organizer',
		);

		$properties['locations_category'] = 'child';

		$properties = apply_filters( 'cmsmasters_elementor/documents/event_singular/get_properties', $properties );

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
			self::TRIBE_EVENTS_SINGULAR_WIDGETS_CATEGORY => array( 'title' => self::get_title() ),
			self::TRIBE_EVENTS_WIDGETS_CATEGORY => array(
				'title' => __( 'Tribe Events', 'cmsmasters-elementor' ),
				'active' => true,
			),
		);

		$categories += parent::get_editor_panel_categories();

		unset( $categories[ self::SINGULAR_WIDGETS_CATEGORY ] );

		return $categories;
	}

	/**
	 * Get container attributes.
	 *
	 * Retrieve the document container attributes.
	 *
	 * @since 1.13.0
	 *
	 * @return string Container attributes.
	 */
	public function get_container_attributes() {
		$attributes = parent::get_container_attributes();

		if ( is_singular() ) {
			$attributes['class'] .= ' tribe_events_singular';
		}

		return $attributes;
	}

	protected function register_controls() {
		parent::register_controls();

		$this->update_control(
			'preview_type',
			array( 'type' => Controls_Manager::HIDDEN )
		);
	}

	/**
	 * Before document content.
	 *
	 * Runs before document content render.
	 *
	 * @since 1.13.0
	 */
	public function before_get_content() {
		parent::before_get_content();

		global $event;

		if ( ! is_object( $event ) ) {
			$event = tribe_get_event( get_the_ID() );
		}
	}

	/**
	 * After document content.
	 *
	 * Runs after document content render.
	 *
	 * @since 1.13.0
	 */
	public function after_get_content() {
		parent::after_get_content();
	}

	protected function set_default_preview() {
		if ( ! empty( $this->preview_type_default ) ) {
			return;
		}

		$latest_post = get_posts( array(
			'post_type' => TribeEventsModule::$post_type,
			'fields' => 'ids',
			'numberposts' => 1,
		) );

		if ( empty( $latest_post ) ) {
			return;
		}

		$this->preview_type_default = sprintf( 'singular/%s', TribeEventsModule::$post_type );

		$this->preview_id_default = $latest_post[0];
	}

	public function get_locations_default() {
		$default_locations = parent::get_locations_default();

		if ( ! empty( $default_locations ) ) {
			return $default_locations;
		}

		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$rules_manager = $locations_module->get_rules_manager();

		if ( $rules_manager->get_rule_instance( TribeEventsModule::$post_type ) ) {
			$default_locations[] = array(
				'stmt' => 'include',
				'main' => 'singular',
				'addl' => TribeEventsModule::$post_type,
			);
		}

		return $default_locations;
	}

}
