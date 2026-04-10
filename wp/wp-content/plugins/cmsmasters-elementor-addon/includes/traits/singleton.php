<?php
namespace CmsmastersElementor\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Singleton trait.
 *
 * Ensures only one instance of the trait base class is loaded
 * or can be loaded.
 *
 * @since 1.0.0
 */
trait Singleton {

	/**
	 * Base class instance.
	 *
	 * Holds the trait base class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var array Array with instance of the base class.
	 */
	protected static $_instances = array();

	/**
	 * Get instance of base class.
	 *
	 * Ensures only one instance of the base class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return object Single instance of the base class.
	 */
	public static function instance() {
		$called_class = get_called_class();

		if ( ! isset( self::$_instances[ $called_class ] ) ) {
			self::$_instances[ $called_class ] = new $called_class();
		}

		return self::$_instances[ $called_class ];
	}

}
