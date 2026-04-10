<?php
namespace CmsmastersFramework\Core\Utils;

use CmsmastersFramework\Core\Utils\File_Manager;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * API Requests handler class is responsible for different utility methods.
 *
 * @since 1.0.0
 */
class API_Requests {

	/**
	 * Check token status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool true if token is valid and false if token is invalid.
	 */
	public static function check_token_status() {
		return get_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status', 'invalid' ) === 'valid';
	}

	/**
	 * Check if empty token status.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_empty_token_status() {
		return empty( get_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status' ) );
	}

	/**
	 * Get theme config from API.
	 *
	 * Fetches theme configuration (marketplaces, notices, links) from the
	 * get-theme-config endpoint. Caches for 6 hours; negative cache for 5 min.
	 *
	 * @since 1.0.14
	 *
	 * @return array Theme config or empty array on error.
	 */
	public static function get_theme_config() {
		$result = self::get_request( 'get-theme-config', array(), 6 * HOUR_IN_SECONDS );

		if ( is_wp_error( $result ) ) {
			return array();
		}

		return is_array( $result ) ? $result : array();
	}

	/**
	 * Clear theme config cache.
	 *
	 * Should be called on license activation/deactivation and token regeneration.
	 *
	 * @since 1.0.14
	 */
	public static function clear_theme_config_cache() {
		$cache_key = CMSMASTERS_OPTIONS_PREFIX . 'cached_request_get-theme-config_' . md5( serialize( array() ) );

		delete_transient( $cache_key );
	}

	/**
	 * Clear all API notice dismissals.
	 *
	 * Called on license re-activation and token data deletion.
	 *
	 * @since 1.0.14
	 */
	public static function clear_all_notice_dismissals() {
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices' );
		delete_transient( CMSMASTERS_OPTIONS_PREFIX . 'dismissed_notices_session' );
	}

