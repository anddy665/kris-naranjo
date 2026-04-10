<?php
namespace CmsmastersElementor\Modules\Testimonials;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Testimonials module.
 *
 * @since 1.1.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.1.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-testimonials';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.1.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array(
			'Testimonials_Slider',
			'Testimonial',
		);
	}

}
