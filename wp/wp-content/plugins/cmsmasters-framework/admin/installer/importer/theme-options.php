<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Admin\Installer\Importer\Importer_Base;
use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme_Options handler class is responsible for different methods on importing theme options.
 *
 * @since 1.0.0
 */
class Theme_Options extends Importer_Base {

	/**
	 * Options.
	 *
	 * @since 1.0.0
	 */
	protected $options = array();

	/**
	 * Activation status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Activation status.
	 */
	public static function activation_status() {
		return true;
	}

	/**
	 * Get import status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $default Import status by default, may be pending or done.
	 *
	 * @return string Import status.
	 */
	public static function get_import_status( $default = 'done' ) {
		return get_option( CMSMASTERS_OPTIONS_PREFIX . 'theme_options_import', $default );
	}

	/**
	 * Set import status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Import status, may be pending or done.
	 */
	public static function set_import_status( $status = 'pending' ) {
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'theme_options_import', $status );
	}

	/**
	 * Set exists options.
	 *
	 * @since 1.0.0
	 */
	protected function set_exists_options() {
		$this->options = get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_theme_options', array() );
	}

	/**
	 * Set options from API.
	 *
	 * @since 1.0.0
	 */
	protected function set_api_options() {
		if ( ! empty( $this->options ) ) {
			return;
		}

		$data = Utils::get_import_demo_data( 'theme-options' );

		if ( empty( $data ) ) {
			return;
		}

		$data = json_decode( $data, true );

		if ( is_array( $data ) && ! empty( $data ) ) {
			$this->options = $data;
		}
	}

	/**
	 * Import options.
	 *
	 * @since 1.0.0
	 */
	protected function import_options() {
		if ( empty( $this->options ) ) {
			return;
		}

		Logger::info( 'Start of import Theme Options' );

		update_option( CMSMASTERS_OPTIONS_NAME, $this->options );

		Logger::info( 'End of import Theme Options' );
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

		$options = Utils::get_theme_options();

		if ( empty( $options ) ) {
			return;
		}

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_theme_options', $options );
	}

}
