<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\File_Manager;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

use Elementor\Plugin as Elementor_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Templates handler class is responsible for different methods on importing "Elementor" plugin templates.
 *
 * @since 1.0.0
 */
class Elementor_Templates {

	/**
	 * Cached data.
	 *
	 * @since 1.0.0
	 */
	private static $cached_import_status = array();

	/**
	 * Import demo data.
	 *
	 * @since 1.0.0
	 */
	private static $import_demo_data = array();

	/**
	 * Elementor Templates Import constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'cmsmasters_set_import_status', array( get_called_class(), 'set_import_status' ) );

		add_action( 'cmsmasters_set_apply_demo_status', array( get_called_class(), 'set_apply_demo_status' ) );

		add_action( 'cmsmasters_set_backup_options', array( get_called_class(), 'set_backup_options' ) );

		if ( self::activation_status() && API_Requests::check_token_status() ) {
			add_action( 'admin_init', array( $this, 'admin_init_actions' ) );

			add_action( 'elementor/template-library/after_save_template', array( $this, 'set_import_templates_ids' ), 10, 2 );
		}
	}

	/**
	 * Activation status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Activation status.
	 */
	public static function activation_status() {
		return ( did_action( 'elementor/loaded' ) && class_exists( 'Cmsmasters_Elementor_Addon' ) );
	}

	/**
	 * Set import status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Import status, may be pending or done.
	 */
	public static function set_import_status( $status = 'pending' ) {
		$demo = Utils::get_demo();

		if ( 'done' !== get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_import' ) ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_import', $status );
		}

