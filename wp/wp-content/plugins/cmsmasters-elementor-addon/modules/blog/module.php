<?php
namespace CmsmastersElementor\Modules\Blog;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\AjaxWidget\Module as AjaxWidgetModule;
use CmsmastersElementor\Modules\Blog\Documents\Entry;
use CmsmastersElementor\Modules\Blog\Widgets\Base_Blog\Base_Blog_Elements;
use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Utils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor blog module.
 *
 * @since 1.0.0
 */
final class Module extends Base_Module {

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters Blog module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters_blog';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( DocumentsModule::class );
	}

	/**
	 * Retrieve widget classes name.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Added 'Archive_Posts' to the list.
	 * @since 1.5.0 Added Ticker widget.
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array(
			'Archive_Posts',
			'Blog_Featured',
			'Blog_Grid',
			'Blog_Slider',
			'Ticker',
		);
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Blog module.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		add_action( 'elementor/template-library/create_new_dialog_fields', array( $this, 'create_entry_field' ) );
		add_action( 'cmsmasters_elementor/ajax_widget/register', array( $this, 'register_ajax_widget' ) );
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Blog module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		// Common
		add_filter( 'cmsmasters_elementor/documents/set_document_types', array( $this, 'set_document_types' ) );

		// Frontend
		add_filter( 'cmsmasters_elementor/frontend/settings', array( $this, 'filter_frontend_settings' ) );
	}

	/**
	 * Create entry field.
	 *
	 * Adds new post type field to template library new template dialog.
	 *
	 * Fired by `elementor/template-library/create_new_dialog_fields` action.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Template part.
	 */
	public function create_entry_field() {
		$post_types_list = Utils::filter_public_post_types( array(), true );

		$options = array();

		foreach ( $post_types_list as $post_type => $post_type_label ) {
			$options[ 'singular/' . $post_type ] = $post_type_label;
		}

		/** @var DocumentsModule $documents_module */
		$documents_module = DocumentsModule::instance();

		$documents_module->print_new_dialog_field_template(
			$options,
			Entry::ENTRY_TEMPLATE_TYPE_META,
			__( 'entry', 'cmsmasters-elementor' )
		);
	}

	/**
	 * Add handler for ajax widget.
	 *
	 * Register handler for the Blog widgets.
	 *
	 * Fired by `cmsmasters_elementor/ajax_widget/register` Addon action hook.
	 *
	 * @since 1.0.0
	 * @since 1.0.3 Added 'cmsmasters-archive-posts' to ajax handler.
	 */
	public function register_ajax_widget( AjaxWidgetModule $ajax_widget ) {
		$ajax_widget->add_handler( 'cmsmasters-archive-posts', array( $this, 'render_ajax_widget' ), false );
		$ajax_widget->add_handler( 'cmsmasters-blog-featured', array( $this, 'render_ajax_widget' ), false );
		$ajax_widget->add_handler( 'cmsmasters-blog-grid', array( $this, 'render_ajax_widget' ), false );
	}

	/**
	 * Set blog module document.
	 *
	 * Fired by `cmsmasters_elementor/documents/set_document_types` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function set_document_types( $document_types ) {
		$module_document_types = array(
			'cmsmasters_entry' => Entry::get_class_full_name(),
		);

		$document_types = array_merge( $document_types, $module_document_types );

		return $document_types;
	}

	/**
	 * Render widgets on Ajax request.
	 *
	 * Sends HTML to frontend
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @return string
	 */
	public function render_ajax_widget( $ajax_vars, Base_Blog_Elements $widget ) {
		/* Render Posts */
		ob_start();

		$widget->render_ajax( $ajax_vars );

		return ob_get_clean();
	}

	/**
	 * Filter frontend settings.
	 *
	 * Filters the Addon settings for elementor frontend.
	 *
	 * Fired by `cmsmasters_elementor/frontend/settings` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Frontend settings.
	 *
	 * @return array Filtered frontend settings.
	 */
	public function filter_frontend_settings( $settings ) {
		$settings = array_replace_recursive( $settings, array(
			'i18n' => array(
				'blog_template_id' => __( 'Template', 'cmsmasters-elementor' ),
				'post_featured_template_id' => __( 'Featured Template', 'cmsmasters-elementor' ),
				'post_regular_template_id' => __( 'Regular Template', 'cmsmasters-elementor' ),
			),
		) );

		return $settings;
	}

	/**
	 * Get allowed query vars.
	 *
	 * Protects against inaccessible user data.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $query_vars
	 *
	 * @return array Filtered transferred Query variables.
	 */
	public static function get_allowed_query_vars( $query_vars ) {
		$available_query_vars = array(
			'tax_query',
			'paged',
		);

		return array_filter(
			$query_vars,
			function ( $key ) use ( $available_query_vars ) {
				return in_array( $key, $available_query_vars, true );
			},
			ARRAY_FILTER_USE_KEY
		);
	}
}
