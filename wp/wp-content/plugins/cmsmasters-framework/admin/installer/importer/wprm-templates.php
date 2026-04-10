<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPRM_Templates handler class is responsible for different methods on importing "WP Recipe Maker" plugin templates.
 *
 * @since 1.0.0
 */
class WPRM_Templates {

	/**
	 * WPRM_Templates Import constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'cmsmasters_set_import_status', array( get_called_class(), 'set_import_status' ) );

		if ( self::activation_status() && API_Requests::check_token_status() ) {
			add_action( 'admin_init', array( $this, 'admin_init_actions' ) );
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
		return class_exists( 'WP_Recipe_Maker' );
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wprm_templates_import', $default );
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

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wprm_templates_import', $status );
	}

	/**
	 * Actions on admin_init hook.
	 *
	 * @since 1.0.0
	 */
	public function admin_init_actions() {
		if ( 'pending' !== self::get_import_status( 'done' ) ) {
			return;
		}

		$this->import_templates();

		self::set_import_status( 'done' );
	}

	/**
	 * Import templates.
	 *
	 * @since 1.0.0
	 */
	protected function import_templates() {
		$data = Utils::get_import_demo_data( 'wprm' );

		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		Logger::info( 'Start of import WP Recipe Maker data' );

		foreach ( $data as $template ) {
			$template = json_decode( $template, true );

			if ( empty( $template ) || ! is_array( $template ) ) {
				continue;
			}

			\WPRM_Template_Manager::save_template( $template );
		}

		Logger::info( 'End of import WP Recipe Maker data' );
	}

}
