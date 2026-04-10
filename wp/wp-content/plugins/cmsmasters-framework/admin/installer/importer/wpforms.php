<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPForms handler class is responsible for different methods on importing "WPForms" plugin forms.
 *
 * @since 1.0.0
 */
class WPForms {

	/**
	 * WPForms Import constructor.
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
		return function_exists( 'wpforms' );
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wpforms_import', $default );
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

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_wpforms_import', $status );
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

		$this->import_forms();

		self::set_import_status( 'done' );
	}

	/**
	 * Import forms.
	 *
	 * @since 1.0.0
	 */
	protected function import_forms() {
		$data = Utils::get_import_demo_data( 'wpforms' );

		if ( empty( $data ) ) {
			return;
		}

		$forms = json_decode( $data, true );

		if ( empty( $forms ) || ! is_array( $forms ) ) {
			return;
		}

		$demo = Utils::get_demo();

		Logger::info( 'Start of import WPForms data' );

		foreach ( $forms as $form ) {
			$title  = ! empty( $form['settings']['form_title'] ) ? $form['settings']['form_title'] : '';
			$desc   = ! empty( $form['settings']['form_desc'] ) ? $form['settings']['form_desc'] : '';

			$new_id = wp_insert_post( array(
				'post_title' => $title,
				'post_status' => 'publish',
				'post_type' => 'wpforms',
				'post_excerpt' => $desc,
			) );

			if ( $new_id ) {
				$displayed_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', array() );

				$displayed_ids['post_id']['wpforms'][ $form['id'] ] = $new_id;

				update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', $displayed_ids, false );

				$form['id'] = $new_id;

				wp_update_post( array(
					'ID' => $new_id,
					'post_content' => wpforms_encode( $form ),
				) );
			}
		}

		Logger::info( 'End of import WPForms data' );
	}

}
