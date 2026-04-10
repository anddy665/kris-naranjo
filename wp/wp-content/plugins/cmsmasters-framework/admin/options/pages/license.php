<?php
namespace CmsmastersFramework\Admin\Options\Pages;

use CmsmastersFramework\Core\Utils\API_Requests;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * License handler class is responsible for different methods on license theme options page.
 *
 * @since 1.0.0
 */
class License extends Base\Base_Page {

	/**
	 * Page constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_cmsmasters_activate_license', array( $this, 'ajax_activate_license' ) );

		add_action( 'wp_ajax_cmsmasters_deactivate_license', array( $this, 'ajax_deactivate_license' ) );
	}

	/**
	 * Get page title.
	 *
	 * @since 1.0.0
	 */
	public static function get_page_title() {
		return esc_attr__( 'License', 'cmsmasters-framework' );
	}

	/**
	 * Get menu title.
	 *
	 * @since 1.0.0
	 */
	public static function get_menu_title() {
		return esc_attr__( 'License', 'cmsmasters-framework' );
	}

	/**
	 * Render page content.
	 *
	 * @since 1.0.0
	 */
	public function render_content() {
		$token_data = API_Requests::get_token_data( false );
		$license_code = '';

		if (
			is_array( $token_data ) &&
			( ! empty( $token_data['purchase_code'] ) || ! empty( $token_data['envato_elements_token'] ) )
		) {
			if ( 'purchase-code' === $token_data['source_code'] ) {
				$license_code = $token_data['purchase_code'];
			} elseif ( 'envato-elements-token' === $token_data['source_code'] ) {
				$license_code = $token_data['envato_elements_token'];
			}

			$replacement = '';
			$visible_count = 5;
			$length = strlen( $license_code ) - $visible_count * 2;

			for ( $i = 0; $i < $length; $i++ ) {
				$replacement .= '*';
			}

			$license_code = substr_replace( $license_code, $replacement, $visible_count, -$visible_count );
		}

		echo '<div class="cmsmasters-options-message' . ( '' === $license_code ? ' cmsmasters-error' : ' cmsmasters-success' ) . '">';

			if ( '' === $license_code ) {
				echo '<p><strong>' . esc_html__( 'Your license is not activated.', 'cmsmasters-framework' ) . '</strong></p>' .
				'<p><strong>' . esc_html__( 'Enter your license code to activate the license.', 'cmsmasters-framework' ) . '</strong></p>';
			} else {
				echo '<p><strong>' . esc_html__( 'Your license is activated! Remote updates and theme support are enabled.', 'cmsmasters-framework' ) . '</strong></p>';
			}

		echo '</div>';

		echo '<div class="cmsmasters-options-license">';

			if ( '' === $license_code ) {
				echo '<h3 class="cmsmasters-options-license__title">' . esc_html__( 'Activate License', 'cmsmasters-framework' ) . '</h3>';

				echo '<div class="cmsmasters-options-license__source-code">
					<div>
						<label>
							<input type="radio" name="cmsmasters_options_license__source_code" value="purchase-code" checked="checked" />
							<span>' . esc_html__( 'I bought the theme on Themeforest', 'cmsmasters-framework' ) . '</span>
						</label>
					</div>
					<div>
						<label>
							<input type="radio" name="cmsmasters_options_license__source_code" value="envato-elements-token" />
							<span>' . esc_html__( 'I downloaded the theme from Envato Elements', 'cmsmasters-framework' ) . '</span>
						</label>
					</div>
				</div>';

				echo '<div class="cmsmasters-options-license__code cmsmasters-options-license--purchase-code">
					<input type="text" name="cmsmasters_options_license__purchase_code" placeholder="' . esc_attr__( 'Enter Your Purchase code', 'cmsmasters-framework' ) . '" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
					<span class="cmsmasters-options-license__code-description">' .
						sprintf(
							esc_html__( 'Where can I find my %1$s?', 'cmsmasters-framework' ),
							'<a href="' . esc_url( 'https://docs.cmsmasters.net/blog/how-to-find-your-envato-purchase-code/' ) . '" target="_blank">' .
								esc_html__( 'purchase code', 'cmsmasters-framework' ) .
							'</a>'
						) .
					'</span>
				</div>';

				echo '<div class="cmsmasters-options-license__code cmsmasters-options-license--envato-elements-token">
					<input type="text" name="cmsmasters_options_license__envato_elements_token" placeholder="' . esc_attr__( 'Envato Elements Token', 'cmsmasters-framework' ) . '" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />
					<span class="cmsmasters-options-license__code-description">' .
						sprintf(
							esc_html__( 'In order to activate the theme you need to %1$s', 'cmsmasters-framework' ),
							'<a href="' . esc_url( 'https://api.extensions.envato.com/extensions/begin_activation?extension_id=cmsmasters-envato-elements&extension_type=envato-wordpress&extension_description=' . wp_get_theme()->get( 'Name' ) . ' (' . get_home_url() . ')&utm_content=settings' ) . '" target="_blank">' .
								esc_html__( 'generate Envato Elements token', 'cmsmasters-framework' ) .
							'</a>'
						) .
					'</span>
				</div>';

				echo '<div class="cmsmasters-options-license__user-info">
					<h3 class="cmsmasters-options-license__user-info--title">' . esc_html__( 'Register your copy', 'cmsmasters-framework' ) . '</h3>
					<div class="cmsmasters-options-license-data__user-info--text">
						<p>' . esc_html__( 'Get information about promotions, new themes and theme updates directly to your Inbox.', 'cmsmasters-framework' ) . '</p>
						<p>' . esc_html__( 'You can change your name and email anytime.', 'cmsmasters-framework' ) . '</p>
					</div>
					<div class="cmsmasters-options-license__user-info--item">
						<input type="text" name="cmsmasters_options_license__user_name" placeholder="' . esc_attr__( 'Your Name', 'cmsmasters-framework' ) . '" />
					</div>
					<div class="cmsmasters-options-license__user-info--item">
						<input type="text" name="cmsmasters_options_license__user_email" placeholder="' . esc_attr__( 'Your Email', 'cmsmasters-framework' ) . '" />
					</div>
					<p class="cmsmasters-options-license__user-info--privacy">' .
						sprintf(
							esc_html__( 'Your data is stored and processed in accordance with our %1$s', 'cmsmasters-framework' ),
							'<a href="' . esc_url( 'https://cmsmasters.studio/privacy-policy/' ) . '" target="_blank">' .
								esc_html__( 'Privacy Policy', 'cmsmasters-framework' ) .
							'</a>'
						) .
					'</p>
				</div>';

				echo '<div class="cmsmasters-options-license__button-wrap">
					<button type="button" class="button cmsmasters-button-spinner" data-license="activate">' . esc_html__( 'Activate', 'cmsmasters-framework' ) . '</button>
					<span class="cmsmasters-notice"></span>
				</div>';
			} else {
				echo '<h3 class="cmsmasters-options-license__title">' . esc_html__( 'Deactivate License', 'cmsmasters-framework' ) . '</h3>';

				echo '<div class="cmsmasters-options-license__code cmsmasters-options-license--purchase-code">
					<input type="text" class="regular-text" value="' . esc_attr( $license_code ) . '" disabled />
				</div>';

				echo '<div class="cmsmasters-options-license__button-wrap">
					<button type="button" class="button cmsmasters-button-spinner" data-license="deactivate">' . esc_html__( 'Deactivate', 'cmsmasters-framework' ) . '</button>
					<span class="cmsmasters-notice"></span>
				</div>';
			}

		echo '</div>';
	}

