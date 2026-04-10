<?php
namespace CmsmastersFramework\Core\Utils;

use CmsmastersFramework\Core\Utils\Utils;

use CmsmastersElementor\Utils as CmsmastersElementor_Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * File_Manager handler class is responsible for different utility methods with files.
 *
 * @since 1.0.0
 */
class File_Manager {

	/**
	 * Get WP_Filesystem.
	 *
	 * @since 1.0.0
	 */
	public static function get_wp_filesystem() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();
		}

		if ( ! $wp_filesystem ) {
			return false;
		}

		return $wp_filesystem;
	}

	/**
	 * Download temp file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url File url.
	 * @param string $file_name File name.
	 * @param string $temp_path Relative path to temp folder.
	 *
	 * @return string Downloaded temp file path.
	 */
	public static function download_temp_file( $url, $file_name, $temp_path = 'elementor/tmp' ) {
		$wp_upload_dir = wp_upload_dir();

		$temp_path = $wp_upload_dir['basedir'] . '/' . $temp_path;

		$created = wp_mkdir_p( $temp_path );

		if ( ! $created ) {
			return false;
		}

		$file_path = $temp_path . '/' . $file_name;

		$response = wp_remote_get(
			$url,
			array(
				'timeout'  => 300,
				'stream'   => true,
				'filename' => $file_path,
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code ) {
			return false;
		}

		return $file_path;
	}

	/**
	 * Upload and extract zip.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_path File path.
	 *
	 * @return string Path to extract directory.
	 */
	public static function upload_and_extract_zip( $file_path ) {
		if ( ! did_action( 'elementor/loaded' ) || ! class_exists( 'Cmsmasters_Elementor_Addon' ) ) {
			return new \WP_Error( 'required_plugins_activation', esc_html__( 'Required plugins not activated', 'cmsmasters-framework' ) );
		}

		$wp_filesystem = self::get_wp_filesystem();

		$extract_to = trailingslashit( get_temp_dir() . pathinfo( $file_path, PATHINFO_FILENAME ) );
		$unzipped = CmsmastersElementor_Utils::extract_zip( $file_path, $extract_to );

		if ( is_wp_error( $unzipped ) ) {
			return $unzipped;
		}

		$source_files = array_keys( $wp_filesystem->dirlist( $extract_to ) ); // Find the right folder.

		if ( 0 === count( $source_files ) ) {
			return new \WP_Error( 'incompatible_archive', esc_html__( 'Incompatible archive', 'cmsmasters-framework' ) );
		} elseif (
			1 === count( $source_files ) &&
			$wp_filesystem->is_dir( $extract_to . $source_files[0] )
		) {
			$directory = $extract_to . trailingslashit( $source_files[0] );
		} else {
			$directory = $extract_to;
		}

		return $directory;
	}

	/**
	 * Get js assets url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name
	 * @param string $relative_url Optional. Default is null.
	 * @param string $add_min_suffix Optional. Default is 'default'.
	 * @param string $path_prefix Optional. Default is ''.
	 *
	 * @return string
	 */
	public static function get_js_assets_url( $file_name, $relative_url = null, $add_min_suffix = 'default', $path_prefix = '' ) {
		return self::get_assets_url( $file_name, 'js', $relative_url, $add_min_suffix, $path_prefix );
	}

	/**
	 * Get css assets url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name
	 * @param string $relative_url Optional. Default is null.
	 * @param string $add_min_suffix Optional. Default is 'default'.
	 * @param bool $add_direction_suffix Optional. Default is `false`.
	 * @param string $path_prefix Optional. Default is ''.
	 *
	 * @return string
	 */
	public static function get_css_assets_url( $file_name, $relative_url = null, $add_min_suffix = 'default', $add_direction_suffix = false, $path_prefix = '' ) {
		static $direction_suffix = null;

		if ( ! $direction_suffix ) {
			$direction_suffix = is_rtl() ? '-rtl' : '';
		}

		if ( $add_direction_suffix ) {
			$file_name .= $direction_suffix;
		}

		return self::get_assets_url( $file_name, 'css', $relative_url, $add_min_suffix, $path_prefix );
	}

	/**
	 * Get assets url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name
	 * @param string $file_extension
	 * @param string $relative_url Optional. Default is null.
	 * @param string $add_min_suffix Optional. Default is 'default'.
	 * @param string $path_prefix Optional. Default is ''.
	 *
	 * @return string
	 */
	public static function get_assets_url( $file_name, $file_extension, $relative_url = null, $add_min_suffix = 'default', $path_prefix = '' ) {
		if ( ! $relative_url ) {
			$relative_url = 'assets/' . $file_extension . '/';
		}

		$url = CMSMASTERS_FRAMEWORK_URL . $path_prefix . $relative_url . $file_name;

		if ( 'default' === $add_min_suffix ) {
			$add_min_suffix = ! Utils::is_dev_mode();
		}

		if ( $add_min_suffix ) {
			$url .= '.min';
		}

		return $url . '.' . $file_extension;
	}

	/**
	 * Get upload files path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dir_name Directory name.
	 * @param string $file_name File name.
	 * @param bool $url true if need url.
	 * @param bool $slash true if need slash in the end of path.
	 *
	 * @return string path to directory/file.
	 */
	public static function get_upload_path( $dir_name = '', $file_name = '', $url = false, $slash = false ) {
		if ( $url ) {
			$out = Utils::get_upload_dir_parameter( 'baseurl' );
		} else {
			$out = Utils::get_upload_dir_parameter( 'basedir' );
		}

		$theme_slug = defined( 'CMSMASTERS_THEME_NAME' ) ? CMSMASTERS_THEME_NAME : 'cmsmasters-framework';
		$out = str_replace( '\\', '/', $out . '/cmsmasters/' . $theme_slug );

		if ( '' !== $dir_name ) {
			$out = $out . '/' . $dir_name;
		}

		if ( '' !== $file_name ) {
			$out = $out . '/' . $file_name;
		} elseif ( $slash ) {
			$out = $out . '/';
		}

		return $out;
	}

	/**
	 * Get file contents.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file File path.
	 *
	 * @return mixed File contents.
	 */
	public static function get_file_contents( $file ) {
		$wp_filesystem = self::get_wp_filesystem();

		if ( ! $wp_filesystem || ! file_exists( $file ) ) {
			return '';
		}

		return $wp_filesystem->get_contents( $file );
	}

	/**
	 * Write file.
	 *
	 * Runs create_folder, create_file and writes content into created file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $content Content to write into file.
	 * @param string $dir_name Directory name.
	 * @param string $filename File name.
	 * @param string $filetype File type.
	 */
	public static function write_file( $content, $dir_name, $filename, $filetype = 'css' ) {
		$upload_dir = self::get_upload_path( $dir_name );

		$is_dir = self::create_folder( $upload_dir );

		if ( false === $is_dir ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $dir_name . '_exists', 'no', false );

			return;
		}

		$file = trailingslashit( $upload_dir ) . $filename . '.' . $filetype;

		$created = self::create_file( $file, $content );

		if ( true === $created ) {
			update_option( CMSMASTERS_OPTIONS_PREFIX . $dir_name . '_exists', 'yes', false );
		}
	}

	/**
	 * Create folder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $folder Folder path.
	 * @param bool $addindex Add index file to created folder.
	 *
	 * @return bool Folder condition.
	 */
	public static function create_folder( &$folder, $addindex = true ) {
		if ( is_dir( $folder ) && false === $addindex ) {
			return true;
		}

		$created = wp_mkdir_p( trailingslashit( $folder ) );

		if ( false === $addindex ) {
			return $created;
		}

		$index_file = trailingslashit( $folder ) . 'index.php';

		if ( file_exists( $index_file ) ) {
			return $created;
		}

		$wp_filesystem = self::get_wp_filesystem();

		if ( ! $wp_filesystem ) {
			return false;
		}

		$wp_filesystem->put_contents(
			$index_file,
			"<?php\n// Silence is golden.\n"
		);

		return $created;
	}

	/**
	 * Create File.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file Path to file.
	 * @param string $content Content to write into file.
	 *
	 * @return bool File condition.
	 */
	public static function create_file( $file, $content = '' ) {
		$wp_filesystem = self::get_wp_filesystem();

		if ( ! $wp_filesystem ) {
			return false;
		}

		$created = $wp_filesystem->put_contents(
			$file,
			$content
		);

		if ( false !== $created ) {
			$created = true;
		}

		return $created;
	}

	/**
	 * Delete uploaded directory.
	 *
	 * @since 1.0.0
	 *
	 * @param string $dir_name Directory name.
	 *
	 * @return bool Deleted status.
	 */
	public static function delete_uploaded_dir( $dir_name = '' ) {
		$wp_filesystem = self::get_wp_filesystem();

		if ( ! $wp_filesystem ) {
			return false;
		}

		$upload_dir = self::get_upload_path( $dir_name );

		$wp_filesystem->delete( $upload_dir, true );

		delete_option( CMSMASTERS_OPTIONS_PREFIX . $dir_name . '_exists' );
	}

	/**
	 * Generate child theme.
	 *
	 * @since 1.0.2
	 *
	 * @return bool Generation status.
	 */
	public static function generate_child() {
		$theme = wp_get_theme();
		$slug = sanitize_title( $theme . ' Child' );
		$path = get_theme_root() . '/' . $slug;

		if ( file_exists( $path ) ) {
			return false;
		}

		$wp_filesystem = self::get_wp_filesystem();

		if ( ! $wp_filesystem ) {
			return false;
		}

		wp_mkdir_p( $path );

		$wp_filesystem->put_contents( $path . '/style.css', self::generate_child_style_css( $theme ) );
		
		$wp_filesystem->put_contents( $path . '/functions.php', self::generate_child_functions_php( $theme->template ) );

		self::generate_child_screenshot( $path, $theme );

		$allowed_themes = get_option( 'allowedthemes' );
		$allowed_themes[ $slug ] = true;

		update_option( 'allowedthemes', $allowed_themes );

		return true;
	}

	/**
	 * Content template for the child theme functions.php file.
	 *
	 * @since 1.0.2
	 *
	 * @param object $theme Theme data.
	 */
	public static function generate_child_style_css( $theme ) {
		$output = "
			/**
			* Theme Name: {$theme->name} Child
			* Description: This is a child theme of {$theme->name}.
			* Author: {$theme->author}
			* Template: {$theme->template}
			* Version: 1.0.0
			* Tested up to: 6.6
			* Requires PHP: 7.4
			* License:
			* License URI:
			* Text Domain: {$theme->template}-child
			* Copyright: cmsmasters 2025 / All Rights Reserved
			*/\n
		";

		$output = trim( preg_replace( '/\t+/', '', $output ) );

		Logger::debug( 'The child theme style.css content was generated' );

		return $output;
	}

	/**
	 * Content template for the child theme functions.php file.
	 *
	 * @since 1.0.2
	 *
	 * @param string $slug Parent theme slug.
	 */
	public static function generate_child_functions_php( $slug ) {
		$slug_no_hyphens = strtolower( preg_replace( '#[^a-zA-Z]#', '', $slug ) );

		$output = "
			<?php
			/**
			 * Theme functions and definitions.
			 */
			function {$slug_no_hyphens}_child_enqueue_styles() {
				wp_enqueue_style( '{$slug}-child-style',
					get_stylesheet_directory_uri() . '/style.css',
					array(),
					wp_get_theme()->get('Version')
				);
			}

			add_action( 'wp_enqueue_scripts', '{$slug_no_hyphens}_child_enqueue_styles', 11 );

		";

		$output = trim( preg_replace( '/\t+/', '', $output ) );

		Logger::debug( 'The child theme functions.php content was generated' );

		return $output;
	}

	/**
	 * Generate child theme screenshot file.
	 *
	 * @since 1.0.2
	 *
	 * @param string $path Child theme path.
	 * @param object $theme Theme data.
	 */
	public static function generate_child_screenshot( $path, $theme ) {
		$screenshot_base_path = get_theme_root() . '/' . $theme->template;

		if ( file_exists( $screenshot_base_path . '/screenshot.png' ) ) {
			$screenshot = $screenshot_base_path . '/screenshot.png';
			$screenshot_ext = 'png';
		} elseif ( file_exists( $screenshot_base_path . '/screenshot.jpg' ) ) {
			$screenshot = $screenshot_base_path . '/screenshot.jpg';
			$screenshot_ext = 'jpg';
		}

		if ( ! empty( $screenshot ) && file_exists( $screenshot ) ) {
			$copied = copy( $screenshot, $path . '/screenshot.' . $screenshot_ext );

			Logger::debug( 'The child theme screenshot was copied to the child theme, with the following result', array( 'copied' => $copied ) );
		} else {
			Logger::debug( 'The child theme screenshot was not generated, because of these results', array( 'screenshot' => $screenshot ) );
		}
	}

}
