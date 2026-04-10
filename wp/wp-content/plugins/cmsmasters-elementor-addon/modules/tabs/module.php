<?php
namespace CmsmastersElementor\Modules\Tabs;

use CmsmastersElementor\Base\Base_Module;
use CmsmastersElementor\Modules\Animation\Classes\Animation as AnimationModule;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * CMSMasters Elementor Tabs module.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'tabs';
	}

	/**
	 * Module activation.
	 *
	 * Check if module is active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_active() {
		return class_exists( AnimationModule::class );
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array(
			'Tabs',
		);
	}

}
