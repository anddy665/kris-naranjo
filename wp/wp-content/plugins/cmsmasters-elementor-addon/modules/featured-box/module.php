<?php
namespace CmsmastersElementor\Modules\FeaturedBox;

use CmsmastersElementor\Base\Base_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Base_Module {
	/**
	 * Get module name.
	 *
	 * Retrieve the CMSMasters Blog module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-featured-box';
	}

	/**
	 * Retrieve widget classes name.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_widgets() {
		return array(
			'Featured_Box',
		);
	}
}
