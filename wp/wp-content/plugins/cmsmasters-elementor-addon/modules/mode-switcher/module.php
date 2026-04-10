<?php
namespace CmsmastersElementor\Modules\ModeSwitcher;

use CmsmastersElementor\Base\Base_Module;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


/**
 * Mode switcher module.
 *
 * @since 1.10.0
 */
class Module extends Base_Module {

	/**
	 * Get name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 1.10.0
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'cmsmasters-mode-switcher';
	}

	/**
	 * Get widgets.
	 *
	 * Retrieve the module widgets.
	 *
	 * @since 1.10.0
	 *
	 * @return array Module widgets.
	 */
	public function get_widgets() {
		return array( 'Mode_Switcher' );
	}

	/**
	 * Init filters.
	 *
	 * @since 1.10.0
	 *
	 * Initialize module filters.
	 */
	protected function init_filters() {
		add_filter( 'cmsmasters_html_class', array( $this, 'html_class_filter' ) );
	}

	/**
	 * HTML css classes filter.
	 *
	 * @since 1.10.0
	 *
	 * @param array $classes CSS classes to filter.
	 *
	 * @return array filtered css classes.
	 */
	public function html_class_filter( $classes ) {
		if (
			isset( $_COOKIE['cmsmasters_mode_switcher_state'] ) &&
			'second' === $_COOKIE['cmsmasters_mode_switcher_state']
		) {
			$classes[] = 'cmsmasters-mode-switcher-active';
		}

		return $classes;
	}

}
