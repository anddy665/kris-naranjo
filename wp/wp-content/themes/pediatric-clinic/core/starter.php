<?php
namespace PediatricClinicSpace;

use PediatricClinicSpace\Core\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Starter class for theme modules.
 *
 * Includes autoloader and Core class.
 */
class Starter {

	/**
	 * Starter constructor.
	 *
	 * Includes autoloader and Core class.
	 */
	public function __construct() {
		require_once get_parent_theme_file_path( '/core/autoloader.php' );

		Autoloader::run();

		new Core();
	}

}

new Starter();
