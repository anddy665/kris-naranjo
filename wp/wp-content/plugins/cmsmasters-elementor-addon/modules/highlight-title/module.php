<?php
namespace CmsmastersElementor\Modules\HighlightTitle;

use CmsmastersElementor\Base\Base_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSMasters Elementor HighlightTitle module.
 *
 * @since 1.20.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.20.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-highlight-title';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.20.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array( 'Highlight_Title' );
	}
}
