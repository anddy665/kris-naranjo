<?php
namespace CmsmastersElementor\Modules\TribeEvents;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\TemplateLocations\Rules_Manager;
use CmsmastersElementor\Modules\TribeEvents\Documents;
use CmsmastersElementor\Modules\TribeEvents\Rules;
use CmsmastersElementor\Modules\TribeEvents\Widgets\Base_Events\Base_Events_Elements;
use CmsmastersElementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Tribe Events module.
 *
 * The tribe events class is responsible for tribe events module controls integration.
 *
 * @since 1.13.0
 */
class Module extends Base_Module {

	const CONTROL_TEMPLATE_NAME = 'cmsmasters_template_id';

	public static $post_type = 'tribe_events';

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.13.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'tribe-events';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'Tribe__Events__Main' );
	}

	/**
	 * Retrieve widget classes name.
	 *
	 * @since 1.13.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array(
			'Event_Title',
			'Event_Image',
			'Event_Short_Description',
			'Event_Content',
			'Event_Date',
			'Event_Cost',
			'Event_Meta',
			'Event_Organizer',
			'Event_Venue',
			'Event_Venue_Map',
			'Events_Grid',
			'Events_Slider',
		);
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Tribe Events module.
	 *
	 * @since 1.13.0
	 */
	protected function init_actions() {
		add_action( 'cmsmasters_elementor/documents/locations/register_rules', array( $this, 'register_location_rules' ) );
		add_action( 'cmsmasters_elementor/ajax_widget/register', array( $this, 'register_ajax_widget_handlers' ) );
		add_action( 'pre_get_posts', array( $this, 'change_pre_get_events_type' ) );
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Tribe Events module.
	 *
	 * @since 1.13.0
	 */
	protected function init_filters() {
		add_filter( 'cmsmasters_elementor/documents/set_document_types', array( $this, 'set_document_types' ) );
		add_filter( 'cmsmasters_elementor/documents/set_elementor_documents', array( $this, 'set_elementor_documents' ) );
		add_filter( 'cmsmasters_elementor/locations/template_include/page_template', array( $this, 'set_custom_document_template' ), 10, 2 );
	}

	/**
	 * Undocumented function
	 *
	 * Description.
	 *
	 * @since 1.13.0
	 *
	 * @param Rules_Manager $rules_manager
	 */
	public function register_location_rules( $rules_manager ) {
		$rules_manager_general = $rules_manager->get_rule_instance( 'general' );

		$tribe_events_general_rule = new Rules\TribeEvents();

		$rules_manager_general->register_child_rule( $tribe_events_general_rule );
	}

	/**
	 * Add handlers for ajax widget.
	 *
	 * @since 1.13.0
	 */
	public function register_ajax_widget_handlers( AjaxWidgetModule $ajax_widget ) {
		$ajax_widget->add_handler( 'cmsmasters-tribe-events-events-grid', array( $this, 'render_ajax_widget' ), false );
	}

	/**
	 * Render widgets on Ajax request.
	 *
	 * Sends HTML to frontend
	 *
	 * @since 1.13.0
	 *
	 * @return string
	 */
	public function render_ajax_widget( $ajax_vars, Base_Events_Elements $widget ) {
		ob_start();

		$widget->render_ajax( $ajax_vars );

		return ob_get_clean();
	}

	/**
	 * Set tribe events module document.
	 *
	 * Fired by `cmsmasters_elementor/documents/set_document_types` Addon filter hook.
	 *
	 * @since 1.13.0
	 *
	 * @return array
	 */
	public function set_document_types( $document_types ) {
		$module_document_types = array(
			'cmsmasters_tribe_events_singular' => Documents\Event_Singular::get_class_full_name(),
			'cmsmasters_tribe_events_archive' => Documents\Event_Archive::get_class_full_name(),
			'cmsmasters_tribe_events_entry' => Documents\Event_Entry::get_class_full_name(),
			'tribe-events-post' => Documents\Tribe\Event_Post::get_class_full_name(),
		);

		$document_types = array_merge( $document_types, $module_document_types );

		return $document_types;
	}

	/**
	 * Set Elementor documents.
	 *
	 * @param string[] $elementor_documents
	 *
	 * @since 1.13.0
	 *
	 * @return string[]
	 */
	public function set_elementor_documents( $elementor_documents ) {
		$elementor_documents[] = 'tribe-events-post';

		return $elementor_documents;
	}

	/**
	 * Set custom document template.
	 *
	 * @param string[] $page_template
	 *
	 * @since 1.13.0
	 *
	 * @return string[]
	 */
	public function set_custom_document_template( $page_template, $location ) {
		if (
			empty( $page_template ) &&
			'cmsmasters_singular' === $location &&
			( is_singular( static::$post_type ) || is_singular( 'tribe_organizer' ) || is_singular( 'tribe_venue' ) )
		) {
			/** @var PageTemplatesModule $page_templates_module */
			$page_templates_module = Plugin::elementor()->modules_manager->get_modules( 'page-templates' );

			$page_template = $page_templates_module::TEMPLATE_HEADER_FOOTER;
		}

		return $page_template;
	}

	/**
	 * Change is_post_type_archive event type to is_tax in category/tag page archive
	 *
	 * @since 1.15.4
	 */
	public function change_pre_get_events_type( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_post_type_archive( 'tribe_events' ) && ( $query->is_tax( 'tribe_events_cat' ) || $query->is_tax( 'tribe_events_tag' ) ) ) {
			$query->is_tax = true;
			$query->is_post_type_archive = false;
		}
	}
}
