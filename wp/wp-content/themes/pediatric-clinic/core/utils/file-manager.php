<?php
namespace PediatricClinicSpace\Core\Utils;

use PediatricClinicSpace\Core\Utils\Utils;

use Elementor\Core\Responsive\Files\Frontend as ElementorResponsiveFilesFrontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * File_Manager handler class is responsible for different utility methods with files.
 */
class File_Manager {

	/**
	 * Get responsive stylesheets path.
	 *
	 * Retrieve the responsive stylesheet templates path.
	 *
	 * @param string $path_prefix Optional. Default is ''.
	 *
	 * @return string Responsive stylesheet templates path.
	 */
	public static function get_responsive_css_path( $path_prefix = '' ) {
		return get_parent_theme_file_path( '/' . $path_prefix . 'assets/css/templates/' );
	}

	/**
	 * Get js assets url
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
	 * Get css template assets url
	 *
	 * @param string $file_name
	 * @param string $relative_url Optional. Default is null.
	 * @param string $add_min_suffix Optional. Default is 'default'.
	 * @param bool $add_direction_suffix Optional. Default is `false`
	 * @param string $path_prefix Optional. Default is ''.
	 *
	 * @return string
	 */
	public static function get_css_template_assets_url( $file_name, $relative_url = null, $add_min_suffix = 'default', $add_direction_suffix = false, $path_prefix = '' ) {
		if ( Utils::has_custom_breakpoints() ) {
			$is_rtl = is_rtl() ? '-rtl' : '';
			$is_min = Utils::is_dev_mode() ? '' : '.min';
			$file = "{$file_name}{$is_rtl}{$is_min}.css";

			$responsive_file = new ElementorResponsiveFilesFrontend( "pediatric-clinic-{$file}", self::get_responsive_css_path( $path_prefix ) . $file );

			if ( ! $responsive_file->get_meta( 'time' ) ) {
				$responsive_file->update();
			}

			$file_url = $responsive_file->get_url();
		} else {
			$file_url = self::get_css_assets_url( $file_name, $relative_url, $add_min_suffix, $add_direction_suffix, $path_prefix );
		}

		return $file_url;
	}

	/**
	 * Get css assets url
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

		$url = get_template_directory_uri() . '/' . $path_prefix . $relative_url . $file_name;

		if ( 'default' === $add_min_suffix ) {
			$add_min_suffix = ! Utils::is_dev_mode();
		}

		if ( $add_min_suffix ) {
			$url .= '.min';
		}

		return $url . '.' . $file_extension;
	}

	/**
	 * Get WP_Filesystem.
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
	 * Get file contents.
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

}
