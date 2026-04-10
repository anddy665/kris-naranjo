<?php
namespace CmsmastersFramework\Admin\Installer\Importer;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Utils;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * GiveWP handler class is responsible for different methods on importing "GiveWP" plugin.
 *
 * @since 1.0.0
 */
class Give_WP {

	/**
	 * Options.
	 *
	 * @since 1.0.0
	 */
	protected $options = array();

	/**
	 * GiveWP Import constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( self::activation_status() && API_Requests::check_token_status() ) {
			add_action( 'admin_init', array( $this, 'admin_init_actions' ) );

			add_action( 'import_end', array( $this, 'import_form_meta' ), 9 );
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
		return class_exists( 'Give' );
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
		return get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_givewp_import', $default );
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

		update_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_givewp_import', $status );
	}

	/**
	 * Set exists options.
	 *
	 * @since 1.0.0
	 */
	protected function set_exists_options() {
		$this->options = get_option( CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_givewp', array() );
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

		$data = Utils::get_import_demo_data( 'givewp' );

		if ( empty( $data ) || empty( $data['settings'] ) ) {
			return;
		}

		$data = json_decode( $data['settings'], true );

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

		Logger::info( 'Start of import GiveWP settings' );

		update_option( 'give_settings', $this->options );

		Logger::info( 'End of import GiveWP settings' );
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

		$options = get_option( 'give_settings', array() );

		$option_name = CMSMASTERS_OPTIONS_PREFIX . Utils::get_demo() . '_givewp';

		if ( $first_install ) {
			$option_name = CMSMASTERS_OPTIONS_PREFIX . 'givewp_backup';
		}

		update_option( $option_name, $options );
	}

	/**
	 * Import form meta.
	 *
	 * @since 1.0.0
	 * @since 1.0.2 Fixed GiveWP forms import.
	 * @since 1.0.5 Fixed GiveWP forms meta import.
	 */
	public function import_form_meta() {
		$data = Utils::get_import_demo_data( 'givewp' );

		if ( empty( $data ) || empty( $data['form-meta'] ) ) {
			return;
		}

		$old_forms_data = json_decode( $data['form-meta'], true );

		if ( ! is_array( $old_forms_data ) || empty( $old_forms_data ) ) {
			return;
		}
		
		$this->delete_exists_campaign_pages();

		$demo = Utils::get_demo();

		Logger::info( 'Start of import GiveWP forms' );

		foreach ( $old_forms_data as $old_form_id => $old_form_meta ) {
			$displayed_ids = get_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', array() );

			if ( isset( $displayed_ids['post_id']['give_forms'][ $old_form_id ] ) ) {
				Logger::debug( 'Attempting to import a form that already exists. Old id: ' . $old_form_id . ' - New id: ' . $displayed_ids['post_id']['give_forms'][ $old_form_id ] );

				continue;
			}

			Logger::info( 'Start of import GiveWP form. Old id: ' . $old_form_id );

			$formBuilderSettings = json_decode( $old_form_meta['formBuilderSettings'], true );

			$formBuilderSettings['designSettingsLogoUrl'] = $this->rearrange_image_url( $formBuilderSettings['designSettingsLogoUrl'] );
			$formBuilderSettings['designSettingsImageUrl'] = $this->rearrange_image_url( $formBuilderSettings['designSettingsImageUrl'] );

			$old_form_meta['formBuilderSettings'] = json_encode( $formBuilderSettings );

			$request = new \WP_REST_Request( 'POST', '/givewp/v3/campaigns' );
			$request->set_param( 'title', $formBuilderSettings['formTitle'] );
			$request->set_param( 'shortDescription', $formBuilderSettings['formExcerpt'] );
			$request->set_param( 'longDescription', $formBuilderSettings['description'] );
			$request->set_param( 'logo', $formBuilderSettings['designSettingsLogoUrl'] );
			$request->set_param( 'image', $formBuilderSettings['designSettingsImageUrl'] );
			$request->set_param( 'primaryColor', $formBuilderSettings['primaryColor'] );
			$request->set_param( 'secondaryColor', $formBuilderSettings['secondaryColor'] );
			$request->set_param( 'goal', $formBuilderSettings['goalAmount'] );
			$request->set_param( 'goalType', $formBuilderSettings['goalType'] );
			$request->set_param( 'startDate', null );
			$request->set_param( 'endDate', null );

			$controller = give( \Give\Campaigns\Controllers\CampaignRequestController::class );

			$response = $controller->createCampaign( $request );

			if ( is_wp_error( $response ) ) {
				Logger::error( 'Form creation error. Old id: ' . $old_form_id );

				Logger::error( $response->get_error_message() );

				continue;
			}

			$new_form_data = $response->get_data();

			if ( ! isset( $new_form_data['defaultFormId'] ) || empty( $new_form_data['defaultFormId'] ) ) {
				Logger::error( 'Form creation error. Empty defaultFormId. Old id: ' . $old_form_id );

				continue;
			}

			$new_form_id = $new_form_data['defaultFormId'];

			$displayed_ids['post_id']['give_forms'][ $old_form_id ] = $new_form_id;

			update_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_displayed_ids', $displayed_ids, false );

			foreach ( $old_form_meta as $old_form_meta_key => $old_form_meta_value ) {
				Give()->form_meta->update_meta( $new_form_id, $old_form_meta_key, wp_slash( $old_form_meta_value ) );
			}

			Logger::info( 'End of import GiveWP form. Old id: ' . $old_form_id . ' - New id: ' . $new_form_id );
		}

		Logger::info( 'End of import GiveWP forms' );
	}

	/**
	 * Delete exists campaigns pages before import.
	 *
	 * @since 1.0.2
	 */
	public function delete_exists_campaign_pages() {
		$pages = get_posts( array(
			'post_type' => 'page',
			'post_status' => 'any',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'give_campaign_id',
					'compare' => 'EXISTS',
				),
			),
			'fields' => 'ids',
		) );

		if ( empty( $pages ) ) {
			return;
		}

		foreach ( $pages as $page_id ) {
			wp_delete_post( $page_id, true );
		}
	}

	/**
	 * Rearrange image url.
	 *
	 * @since 1.0.2
	 */
	public function rearrange_image_url( $url = '' ) {
		if ( empty( $url ) ) {
			return '';
		}

		if ( preg_match( '#/uploads/(?:sites/\d+/)?(.+)$#', $url, $matches ) ) {
			$relativePath = $matches[1];

			$uploadDir = wp_upload_dir();
			$newUrl = trailingslashit( $uploadDir['baseurl'] ) . ltrim( $relativePath, '/' );
			$newPath = trailingslashit( $uploadDir['basedir'] ) . ltrim( $relativePath, '/' );

			if ( file_exists( $newPath ) ) {
				return $newUrl;
			}
		}

		return $url;
	}

}
