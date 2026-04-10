<?php
namespace CmsmastersElementor\Modules\TemplateLocations;

use CmsmastersElementor\Base\Base_Document;
use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Controls_Manager as CmsmastersControls;
use CmsmastersElementor\Modules\TemplateDocuments\Module as DocumentsModule;
use CmsmastersElementor\Modules\TemplateLocations\Locations_Manager;
use CmsmastersElementor\Modules\TemplateLocations\Rules;
use CmsmastersElementor\Modules\TemplateLocations\Rules_Manager;
use CmsmastersElementor\Plugin;
use CmsmastersElementor\Utils;

use Elementor\Controls_Manager;
use Elementor\Core\Common\Modules\Ajax\Module as AjaxModule;
use Elementor\Core\Settings\Page\Manager as PageManager;
use Elementor\Repeater;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils as ElementorUtils;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Template Locations module.
 *
 * The template locations handler class is responsible for replacing
 * WordPress templates with Elementor documents.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Documents module.
	 *
	 * @since 1.0.0
	 *
	 * @var DocumentsModule
	 */
	private $documents_module;

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters template preview module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'template-locations';
	}

	/**
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
	 * Template Locations module constructor.
	 *
	 * Initializing the Addon Template Locations module.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->documents_module = DocumentsModule::instance();

		$this->add_component( 'rules_manager', new Rules_Manager() );
		$this->add_component( 'locations_manager', new Locations_Manager() );

		parent::__construct();

		/**
		 * Addon theme functions.
		 */
		require __DIR__ . '/theme-functions.php';
	}

	/**
	 * Get rules manager.
	 *
	 * Retrieve the location rules manager module component.
	 *
	 * @return Rules_Manager Rules Manager component.
	 */
	public function get_rules_manager() {
		return $this->get_component( 'rules_manager' );
	}

	/**
	 * Get locations manager.
	 *
	 * Retrieve the locations manager module component.
	 *
	 * @since 1.0.0
	 *
	 * @return Locations_Manager Locations Manager component.
	 */
	public function get_locations_manager() {
		return $this->get_component( 'locations_manager' );
	}

	/**
	 * Add actions initialization.
	 *
	 * Register actions for the Template Locations module.
	 *
	 * @since 1.0.0
	 */
	protected function init_actions() {
		// Admin
		add_action( 'manage_' . Source_Local::CPT . '_posts_columns', array( $this, 'manage_posts_columns_headers' ) );
		add_action( 'manage_' . Source_Local::CPT . '_posts_custom_column', array( $this, 'manage_posts_columns_content' ), 10, 2 );

		// Editor
		add_action( 'cmsmasters_elementor/documents/header_footer/register_controls', array( $this, 'register_controls' ) );
		add_action( 'cmsmasters_elementor/documents/archive_singular/register_controls', array( $this, 'register_controls' ) );

		add_action( 'elementor/template-library/after_save_template', array( $this, 'on_import_check_template_locations' ), 10, 2 );
		add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ) );

		// Theme
		add_action( 'cmsmasters_regenerate_elementor_locations', array( $this, 'regenerate_location_rules' ) );
		add_action( 'cmsmasters_remove_unique_elementor_locations', array( $this, 'remove_unique_location_rules' ) );
		add_action( 'cmsmasters_remove_all_elementor_locations', array( $this, 'remove_all_location_rules' ) );
		add_action( 'cmsmasters_replace_elementor_locations_id', array( $this, 'replace_location_rules_id' ) );
		add_action( 'cmsmasters_restore_elementor_locations', array( $this, 'restore_location_rules' ) );
	}

	/**
	 * Add filters initialization.
	 *
	 * Register filters for the Template Locations module.
	 *
	 * @since 1.0.0
	 */
	protected function init_filters() {
		// Editor
		add_filter( 'cmsmasters_elementor/documents/before_save_settings', array( $this, 'filter_document_settings_saving' ) );

		add_filter( 'cmsmasters_elementor/editor/settings', array( $this, 'filter_editor_settings' ) );

		add_filter( 'elementor/document/config', array( $this, 'document_config' ), 10, 2 );

		// Common
		add_filter( 'cmsmasters_elementor/documents/container_attributes', array( $this, 'get_container_attributes' ) );
	}

	public function manage_posts_columns_headers( $posts_columns ) {
		return ElementorUtils::array_inject(
			$posts_columns,
			'elementor_library_type',
			array( 'location-rules' => __( 'Location Rules', 'cmsmasters-elementor' ) )
		);
	}

	public function manage_posts_columns_content( $column_name, $post_id ) {
		if ( 'location-rules' !== $column_name ) {
			return;
		}

		$document = $this->documents_module->get_document( $post_id );

		if ( ! $document || ! method_exists( $document, 'get_properties' ) ) {
			printf( '<i>%s</i>', __( 'Disabled', 'cmsmasters-elementor' ) );

			return;
		}

		$document_properties = $document->get_properties();

		if (
			'cmsmasters' !== $document_properties['admin_tab_group'] ||
			'disabled' === $document_properties['location_type']
		) {
			printf( '<i>%s</i>', __( 'Disabled', 'cmsmasters-elementor' ) );

			return;
		}

		$rules_manager = $this->get_rules_manager();

		$document_locations = $rules_manager->get_document_locations( $document );

		if ( empty( $document_locations ) ) {
			_e( 'None', 'cmsmasters-elementor' );

			return;
		}

		$locations = array();
		$exclude_locations = array();

		foreach ( $document_locations as $location ) {
			$location_name = Utils::get_if_not_empty( $location, 'addl', $location['main'] );
			$location_instance = $rules_manager->get_rule_instance( $location_name );

			if ( ! $location_instance ) {
				continue;
			}

			$location_label = $location_instance->get_multiple_title();

			if ( empty( $location['args'] ) ) {
				if ( 'exclude' !== $location['stmt'] ) {
					$locations[] = $location_label;
				} else {
					$exclude_locations[] = $location_label;
				}

				continue;
			}

			$instance_type = $this->get_location_instance_type( $location_instance );
			$location_links = array();

			foreach ( $location['args'] as $id ) {
				$location_links[] = sprintf(
					'<a href="%1$s" title="%2$s" target="_blank">#%3$s</a>',
					$this->get_location_permalink( $id, $instance_type ),
					$this->get_location_title( $id, $instance_type ),
					$id
				);
			}

			if ( 'exclude' !== $location['stmt'] ) {
				$locations[] = sprintf( '%1$s: %2$s', $location_label, implode( ', ', $location_links ) );
			} else {
				$exclude_locations[] = sprintf( '%1$s: %2$s', $location_label, implode( ', ', $location_links ) );
			}
		}

		if ( empty( $locations ) ) {
			_e( 'None', 'cmsmasters-elementor' );
		}

		printf(
			'<strong title="%2$s">%1$s</strong>',
			implode( '<br>', $locations ),
			__( 'Template document include location rule.', 'cmsmasters-elementor' )
		);

		printf(
			'<br><em class="exclude_locations" title="%2$s">%1$s</em>',
			implode( '<br>', $exclude_locations ),
			__( 'Template document exclude location rule.', 'cmsmasters-elementor' )
		);
	}

	protected function get_location_instance_type( $instance ) {
		$type = 'post';

		switch ( $instance->get_group() ) {
			case 'singular':
				if (
					$instance instanceof Rules\Singular\By_Author ||
					$instance instanceof Rules\Singular\Post_Type_By_Author
				) {
					$type = 'author';
				} elseif ( $instance instanceof Rules\Singular\In_Taxonomy ) {
					$type = 'taxonomy';
				}

				break;
			case 'archive':
				if ( $instance instanceof Rules\Archive\Author ) {
					$type = 'author';
				} elseif ( $instance instanceof Rules\Archive\Taxonomy ) {
					$type = 'taxonomy';
				}

				break;
		}

		return $type;
	}

	protected function get_location_permalink( $id, $type ) {
		$link = '';

		switch ( $type ) {
			case 'post':
				$link = get_edit_post_link( $id );

				break;
			case 'author':
				$link = get_edit_user_link( $id );

				break;
			case 'taxonomy':
				$link = get_edit_term_link( $id );

				break;
		}

		if ( empty( $link ) ) {
			return '';
		}

		return esc_url( $link );
	}

	protected function get_location_title( $id, $type ) {
		$title = '';

		switch ( $type ) {
			case 'post':
				$title = get_the_title( $id );

				break;
			case 'author':
				$title = get_the_author_meta( 'display_name', $id );

				break;
			case 'taxonomy':
				if (
					! is_wp_error( get_term( $id ) ) &&
					is_object( get_term( $id ) )
				) {
					$title = get_term( $id )->name;
				}

				break;
		}

		return esc_attr( $title );
	}

	/**
	 * Register Addon document controls.
	 *
	 * Used to add new controls to the global document settings.
	 *
	 * Fired by multiple Addon action hooks.
	 *
	 * @since 1.0.0
	 *
	 * @param Base_Document $document Addon base document instance.
	 */
	public function register_controls( $document ) {
		$title = $document->get_title();

		$rules_config = $this->get_rules_manager()->get_location_rules_config();
		$location_type = $document::get_property( 'location_type' );

		$document->start_controls_section(
			'locations_settings',
			array(
				'tab' => Controls_Manager::TAB_SETTINGS,
				'label' => __( 'Locations', 'cmsmasters-elementor' ),
			)
		);

		$document->add_control(
			'locations',
			array(
				'label' => sprintf(
					/* translators: Template locations control label. %s: Template document type */
					__( 'Where to use your %s?', 'cmsmasters-elementor' ),
					$title
				),
				'type' => CmsmastersControls::LOCATIONS_REPEATER,
				'description' => sprintf(
					/* translators: Template locations control description. 1: Template document type, 2: Main location rule name */
					__( 'Choose where you want this %1$s template to be used - \'%2$s\' or in specific places.', 'cmsmasters-elementor' ),
					$title,
					$rules_config[ $location_type ]['multiple_title']
				),
				'placeholder' => sprintf(
					/* translators: Template locations control placeholder. %s: Template document type */
					__( 'Please add at least one %s location rule, for showing it on your website.', 'cmsmasters-elementor' ),
					$title
				),
				'fields' => $this->get_locations_fields(),
				'default' => $document->get_locations_default(),
				'title_field' => $this->get_locations_title_field(),
				'render_type' => 'none',
				'export' => true,
			)
		);

		$document->end_controls_section();
	}

	protected function get_locations_fields() {
		$repeater = new Repeater();

		$repeater->add_control(
			'truth',
			array(
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => true,
			)
		);

		$repeater->add_control(
			'stmt',
			array(
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'default' => 'include',
			)
		);

		$repeater->add_control(
			'main',
			array(
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
			)
		);

		$repeater->add_control(
			'addl',
			array(
				'label_block' => true,
				'type' => Controls_Manager::SELECT,
			)
		);

		$repeater->add_control(
			'args',
			array(
				'label_block' => true,
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'placeholder' => __( 'All', 'cmsmasters-elementor' ),
			)
		);

		return $repeater->get_controls();
	}

	protected function get_locations_title_field() {
		return '<i class="{{ ( stmt === \'exclude\' ) ? \'eicon-close\' : \'eicon-plus\' }}"></i>' .
		' {{{ cmsmastersElementor.modules.templateLocations.getRepeaterItemTitle( obj ) }}}';
	}

	/**
	 * On import check template locations.
	 *
	 * Checks Addon document locations after Elementor library template saving.
	 *
	 * Fired by `elementor/template-library/after_save_template` action hook.
	 *
	 * @param int $template_id Template ID.
	 * @param array $template_data Template Data.
	 */
	public function on_import_check_template_locations( $template_id, $template_data ) {
		if ( ! isset( $template_data['page_settings'] ) || empty( $template_data['page_settings'] ) ) {
			return;
		}

		$document = $this->documents_module->get_document( $template_id );

		if ( ! $document ) {
			return;
		}

		$available_locations = $this->documents_module->get_location_available_document_types( 'all' );

		if ( ! in_array( $document->get_name(), $available_locations, true ) ) {
			return;
		}

		$document_locations = $document->get_main_meta( Rules_Manager::META_NAME );

		if ( ! empty( $document_locations ) ) {
			return;
		}

		$page_settings = $template_data['page_settings'];

		if ( ! isset( $page_settings['locations'] ) ) {
			$this->remove_template_import_settings( $page_settings, $document );

			return;
		}

		$locations = $this->trim_locations_args( $page_settings['locations'] );

		if ( ! empty( $locations ) ) {
			$locations_array = array();

			foreach ( $locations as $location ) {
				if ( ! $location['truth'] ) {
					continue;
				}

				$locations_array[] = $this->compact_location_rule( $location );
			}

			if ( ! empty( $locations_array ) ) {
				$document->update_main_meta( Rules_Manager::META_NAME, $locations_array );

				$this->regenerate_location_rules();
			}
		}

		$this->remove_template_import_settings( $page_settings, $document );
	}

	public function regenerate_location_rules() {
		$this->get_rules_manager()->regenerate_locations();
	}

	private function remove_template_import_settings( $page_settings, $document ) {
		unset( $page_settings['locations'] );

		$page_settings['cmsmasters_document_export_id'] = $document->get_main_id();
		$page_settings['cmsmasters_document_export_url'] = get_site_url();

		$document->update_main_meta( PageManager::META_KEY, $page_settings );
	}

	public function register_ajax_actions( AjaxModule $ajax ) {
		$ajax->register_ajax_action( 'get_revision_data', array( $this, 'ajax_get_revision_data' ) );

		$ajax->register_ajax_action( 'cmsmasters_template_locations_check_conflicts', array( $this, 'ajax_check_template_locations_conflicts' ) );
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function ajax_get_revision_data( $data ) {
		$revision_data = Plugin::elementor()->revisions_manager->ajax_get_revision_data( $data );

		unset( $revision_data['settings']['locations'] );

		return $revision_data;
	}

	public function ajax_check_template_locations_conflicts( $request ) {
		$post_id = $request['editor_post_id'];
		$location = $request['location'];
		$conflicts = array();

		if ( 'exclude' === $location['stmt'] ) {
			return $conflicts;
		}

		$document = $this->documents_module->get_document( $post_id );

		if ( $document::get_property( 'multiple' ) ) {
			return $conflicts;
		}

		$rules_manager = $this->get_rules_manager();
		$location_groups = $rules_manager->get_data_by_id( $document->get_document_location_type() );

		if ( empty( $location_groups ) ) {
			return $conflicts;
		}

		$searched_location = $this->compact_location_rule( $location, true );

		foreach ( $location_groups as $template_id => $locations_array ) {
			if ( $post_id === $template_id ) {
				continue;
			}

			if ( ! get_post( $template_id ) ) {
				$rules_manager->remove_post_from_storage( $template_id );

				continue;
			}

			$location_rules = $this->combine_unpacked_location_rules( $locations_array );
			$conflicted_links = array_filter( $this->get_conflicted_location_links( $searched_location, $location_rules, $template_id ) );

			if ( ! empty( $conflicted_links ) ) {
				$conflicts[] = $conflicted_links;
			}
		}

		if ( ! empty( $conflicts ) ) {
			return Utils::array_flatten( $conflicts );
		}
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $locations_array
	 *
	 * @return array
	 */
	private function combine_unpacked_location_rules( $locations_array ) {
		$rules_manager = $this->get_rules_manager();
		$location_rules = array();

		foreach ( $locations_array as $location_rule ) {
			$parsed_rule = $rules_manager->parse_location_rule( $location_rule );
			$compacted_rule = $this->compact_location_rule( $parsed_rule, true );

			if ( is_array( $compacted_rule ) ) {
				$location_rules = array_merge( $location_rules, $compacted_rule );
			} else {
				$location_rules[] = $compacted_rule;
			}
		}

		return $location_rules;
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param string $searched_location
	 * @param array $location_rules
	 * @param int $template_id
	 *
	 * @return array
	 */
	private function get_conflicted_location_links( $searched_location, $location_rules, $template_id ) {
		$conflicted_links = array();

		if ( is_array( $searched_location ) ) {
			foreach ( $searched_location as $searched_location_rule ) {
				$conflicted_links[] = current( $this->get_conflicted_location_links( $searched_location_rule, $location_rules, $template_id ) );
			}
		} else {
			if ( false === array_search( $searched_location, $location_rules, true ) ) {
				return array();
			}

			$template_document = $this->documents_module->get_document( $template_id );

			$item_id = substr( strrchr( $searched_location, '/' ), 1 );
			$numeric_id = ( is_numeric( $item_id ) ) ? $item_id : '';

			$conflicted_links[] = sprintf( '<a href="%1$s"%3$s target="_blank">%2$s</a>',
				esc_url( $template_document->get_edit_url() ),
				esc_html( get_the_title( $template_id ) ),
				! empty( $numeric_id ) ? " data-id=\"{$numeric_id}\"" : ''
			);
		}

		return $conflicted_links;
	}

	public function remove_unique_location_rules() {
		foreach ( Rules_Manager::get_templates_with_locations() as $post_id ) {
			$document = $this->documents_module->get_document( $post_id );

			if ( ! $document ) {
				continue;
			}

			$available_locations = $this->documents_module->get_location_available_document_types();

			if ( ! in_array( $document->get_name(), $available_locations, true ) ) {
				continue;
			}

			$locations = $document->get_meta( Rules_Manager::META_NAME );

			if ( empty( $locations ) ) {
				continue;
			}

			$global_locations = array();

			foreach ( $locations as $location ) {
				$location_array = explode( '/', $location );

				if ( 4 > count( $location_array ) ) {
					$global_locations[] = $location;
				}
			}

			if ( ! empty( $global_locations ) ) {
				$document->update_meta( Rules_Manager::META_NAME, $global_locations );
			} else {
				$document->delete_meta( Rules_Manager::META_NAME );
			}
		}

		$this->regenerate_location_rules();
	}

	public function remove_all_location_rules() {
		foreach ( Rules_Manager::get_templates_with_locations() as $post_id ) {
			$document = $this->documents_module->get_document( $post_id );

			if ( ! $document ) {
				continue;
			}

			$document->delete_meta( Rules_Manager::META_NAME );
		}

		$this->regenerate_location_rules();
	}

	public function replace_location_rules_id( $replaces ) {
		foreach ( Rules_Manager::get_templates_with_locations() as $post_id ) {
			$document = $this->documents_module->get_document( $post_id );

			if ( ! $document ) {
				continue;
			}

			$available_locations = $this->documents_module->get_location_available_document_types();

			if ( ! in_array( $document->get_name(), $available_locations, true ) ) {
				continue;
			}

			$locations = $document->get_meta( Rules_Manager::META_NAME );

			if ( empty( $locations ) ) {
				continue;
			}

			$final_locations = array();
			$locations_changed = false;

			foreach ( $locations as $location ) {
				$location_array = explode( '/', $location );

				if ( 4 !== count( $location_array ) ) {
					$final_locations[] = $location;

					continue;
				}

				list(
					$new_location_ids,
					$location_changed
				) = $this->replace_location_rule_ids( $location_array, $replaces );

				if ( $location_changed ) {
					$locations_changed = true;
				}

				$location_array[3] = implode( '|', $new_location_ids );

				$final_locations[] = implode( '/', $location_array );
			}

			if ( ! $locations_changed ) {
				continue;
			}

			$document->update_meta( Rules_Manager::META_NAME, $final_locations );
		}

		$this->regenerate_location_rules();
	}

	private function replace_location_rule_ids( $location_array, $replaces ) {
		$rule_mask = $location_array[2];
		$location_ids = explode( '|', $location_array[3] );

		$new_location_ids = $location_ids;
		$location_changed = false;

		$user_id = get_current_user_id();

		foreach ( $this->filter_location_rule_types( $replaces ) as $location_control => $location_rules ) {
			if ( ! in_array( $rule_mask, $location_rules, true ) ) {
				continue;
			}

			if ( 'author_id' === $location_control ) {
				foreach ( $location_ids as $id_key => $location_id ) {
					$new_location_ids[ $id_key ] = $user_id;
					$location_changed = true;
				}

				return array( $new_location_ids, $location_changed );
			} else {
				$control_replaces = $replaces[ $location_control ];
				$rule_name = $this->get_location_rule_name_by_mask( $rule_mask, $location_control );

				if ( ! isset( $control_replaces[ $rule_name ] ) ) {
					continue;
				}

				$rule_ids = $control_replaces[ $rule_name ];
			}

			foreach ( $location_ids as $id_key => $location_id ) {
				foreach ( $rule_ids as $old_id => $new_id ) {
					if ( (int) $location_id !== $old_id ) {
						continue;
					}

					$new_location_ids[ $id_key ] = $new_id;
					$location_changed = true;
				}
			}
		}

		return array( $new_location_ids, $location_changed );
	}

	private function filter_location_rule_types( $replaces ) {
		$location_rules_config = $this->get_rules_manager()->get_location_rules_config();
		$location_types = array();

		foreach ( $location_rules_config as $location_rule => $location_config ) {
			$location_controls = $location_config['controls'];

			if ( empty( $location_controls ) ) {
				continue;
			}

			$control_type = key( $location_controls );

			if ( ! isset( $location_types[ $control_type ] ) ) {
				$location_types[ $control_type ] = array();
			}

			$location_types[ $control_type ][] = $location_rule;
		}

		foreach ( array_keys( $location_types ) as $current_location_control ) {
			if (
				'author_id' !== $current_location_control &&
				! in_array( $current_location_control, array_keys( $replaces ), true )
			) {
				unset( $location_types[ $current_location_control ] );
			}
		}

		return $location_types;
	}

	private function get_location_rule_name_by_mask( $mask, $control ) {
		switch ( $control ) {
			case 'taxonomy':
				$rule = str_replace( 'in_', '', $mask );

				break;
			case 'post_id':
			case 'author_id':
				$rule = $mask;
		}

		return $rule;
	}

	public function restore_location_rules() {
		$global_locations = get_option( Rules_Manager::OPTION_NAME, array() );
		$available_locations = $this->documents_module->get_location_available_document_types( 'parent' );

		foreach ( $global_locations as $document_location => $document_ids ) {
			if ( ! in_array( $document_location, $available_locations, true ) ) {
				continue;
			}

			foreach ( $document_ids as $template_id => $template_locations ) {
				$document = $this->documents_module->get_document( $template_id );

				if ( ! $document ) {
					continue;
				}

				$document->update_meta( Rules_Manager::META_NAME, $template_locations );
			}
		}

		$this->regenerate_location_rules();
	}

	/**
	 * Filter Addon document settings saving.
	 *
	 * Filters the Addon document settings before saving.
	 *
	 * Fired by `cmsmasters_elementor/documents/before_save_settings` filter hook.
	 *
	 * @param array $settings Document settings.
	 *
	 * @return array Filtered document settings.
	 */
	public function filter_document_settings_saving( $settings ) {
		if ( ! isset( $settings['locations'] ) ) {
			return $settings;
		}

		$document = $this->documents_module->get_document( get_the_ID() );

		if ( ! $document ) {
			return $settings;
		}

		$available_locations = $this->documents_module->get_location_available_document_types();

		if ( ! in_array( $document->get_name(), $available_locations, true ) ) {
			return $settings;
		}

		$locations = $this->trim_locations_args( $settings['locations'] );

		if ( empty( $locations ) ) {
			$document->delete_main_meta( Rules_Manager::META_NAME );
		} else {
			$locations_array = array();

			foreach ( $locations as $location ) {
				if ( ! $location['truth'] ) {
					continue;
				}

				$locations_array[] = $this->compact_location_rule( $location );
			}

			if ( ! empty( $locations_array ) ) {
				$document->update_main_meta( Rules_Manager::META_NAME, $locations_array );
			} else {
				$document->delete_main_meta( Rules_Manager::META_NAME );
			}
		}

		$this->regenerate_location_rules();

		return $settings;
	}

	private function trim_locations_args( $locations ) {
		$trimmed_locations = array();

		if ( ! empty( $locations ) ) {
			foreach ( $locations as $location ) {
				if ( isset( $location['args'] ) && is_array( $location['args'] ) ) {
					$new_args = array_filter( $location['args'] );

					if ( 1 >= count( $new_args ) ) {
						$new_args = implode( '', $new_args );
					}

					$location['args'] = $new_args;
				}

				$trimmed_locations[] = $location;
			}
		}

		return $trimmed_locations;
	}

	/**
	 * @since 1.0.0
	 * @since 1.0.1 Fixed PHP 5.6 support.
	 *
	 * @param array $location
	 * @param bool $check_multiple
	 *
	 * @return string|array
	 */
	public function compact_location_rule( $location, $check_multiple = false ) {
		unset( $location['_id'], $location['truth'] );

		if ( empty( $location['args'] ) ) {
			unset( $location['args'] );
		}

		if ( isset( $location['args'] ) && is_array( $location['args'] ) ) {
			if ( ! $check_multiple ) {
				$location['args'] = implode( '|', $location['args'] );
			} else {
				$locations_array = array();

				foreach ( $location['args'] as $arg ) {
					$location['args'] = $arg;

					$locations_array[] = $this->compact_location_rule( $location );
				}

				return $locations_array;
			}
		}

		return rtrim( implode( '/', $location ), '/' );
	}

	/**
	 * Filter editor settings.
	 *
	 * Filters the Addon settings for elementor editor.
	 *
	 * Fired by `cmsmasters_elementor/editor/settings` Addon filter hook.
	 *
	 * @since 1.0.0
	 *
	 * @param array $settings Editor settings.
	 *
	 * @return array Filtered editor settings.
	 */
	public function filter_editor_settings( $settings ) {
		$settings = array_replace_recursive( $settings, array(
			'i18n' => array(
				'choose' => __( 'Choose', 'cmsmasters-elementor' ),
				'use_for' => __( 'Use for', 'cmsmasters-elementor' ),
				'except' => __( 'Except', 'cmsmasters-elementor' ),
				'save_as_is' => __( 'Save as is', 'cmsmasters-elementor' ),
				'no_locations_notification' => __( 'Please add at least one location rule, for showing this template on your website, or click `UPDATE` once more to save it as is.', 'cmsmasters-elementor' ),
				'draft_no_locations_notification' => __( 'Please add at least one location rule, for showing this template on your website, or click `PUBLISH` once more to save it as is.', 'cmsmasters-elementor' ),
				'draft_check_locations_notification' => __( 'Please check location rules before template publishing, then click `PUBLISH` once more to save template.', 'cmsmasters-elementor' ),
				'incorrect_locations_notification' => __( 'There is an error(s) in your locations! Please fix it before save.', 'cmsmasters-elementor' ),
				'location_exception_message' => __( 'Please add at least 1 correct include rule before using exceptions for template locations.', 'cmsmasters-elementor' ),
				/* translators: Location rules conflict message. %s: Template link */
				'location_conflict_message' => __( 'It looks like you already set this location in another template %s. Please remove it or set more accurate rule.', 'cmsmasters-elementor' ),
				'location_conflicts_message_start' => _x( 'It looks like you already set:', 'Multiple location conflicts message start', 'cmsmasters-elementor' ),
				/* translators: Multiple location rules conflict message links description part. 1: Tag name, 2: Template link */
				'location_conflicts_message_links' => _x( '%1$s location in %2$s template', 'Multiple location conflicts message links description part', 'cmsmasters-elementor' ),
				'location_conflicts_message_instruction' => _x( 'Please remove repetitive rules.', 'Multiple location conflicts message instruction', 'cmsmasters-elementor' ),
			),
		) );

		return $settings;
	}

	public function document_config( $config, $post_id ) {
		$document = $this->documents_module->get_document( $post_id );

		if ( ! $document ) {
			return $config;
		}

		$rules_manager = $this->get_rules_manager();

		$config = array_replace_recursive( $config, array(
			'template_locations' => array(
				'document_types' => $this->documents_module->get_document_types_properties(),
				'location_rules' => $rules_manager->get_location_rules_config(),
				'settings_array' => $rules_manager->get_document_locations( $document ),
				'template_type' => Source_Local::get_template_type( $post_id ),
			),
		) );

		return $config;
	}

	public function get_container_attributes( $attributes ) {
		$location = $this->get_locations_manager()->get_current_location();

		if ( $location ) {
			$attributes['class'] .= ' cmsmasters-location-' . $location;
		}

		return $attributes;
	}

}
