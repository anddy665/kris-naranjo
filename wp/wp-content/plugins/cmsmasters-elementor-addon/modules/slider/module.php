<?php
namespace CmsmastersElementor\Modules\Slider;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Addon Slider module.
 *
 * The slider class is responsible for slider module controls integration.
 *
 * @since 1.0.0
 */
class Module extends Base_Module {

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'slider';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the modules widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array(
			'Media_Carousel',
			'Slider',
		);
	}

}
