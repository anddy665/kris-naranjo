<?php
namespace PediatricClinicSpace\Admin\Plugin_Activator;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin Activator.
 *
 * Main class for plugin activator.
 */
class Plugin_Activator {

	/**
	 * Plugin_Activator constructor.
	 */
	public function __construct() {
		// Include the TGM_Plugin_Activation class.
		require_once get_template_directory() . '/admin/plugin-activator/class-tgm-plugin-activation.php';

		add_action( 'tgmpa_register', array( $this, 'tgmpa_run' ) );
	}

	/**
	 * Run TGMPA.
	 */
	public function tgmpa_run() {
		$plugins_list = $this->get_api_public_plugins();

		if ( empty( $plugins_list ) || ! is_array( $plugins_list ) ) {
			return;
		}

		$api_plugins_list = apply_filters( 'cmsmasters_plugins_list_filter', array() );

		if ( is_array( $api_plugins_list ) && ! empty( $api_plugins_list ) ) {
			$plugins_list = array_merge( $plugins_list, $api_plugins_list );
		}

		$config = $this->get_config();

		tgmpa( $plugins_list, $config );
	}

	/**
	 * Get API public plugins.
	 *
	 * @return array Plugins list.
	 */
	public function get_api_public_plugins() {
		$public_plugins_list = get_transient( 'cmsmasters_public_plugins_list' );

		if ( ! empty( $public_plugins_list ) && is_array( $public_plugins_list ) ) {
			return $public_plugins_list;
		}

		if ( is_numeric( $public_plugins_list ) && 2 < (int) $public_plugins_list ) {
			return false;
		}

		if ( 'failed_format' === $public_plugins_list ) {
			return false;
		}

		$response = wp_remote_get(
			'https://github.com/cmsmasters/public-plugins/releases/download/list/public-plugins.json',
			array(
				'timeout' => 120,
			)
		);

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$attempt = is_numeric( $public_plugins_list ) ? (int) $public_plugins_list + 1 : 1;
			$lifetime = ( 2 < $attempt ) ? MINUTE_IN_SECONDS * 10 : MINUTE_IN_SECONDS;

			set_transient( 'cmsmasters_public_plugins_list', $attempt, $lifetime );

			$this->get_api_public_plugins();

			return false;
		}

		$public_plugins_list = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $public_plugins_list ) || ! is_array( $public_plugins_list ) ) {
			set_transient( 'cmsmasters_public_plugins_list', 'failed_format', MINUTE_IN_SECONDS * 10 );

			return false;
		}

		set_transient( 'cmsmasters_public_plugins_list', $public_plugins_list, DAY_IN_SECONDS );

		return $public_plugins_list;
	}

	/**
	 * Get configuration settings list.
	 *
	 * @return array Configuration settings list.
	 */
	private function get_config() {
		return array(
			'id' => 'pediatric-clinic', // Unique ID for hashing notices for multiple instances of TGMPA.
			'menu' => 'tgmpa-install-plugins', // Menu slug.
			'has_notices' => true, // Show admin notices or not.
			'dismissable' => true, // If false, a user cannot dismiss the nag message.
			'is_automatic' => false, // Automatically activate plugins after installation or not.
		);
	}

}
