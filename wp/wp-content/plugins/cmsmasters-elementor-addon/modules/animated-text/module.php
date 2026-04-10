<?php
namespace CmsmastersElementor\Modules\AnimatedText;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * CMSMasters elementor animated text module.
 *
 * CMSMasters elementor animated text module handler class is responsible for
 * registering and managing group.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters animated text module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-animated-text';
	}

	/**
	 * Get widget names.
	 *
	 * Retrieve the CMSMasters animated text widget names.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget names.
	 */
	public function get_widgets() {
		return array(
			'Animated_Text',
			'Fancy_Text',
		);
	}
}
