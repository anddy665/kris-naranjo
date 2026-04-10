<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\File_Manager;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

use CmsmastersElementor\Modules\WebFonts\Types\Local as CmsmastersElementor_WebFonts_Local;
use CmsmastersElementor\Modules\WebFonts\Services\Base\Base_Service as CmsmastersElementor_WebFonts_Base_Service;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Fonts handler class is responsible for different methods on importing "Elementor" plugin fonts.
 *
 * @since 1.0.0
 */
class Elementor_Fonts {

	/**
	 * Elementor Fonts Import constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'cmsmasters_set_import_status', array( get_called_class(), 'set_import_status' ) );

		if ( self::activation_status() && API_Requests::check_token_status() ) {
			add_action( 'admin_init', array( $this, 'get_local_fonts' ) );
		}
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

		if ( 'done' === get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_fonts_import' ) ) {
			return;
		}

		update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_fonts_import', $status );
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
	 * Import fonts.
	 *
	 * @since 1.0.0
	 */
	public function get_local_fonts() {
		$demo = Utils::get_demo();

		if ( 'pending' !== get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_fonts_import', 'done' ) ) {
			return;
		}

		$data = Utils::get_import_demo_data( 'files_urls' );

		if ( false === $data || empty( $data['fonts'] ) ) {
			return;
		}

		$file_path = File_Manager::download_temp_file( $data['fonts'], 'fonts-' . uniqid() . '.zip' );

		$this->import_fonts( basename( $file_path ), $file_path );

		@unlink( $file_path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_elementor_fonts_import', 'done' );
	}

	/**
	 * Import fonts.
	 *
	 * Import font from a file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name - The file name
	 * @param string $path - The file path
	 *
	 * @return \WP_Error|array An array of items on success, 'WP_Error' on failure.
	 */
	public function import_fonts( $name, $path ) {
		if ( empty( $path ) ) {
			return new \WP_Error( 'file_error', 'Please upload a file to import' );
		}

		$file_extension = pathinfo( $name, PATHINFO_EXTENSION );

		if ( 'zip' === $file_extension ) {
			if ( ! class_exists( '\ZipArchive' ) ) {
				return new \WP_Error( 'zip_error', 'PHP Zip extension not loaded' );
			}

			Logger::info( 'Start of import Fonts' );

			$zip = new \ZipArchive();

			$wp_upload_dir = wp_upload_dir();

			$temp_path = $wp_upload_dir['basedir'] . '/elementor/tmp/' . uniqid();

			$zip->open( $path );

			$valid_entries = [];

			// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
			for ( $i = 0; $i < $zip->numFiles; $i++ ) {
				$zipped_file_name = $zip->getNameIndex( $i );
				$zipped_extension = pathinfo( $zipped_file_name, PATHINFO_EXTENSION );

				if ( 'zip' === $zipped_extension ) {
					$valid_entries[] = $zipped_file_name;
				}
			}

			if ( ! empty( $valid_entries ) ) {
				$zip->extractTo( $temp_path, $valid_entries );
			}

			$zip->close();

			$files_paths = $this->find_temp_files( $temp_path );

			foreach ( $files_paths as $file_path ) {
				$import_result = $this->import_single_font( $file_path );

				if ( is_wp_error( $import_result ) ) {
					return $import_result;
				}
			}

			rmdir( $temp_path );

			Logger::info( 'End of import Fonts' );
		}
	}

	/**
	 * Find temporary files.
	 *
	 * Recursively finds a list of temporary files from the extracted zip file.
	 *
	 * Example return data:
	 *
	 * [
	 *  0 => '/www/wp-content/uploads/elementor/tmp/5eb3a7a411d44/templates/block-2-col-marble-title.json',
	 *  1 => '/www/wp-content/uploads/elementor/tmp/5eb3a7a411d44/templates/block-2-col-text-and-photo.json',
	 * ]
	 *
	 * @since 1.0.0
	 *
	 * @param string $temp_path - The temporary file path to scan for template files
	 *
	 * @return array An array of temporary files on the filesystem
	 */
	private function find_temp_files( $temp_path ) {
		$file_names = array();

		$possible_file_names = array_diff( scandir( $temp_path ), array( '.', '..' ) );

		// Find nested files in the unzipped path. This happens for example when the user imports a Template Kit.
		foreach ( $possible_file_names as $possible_file_name ) {
			$full_possible_file_name = $temp_path . '/' . $possible_file_name;

			if ( is_dir( $full_possible_file_name ) ) {
				$file_names = $file_names + $this->find_temp_files( $full_possible_file_name );
			} else {
				$file_names[] = $full_possible_file_name;
			}
		}

		return $file_names;
	}

	/**
	 * Import single font.
	 *
	 * Import font from a file to the database.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name File name.
	 *
	 * @return \WP_Error|int|array Local font array, or font ID, or
	 *                             `WP_Error`.
	 */
	private function import_single_font( $file_path ) {
		$post_id = wp_insert_post( wp_slash( array(
			'post_type' => CmsmastersElementor_WebFonts_Local::CPT,
			'post_title' => esc_html__( '(no title)', 'cmsmasters-framework' ),
			'post_status' => 'draft',
		) ), true );

		if ( is_wp_error( $post_id ) ) {
			/**
			 * @var \WP_Error $post_id
			 */
			return $post_id;
		}

		// do_action( 'cmsmasters_local_font_upload' );

		$upload_result = $this->local_font_upload_handler( array(
			'post_id' => $post_id,
			'file_path' => $file_path,
		) );

		if ( is_wp_error( $upload_result ) ) {
			return $upload_result;
		}
	}

	/**
	 * Upload local font.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data.
	 *
	 * @return array Config.
	 */
	public function local_font_upload_handler( $data ) {
		$current_post_id = $data['post_id'];

		$extract_directory = File_Manager::upload_and_extract_zip( $data['file_path'] );

		if ( is_wp_error( $extract_directory ) ) {
			return $extract_directory;
		}

		$config = '';

		foreach ( CmsmastersElementor_WebFonts_Local::get_supported_services() as $handler ) {
			/**
			 * @var CmsmastersElementor_WebFonts_Base_Service $service
			 */
			$service = new $handler( $extract_directory );

			if ( ! $service || ! $service->is_valid() ) {
				continue;
			}

			$service->handle_new_local_font();

			$service->move_files( $current_post_id );

			$config = $service->build_config();

			break;
		}

		if ( empty( $config ) ) {
			return new \WP_Error( 'unsupported_zip_format', 'The zip file provided is not supported!' );
		}

		$post_data = array(
			'ID' => $current_post_id,
			'post_title' => $config['label'],
			'post_status' => 'publish',
			'meta_input' => array(
				CmsmastersElementor_WebFonts_Local::FONTS_META_KEY => json_encode( $config ),
				CmsmastersElementor_WebFonts_Local::STYLES_META_KEY => CmsmastersElementor_WebFonts_Local::generate_font_faces( $config ),
			),
		);

		wp_update_post( wp_slash( $post_data ) );

		CmsmastersElementor_WebFonts_Local::refresh_local_fonts_config();

		return true;
	}

}
