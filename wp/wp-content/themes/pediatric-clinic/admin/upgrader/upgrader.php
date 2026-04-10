<?php
namespace PediatricClinicSpace\Admin\Upgrader;

use PediatricClinicSpace\Admin\Upgrader\Upgrader_Utils;
use PediatricClinicSpace\ThemeConfig\Theme_Config;

use Elementor\Plugin as Elementor_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Upgrader.
 *
 * Main class for upgrader.
 */
class Upgrader {

	/**
	 * Upgrader constructor.
	 */
	public function __construct() {
		if ( CMSMASTERS_THEME_VERSION === Upgrader_Utils::get_current_version() ) {
			return;
		}

		update_option( 'cmsmasters_pediatric-clinic_clear_cache', 'pending', false );

		$this->run_upgrades();

		$this->set_version();

		$this->clear_cache();
	}

	/**
	 * Run upgrades.
	 *
	 * Runs upgrades from the current version to the latest.
	 */
	public function run_upgrades() {
		if ( empty( Theme_Config::MAJOR_VERSIONS ) ) {
			return;
		}

		$current_major_version = Upgrader_Utils::get_major_version();

		foreach ( Theme_Config::MAJOR_VERSIONS as $major_version ) {
			$compare_result = version_compare( $current_major_version, $major_version );

			if ( 0 < $compare_result ) {
				continue;
			}

			$class_name = 'PediatricClinicSpace\\ThemeConfig\\UpgraderVersions\\Version_' . str_replace( array( '.', '-' ), '', $major_version );

			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			new $class_name( 0 === $compare_result );
		}
	}

	/**
	 * Set latest version.
	 */
	protected function set_version() {
		update_option( 'cmsmasters_pediatric-clinic_version', CMSMASTERS_THEME_VERSION );
	}

	/**
	 * Clear cache.
	 *
	 * Delete all meta containing files data. And delete the actual
	 * files from the upload directory.
	 */
	public function clear_cache() {
		if (
			'pending' !== get_option( 'cmsmasters_pediatric-clinic_clear_cache' ) ||
			! did_action( 'elementor/loaded' )
		) {
			return;
		}

		add_action( 'elementor/init', function() {
			Elementor_Plugin::$instance->files_manager->clear_cache();

			delete_option( 'cmsmasters_pediatric-clinic_clear_cache' );
		} );
	}

}
