<?php
namespace CmsmastersElementor\Modules\BeforeAfter;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CMSMasters Elementor BeforeAfter module.
 *
 * @since 1.7.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.7.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-before-after';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.7.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array( 'Before_After' );
	}

}
