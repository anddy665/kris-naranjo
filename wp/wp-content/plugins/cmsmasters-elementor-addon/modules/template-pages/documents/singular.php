<?php
namespace CmsmastersElementor\Modules\TemplatePages\Documents;

use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Modules\TemplatePages\Documents\Base\Archive_Singular_Document;
use CmsmastersElementor\Modules\TemplatePages\Documents\Elementor\Page;
use CmsmastersElementor\Modules\TemplatePreview\Traits\Preview_Type;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\DB;
use Elementor\Modules\Library\Documents\Section;
use Elementor\TemplateLibrary\Source_Local;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Singular extends Archive_Singular_Document {

	use Preview_Type;

	/**
	 * Document post type meta key.
	 */
	const SINGULAR_TEMPLATE_TYPE_META = '_cmsmasters_singular_post_type';

	public static $widgets_visibility = array( 'Post_Content' => true );

	protected $preview_type_default = '';

	protected $preview_id_default = '';

	/**
	 * Get document properties.
	 *
	 * Retrieve the document properties.
	 *
	 * @since 1.0.0
	 *
	 * @return array Document properties.
	 */
	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location_type'] = 'singular';
		$properties['location_exclude'] = array(
			// WooCommerce
			'product',
			// Tribe Events
			'tribe_events',
			'tribe_venue',
			'tribe_organizer',
		);

		$properties = apply_filters( 'cmsmasters_elementor/documents/singular/get_properties', $properties );

		return $properties;
	}

	/**
	 * Get document name.
	 *
	 * Retrieve the document name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document name.
	 */
	public function get_name() {
		return 'cmsmasters_singular';
	}

	/**
	 * Get document title.
	 *
	 * Retrieve the document title.
	 *
	 * @since 1.0.0
	 *
	 * @return string Document title.
	 */
	public static function get_title() {
		return __( 'Singular', 'cmsmasters-elementor' );
	}

	/**
	 * Get editor widgets panel categories.
	 *
	 * Retrieve an array of editor widgets panel categories.
	 *
	 * Move singular category widgets to the beginning of editor panel.
	 *
	 * @since 1.0.0
	 *
	 * @return array Reordered array of widget categories.
	 */
	protected static function get_editor_panel_categories() {
		$categories = array(
			self::SINGULAR_WIDGETS_CATEGORY => array( 'title' => self::get_title() ),
		);

		if ( Utils::is_pro() ) {
			$categories['theme-elements-single'] = array(
				'title' => __( 'Single', 'cmsmasters-elementor' ),
				'active' => true,
			);
		}

		$categories += parent::get_editor_panel_categories();

		if ( ! Utils::is_pro() ) {
			$categories['pro-elements']['active'] = false;
		}

		return $categories;
	}

	/**
	 * Get container attributes.
	 *
	 * Retrieve the document container attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return string Container attributes.
	 */
	public function get_container_attributes() {
		$attributes = parent::get_container_attributes();

		if ( is_singular() ) {
			$post_classes = get_post_class( '', get_the_ID() );

			$attributes['class'] .= ' ' . implode( ' ', $post_classes );
		}

		return $attributes;
	}

	/**
	 * Before document content.
	 *
	 * Runs before document content render.
	 *
	 * @since 1.0.0
	 */
	public function before_get_content() {
		parent::before_get_content();

		if ( have_posts() ) {
			the_post();
		}
	}

	/**
	 * After document content.
	 *
	 * Runs after document content render.
	 *
	 * @since 1.0.0
	 */
	public function after_get_content() {
		wp_reset_postdata();

		parent::after_get_content();
	}

	public function print_content() {
		$preview_post_id = get_the_ID();

		if ( $preview_post_id !== $this->post->ID ) {
			/** @var DocumentsModule $documents_module */
			$documents_module = DocumentsModule::instance();
			$preview_document = $documents_module->get_document( $preview_post_id );

			if ( $preview_document ) {
				$is_section = $preview_document instanceof Section;
				$is_page = $preview_document instanceof Page;
				$names_not_equal = $preview_document->get_name() !== $this->get_name();

				/**
				 * If current requested document is `Base_Document` & it's not a content type
				 * (like header or footer) show a placeholder instead of content.
				 */
				if ( ! $is_section && ! $is_page && $names_not_equal ) {
					printf(
						'<div class="cmsmasters-template-content-area-placeholder cmsmasters-singular-content">%s</div>',
						__( 'Content Area', 'cmsmasters-elementor' )
					);

					return;
				}
			}
		}

		parent::print_content();
	}

	/**
	 * Get editor elements data.
	 *
	 * Retrieve Elementor editor elements data array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Post status.
	 *
	 * @return array Editor elements.
	 */
	public function get_elements_data( $status = DB::STATUS_PUBLISH ) {
		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$locations_manager = $locations_module->get_locations_manager();

		$elements_data = parent::get_elements_data();
		$locations_equal = $this->get_location_type() === $locations_manager->get_current_location();

		if ( Utils::is_preview_mode() && $locations_equal ) {
			$content_exists = false;

			$content_widget_name = $this->get_content_widget()->get_name();

			Plugin::elementor()->db->iterate_data(
				$elements_data,
				function( $element ) use ( &$content_exists, $content_widget_name ) {
					if (
						isset( $element['widgetType'] ) &&
						$element['widgetType'] === $content_widget_name
					) {
						$content_exists = true;
					}
				}
			);

			if ( ! $content_exists ) {
				add_action( 'wp_footer', array( $this, 'preview_error_args' ) );
			}
		}

		return $elements_data;
	}

	public function get_content_widget() {
		return Plugin::elementor()->widgets_manager->get_widget_types( 'cmsmasters-post-content' );
	}

	public function preview_error_args() {
		$content_widget_title = $this->get_content_widget()->get_title();

		wp_localize_script( 'elementor-frontend', 'elementorPreviewErrorArgs', array(
			/* translators: Elementor 'required widget' preview error. %s: Widget name */
			'headerMessage' => sprintf( esc_html__( 'The %s Widget was not found in your template.', 'cmsmasters-elementor' ), esc_html( $content_widget_title ) ),
			/* translators: Elementor 'required widget' preview error. 1: Widget name. 2: Template name */
			'message' => sprintf( esc_html__( 'You must include the %1$s Widget in your %2$s template, in order for Elementor to work on this page.', 'cmsmasters-elementor' ),
				esc_html( $content_widget_title ),
				'<strong>' . esc_html( static::get_title() ) . '</strong>'
			),
			'strings' => array(
				/* translators: Elementor 'required widget' preview error. %s: Template name */
				'confirm' => sprintf( esc_html__( 'Edit %s', 'cmsmasters-elementor' ), esc_html( static::get_title() ) ),
			),
			'confirmURL' => esc_url( $this->get_edit_url() ),
		) );
	}

	/**
	 * Save template type.
	 *
	 * Set document name as template type meta key.
	 *
	 * Set singular template post type meta key if selected.
	 *
	 * @since 1.0.0
	 */
	public function save_template_type() {
		parent::save_template_type();

		if ( ! empty( $_REQUEST[ self::SINGULAR_TEMPLATE_TYPE_META ] ) ) {
			$template_type = $_REQUEST[ self::SINGULAR_TEMPLATE_TYPE_META ];

			$this->update_meta( self::SINGULAR_TEMPLATE_TYPE_META, $template_type );
		}
	}

	/**
	 * Singular document constructor.
	 *
	 * Initializing the Addon singular document.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Class initial data.
	 */
	public function __construct( array $data = array() ) {
		if ( $data ) {
			add_filter( 'body_class', array( $this, 'filter_body_classes' ) );
		}

		parent::__construct( $data );
	}

	/**
	 * Add body classes.
	 *
	 * Filters body classes for the `style` controls selector.
	 *
	 * Fires by `body_class` WordPress filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $body_classes Body classes array.
	 *
	 * @return array Filtered body classes array.
	 */
	public function filter_body_classes( $body_classes ) {
		$template_type = Source_Local::get_template_type( get_the_ID() );

		if ( ( is_singular() || is_404() ) && 'archive' !== $template_type ) {
			$body_classes[] = 'elementor-page-' . $this->get_main_id();
		}

		return $body_classes;
	}

	public static function get_preview_type_options() {
		return array_merge( parent::get_preview_type_options(), static::get_singular_preview_type_options_choices( false, false ) );
	}

	public static function get_singular_options_choices() {
		$post_types_list = Utils::filter_public_post_types( array(), true );
		$post_types_list['attachment'] = get_post_type_object( 'attachment' )->label;

		$singular_options = array();

		foreach ( $post_types_list as $post_type => $post_type_label ) {
			$singular_options[ 'singular/' . $post_type ] = $post_type_label;
		}

		return $singular_options;
	}

	public function get_preview_type_default() {
		$this->set_default_preview();

		return $this->preview_type_default;
	}

	public function get_preview_id_default() {
		$this->set_default_preview();

		return $this->preview_id_default;
	}

	protected function set_default_preview() {
		if ( ! empty( $this->preview_type_default ) ) {
			return;
		}

		$template_type = $this->get_main_meta( self::SINGULAR_TEMPLATE_TYPE_META );

		list( $rule_type, $rule_subtype ) = array_pad( explode( '/', $template_type ), 2, '' );

		if ( 'page' === $rule_type ) {
			$this->preview_type_default = $template_type;

			return;
		}

		$latest_post = get_posts( array(
			'post_type' => $rule_subtype,
			'fields' => 'ids',
			'numberposts' => 1,
		) );

		if ( empty( $latest_post ) ) {
			return;
		}

		$this->preview_type_default = $template_type;
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

		$template_type = explode( '/', $this->get_main_meta( self::SINGULAR_TEMPLATE_TYPE_META ) );

		$rule_type = isset( $template_type[1] ) ? $template_type[1] : $template_type[0];

		if ( $rules_manager->get_rule_instance( $rule_type ) ) {
			$default_locations[] = array(
				'stmt' => 'include',
				'main' => 'singular',
				'addl' => $rule_type,
			);
		}

		return $default_locations;
	}

	/**
	 * Get remote library config.
	 *
	 * Retrieves Addon remote templates library config.
	 *
	 * @since 1.0.0
	 *
	 * @return array Addon templates library config.
	 */
	protected function get_remote_library_config() {
		$config = parent::get_remote_library_config();

		$template_type = $this->get_main_meta( self::SINGULAR_TEMPLATE_TYPE_META );

		if ( $template_type ) {
			if ( 'error_404' === $template_type ) {
				$config['category'] = '404 page';
			} else {
				$template_parts = array_pad( explode( '/', $template_type ), 2, '' );

				$config['category'] = 'single ' . $template_parts[1];
			}
		} else {
			$config['category'] = 'single post';
		}

		return $config;
	}

}