		if ( 'done' !== get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_woocommerce_import' ) ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_woocommerce_import', $status );
		}

		if ( 'done' !== get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_pmpro_import' ) ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_pmpro_import', $status );
		}

		if ( 'done' !== get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_givewp_import' ) ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_givewp_import', $status );
		}

		if ( 'done' !== get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_tribe-events_import' ) ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_tribe-events_import', $status );
		}
	}

	/**
	 * Set apply demo status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Apply demo status, may be pending or done.
	 */
	public static function set_apply_demo_status( $status = 'pending' ) {
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'elementor_templates_apply_demo', $status );
	}

	/**
	 * Backup current options.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $first_install First install trigger, if need to backup customer option from previous theme.
	 */
	public static function set_backup_options( $first_install = false ) {
		if ( $first_install ) {
			return;
		}

		$options = get_option( 'cmsmasters_elementor_documents_locations', array() );

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_elementor_documents_locations', $options );

		do_action( 'cmsmasters_remove_all_elementor_locations' );
	}

	/**
	 * Actions on admin_init hook.
	 *
	 * @since 1.0.0
	 */
	public function admin_init_actions() {
		if ( wp_doing_ajax() ) {
			return;
		}

		if (
			isset( $_POST['tgmpa-page'] ) &&
			'tgmpa-install-plugins' === $_POST['tgmpa-page']
		) {
			return;
		}

		$demo = Utils::get_demo();

		if ( 'pending' === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_import', 'done' ) ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_import', 'done' );

			$this->import_templates( 'templates' );
		}

		if (
			class_exists( 'woocommerce' ) &&
			'pending' === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_woocommerce_import', 'done' )
		) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_woocommerce_import', 'done' );

			$this->import_templates( 'templates-woocommerce' );
		}

		if (
			function_exists( 'pmpro_is_plugin_active' ) &&
			'pending' === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_pmpro_import', 'done' )
		) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_pmpro_import', 'done' );

			$this->import_templates( 'templates-pmpro' );
		}

		if (
			class_exists( 'Give' ) &&
			'pending' === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_givewp_import', 'done' )
		) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_givewp_import', 'done' );

			$this->import_templates( 'templates-givewp' );
		}

		if (
			class_exists( 'Tribe__Events__Main' ) &&
			'pending' === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_tribe-events_import', 'done' )
		) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_templates_tribe-events_import', 'done' );

			$this->import_templates( 'templates-tribe-events' );
		}

		if ( 'pending' === get_option( CMSMASTERS_OPTIONS_PREFIX . 'elementor_templates_apply_demo', 'done' ) ) {
			if ( false === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_documents_locations' ) ) {
				if ( ! did_action( 'cmsmasters_remove_unique_elementor_locations' ) ) {
					do_action( 'cmsmasters_remove_unique_elementor_locations' );
				}
			} else {
				$locations = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_documents_locations', array() );

				update_option( 'cmsmasters_elementor_documents_locations', $locations );

				if ( ! did_action( 'cmsmasters_restore_elementor_locations' ) ) {
					do_action( 'cmsmasters_restore_elementor_locations' );
				}
			}

			update_option( CMSMASTERS_OPTIONS_PREFIX . 'elementor_templates_apply_demo', 'done' );
		}
	}

	/**
	 * Import templates.
	 *
	 * @since 1.0.0
	 */
	protected function import_templates( $data_key ) {
		if (
			! empty( self::$cached_import_status[ $data_key ] ) &&
			'done' === self::$cached_import_status[ $data_key ]
		) {
			return;
		}

		if ( empty( self::$import_demo_data ) ) {
			self::$import_demo_data = Utils::get_import_demo_data( 'files_urls' );
		}

		if ( empty( self::$import_demo_data ) || empty( self::$import_demo_data[ $data_key ] ) ) {
			return;
		}

		self::$cached_import_status[ $data_key ] = 'done';

		$file_path = File_Manager::download_temp_file( self::$import_demo_data[ $data_key ], $data_key . '-' . uniqid() . '.zip' );

		$extracted_files = Elementor_Plugin::$instance->uploads_manager->extract_and_validate_zip( $file_path, [ 'json' ] );

		if ( is_wp_error( $extracted_files ) ) {
			// Delete the temporary extraction directory, since it's now not necessary.
			Elementor_Plugin::$instance->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );

			Logger::error( "Templates import: Error with extracted files from {$file_path}" );

			return;
		}

		$demo = Utils::get_demo();

		Logger::info( 'Start of import templates.' );

		foreach ( $extracted_files['files'] as $template_file_path ) {
			$template_data = json_decode( File_Manager::get_file_contents( $template_file_path ), true );

			if ( empty( $template_data ) || ! isset( $template_data['page_settings']['cmsmasters_document_export_id'] ) ) {
				Logger::error( 'Templates import: Invalid template file data' );

				continue;
			}

			$imported_templates_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_import_templates_ids' );

			if ( isset( $imported_templates_ids[ $template_data['page_settings']['cmsmasters_document_export_id'] ] ) ) {
				Logger::error( 'Skipping import template ' . $template_data['title'] . ', this template already imported' );

				continue;
			}

			$source = Elementor_Plugin::$instance->templates_manager->get_source( 'local' );

			Logger::info( 'Start of import template ' . $template_data['title'] );

			$source->import_template( basename( $template_file_path ), $template_file_path );

			Logger::info( 'End of import template ' . $template_data['title'] );
		}

		// Delete the temporary extraction directory, since it's now not necessary.
		Elementor_Plugin::$instance->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );

		@unlink( $file_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		Logger::info( 'End of import templates.' );
	}

	/**
	 * Set import templates ids.
	 *
	 * @since 1.0.0
	 *
	 * @param int $template_id Template id.
	 * @param array $template_data Template data.
	 */
	public function set_import_templates_ids( $template_id, $template_data ) {
		$demo = Utils::get_demo();

		$templates_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_import_templates_ids' );

		if ( false === $templates_ids ) {
			$templates_ids = array();
		}

		if ( ! isset( $template_data['page_settings']['cmsmasters_document_export_id'] ) ) {
			return;
		}

		$old_id = $template_data['page_settings']['cmsmasters_document_export_id'];

		if ( empty( $old_id ) ) {
			return;
		}

		$templates_ids[ $old_id ] = $template_id;

		update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_import_templates_ids', $templates_ids, false );
	}

}
