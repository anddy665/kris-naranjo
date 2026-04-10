<?php
namespace CmsmastersFramework;

use CmsmastersFramework\Admin\Admin;
use CmsmastersFramework\Core\Utils\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSMasters Framework plugin.
 *
 * The main plugin handler class is responsible for initializing Framework.
 * The class registers all the components required for the plugin.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 * That's why cloning instances of the class is forbidden.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'cmsmasters-framework' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class.
	 *
	 * Unserializing instances of the class is forbidden.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'cmsmasters-framework' ), '1.0.0' );
	}

	/**
	 * Main class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		new Logger();

		new Admin();
	}

}
