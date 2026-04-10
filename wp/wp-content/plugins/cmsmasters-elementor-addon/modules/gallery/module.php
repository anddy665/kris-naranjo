<?php

namespace CmsmastersElementor\Modules\Gallery;

use CmsmastersElementor\Base\Base_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Base_Module {
	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-gallery';
	}

	public function get_widgets() {
		return array( 'Gallery' );
	}
}