	/**
	 * CMSMasters API GET request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $route API route.
	 * @param array $args request args.
	 * @param int $cache_ttl Cache TTL in seconds. Default 300 (5 min).
	 *
	 * @return object API response.
	 */
	public static function get_request( $route, $args = array(), $cache_ttl = 300 ) {
		$cache_key = CMSMASTERS_OPTIONS_PREFIX . 'cached_request_' . $route . '_' . md5( serialize( $args ) );
		$cached_response = get_transient( $cache_key );

		if ( false !== $cached_response ) {
			if ( 'error' === $cached_response ) {
				return new \WP_Error( $route, $route . ': cached error' );
			}

			return $cached_response;
		}

		$args = wp_parse_args( $args, array(
			'product_key' => CMSMASTERS_THEME_PRODUCT_KEY,
		) );

		$url = add_query_arg( $args, CMSMASTERS_FRAMEWORK_API_ROUTES_URL . $route );

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 60,
			)
		);

		// Check for connection errors (cURL 28, etc.)
		if ( is_wp_error( $response ) ) {
			Logger::error(
				'API GET request failed: connection error',
				array(
					'route' => $route,
					'message' => $response->get_error_message(),
				)
			);

			// Negative cache to avoid hammering API on repeated errors.
			set_transient( $cache_key, 'error', 300 );

			return new \WP_Error( $route, $route . ': ' . $response->get_error_message() );
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$response_code = wp_remote_retrieve_response_code( $response );

			// 404 = endpoint doesn't exist (old API). Cache longer to avoid request storms.
			$negative_ttl = ( 404 === $response_code ) ? DAY_IN_SECONDS : 300;

			set_transient( $cache_key, 'error', $negative_ttl );

			return new \WP_Error( $route, $route . ': ' . wp_remote_retrieve_response_message( $response ) );
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		$data = $response_body['data'];

		set_transient( $cache_key, $data, $cache_ttl );

		return $data;
	}

	/**
	 * CMSMasters API POST request.
	 *
	 * @since 1.0.0
	 *
	 * @param string $route API route.
	 * @param array $args request args.
	 *
	 * @return mixed Json decode response body data.
	 */
	public static function post_request( $route, $args = array() ) {
		if ( self::is_empty_token_status() ) {
			return false;
		}

		$cache_key = CMSMASTERS_OPTIONS_PREFIX . 'cached_request_' . $route . '_' . md5( serialize( $args ) );
		$cached_response = get_transient( $cache_key );

		if ( $cached_response ) {
			return $cached_response;
		}

		if ( ! self::check_token_status() ) {
			if ( ! self::regenerate_token() ) {
				return false;
			}
		}

		$args = wp_parse_args( $args, array(
			'product_key' => CMSMASTERS_THEME_PRODUCT_KEY,
		) );

		$response = wp_remote_post(
			CMSMASTERS_FRAMEWORK_API_ROUTES_URL . $route,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . get_option( CMSMASTERS_OPTIONS_PREFIX . 'token', 'invalid' ),
				),
				'body' => $args,
				'timeout' => 120,
			)
		);

		// Check for connection errors (cURL 28, etc.)
		if ( is_wp_error( $response ) ) {
			Logger::error(
				'API POST request failed: connection error',
				array(
					'route' => $route,
					'message' => $response->get_error_message(),
				)
			);

			return new \WP_Error( $route, $route . ': ' . $response->get_error_message() );
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( 'regenerated' !== get_transient( CMSMASTERS_OPTIONS_PREFIX . 'token_regeneration_status' ) ) {
				self::regenerate_token();

				return self::post_request( $route, $args );
			} else {
				return new \WP_Error( $route, $route . ': ' . wp_remote_retrieve_response_message( $response ) );
			}
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		$data = $response_body['data'];

		set_transient( $cache_key, $data, 300 );

		return $data;
	}

	/**
	 * Regenerate token.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $die Run wp_send_json or return false if invalid data.
	 */
	public static function regenerate_token( $die = false ) {
		if ( 'regenerated' === get_transient( CMSMASTERS_OPTIONS_PREFIX . 'token_regeneration_status' ) ) {
			if ( ! $die ) {
				return false;
			}

			wp_send_json_error( esc_html__( 'Token not regenerated. The token was regenerated earlier.', 'cmsmasters-framework' ), 403 );
		}

		$token_data = self::get_token_data( $die );
		$token_data['product_key'] = CMSMASTERS_THEME_PRODUCT_KEY;

		$response = wp_remote_post(
			CMSMASTERS_FRAMEWORK_API_ROUTES_URL . 'regenerate-token',
			array(
				'body' => $token_data,
				'timeout' => 60,
			)
		);

		// Check for connection errors (cURL 28, etc.)
		if ( is_wp_error( $response ) ) {
			Logger::error(
				'Token regeneration failed: connection error',
				array(
					'message' => $response->get_error_message(),
				)
			);

			set_transient( CMSMASTERS_OPTIONS_PREFIX . 'token_regeneration_status', 'regenerated', 1200 );

			if ( ! self::is_empty_token_status() ) {
				update_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status', 'invalid', false );
			}

			if ( ! $die ) {
				return false;
			}

			wp_send_json_error(
				array(
					'code' => 'connection_error',
					'message' => $response->get_error_message(),
				),
				503
			);
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code ) {
			if ( isset( $response_body['data']['code'] ) && 'regenerate_token__marketplace_unavailable' === $response_body['data']['code'] ) {
				// Marketplace temporarily unavailable — keep all data, shorter retry lock.
				Logger::error( 'Token regeneration deferred: marketplace API temporarily unavailable' );

				set_transient( CMSMASTERS_OPTIONS_PREFIX . 'token_regeneration_status', 'regenerated', 300 );

				if ( ! $die ) {
					return false;
				}

				wp_send_json_error( $response_body, $response_code );
			}

			set_transient( CMSMASTERS_OPTIONS_PREFIX . 'token_regeneration_status', 'regenerated', 1200 );

			if ( isset( $response_body['data']['code'] ) && 'ee_theme_unavailable' === $response_body['data']['code'] ) {
				// Theme removed from Envato Elements — invalidate but keep source_code for notice.
				Logger::error( 'Theme removed from Envato Elements. License deactivated.' );

				update_option( CMSMASTERS_OPTIONS_PREFIX . 'ee_theme_unavailable', true, false );

				if ( ! self::is_empty_token_status() ) {
					update_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status', 'invalid', false );
				}

				delete_option( CMSMASTERS_OPTIONS_PREFIX . 'token' );
				do_action( 'cmsmasters_remove_temp_data' );

				if ( ! $die ) {
					return false;
				}

				wp_send_json_error( $response_body, $response_code );
			} elseif ( isset( $response_body['data']['code'] ) && 'regenerate_token__invalid_license_code' === $response_body['data']['code'] ) {
				Logger::error( 'Invalid license code. Please re-register your license.' );

				self::delete_token_data();
			} else {
				Logger::error(
					'Token regeneration failed: server error',
					array(
						'response_code' => $response_code,
						'response_body' => $response_body,
					)
				);
			}

			if ( ! self::is_empty_token_status() ) {
				update_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status', 'invalid', false );
			}

			if ( ! $die ) {
				return false;
			}

			wp_send_json_error( $response_body, $response_code );
		}

		set_transient( CMSMASTERS_OPTIONS_PREFIX . 'token_regeneration_status', 'regenerated', 1200 );

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'token', $response_body['data']['token'], false );
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status', 'valid', false );

		self::clear_theme_config_cache();

		do_action( 'cmsmasters_remove_temp_data' );

		return true;
	}

	/**
	 * Generate token.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments.
	 */
	public static function generate_token( $args = array() ) {
		$current_user = wp_get_current_user();

		$args['admin_email'] = $current_user->user_email;

		if ( ! empty( $args['user_email'] ) && false === is_email( $args['user_email'] ) ) {
			wp_send_json(
				array(
					'success' => false,
					'code' => 'invalid_email',
					'error_field' => 'email',
					'message' => esc_html__( 'Oops, looks like you made a mistake with the email address', 'cmsmasters-framework' ),
				)
			);
		}

		$args['domain'] = home_url();
		$args['product_key'] = CMSMASTERS_THEME_PRODUCT_KEY;

		$response = wp_remote_post(
			CMSMASTERS_FRAMEWORK_API_ROUTES_URL . 'generate-token',
			array(
				'body' => $args,
				'timeout' => 60,
			)
		);

		// Check for connection errors (cURL 28, etc.)
		if ( is_wp_error( $response ) ) {
			Logger::error(
				'License activation failed: connection error',
				array(
					'message' => $response->get_error_message(),
					'domain' => $args['domain'],
				)
			);

			wp_send_json(
				array(
					'success' => false,
					'code' => 'connection_error',
					'error_field' => 'connection',
					'message' => $response->get_error_message(),
				)
			);
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$error_message = isset( $response_body['data']['message'] )
				? $response_body['data']['message']
				: wp_remote_retrieve_response_message( $response );

			// Theme removed from Envato Elements — set flag for notices.
			if ( isset( $response_body['data']['code'] ) && 'ee_theme_unavailable' === $response_body['data']['code'] ) {
				Logger::error( 'EE activation blocked: theme unavailable on Envato Elements' );

				update_option( CMSMASTERS_OPTIONS_PREFIX . 'ee_theme_unavailable', true, false );
			}

			Logger::error(
				'License activation failed: server error',
				array(
					'response_code' => wp_remote_retrieve_response_code( $response ),
					'message' => $error_message,
					'domain' => $args['domain'],
				)
			);

			wp_send_json(
				array(
					'success' => false,
					'error_field' => 'license_key',
					'message' => $error_message,
				)
			);
		}

		$token_data = array(
			'user' => $response_body['data']['user'],
			'user_name' => $args['user_name'],
			'user_email' => $args['user_email'],
			'source_code' => $args['source_code'],
			'purchase_code' => $args['purchase_code'],
			'envato_elements_token' => $args['envato_elements_token'],
		);

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'token_data', $token_data, false );
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'token', $response_body['data']['token'], false );

		File_Manager::write_file( wp_json_encode( $token_data ), 'token-data', 'token-data', 'json' );

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status', 'valid', false );

		// Clear EE-related flags on successful re-activation.
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'ee_theme_unavailable' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'ee_upgrade_suggestion_notice_visibility' );

		self::clear_theme_config_cache();
		self::clear_all_notice_dismissals();

		do_action( 'cmsmasters_remove_temp_data' );
	}

	/**
	 * Remove token.
	 *
	 * @since 1.0.0
	 */
	public static function remove_token() {
		do_action( 'cmsmasters_remove_temp_data' );

		$data = self::post_request( 'remove-token' );

		self::delete_token_data();

		if ( is_wp_error( $data ) ) {
			wp_send_json(
				array(
					'success' => false,
					'message' => $data->get_error_message(),
				)
			);
		}
	}

	/**
	 * Get token data.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $die Send json or return empty array if invalid data.
	 *
	 * @return array Token data.
	 */
	public static function get_token_data( $die = true ) {
		$data = get_option( CMSMASTERS_OPTIONS_PREFIX . 'token_data', array() );

		if (
			is_array( $data ) &&
			isset( $data['user'] ) &&
			( ! empty( $data['purchase_code'] ) || ! empty( $data['envato_elements_token'] ) )
		) {
			$data['source_code'] = ( empty( $data['source_code'] ) ? 'purchase-code' : $data['source_code'] );

			return $data;
		}

		$file = File_Manager::get_upload_path( 'token-data', 'token-data.json' );
		$data = File_Manager::get_file_contents( $file );
		$data = json_decode( $data, true );

		$data['source_code'] = ( empty( $data['source_code'] ) ? 'purchase-code' : $data['source_code'] );

		if (
			! is_array( $data ) ||
			! isset( $data['user'] ) ||
			( empty( $data['purchase_code'] ) && empty( $data['envato_elements_token'] ) )
		) {
			if ( ! $die ) {
				return array();
			}

			wp_send_json( array(
				'success' => false,
				'code' => 'invalid_token_data',
				'message' => esc_html__( 'Your token data is invalid.', 'cmsmasters-framework' ),
			) );
		}

		update_option( CMSMASTERS_OPTIONS_PREFIX . 'token_data', $data, false );

		return $data;
	}

	/**
	 * Delete token data.
	 *
	 * @since 1.0.0
	 */
	protected static function delete_token_data() {
		File_Manager::delete_uploaded_dir( 'token-data' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'token_data' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'token' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'token_status' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'ee_theme_unavailable' );
		delete_option( CMSMASTERS_OPTIONS_PREFIX . 'ee_upgrade_suggestion_notice_visibility' );
		self::clear_theme_config_cache();
		self::clear_all_notice_dismissals();
		delete_transient( 'cmsmasters_plugins_list' );
		delete_transient( CMSMASTERS_OPTIONS_PREFIX . 'token_regeneration_status' );
		do_action( 'cmsmasters_remove_temp_data' );
	}

}
