<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Admin\Installer\Importer\Importer_Base;
use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\File_Manager;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

use Elementor\Plugin as Elementor_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Elementor Kit handler class is responsible for different methods on importing "Elementor" plugin kit.
 *
 * @since 1.0.0
 */
class Elementor_Kit extends Importer_Base {

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
		return did_action( 'elementor/loaded' );
	}

	/**
	 * Actions on admin_init hook.
	 *
	 * @since 1.0.0
	 */
	public function admin_init_actions() {
		$this->create_kit();

		parent::admin_init_actions();
	}

	/**
	 * Create kit.
	 *
	 * Creates a kit if the kit was not created earlier.
	 *
	 * @since 1.0.0
	 */
	protected function create_kit() {
		if ( '' !== Utils::get_active_kit() ) {
			return;
		}

		Elementor_Plugin::$instance->kits_manager->get_active_kit();

		$kits_path = get_parent_theme_file_path( '/theme-config/defaults/main-kits.json' );
		$kits = File_Manager::get_file_contents( $kits_path );

		if ( '' !== $kits ) {
			$kits = json_decode( $kits, true );

			$this->set_kit_options( $kits );
		}
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . 'elementor_kit_import', $default );
	}

	/**
	 * Set import status.
	 *
	 * @since 1.0.0
	 *
	 * @param string $status Import status, may be pending or done.
	 */
	public static function set_import_status( $status = 'pending' ) {
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'elementor_kit_import', $status );
	}

	/**
	 * Set exists options.
	 *
	 * @since 1.0.0
	 */
	protected function set_exists_options() {
		if ( 'demos' === CMSMASTERS_THEME_IMPORT_TYPE ) {
			$demo_key = Utils::get_demo();
		} else {
			$demo_key = Utils::get_demo_kit();
		}

		$this->options = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo_key . '_elementor_kit', array() );
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

		if ( 'demos' === CMSMASTERS_THEME_IMPORT_TYPE ) {
			$demo_key = Utils::get_demo();
		} else {
			$demo_key = Utils::get_demo_kit();
		}

		$data = '';

		$demo_data = Utils::get_import_demo_data();

		if (
			! empty( $demo_data ) &&
			$demo_key === $demo_data['demo_kit'] &&
			! empty( $demo_data['kits'] )
		) {
			$data = $demo_data['kits'];
		}

		if ( empty( $data ) ) {
			$data = API_Requests::post_request( 'get-elementor-kits', array( 'demo' => $demo_key ) );

			if ( is_wp_error( $data ) ) {
				Logger::error( $data->get_error_message() );

				return;
			}
		}

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

		Logger::info( 'Start of import Kits' );

		$this->set_kit_options( $this->options );

		Logger::info( 'End of import Kits' );

		Elementor_Plugin::$instance->files_manager->clear_cache();
	}

	/**
	 * Set kit options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Kit options.
	 */
	protected function set_kit_options( $options ) {
		unset( $options['site_name'], $options['site_description'], $options['site_favicon'] );

		Utils::set_kit_options( $options );
	}

	/**
	 * Backup current options.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $first_install First install trigger, if need to backup customer option from previous theme.
	 */
	public static function set_backup_options( $first_install = false ) {
		if (
			! self::activation_status() ||
			'' === Utils::get_active_kit()
		) {
			return;
		}

		$options = Utils::get_kit_options();

		if ( empty( $options ) ) {
			return;
		}

		if ( 'demos' === CMSMASTERS_THEME_IMPORT_TYPE ) {
			$demo_key = Utils::get_demo();
		} else {
			$demo_key = Utils::get_demo_kit();
		}

		$option_name = CMSMASTERS_OPTIONS_PREFIX . $demo_key . '_elementor_kit';

		if ( $first_install ) {
			$option_name = CMSMASTERS_OPTIONS_PREFIX . 'elementor_kit_backup';
		}

		update_option( $option_name, $options );
	}

}
