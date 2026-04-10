<?php
namespace CmsmastersElementor\Modules\Timetable;

use CmsmastersElementor\Base\Base_Module;
use Elementor\Plugin;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters Elementor Timetable module.
 *
 * @since 1.6.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.6.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-timetable';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( 'Mp_Time_Table' );
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.6.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		$widgets = array();

		if ( class_exists( 'Mp_Time_Table' ) ) {
			$widgets[] = 'Timetable';
		}

		return $widgets;
	}
}
