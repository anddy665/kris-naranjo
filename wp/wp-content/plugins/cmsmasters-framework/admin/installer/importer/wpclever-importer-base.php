<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPClever_Importer_Base handler class is responsible for different methods on importing "WPClever" plugins.
 *
 * @since 1.0.0
 */
class WPClever_Importer_Base {

	/**
	 * Module data.
	 *
	 * @since 1.0.0
	 */
	const MODULE_NAME = '';
	const MODULE_OPTION_NAME = '';

	/**
	 * Options.
	 *
	 * @since 1.0.0
	 */
	protected $options = array();

	/**
	 * WPClever Import constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( self::activation_status() && API_Requests::check_token_status() ) {
			add_action( 'admin_init', array( $this, 'admin_init_actions' ) );
		}

		add_action( 'cmsmasters_set_backup_options', array( $this, 'set_backup_options' ) );

		add_action( 'cmsmasters_set_import_status', array( $this, 'set_import_status' ) );
	}

	/**
	 * Activation status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Activation status.
	 */
	public static function activation_status() {
		return false;
	}

	/**
	 * Actions on admin_init hook.
	 *
	 * @since 1.0.0
	 */
	public function admin_init_actions() {
		if ( 'pending' !== static::get_import_status( 'done' ) ) {
			return;
		}

		$this->set_exists_options();

		$this->set_api_options();

		$this->import_options();

		static::set_import_status( 'done' );
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wpclever_' . self::MODULE_NAME . '_import', $default );
	}

	/**
	 * Set import status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Import status, may be pending or done.
	 */
	public static function set_import_status( $status = 'pending' ) {
		if ( 'done' === self::get_import_status( false ) ) {
			return;
		}

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wpclever_' . self::MODULE_NAME . '_import', $status );
	}

	/**
	 * Set exists options.
	 *
	 * @since 1.0.0
	 */
	protected function set_exists_options() {
		$this->options = get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wpclever_' . self::MODULE_NAME, array() );
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

		$data = Utils::get_import_demo_data( 'wpclever' );

		if ( empty( $data ) || empty( $data[ self::MODULE_NAME ] ) ) {
			return;
		}

		$data = json_decode( $data[ self::MODULE_NAME ], true );

		if ( empty( $data ) ) {
			return;
		}

		$this->options = $data;
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

		Logger::error( 'Start of import WpClever ' . str_replace( '-', ' ', ucwords( self::MODULE_NAME, '-' ) ) );

		update_option( self::MODULE_OPTION_NAME, $this->options );

		Logger::error( 'End of import WpClever ' . str_replace( '-', ' ', ucwords( self::MODULE_NAME, '-' ) ) );
	}

	/**
	 * Backup current options.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $first_install First install trigger, if need to backup customer option from previous theme.
	 */
	public static function set_backup_options( $first_install = false ) {
		if ( ! self::activation_status() ) {
			return;
		}

		$options = get_option( self::MODULE_OPTION_NAME, array() );

		$option_name = CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wpclever_' . self::MODULE_NAME;

		if ( $first_install ) {
			$option_name = CMSMASTERS_OPTIONS_PREFIX . 'wpclever_' . self::MODULE_NAME . '_backup';
		}

		update_option( $option_name, $options );
	}

}
