<?php
namespace CmsmastersElementor\Modules\Button;

use CmsmastersElementor\Base\Base_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * CMSMasters Elementor button module.
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
		return 'cmsmasters-button';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.0.0
	 * @since 1.21.0 Added new `Swap Button` widget.
	 * @since 1.22.0 Added new `Split Button` widget.
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array(
			'Button',
			'Swap_Button',
			'Split_Button',
		);
	}
}
