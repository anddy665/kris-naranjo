<?php
namespace CmsmastersElementor\Modules\TribeEvents\Documents;

use CmsmastersElementor\Modules\Blog\Documents\Entry;
use CmsmastersElementor\Modules\TribeEvents\Module as TribeEventsModule;
use CmsmastersElementor\Modules\TribeEvents\Traits\Tribe_Events_Document;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSmasters event entry library document.
 *
 * CMSmasters event entry library document handler class is responsible for
 * handling a document of a event entry type.
 *
 * @since 1.13.0
 */
class Event_Entry extends Entry {

	use Tribe_Events_Document;

	/**
	 * @since 1.13.0
	 */
	public static $widgets_visibility = array(
		'Breadcrumbs' => false,
	);

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
		return 'cmsmasters_tribe_events_entry';
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
		return __( 'Event Entry', 'cmsmasters-elementor' );
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
		);

		$categories += parent::get_editor_panel_categories();

		unset( $categories[ self::SINGULAR_WIDGETS_CATEGORY ] );

		if ( Utils::is_pro() ) {
			unset( $categories['theme-elements-single'] );
		}

		return $categories;
	}

	/**
	 * @since 1.13.0
	 */
	protected function register_controls() {
		parent::register_controls();

		$this->update_control(
			'preview_type',
			array( 'type' => Controls_Manager::HIDDEN )
		);
	}

	/**
	 * @since 1.13.0
	 */
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

	/**
	 * @since 1.0.0
	 */
	public function get_wp_preview_url() {
		$main_post_id = $this->get_main_id();

		return get_preview_post_link(
			$main_post_id,
			array(
				'preview_id' => $main_post_id,
				'preview_nonce' => wp_create_nonce( 'post_preview_' . $main_post_id ),
			)
		);
	}

	/**
	 * @since 1.13.0
	 */
	protected function get_preview_elements_data() {
		$widget = Plugin::elementor()->widgets_manager->get_widget_types( 'cmsmasters-tribe-events-events-grid' );

		if ( ! $widget ) {
			return;
		}

		return array(
			array(
				'id' => ElementorUtils::generate_random_string(),
				'elType' => 'section',
				'elements' => array(
					array(
						'id' => ElementorUtils::generate_random_string(),
						'elType' => 'column',
						'settings' => array( '_column_size' => 100 ),
						'elements' => array(
							array(
								'id' => ElementorUtils::generate_random_string(),
								'elType' => $widget::get_type(),
								'widgetType' => $widget->get_name(),
								'settings' => array(
									'event_template' => 'custom',
									TribeEventsModule::CONTROL_TEMPLATE_NAME => $this->get_main_id(),
									'posts_per_page' => 4,
									'pagination_show' => '',
								),
							),
						),
					),
				),
			),
		);
	}

}
