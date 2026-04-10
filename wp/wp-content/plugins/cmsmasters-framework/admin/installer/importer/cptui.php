<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Admin\Installer\Importer\Importer_Base;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CPTUI handler class is responsible for different methods on importing "Custom Post Type UI" plugin options.
 *
 * @since 1.0.0
 */
class CPTUI extends Importer_Base {

	/**
	 * Post types options.
	 *
	 * @since 1.0.0
	 */
	protected $post_types_options = array();

	/**
	 * Taxonomies options.
	 *
	 * @since 1.0.0
	 */
	protected $tax_options = array();

	/**
	 * Activation status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Activation status.
	 */
	public static function activation_status() {
		return function_exists( 'cptui_import_types_taxes_settings' );
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . 'cptui_import', $default );
	}

	/**
	 * Set import status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Import status, may be pending or done.
	 */
	public static function set_import_status( $status = 'pending' ) {
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'cptui_import', $status );
	}

	/**
	 * Set exists options.
	 *
	 * @since 1.0.0
	 */
	protected function set_exists_options() {
		$demo = Utils::get_demo();

		$this->post_types_options = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_cptui_post_types', array() );
		$this->tax_options = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_cptui_taxonomies', array() );
	}

	/**
	 * Set options from API.
	 *
	 * @since 1.0.0
	 */
	protected function set_api_options() {
		if ( ! empty( $this->post_types_options ) || ! empty( $this->tax_options ) ) {
			return;
		}

		$data = Utils::get_import_demo_data( 'cptui' );

		if ( false === $data ) {
			return;
		}

		if ( isset( $data['post-types'] ) && '' !== $data['post-types'] ) {
			$post_types_data = stripslashes_deep( trim( $data['post-types'] ) );
			$post_types_data = json_decode( $post_types_data, true );

			if ( is_array( $post_types_data ) ) {
				$this->post_types_options = $post_types_data;
			}
		}

		if ( isset( $data['taxonomies'] ) && '' !== $data['taxonomies'] ) {
			$tax_data = stripslashes_deep( trim( $data['taxonomies'] ) );
			$tax_data = json_decode( $tax_data, true );

			if ( is_array( $tax_data ) ) {
				$this->tax_options = $tax_data;
			}
		}
	}

	/**
	 * Import options.
	 *
	 * @since 1.0.0
	 */
	protected function import_options() {
		if ( empty( $this->post_types_options ) && empty( $this->tax_options ) ) {
			return;
		}

		Logger::info( 'Start of import CPTUI data' );

		update_option( 'cptui_post_types', $this->post_types_options );
		update_option( 'cptui_taxonomies', $this->tax_options );
		set_transient( 'cptui_flush_rewrite_rules', 'true', 5 * 60 );

		Logger::info( 'End of import CPTUI data' );
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

		$post_types_options = get_option( 'cptui_post_types', array() );
		$tax_options = get_option( 'cptui_taxonomies', array() );

		$demo = Utils::get_demo();
		$post_types_option_name = CMSMASTERS_OPTIONS_PREFIX . $demo . '_cptui_post_types';
		$tax_option_name = CMSMASTERS_OPTIONS_PREFIX . $demo . '_cptui_taxonomies';

		if ( $first_install ) {
			$post_types_option_name = CMSMASTERS_OPTIONS_PREFIX . 'cptui_post_types_backup';
			$tax_option_name = CMSMASTERS_OPTIONS_PREFIX . 'cptui_taxonomies_backup';
		}

		update_option( $post_types_option_name, $post_types_options );
		update_option( $tax_option_name, $tax_options );
	}

}
