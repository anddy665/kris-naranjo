<?php
namespace PediatricClinicSpace\ThemeConfig;

use PediatricClinicSpace\Woocommerce\CmsmastersFramework\Plugin as Woocommerce_Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Theme Plugins.
 *
 * Main class for theme plugins.
 */
class Theme_Plugins {

	/**
	 * Theme_Plugins constructor.
	 */
	public function __construct() {
		new Woocommerce_Plugin();
	}
}
