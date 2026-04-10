<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Forminator handler class is responsible for different methods on importing "Forminator" plugin forms.
 *
 * @since 1.0.0
 */
class Forminator {

	/**
	 * Forminator Import constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'cmsmasters_set_import_status', array( get_called_class(), 'set_import_status' ) );

		if ( self::activation_status() && API_Requests::check_token_status() ) {
			add_action( 'admin_init', array( $this, 'remove_from_wp_export' ) );

			add_action( 'admin_init', array( $this, 'admin_init_actions' ) );
		}

		add_filter( 'forminator_form_model_to_exportable_data', array( $this, 'filter_export_data' ), 10, 3 );
	}

	/**
	 * Remove custom post types from WP export.
	 *
	 * @since 1.0.0
	 */
	public function remove_from_wp_export() {
		global $wp_post_types;

		$post_types_to_disable = array(
			'forminator_forms',
			'forminator_polls',
			'forminator_quizzes',
		);

		foreach ( $post_types_to_disable as $cpt ) {
			if ( isset( $wp_post_types[ $cpt ] ) ) {
				$wp_post_types[ $cpt ]->can_export = false;
			}
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
		return class_exists( 'Forminator' );
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_forminator_import', $default );
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

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_forminator_import', $status );
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
		$demo = Utils::get_demo();

		$data = Utils::get_import_demo_data( 'forminator' );

		if ( false === $data ) {
			return;
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}

		Logger::info( 'Start of import Forminator data' );

		foreach ( $data as $type => $forms ) {
			if ( empty( $forms ) ) {
				continue;
			}

			$class_type = ucfirst( $type );
			$class_name = "Forminator_{$class_type}_Model";

			foreach ( $forms as $form ) {
				$original_form = json_decode( $form, true );
				$new_form = $class_name::create_from_import_data( $original_form );
				$new_id = $new_form->id;

				if ( $new_id && isset( $original_form['post_id'] ) ) {
					$original_id = $original_form['post_id'];

					$displayed_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', array() );

					$displayed_ids['post_id']['forminator_forms'][ $original_id ] = $new_id;

					update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', $displayed_ids, false );
				}
			}
		}

		Logger::info( 'End of import Forminator data' );
	}

	/**
	 * Filter export data.
	 *
	 * @since 1.0.0
	 *
	 * @param array $exportable_data Exportable data.
	 * @param string $module_type Module type.
	 * @param int $model_id Model ID.
	 *
	 * @return array Filtered exportable data.
	 */
	public function filter_export_data( $exportable_data, $module_type, $model_id ) {
		$exportable_data['post_id'] = $model_id;

		return $exportable_data;
	}

}
