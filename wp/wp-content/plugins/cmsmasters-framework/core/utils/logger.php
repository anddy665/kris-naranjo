<?php
namespace CmsmastersFramework\Core\Utils;

use CmsmastersFramework\Core\Utils\File_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Logger class for theme.
 *
 * @since 1.0.0
 */
class Logger {

	private static $log_path;

	/**
	 * Logger constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$upload_dir = File_Manager::get_upload_path( 'theme-log' );
		File_Manager::create_folder( $upload_dir );

		self::$log_path = $upload_dir . '/theme-log.log';
	}

	/**
	 * Get theme log file URL.
	 *
	 * @since 1.0.0
	 *
	 * @return string Theme log file URL.
	 */
	public static function get_theme_log_url() {
		return File_Manager::get_upload_path( 'theme-log', 'theme-log.log', true );
	}

	/**
	 * Log message with specific level.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param string $level Log level (e.g., INFO, ERROR, WARNING).
	 * @param array  $context Additional context data (optional).
	 */
	public static function log( $message, $level = 'INFO', $context = array() ) {
		$time = date( 'Y-m-d H:i:s' );

		$context = ! empty( $context ) ? json_encode( $context ) : '';

		$formatted_message = sprintf(
			"[%s] [%s] %s %s\n",
			$time,
			$level,
			$message,
			$context
		);

		error_log( $formatted_message, 3, self::$log_path );
	}

	/**
	 * Log debug message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function debug( $message, $context = array() ) {
		self::log( $message, 'DEBUG', $context );
	}

	/**
	 * Log notice message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function notice( $message, $context = array() ) {
		self::log( $message, 'NOTICE', $context );
	}

	/**
	 * Log info message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function info( $message, $context = array() ) {
		self::log( $message, 'INFO', $context );
	}

	/**
	 * Log warning message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function warning( $message, $context = array() ) {
		self::log( $message, 'WARNING', $context );
	}

	/**
	 * Log error message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function error( $message, $context = array() ) {
		self::log( $message, 'ERROR', $context );
	}

	/**
	 * Log alert message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function alert( $message, $context = array() ) {
		self::log( $message, 'ALERT', $context );
	}

	/**
	 * Log emergency message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function emergency( $message, $context = array() ) {
		self::log( $message, 'EMERGENCY', $context );
	}

	/**
	 * Log critical message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Log message.
	 * @param array $context Additional context data (optional).
	 */
	public static function critical( $message, $context = array() ) {
		self::log( $message, 'CRITICAL', $context );
	}

}
