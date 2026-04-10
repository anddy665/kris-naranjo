<?php
namespace CmsmastersElementor\Modules\TemplateLocations;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Modules\TemplateLocations\Rules\Base\Base_Rule;
use CmsmastersElementor\Traits\Data_Storage;
use CmsmastersElementor\Utils;

use Elementor\Core\Base\Document;
use Elementor\TemplateLibrary\Source_Local;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Rules_Manager {

	use Data_Storage;

	const OPTION_NAME = 'cmsmasters_elementor_documents_locations';

	const META_NAME = '_cmsmasters_locations';

	/**
	 * Documents module.
	 *
	 * @since 1.0.0
	 *
	 * @var DocumentsModule
	 */
	private $documents_module;

	private static $available_locations = array();

	/**
	 * @var Base_Rule[]
	 */
	private $location_rules = array();

	private $location_cache = array();

	public function __construct() {
		/** @var DocumentsModule $documents_module */
		$this->documents_module = DocumentsModule::instance();

		self::$available_locations = $this->documents_module->get_location_available_document_types();

		$this->load_data();

		add_action( 'wp_loaded', array( $this, 'register_location_rules' ) );

		add_action( 'cmsmasters_elementor/documents/locations/register_child_rule', array( $this, 'register_rule_instance' ) );

		add_action( 'wp_trash_post', array( $this, 'remove_post_from_storage' ) );
		add_action( 'untrashed_post', array( $this, 'on_untrash_post' ) );
	}

	public function register_location_rules() {
		$this->register_location_rule( 'general' );

		do_action( 'cmsmasters_elementor/documents/locations/register_rules', $this );
	}

	private function register_location_rule( $name, $group = '' ) {
		if ( isset( $this->location_rules[ $name ] ) ) {
			return;
		}

		$class_name = ucfirst( $name );

		if ( ! empty( $group ) && 'general' !== $group ) {
			$class_name = ucfirst( $group ) . '\\' . $class_name;
		}

		$class = __NAMESPACE__ . '\\Rules\\' . $class_name;
		/** @var Base_Rule $location_class */
		$location_class = new $class();

		$this->register_rule_instance( $location_class );

		$group = $location_class->get_group();

		foreach ( $location_class->get_child_rules() as $rule_name ) {
			$this->register_location_rule( $rule_name, $group );
		}
	}

	/**
	 * @param Base_Rule $instance
	 */
	public function register_rule_instance( $instance ) {
		$this->location_rules[ $instance->get_name() ] = $instance;
	}

	/**
	 * Undocumented function
	 *
	 * @param [type] $post_id
	 */
	public function remove_post_from_storage( $post_id ) {
		return $this->remove_data( $post_id )->update_db();
	}

	/**
	 * Undocumented function
	 *
	 * @param int $post_id
	 */
	public function on_untrash_post( $post_id ) {
		$document = $this->documents_module->get_document( $post_id );

		if ( $document ) {
			$locations = $document->get_meta( self::META_NAME );

			if ( $locations ) {
				$this->add_data( $document, $locations )->update_db();
			}
		}
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param \CmsmastersElementor\Base\Base_Document $document
	 * @param array $locations
	 *
	 * @return Rules_Manager
	 */
	public function add_data( Base_Document $document, $rules ) {
		$location = $document->get_document_location_type();
		$document_id = $document->get_main_id();

		if ( ! isset( $this->option_data[ $location ] ) ) {
			$this->option_data[ $location ] = array();
		}

		$this->option_data[ $location ][ $document_id ] = $rules;

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param int $data_id
	 *
	 * @return Rules_Manager
	 */
	public function remove_data( $data_id ) {
		$data_id = absint( $data_id );

		foreach ( $this->option_data as $location => $rules ) {
			foreach ( array_keys( $rules ) as $template_id ) {
				if ( $data_id === $template_id ) {
					unset( $this->option_data[ $location ][ $template_id ] );
				}
			}
		}

		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @return Rules_Manager
	 */
	public function regenerate_locations() {
		$this->clear_data();

		foreach ( self::get_templates_with_locations() as $post_id ) {
			$document = $this->documents_module->get_document( $post_id );

			if ( ! $document ) {
				continue;
			}

			$available_locations = $this->documents_module->get_location_available_document_types();
	
			if ( ! in_array( $document->get_name(), $available_locations, true ) ) {
				continue;
			}

			$locations = $document->get_meta( self::META_NAME );

			$this->add_data( $document, $locations );
		}

		$this->update_db();

		return $this;
	}

	public static function get_templates_with_locations() {
		do_action( 'cmsmasters_elementor/locations/before_get_templates' );

		$templates_query = new \WP_Query( array(
			'post_type' => Source_Local::CPT,
			'meta_query' => array(
				array(
					'key' => Document::TYPE_META_KEY,
					'value' => self::$available_locations,
					'compare' => 'IN',
				),
				array( 'key' => self::META_NAME ),
			),
			'fields' => 'ids',
			'posts_per_page' => -1,
		) );

		do_action( 'cmsmasters_elementor/locations/after_get_templates' );

		return $templates_query->posts;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $location
	 *
	 * @return array
	 */
	public function get_location_templates( $location ) {
		$website_rules = $this->get_data_by_id( $location );

		if ( empty( $website_rules ) ) {
			return array();
		}

		$rules_priority = array();
		$exclude_rules = array();

		do_action( 'cmsmasters_elementor/location_rules_manager/before_get_location_templates' );

		foreach ( $website_rules as $document_id => $rules ) {
			foreach ( $rules as $rule ) {
				$rule_array = $this->parse_location_rule( $rule );

				list(
					$statement,
					$main_location,
					$child_location,
					$location_args
				) = array_values( $rule_array );

				$main_instance = $this->get_rule_instance( $main_location );

				if ( ! $main_instance || ! $main_instance->verify_expression() ) {
					continue;
				}

				$child_instance = false;

				if ( ! empty( $child_location ) ) {
					$child_instance = $this->get_rule_instance( $child_location );

					// Translate location args for multilingual plugins (WPML, Polylang)
					$translated_args = apply_filters( 'cmsmasters_translated_location_args', $location_args );

					if ( ! $child_instance || ! $child_instance->verify_expression( $translated_args ) ) {
						continue;
					}
				}

				if ( ! Utils::is_publish( $document_id ) ) {
					continue;
				}

				if ( 'exclude' === $statement ) {
					$exclude_rules[] = $document_id;

					continue;
				}

				$rules_priority[ $document_id ] = $this->get_rule_priority( $main_instance, $child_instance, $location_args );
			}
		}

		do_action( 'cmsmasters_elementor/location_rules_manager/after_get_location_templates' );

		foreach ( $exclude_rules as $template_id ) {
			unset( $rules_priority[ $template_id ] );
		}

		asort( $rules_priority );

		return $rules_priority;
	}

	/**
	 * @param string $rule
	 *
	 * @return array
	 */
	public function parse_location_rule( $rule ) {
		$rule_array = array_pad( explode( '/', $rule ), 4, '' );

		$args = $rule_array[3];

		if ( ! empty( $args ) ) {
			$args = array_map( 'intval', explode( '|', $args ) );
		}

		return array(
			'stmt' => $rule_array[0],
			'main' => $rule_array[1],
			'addl' => $rule_array[2],
			'args' => $args,
		);
	}

	/**
	 * @param string $name
	 *
	 * @return Base_Rule|bool
	 */
	public function get_rule_instance( $name ) {
		if ( ! isset( $this->location_rules[ $name ] ) ) {
			return false;
		}

		return $this->location_rules[ $name ];
	}

	/**
	 * @param Base_Rule $main_instance
	 * @param Base_Rule $child_instance
	 * @param int[] $location_args
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	private function get_rule_priority( $main_instance, $child_instance, $location_args ) {
		$priority = $main_instance::get_priority();

		if ( ! $child_instance ) {
			return $priority;
		}

		$child_priority = $child_instance::get_priority();

		if ( $child_priority < $priority ) {
			$priority = $child_priority;
		}

		if ( $location_args ) {
			$args_priority = $child_instance::get_args_priority();

			if ( $args_priority < $priority ) {
				$priority = $args_priority;
			} else {
				$priority -= 5;
			}
		} elseif ( ! count( $child_instance->get_child_rules() ) ) {
			$priority--;
		}

		return $priority;
	}

	/**
	 * @param $location
	 *
	 * @return Base_Document[]
	 */
	public function get_documents_for_location( $location ) {
		if ( isset( $this->location_cache[ $location ] ) ) {
			return $this->location_cache[ $location ];
		}

		$cmsmasters_documents = $this->get_cmsmasters_documents( $location );

		$documents = array();

		foreach ( array_keys( $cmsmasters_documents ) as $document_id ) {
			$document_id = apply_filters( 'cmsmasters_translated_template_id', $document_id );

			$document = $this->documents_module->get_document( $document_id );

			if ( $document ) {
				$documents[ $document_id ] = $document;
			} else {
				$this->remove_post_from_storage( $document_id );
			}

			if ( ! $document::get_property( 'multiple' ) ) {
				break;
			}
		}

		$this->location_cache[ $location ] = $documents;

		return $documents;
	}

	public function get_cmsmasters_documents( $location ) {
		// In case the user want to preview any page with a cmsmasters_template_id,
		// like https://domain.com/any-post/?preview=1&cmsmasters_template_id=6453
		if ( ! empty( $_GET['cmsmasters_template_id'] ) ) {
			$cmsmasters_template_id = $_GET['cmsmasters_template_id'];

			$document = $this->documents_module->get_document( $cmsmasters_template_id );

			if ( $document && $location === $document->get_document_location_type() ) {
				return array( $cmsmasters_template_id => 1 );
			}
		}

		$post_id = get_the_ID();

		$document = $this->documents_module->get_document( $post_id );

		if ( $document && $location === $document->get_document_location_type() ) {
			return array( $post_id => 1 );
		}

		$templates = $this->get_location_templates( $location );

		return $templates;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_location_rules_config() {
		$config = array();

		foreach ( $this->location_rules as $location ) {
			$config[ $location->get_name() ] = $location->get_location_config();
		}

		return $config;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param Base_Document $document
	 *
	 * @return array
	 */
	public function get_document_locations( Base_Document $document ) {
		$document_locations = $document->get_main_meta( self::META_NAME );
		$locations_array = array();

		if ( is_array( $document_locations ) ) {
			foreach ( $document_locations as $location ) {
				$locations_array[] = $this->parse_location_rule( $location );
			}
		}

		return $locations_array;
	}

}
