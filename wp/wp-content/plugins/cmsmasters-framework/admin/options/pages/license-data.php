<?php
namespace CmsmastersFramework\Admin\Options\Pages;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * License Data handler class is responsible for different methods on license theme options page.
 *
 * @since 1.0.0
 */
class License_Data extends Base\Base_Page {

	/**
	 * Page constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_cmsmasters_update_license_data', array( $this, 'ajax_update_license_data' ) );
	}

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return esc_attr__( 'License Data', 'cmsmasters-framework' );
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return esc_attr__( 'License Data', 'cmsmasters-framework' );
	}

	/**
	 * Visibility Status.
	 *
	 * @since 1.0.0
	 */
	public static function get_visibility_status() {
		return API_Requests::check_token_status();
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		$user_name = '';
		$user_email = '';

		$data = API_Requests::post_request( 'get-client-data' );

		if ( is_wp_error( $data ) ) {
			Logger::error( $data->get_error_message() );
		}

		if ( ! is_wp_error( $data ) && ! empty( $data ) ) {
			$user_name = ( ! empty( $data['user_name'] ) ? $data['user_name'] : '' );
			$user_email = ( ! empty( $data['verified_email'] ) ? $data['verified_email'] : '' );
		}

		echo '<div class="cmsmasters-options-license-data">';

			echo '<h3 class="cmsmasters-options-license-data__title">' . esc_html__( 'License Data', 'cmsmasters-framework' ) . '</h3>';

			echo '<div class="cmsmasters-options-license-data__user-info">
				<div class="cmsmasters-options-license-data__user-info--text">
					<p>' . esc_html__( 'Get information about promotions, new themes and theme updates directly to your Inbox.', 'cmsmasters-framework' ) . '</p>
					<p>' . esc_html__( 'You can change your name and email anytime.', 'cmsmasters-framework' ) . '</p>
				</div>
				<div class="cmsmasters-options-license-data__user-info--item">
					<input type="text" name="cmsmasters_options_license_data__user_name" placeholder="' . esc_attr__( 'Your Name', 'cmsmasters-framework' ) . '" value="' . esc_attr( $user_name ) . '" />
				</div>
				<div class="cmsmasters-options-license-data__user-info--item">
					<input type="text" name="cmsmasters_options_license_data__user_email" placeholder="' . esc_attr__( 'Your Email', 'cmsmasters-framework' ) . '" value="' . esc_attr( $user_email ) . '" />
				</div>
				<p class="cmsmasters-options-license-data__user-info--privacy">' .
					sprintf(
						esc_html__( 'Your data is stored and processed in accordance with our %1$s', 'cmsmasters-framework' ),
						'<a href="' . esc_url( 'https://cmsmasters.studio/privacy-policy/' ) . '" target="_blank">' .
							esc_html__( 'Privacy Policy', 'cmsmasters-framework' ) .
						'</a>'
					) .
				'</p>
			</div>';

			echo '<div class="cmsmasters-options-license-data__button-wrap">
				<button type="button" class="button cmsmasters-button-spinner" data-action="update-license-data">' . esc_html__( 'Update', 'cmsmasters-framework' ) . '</button>
				<span class="cmsmasters-notice"></span>
			</div>';

		echo '</div>';
	}

	/**
	 * Ajax update license data.
	 *
	 * @since 1.0.0
	 */
	public function ajax_update_license_data() {
		if ( ! check_ajax_referer( 'cmsmasters_options_nonce', 'nonce' ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_nonce',
				'message' => esc_html__( 'Yikes! Data update failed. Please try again.', 'cmsmasters-framework' ),
			) );
		}

		if ( empty( $_POST['user_email'] ) || false === is_email( $_POST['user_email'] ) ) {
			wp_send_json(
				array(
					'success' => false,
					'code' => 'invalid_email',
					'error_field' => 'email',
					'message' => esc_html__( 'Oops, looks like you made a mistake with the email address', 'cmsmasters-framework' ),
				)
			);
		}

		$data = API_Requests::post_request( 'update-client-data', array(
			'user_name' => empty( $_POST['user_name'] ) ? '' : $_POST['user_name'],
			'user_email' => $_POST['user_email'],
			'token' => get_option( CMSMASTERS_OPTIONS_PREFIX . 'token', 'invalid' ),
		) );

		if ( is_wp_error( $data ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => $data->get_error_message(),
				)
			);
		}

		if ( empty( $data ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => esc_html__( 'Empty data in request update-client-data', 'cmsmasters-framework' ),
				)
			);
		}

		wp_send_json( array(
			'success' => true,
			'message' => $data['message'],
		) );
	}

}
