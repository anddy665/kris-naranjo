<?php
namespace PediatricClinicSpace\Modules;

use PediatricClinicSpace\Modules\CSS_Vars;
use PediatricClinicSpace\Modules\Gutenberg;
use PediatricClinicSpace\Modules\Swiper;
use PediatricClinicSpace\Modules\Page_Preloader;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme modules.
 *
 * Main class for theme modules.
 */
class Modules {

	/**
	 * Theme modules constructor.
	 *
	 * Run modules for theme.
	 */
	public function __construct() {
		new CSS_Vars();

		new Swiper();

		new Gutenberg();

		new Page_Preloader();
	}

}