	/**
	 * Activate theme license.
	 *
	 * @since 1.0.0
	 */
	public function ajax_activate_license() {
		if ( ! check_ajax_referer( 'cmsmasters_options_nonce', 'nonce' ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_nonce',
				'message' => esc_html__( 'Yikes! The theme activation failed. Please try again.', 'cmsmasters-framework' ),
			) );
		}

		$error_code = '';
		$source_code = empty( $_POST['source_code'] ) ? 'purchase-code' : $_POST['source_code'];

		if ( 'purchase-code' === $source_code && empty( $_POST['purchase_code'] ) ) {
			$error_code = 'empty_purchase_code';
		} elseif ( 'envato-elements-token' === $source_code && empty( $_POST['envato_elements_token'] ) ) {
			$error_code = 'empty_envato_elements_token';
		}

		if ( ! empty( $error_code ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => $error_code,
				'error_field' => 'license_key',
				'message' => esc_html__( 'License key field is empty', 'cmsmasters-framework' ),
			) );
		}

		API_Requests::generate_token( array(
			'user_name' => empty( $_POST['user_name'] ) ? '' : $_POST['user_name'],
			'user_email' => empty( $_POST['user_email'] ) ? '' : $_POST['user_email'],
			'source_code' => $source_code,
			'purchase_code' => empty( $_POST['purchase_code'] ) ? '' : $_POST['purchase_code'],
			'envato_elements_token' => empty( $_POST['envato_elements_token'] ) ? '' : $_POST['envato_elements_token'],
			'input_data_source' => 'options',
		) );

		wp_send_json( array(
			'success' => true,
			'message' => esc_html__( 'Your license is activated! Remote updates and theme support are enabled.', 'cmsmasters-framework' ),
		) );
	}

	/**
	 * Deactivate theme license.
	 *
	 * @since 1.0.0
	 */
	public function ajax_deactivate_license() {
		if ( ! check_ajax_referer( 'cmsmasters_options_nonce', 'nonce' ) ) {
			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_nonce',
				'message' => esc_html__( 'Yikes! The theme deactivation failed. Please try again.', 'cmsmasters-framework' ),
			) );
		}

		API_Requests::remove_token();

		wp_send_json( array(
			'success' => true,
			'message' => esc_html__( 'Your license is deactivated!', 'cmsmasters-framework' ),
		) );
	}

}
