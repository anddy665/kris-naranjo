<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Admin\Installer\Importer\Importer_Base;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * ACF handler class is responsible for different methods on importing "Advanced Custom Fields" plugin options.
 *
 * @since 1.0.0
 */
class ACF extends Importer_Base {

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
		return function_exists( 'acf_import_field_group' );
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . 'acf_import', $default );
	}

	/**
	 * Set import status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Import status, may be pending or done.
	 */
	public static function set_import_status( $status = 'pending' ) {
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'acf_import', $status );
	}

	/**
	 * Set exists options.
	 *
	 * @since 1.0.0
	 */
	protected function set_exists_options() {
		$this->options = get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_acf', array() );
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

		$data = Utils::get_import_demo_data( 'acf' );

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

		Logger::info( 'Start of import ACF data' );

		foreach ( $this->options as $field_group ) {
			// Search database for existing field group.
			$import_post = acf_get_field_group_post( $field_group['key'] );

			if ( $import_post ) {
				$field_group['ID'] = $import_post->ID;
			}

			// Import field group.
			acf_import_field_group( $field_group );
		}

		Logger::info( 'End of import ACF data' );
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

		$options = array();
		$field_groups = acf_get_field_groups();

		foreach ( $field_groups as $field_args ) {
			$field_group = acf_get_field_group( $field_args['key'] );

			if ( empty( $field_group ) ) {
				continue;
			}

			$field_group['fields'] = acf_get_fields( $field_group );

			$field_group = acf_prepare_field_group_for_export( $field_group );

			$options[] = $field_group;
		}

		if ( empty( $options ) ) {
			return;
		}

		$option_name = CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_acf';

		if ( $first_install ) {
			$option_name = CMSMASTERS_OPTIONS_PREFIX . 'acf_backup';
		}

		update_option( $option_name, $options );
	}

}
