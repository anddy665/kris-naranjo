<?php
namespace CmsmastersElementor\Modules\Hotspot;

use CmsmastersElementor\Base\Base_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.8.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-hotspot';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.8.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		$widgets = array();

		$widgets[] = 'Hotspot';
		
		return $widgets;
	}
}
