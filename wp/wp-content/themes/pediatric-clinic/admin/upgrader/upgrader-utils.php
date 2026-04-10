<?php
namespace PediatricClinicSpace\Admin\Upgrader;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Upgrader.
 *
 * Main class for upgrader.
 */
class Upgrader_Utils {

	/**
	 * Get current version from DB.
	 *
	 * @return string
	 */
	public static function get_current_version() {
		return get_option( 'cmsmasters_pediatric-clinic_version', CMSMASTERS_THEME_VERSION );
	}

	/**
	 * Get current major version.
	 *
	 * @param mixed $version Version string or false to get current version.
	 *
	 * @return string
	 */
	public static function get_major_version( $version = false ) {
		if ( ! $version ) {
			$version = self::get_current_version();
		}

		return substr( $version, 0, strrpos( $version, '.' ) );
	}

	/**
	 * Get current minor version.
	 *
	 * @param mixed $version Version string or false to get current version.
	 *
	 * @return string
	 */
	public static function get_minor_version( $version = false ) {
		if ( ! $version ) {
			$version = self::get_current_version();
		}

		return substr( $version, strrpos( $version, '.' ) + 1 );
	}

}
