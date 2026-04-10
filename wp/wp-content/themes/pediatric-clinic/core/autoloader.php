<?php
namespace PediatricClinicSpace;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme Autoloader.
 *
 * Theme autoloader handler class is responsible for
 * loading the different classes needed to run theme.
 */
final class Autoloader {

	/**
	 * Classes map.
	 *
	 * Maps classes to file names.
	 */
	private static $classes_map = array();

	/**
	 * Run autoloader.
	 *
	 * Register a function as `__autoload()` implementation.
	 */
	public static function run() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoloader method.
	 *
	 * For a given class, check if it exist and load it.
	 * Fired by `spl_autoload_register` function.
	 */
	private static function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		if ( ! class_exists( $class ) ) {
			$relative_class_name = preg_replace( '/^' . __NAMESPACE__ . '\\\/', '', $class );

			if ( isset( self::$classes_map[ $relative_class_name ] ) ) {
				$filepath = get_template_directory() . '/' . self::$classes_map[ $relative_class_name ];
			} else {
				$filename = strtolower(
					preg_replace(
						array( '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
						array( '$1-$2', '-', DIRECTORY_SEPARATOR ),
						$relative_class_name
					)
				);

				$filepath = get_template_directory() . '/' . $filename . '.php';
			}

			if ( is_readable( $filepath ) ) {
				require $filepath;
			}
		}
	}
}
