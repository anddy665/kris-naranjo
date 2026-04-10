<?php
namespace CmsmastersElementor\Modules\CircleProgressBar;

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
		return 'cmsmasters-circle-progress-bar';
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

		$widgets[] = 'Circle_Progress_Bar';
		
		return $widgets;
	}
}
