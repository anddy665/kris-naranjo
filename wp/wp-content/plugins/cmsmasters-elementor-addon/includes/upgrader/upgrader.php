<?php
namespace CmsmastersElementor\Upgrader;

use CmsmastersElementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Upgrader.
 *
 * Main class for upgrader.
 *
 * @since 1.7.4
 */
class Upgrader {

	const VERSIONS = array(
		'1.11.2',
	);

	const VERSION_OPTION_NAME = 'cmsmasters_elementor_version';

	/**
	 * Upgrader constructor.
	 *
	 * @since 1.7.4
	 */
	public function __construct() {
		$this->set_first_version();

		if ( CMSMASTERS_ELEMENTOR_VERSION === self::get_current_version() ) {
			return;
		}

		set_transient( 'cmsmasters_elementor_clear_cache', 'pending' );

		$this->run_upgrades();

		$this->set_latest_version();

		$this->clear_cache();
	}

	/**
	 * Run upgrades.
	 *
	 * Runs upgrades from the current version to the latest.
	 *
	 * @since 1.7.4
	 */
	public function run_upgrades() {
		if ( empty( self::VERSIONS ) ) {
			return;
		}

		$current_version = self::get_current_version();

		foreach ( self::VERSIONS as $version ) {
			if ( 0 <= version_compare( $current_version, $version ) ) {
				continue;
			}

			$class_name = 'CmsmastersElementor\\Upgrader\\Versions\\Version_' . str_replace( array( '.', '-' ), '_', $version );

			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			new $class_name();
		}
	}

	/**
	 * Set first time version.
	 *
	 * @since 1.11.6
	 *
	 * @return string
	 */
	protected function set_first_version() {
		if ( get_option( self::VERSION_OPTION_NAME, false ) ) {
			return;
		}

		if ( 0 >= version_compare( CMSMASTERS_ELEMENTOR_VERSION, '1.12.0' ) ) {
			update_option( self::VERSION_OPTION_NAME, '1.11.0' );

			return;
		}

		$this->set_latest_version();
	}

	/**
	 * Get current version from DB.
	 *
	 * @since 1.7.4
	 *
	 * @return string
	 */
	public static function get_current_version() {
		return get_option( self::VERSION_OPTION_NAME, '1.0.0' );
	}

	/**
	 * Set latest version.
	 *
	 * @since 1.7.4
	 */
	protected function set_latest_version() {
		update_option( self::VERSION_OPTION_NAME, CMSMASTERS_ELEMENTOR_VERSION );
	}

	/**
	 * Clear cache.
	 *
	 * Delete all meta containing files data. And delete the actual
	 * files from the upload directory.
	 *
	 * @since 1.7.4
	 */
	public function clear_cache() {
		if ( 'pending' !== get_transient( 'cmsmasters_elementor_clear_cache' ) ) {
			return;
		}

		Plugin::elementor()->files_manager->clear_cache();

		delete_transient( 'cmsmasters_elementor_clear_cache' );
	}

}
