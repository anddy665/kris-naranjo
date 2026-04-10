<?php
namespace CmsmastersElementor\Modules\ImageAccordion;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Image Accordion module.
 *
 * @since 1.21.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.21.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-image-accordion';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.21.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array(
			'Image_Accordion',
		);
	}

}



