<?php
namespace CmsmastersFramework\Core\Utils;

use CmsmastersFramework\Core\Utils\API_Requests;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Utils handler class is responsible for different utility methods.
 *
 * @since 1.0.0
 */
class Utils {

	/**
	 * Demo key.
	 *
	 * @since 1.0.0
	 */
	private static $demo_key;

	/**
	 * Demo Kit key.
	 *
	 * @since 1.0.0
	 */
	private static $demo_kit_key;

	/**
	 * Kit options.
	 *
	 * @since 1.0.0
	 */
	private static $kit_options;

	/**
	 * Default kits.
	 *
	 * @since 1.0.0
	 */
	private static $default_kits;

	/**
	 * Get demo.
	 *
	 * @since 1.0.0
	 *
	 * @return string Demo key.
	 */
	public static function get_demo() {
		if ( ! self::$demo_key ) {
			self::$demo_key = get_option( CMSMASTERS_OPTIONS_PREFIX . 'demo', 'main' );
		}

		return self::$demo_key;
	}

	/**
	 * Set demo.
	 *
	 * @since 1.0.0
	 */
	public static function set_demo( $demo_key ) {
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'demo', $demo_key, false );

		self::$demo_key = $demo_key;
	}

	/**
	 * Get demo kit.
	 *
	 * @since 1.0.0
	 *
	 * @return string Demo kit key.
	 */
	public static function get_demo_kit() {
		if ( ! self::$demo_kit_key ) {
			self::$demo_kit_key = get_option( CMSMASTERS_OPTIONS_PREFIX . 'demo_kit', self::get_demo() );
		}

		return self::$demo_kit_key;
	}

	/**
	 * Set demo kit.
	 *
	 * @since 1.0.0
	 */
	public static function set_demo_kit( $demo_kit_key ) {
		update_option( CMSMASTERS_OPTIONS_PREFIX . 'demo_kit', $demo_kit_key );

		self::$demo_kit_key = $demo_kit_key;
	}

	/**
	 * Get theme options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Theme options.
	 */
	public static function get_theme_options() {
		return get_option( CMSMASTERS_OPTIONS_NAME, array() );
	}

	/**
	 * Get theme option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @param mixed $def Default value for option.
	 *
	 * @return mixed Theme option.
	 */
	public static function get_theme_option( $key, $def = false ) {
		$options = self::get_theme_options();

		if ( isset( $options[ $key ] ) ) {
			return $options[ $key ];
		}

		return $def;
	}

	/**
	 * Set theme option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @param mixed $value Option value.
	 */
	public static function set_theme_option( $key, $value ) {
		$options = self::get_theme_options();

		$options[ $key ] = $value;

		update_option( CMSMASTERS_OPTIONS_NAME, $options );
	}

	/**
	 * Get Elementor active kit ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string Elementor active kit ID.
	 */
	public static function get_active_kit() {
		$active_kit = get_option( 'elementor_active_kit', '' );

		if ( ! empty( $active_kit ) && did_action( 'wpml_loaded' ) ) {
			$post_type = get_post_type( $active_kit );

			$active_kit = apply_filters( 'wpml_object_id', $active_kit, $post_type, true );
		}

		return $active_kit;
	}

	/**
	 * Get kit options.
	 *
	 * @since 1.0.0
	 *
	 * @return array Kit options.
	 */
	public static function get_kit_options() {
		if ( ! self::$kit_options ) {
			$active_kit = self::get_active_kit();

			self::$kit_options = get_post_meta( $active_kit, '_elementor_page_settings', true );
		}

		return self::$kit_options;
	}

	/**
	 * Set kit options.
	 *
	 * @since 1.0.0
	 *
	 * @param array $options Kit options.
	 */
	public static function set_kit_options( $options ) {
		if ( empty( $options ) ) {
			return;
		}

		$active_kit = self::get_active_kit();

		update_post_meta( $active_kit, '_elementor_page_settings', $options );

		self::$kit_options = $options;
	}

	/**
	 * Get kit option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @param mixed $def Default value for option.
	 *
	 * @return mixed Kit option.
	 */
	public static function get_kit_option( $key, $def = false ) {
		$options = self::get_kit_options();

		if ( isset( $options[ $key ] ) ) {
			return $options[ $key ];
		}

		return self::get_default_kit( $key, $def );
	}

	/**
	 * Gets default kits.
	 *
	 * @since 1.0.0
	 *
	 * @return array default kits.
	 */
	public static function get_default_kits() {
		if ( ! self::$default_kits ) {
			self::$default_kits = get_option( CMSMASTERS_OPTIONS_PREFIX . 'default_kits', array() );
		}

		return self::$default_kits;
	}

	/**
	 * Get default kit.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key kit key.
	 * @param string $def kit default.
	 *
	 * @return string default kit.
	 */
	public static function get_default_kit( $key, $def = '' ) {
		$kits = self::get_default_kits();

		if ( isset( $kits[ $key ] ) ) {
			return $kits[ $key ];
		}

		return $def;
	}

	/**
	 * Update temporary option.
	 *
	 * @since 1.0.1
	 *
	 * @param string $name Option name.
	 * @param string $value Option value.
	 * @param string $expiration_in_seconds Expiration in seconds.
	 */
	public static function update_temp_option( $name, $value, $expiration_in_seconds ) {
		$data = array(
			'value' => $value,
			'expires' => time() + $expiration_in_seconds,
		);

		update_option( $name, $data, false );
	}

	/**
	 * Get temporary option.
	 *
	 * @since 1.0.1
	 *
	 * @param string $name Option name.
	 */
	public static function get_temp_option( $name ) {
		$data = get_option( $name );

		if ( ! is_array( $data ) || ! isset( $data['value'], $data['expires'] ) ) {
			return false;
		}

		if ( time() >= $data['expires'] ) {
			delete_option( $name );

			return false;
		}

		return $data['value'];
	}

	/**
	 * Get import demo data.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Optimization.
	 *
	 * @return mixed Import demo data.
	 */
	public static function get_import_demo_data( $key = '' ) {
		$demo = self::get_demo();

		$data = self::get_temp_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_demo_data' );

		if ( 'not_first_request_error' === $data ) {
			return false;
		}

		if ( empty( $data ) ) {
			$data = self::set_import_demo_data();

			if ( is_wp_error( $data ) ) {
				return false;
			}

			return self::get_import_demo_data( $key );
		}

		if ( empty( $data ) ) {
			return false;
		}

		if ( '' !== $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return false;
			}

			return $data[ $key ];
		}

		return $data;
	}

	/**
	 * Set import demo data.
	 *
	 * Set import demo data in transient.
	 *
	 * @since 1.0.1
	 */
	public static function set_import_demo_data() {
		if ( ! API_Requests::check_token_status() ) {
			return new \WP_Error( 'set_import_demo_data__invalid-token-status', 'Invalid token status' );
		}

		$demo = self::get_demo();

		$data = API_Requests::post_request( 'get-demo-data', array(
			'demo' => $demo,
			'demo_kit' => self::get_demo_kit(),
		) );

		if ( is_wp_error( $data ) ) {
			Logger::error( $data->get_error_message() );
			
			self::update_temp_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_demo_data', 'not_first_request_error', MINUTE_IN_SECONDS * 5 );

			return new \WP_Error( 'set_import_demo_data__request-error', 'Request error (get-demo-data): ' . $data->get_error_message() );
		}

		if ( empty( $data ) ) {
			return new \WP_Error( 'set_import_demo_data__empty-demo-data', 'Empty demo data' );
		}

		self::update_temp_option( CMSMASTERS_OPTIONS_PREFIX . $demo . '_import_demo_data', $data, HOUR_IN_SECONDS );
	}

	/**
	 * Check if developer mode is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_dev_mode() {
		return (
			defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ||
			defined( 'CMSMASTERS_DEVELOPER_MODE' ) && CMSMASTERS_DEVELOPER_MODE ||
			defined( 'ELEMENTOR_TESTS' ) && ELEMENTOR_TESTS
		);
	}

	/**
	 * Is required PHP modules enabled.
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public static function is_required_php_modules_enabled() {
		return extension_loaded('gd') && extension_loaded('zip');
	}

	/**
	 * Check if theme have needed constants.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function has_theme_constants() {
		if (
			defined( 'CMSMASTERS_FRAMEWORK_COMPATIBILITY' ) &&
			CMSMASTERS_FRAMEWORK_COMPATIBILITY &&
			defined( 'CMSMASTERS_THEME_NAME' ) &&
			defined( 'CMSMASTERS_THEME_PRODUCT_KEY' ) &&
			defined( 'CMSMASTERS_OPTIONS_PREFIX' ) &&
			defined( 'CMSMASTERS_OPTIONS_NAME' ) &&
			defined( 'CMSMASTERS_THEME_IMPORT_TYPE' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Get elementor post ids.
	 *
	 * @since 1.0.0
	 *
	 * @return array Posts ids.
	 */
	public static function get_elementor_post_ids() {
		global $wpdb;

		$meta_value = '_elementor_data';

		$post_ids = $wpdb->get_col( $wpdb->prepare(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '` 
				WHERE `meta_key` = \'%s\';',
			$meta_value
		) );

		return $post_ids;
	}

	/**
	 * Check if request is ajax.
	 *
	 * Whether the current request is a WordPress ajax request.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if it's a WordPress ajax request, false otherwise.
	 */
	public static function is_ajax() {
		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Get upload dir parameter.
	 *
	 * Retrieve the upload URL/path in right way (works with SSL).
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Upload dir parameter name (basedir or baseurl).
	 * @param string $subfolder Upload dir parameter address subfolder.
	 *
	 * @return string Upload dir parameter address.
	 */
	public static function get_upload_dir_parameter( $name, $subfolder = '' ) {
		$upload_dir = wp_upload_dir();
		$address = $upload_dir[ $name ];
		$urls = array( 'url', 'baseurl' );

		if ( in_array( $name, $urls, true ) && is_ssl() ) {
			$address = str_replace( 'http://', 'https://', $address );
		}

		return $address . $subfolder;
	}

}
