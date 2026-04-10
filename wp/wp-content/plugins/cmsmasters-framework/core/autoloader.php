<?php
namespace CmsmastersFramework;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Autoloader.
 *
 * Autoloader handler class is responsible for
 * loading the different classes needed to run the plugin.
 *
 * @since 1.0.0
 */
final class Autoloader {

	/**
	 * Classes map.
	 *
	 * Maps classes to file names.
	 *
	 * @since 1.0.0
	 */
	private static $classes_map = array();

	/**
	 * Run autoloader.
	 *
	 * Register a function as `__autoload()` implementation.
	 *
	 * @since 1.0.0
	 */
	public static function run() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoloader method.
	 *
	 * For a given class, check if it exist and load it.
	 * Fired by `spl_autoload_register` function.
	 *
	 * @since 1.0.0
	 */
	private static function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		if ( ! class_exists( $class ) ) {
			$relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class );
			$classes_map = self::get_classes_map();

			if ( isset( $classes_map[ $relative_class_name ] ) ) {
				$filepath = CMSMASTERS_FRAMEWORK_PATH . $classes_map[ $relative_class_name ];
			} else {
				$filename = strtolower(
					preg_replace(
						array( '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
						array( '$1-$2', '-', DIRECTORY_SEPARATOR ),
						$relative_class_name
					)
				);

				$filepath = CMSMASTERS_FRAMEWORK_PATH . $filename . '.php';
			}

			if ( ! is_readable( $filepath ) ) {
				return;
			}

			require $filepath;
		}
	}

	/**
	 * Get classes map.
	 *
	 * Retrieve the classes file names.
	 *
	 * @since 1.0.0
	 *
	 * @return array Classes map.
	 */
	private static function get_classes_map() {
		return self::$classes_map;
	}
}
