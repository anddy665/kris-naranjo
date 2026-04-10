<?php
namespace CmsmastersElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor Addon autoloader.
 *
 * CMSMasters Elementor Addon autoloader handler class is responsible for
 * loading the different classes needed to run the plugin.
 *
 * @since 1.0.0
 */
final class Autoloader {

	/**
	 * Classes map.
	 *
	 * Maps CMSMasters Elementor classes to file names.
	 *
	 * @since 1.0.0
	 *
	 * @var array Classes used by Addon.
	 */
	private static $classes_map = array(
		'Controls_Manager' => 'includes/managers/controls.php',
		'Modules_Manager' => 'includes/managers/modules.php',
		'Tags_Manager' => 'includes/managers/tags.php',
		'Modules\TemplateLocations\Locations_Manager' => 'modules/template-locations/managers/locations-manager.php',
		'Modules\TemplateLocations\Rules_Manager' => 'modules/template-locations/managers/rules-manager.php',
		// 'Modules\TemplateLocations\Themes_Manager' => 'modules/template-locations/managers/themes-manager.php',
	);

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
				$filepath = CMSMASTERS_ELEMENTOR_PATH . $classes_map[ $relative_class_name ];
			} else {
				$filename = strtolower(
					preg_replace(
						array( '/([a-z])([A-Z])/', '/_/', '/\\\/' ),
						array( '$1-$2', '-', DIRECTORY_SEPARATOR ),
						$relative_class_name
					)
				);

				$filepath = CMSMASTERS_ELEMENTOR_PATH . $filename . '.php';

				if ( ! file_exists( $filepath ) ) {
					$filepath = CMSMASTERS_ELEMENTOR_INCLUDES_PATH . $filename . '.php';
				}
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
		/**
		 * Addon tags list.
		 *
		 * Filters the Addon dynamic tags list.
		 *
		 * @since 1.0.0
		 *
		 * @param array $classes_map Addon dynamic tags list.
		 */
		return apply_filters( 'cmsmasters_elementor/autoloader/classes_map', self::$classes_map );
	}

}
