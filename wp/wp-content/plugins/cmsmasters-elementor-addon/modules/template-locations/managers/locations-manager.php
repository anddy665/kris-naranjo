<?php
namespace CmsmastersElementor\Modules\TemplateLocations;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Modules\TemplateLocations\Module as LocationsModule;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Core\Base\Elements_Iteration_Actions\Assets;
use Elementor\Core\Documents_Manager;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Locations_Manager {

	protected $internal_locations = array();

	protected $locations = array();

	protected $current_location;

	protected $current_page_template = '';

	public function __construct() {
		$this->set_internal_locations();

		$this->init_actions();
		$this->init_filters();

		$this->register_internal_locations();
	}

	/**
	 * @since 1.0.0
	 */
	private function set_internal_locations() {
		$this->internal_locations = array(
			'cmsmasters_header' => array(
				'edit_in_content' => false,
			),
			'cmsmasters_footer' => array(
				'edit_in_content' => false,
			),
			'cmsmasters_popup' => array(
				'edit_in_content' => false,
			),
			'cmsmasters_archive' => array(
				'edit_in_content' => true,
				'overwrite' => true,
			),
			'cmsmasters_singular' => array(
				'edit_in_content' => true,
			),
		);
	}

	/**
	 * Add actions initialization.
	 *
	 * Register locations manager actions.
	 *
	 * @since 1.0.0
	 */
	private function init_actions() {
		add_action( 'template_redirect', array( $this, 'register_locations' ) );

		if ( ! Utils::is_preview() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 9 );
		}
	}

	/**
	 * Add filters initialization.
	 *
	 * Register locations manager filters.
	 *
	 * @since 1.0.0
	 */
	private function init_filters() {
		add_filter( 'template_include', array( $this, 'template_include' ), 51 ); // 11 = after WooCommerce. 51 = after Tribe Events.
	}

	public function register_internal_locations() {
		foreach ( $this->internal_locations as $location => $settings ) {
			$this->register_location( $location, $settings );
		}
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $location
	 *
	 * @return array
	 */
	public function get_location_config( $location ) {
		$registered_locations = $this->get_registered_locations();

		if ( ! isset( $registered_locations[ $location ] ) ) {
			return array();
		}

		return $registered_locations[ $location ];
	}

	/**
	 * @since 1.0.0
	 *
	 * @param array $filter_args
	 *
	 * @return array
	 */
	public function get_registered_locations( $filter_args = array() ) {
		$this->register_locations();

		return wp_list_filter( $this->locations, $filter_args );
	}

	public function get_registered_location( $location ) {
		return Utils::get_if_isset( $this->get_registered_locations(), $location, array() );
	}

	/**
	 * Undocumented function
	 *
	 * Description.
	 *
	 * @since 1.0.0
	 *
	 * @param string $location
	 * @param array $args
	 */
	public function register_internal_location( $location, $args = array() ) {
		$internal_location = $this->get_internal_location( $location );

		if ( ! $internal_location ) {
			/* translators: Internal template locations registration error. %s: Location name. */
			wp_die( esc_html( sprintf( __( 'Location %s is not an internal location.', 'cmsmasters-elementor' ), "'{$location}'" ) ) );
		}

		$args = array_replace_recursive( $internal_location, $args );

		$this->register_location( $location, $args );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $location
	 *
	 * @return array
	 */
	public function get_internal_location( $location ) {
		if ( ! isset( $this->internal_locations[ $location ] ) ) {
			return false;
		}

		return $this->internal_locations[ $location ];
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $location
	 * @param array $args
	 */
	public function register_location( $location, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'edit_in_content' => true,
			'action_tag' => 'cmsmasters_elementor/locations/' . $location, // APPLY HOOKS
		) );

		$this->locations[ $location ] = $args;

		add_action( $args['action_tag'], function() use ( $location, $args ) {
			/** @var LocationsModule $locations_module */
			$locations_module = LocationsModule::instance();
			$location_printed = $locations_module->get_locations_manager()->do_location( $location );

			if ( ! $location_printed || empty( $args['functions_to_remove'] ) ) {
				return false;
			}

			foreach ( $args['functions_to_remove'] as $function_name ) {
				remove_action( $args['action_tag'], $function_name );
			}
		}, 1 );
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $location
	 *
	 * @return bool
	 */
	public function do_location( $location ) {
		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$location_documents = $locations_module->get_rules_manager()->get_documents_for_location( $location );

		if ( empty( $location_documents ) ) {
			return false;
		}

		if ( is_singular() ) {
			Utils::set_global_authordata();
		}

		/**
		 * Before location content printed.
		 *
		 * The dynamic portion of the hook name.
		 *
		 * Fires before Elementor location was printed.
		 *
		 * @since 1.0.0
		 *
		 * @param string $location Location name.
		 * @param Locations_Manager $this An instance of locations manager.
		 */
		do_action( 'cmsmasters_elementor/locations/before_do_location', $location, $this );

		foreach ( $location_documents as $document ) {
			$this->current_location = $location;

			$document->print_content();

			$this->current_location = null;
		}

		/**
		 * After location content printed.
		 *
		 * Fires after Elementor location was printed.
		 *
		 * The dynamic portion of the hook name, `$location`, refers to the location name.
		 *
		 * @since 1.0.0
		 *
		 * @param Locations_Manager $this An instance of locations manager.
		 */
		do_action( 'cmsmasters_elementor/locations/after_do_location', $location, $this );

		return true;
	}

	public function register_locations() {
		if ( ! did_action( 'cmsmasters_elementor/locations/register' ) ) {
			/**
			 * Register theme locations.
			 *
			 * Fires after template files where included but before locations
			 * have been registered.
			 *
			 * This is where Elementor theme locations are registered by
			 * external themes.
			 *
			 * @since 1.0.0
			 *
			 * @param Locations_Manager $this An instance of locations manager.
			 */
			do_action( 'cmsmasters_elementor/locations/register', $this );
		}
	}

	/**
	 * Enqueue styles.
	 *
	 * @since 1.0.0
	 * @since 1.24.0 Fixed a bug where Elementor V4 atomic widget styles were not loading in the template location.
	 */
	public function enqueue_styles() {
		$locations = $this->get_registered_locations();

		if ( empty( $locations ) ) {
			return;
		}

		if ( ! empty( $this->current_page_template ) ) {
			$locations = $this->filter_page_template_locations( $locations );
		}

		$current_post_id = get_the_ID();

		/** @var Post_CSS[] $css_files */
		$css_files = array();

		foreach ( array_keys( $locations ) as $location ) {
			/** @var LocationsModule $locations_module */
			$locations_module = LocationsModule::instance();
			$location_documents = $locations_module->get_rules_manager()->get_documents_for_location( $location );

			foreach ( $location_documents as $document ) {
				$post_id = $document->get_post()->ID;

				// Don't enqueue current post here (let the  preview/frontend components to handle it)
				if ( $current_post_id === $post_id ) {
					continue;
				}

				// Required for Elementor V4 atomic widget styles to be enqueued.
				do_action( 'elementor/post/render', $post_id );

				$css_file = new Post_CSS( $post_id );

				$css_files[] = $css_file;

				$this->handle_page_assets( $post_id, $document );
			}
		}

		if ( empty( $css_files ) ) {
			return;
		}

		Plugin::elementor()->frontend->enqueue_styles();

		foreach ( $css_files as $css_file ) {
			$css_file->enqueue();
		}
	}

	private function handle_page_assets( $post_id, $document ): void {
		$page_assets = get_post_meta( $post_id, Assets::ASSETS_META_KEY, true );
		if ( ! empty( $page_assets ) ) {
			Plugin::elementor()->assets_loader->enable_assets( $page_assets );
			return;
		}

		if ( ! method_exists( $document, 'update_runtime_elements' ) ) {
			return;
		}

		$document->update_runtime_elements();
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 */
	private function filter_page_template_locations( $locations ) {
		$templates_to_filter = array(
			PageTemplatesModule::TEMPLATE_CANVAS,
			PageTemplatesModule::TEMPLATE_HEADER_FOOTER,
		);

		if ( ! in_array( $this->current_page_template, $templates_to_filter, true ) ) {
			return $locations;
		}

		if ( PageTemplatesModule::TEMPLATE_CANVAS === $this->current_page_template ) {
			$allowed_core = array();
		} else {
			$allowed_core = array(
				'cmsmasters_header',
				'cmsmasters_footer',
			);
		}

		foreach ( $locations as $name => $settings ) {
			if ( empty( $settings['is_core'] ) || in_array( $name, $allowed_core, true ) ) {
				continue;
			}

			unset( $locations[ $name ] );
		}

		return $locations;
	}

	public function template_include( $template ) {
		$location = '';

		if ( is_singular() ) {
			/** @var Documents_Manager $documents_manager */
			$documents_manager = Plugin::elementor()->documents;
			$document = $documents_manager->get_doc_for_frontend( get_the_ID() );

			if ( $document ) {
				if ( $this->check_document_template( $document ) ) {
					return $template;
				}

				if ( $document instanceof Base_Document ) {
					$location = $document->get_document_location_type();
				}
			}
		}

		$location = apply_filters( 'cmsmasters_elementor/locations/template_include/location', $location );

		if ( empty( $location ) ) {
			if ( $this->verify_location_expression( 'archive' ) ) {
				$location = 'cmsmasters_archive';
			} elseif ( $this->verify_location_expression( 'singular' ) ) {
				$location = 'cmsmasters_singular';
			}
		}

		/** @var PageTemplatesModule $page_templates_module */
		$page_templates_module = Plugin::elementor()->modules_manager->get_modules( 'page-templates' );

		if ( $location ) {
			$document_template = $this->get_location_document_template( $location );

			if ( ! is_bool( $document_template ) ) {
				$page_template = $document_template;
			} else {
				if ( $document_template ) {
					return $template;
				}

				$location_settings = $this->get_registered_location( $location );

				if ( empty( $location_settings ) || ! empty( $location_settings['overwrite'] ) ) {
					$page_template = $page_templates_module::TEMPLATE_HEADER_FOOTER;
				}
			}
		}

		$page_template = isset( $page_template ) ? $page_template : '';

		$page_template = apply_filters( 'cmsmasters_elementor/locations/template_include/page_template', $page_template, $location );

		if ( ! empty( $page_template ) ) {
			$template_path = $page_templates_module->get_template_path( $page_template );

			if ( $template_path ) {
				$page_templates_module->set_print_callback( function() use ( $location ) {
					/** @var LocationsModule $locations_module */
					$locations_module = LocationsModule::instance();

					$locations_module->get_locations_manager()->do_location( $location );
				} );

				return $template_path;
			}
		}

		return $template;
	}

	public function check_document_template( $document ) {
		if ( ! $document::get_property( 'support_wp_page_templates' ) ) {
			return false;
		}

		$wp_page_template = $document->get_meta( '_wp_page_template' );

		if ( ! $wp_page_template || 'default' === $wp_page_template ) {
			return false;
		}

		$this->current_page_template = $wp_page_template;

		return true;
	}

	public function get_location_document_template( $location ) {
		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();
		$location_documents = $locations_module->get_rules_manager()->get_documents_for_location( $location );

		if ( empty( $location_documents ) ) {
			return true;
		}

		if ( 'cmsmasters_singular' !== $location && 'cmsmasters_archive' !== $location ) {
			return false;
		}

		$current_document = current( $location_documents );

		if ( Utils::is_preview() && $current_document->get_autosave_id() ) {
			$current_document = $current_document->get_autosave();
		}

		$document_template = $current_document->get_settings( 'template' );

		if ( ! $document_template || 'default' === $document_template ) {
			return false;
		}

		return $document_template;
	}

	public function verify_location_expression( $location ) {
		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();

		return $locations_module->get_rules_manager()->get_rule_instance( $location )->verify_expression();
	}

	public function get_current_location() {
		return $this->current_location;
	}

	public function get_internal_locations() {
		return $this->internal_locations;
	}

	public function location_exists( $location = '', $check_match = false ) {
		$location = self::verify_location( $location );

		$location_exists = ! ! $this->get_registered_location( $location );

		if ( $location_exists && $check_match ) {
			/** @var LocationsModule $locations_module */
			$locations_module = LocationsModule::instance();

			$location_exists = ! ! $locations_module->get_rules_manager()->get_documents_for_location( $location );
		}

		return $location_exists;
	}

	public function do_template_location( $location ) {
		$location = self::verify_location( $location );

		/** @var LocationsModule $locations_module */
		$locations_module = LocationsModule::instance();

		return $locations_module->get_locations_manager()->do_location( $location );
	}

	public static function verify_location( $location ) {
		$location = ( 'single' === $location ) ? 'singular' : $location;

		return "cmsmasters_{$location}";
	}

}
